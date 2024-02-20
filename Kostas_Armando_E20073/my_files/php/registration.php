<?php

require 'vendor/autoload.php';

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->Store;
$collection = $db->users;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST["name"]);
    $username = htmlspecialchars($_POST["username"]);
    $email = htmlspecialchars($_POST["email"]);
    $password = $_POST["password"];

    if ($collection->countDocuments(['username' => $username]) > 0) {
        echo "Username is used. Choose another.";
        exit;
    }

    if ($collection->countDocuments(['email' => $email]) > 0) {
        echo "Email is used. Choose another.";
        exit;
    }

    // Password
    if (strlen($password) < 5) {
        echo "Password must be at least 5 characters long.";
        exit;
    }
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $userDocument = [
        'name' => $name,
        'username' => $username,
        'email' => $email,
        'password' => $hashedPassword,
        'role' => 'user', // or 'admin' 
        'login_history' => [],
    ];
    $insertResult = $collection->insertOne($userDocument);

    if ($insertResult->getInsertedCount() > 0) {
        echo "Registration successful!";
    } else {
        echo "Error occurred.";
    }
}
