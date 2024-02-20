<?php

require 'vendor/autoload.php';
session_start();

//intelephense Egatastash mongodb
use MongoDB\Client;
use MongoDB\BSON\ObjectId;

// Check if the user admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Redirect to the login page or any other page
    header("Location: login.php");
    exit;
}


$client = new Client("mongodb://localhost:27017");
$db = $client->Store;
$usersCollection = $db->users;

$user_id = $_GET['id'] ?? null;
$user = $usersCollection->findOne(['_id' => new ObjectId($user_id)]);

// Check if the user exists
if (!$user) {
    header("Location: admin.php");
    exit;
}

// Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newName = htmlspecialchars($_POST["new_name"]);
    $newEmail = htmlspecialchars($_POST["new_email"]);

    $updateFields = [];
    if (!empty($newName)) {
        $updateFields['name'] = $newName;
    }
    if (!empty($newEmail)) {
        $updateFields['email'] = $newEmail;
    }
    $updateResult = $usersCollection->updateOne(
        ['_id' => new ObjectId($user_id)],
        ['$set' => $updateFields]
    );

    if ($updateResult->getModifiedCount() > 0) {
        $user = $usersCollection->findOne(['_id' => new ObjectId($user_id)]);

        echo "User profile updated!";
    } else {

        echo "Error updating profile.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit User Profile</title>
    <link rel="stylesheet" href="/Kostas_Armando_E20073/my_files/styles/profile.css" />
    <link rel="stylesheet" href="/Kostas_Armando_E20073/my_files/repeated/styles.css" />
</head>

<body style="background-color: #e4e2d6;">

    <div id="header-placeholder"></div>
    <h2>Edit User Profile</h2>
    <p style="text-align: center;">Name: <?php echo htmlspecialchars($user['name']); ?></p>
    <p style="text-align: center;">Username: <?php echo htmlspecialchars($user['username']); ?></p>
    <p style="text-align: center;">Email: <?php echo htmlspecialchars($user['email']); ?></p>

    <div class="centered-form">
        <h2>Edit Profile</h2>
        <form id="fr" method="post" action="edit_user.php?id=<?php echo $user_id; ?>">
            <label for="new_name">New Name:</label>
            <input type="text" name="new_name" placeholder="Enter new name">
            <label for="new_email">New Email:</label>
            <input type="email" name="new_email" placeholder="Enter new email">
            <button id="btn" type="submit">Update Profile</button>
        </form>
    </div>

    <div id="footer-placeholder"></div>
    <script src="/Kostas_Armando_E20073/my_files/repeated/script.js"></script>
    <script></script>
</body>

</html>