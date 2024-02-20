<?php

require 'vendor/autoload.php';
session_start();

// JSON response
if (isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'User is already logged in']);
    exit;
}

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->Store;
$usersCollection = $db->users;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $loginUsername = htmlspecialchars($_POST["login_username"]);
    $loginPassword = $_POST["login_password"];

    $user = $usersCollection->findOne(['username' => $loginUsername]);

    if ($user) {
        if (password_verify($loginPassword, $user['password'])) {
            $_SESSION['user_id'] = $user['_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Login history
            $loginTimestamp = time();
            $loginRecord = [
                'loginTime' => date('Y-m-d H:i:s', $loginTimestamp),
                'logoutTime' => null,
                'duration' => null,
            ];

            // Initializes
            if (!isset($_SESSION['login_history'])) {
                $_SESSION['login_history'] = [];
            }

            // Appends
            $_SESSION['login_history'][] = $loginRecord;

            // Updates MongoDB with new login history
            $usersCollection->findOneAndUpdate(
                ['_id' => $user['_id']],
                ['$push' => ['login_history' => $loginRecord]]
            );
            // JSON response
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Login successful!']);
            exit;
        } else {
            // JSON response
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Invalid password.']);
            exit;
        }
    } else {
        // JSON response
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'User not found.']);
        exit;
    }
}
