<?php
require 'vendor/autoload.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: \Kostas_Armando_E20073\my_files\php\login.php");
    exit;
}

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->Store;
$collection = $db->users;


$user_id = $_SESSION['user_id'];
$user = $collection->findOne(['_id' => $user_id]);

if (!$user) {
    echo "Error: User not found.";
    exit;
}

// Update form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newName = htmlspecialchars($_POST["new_name"]);
    $newEmail = htmlspecialchars($_POST["new_email"]);
    $newPassword = $_POST["new_password"];

    // Update user information
    $updateFields = [];

    if (!empty($newName)) {
        $updateFields['name'] = $newName;
    }

    if (!empty($newEmail)) {
        $updateFields['email'] = $newEmail;
    }

    if (!empty($newPassword)) {
        if (strlen($newPassword) >= 5) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateFields['password'] = $hashedPassword;
        } else {
            echo "Error: Password must be at least 8 characters long.";
            exit;
        }
    }

    $updateResult = $collection->updateOne(
        ['_id' => $user_id],
        ['$set' => $updateFields]
    );

    if ($updateResult->getModifiedCount() > 0) {
        $user = $collection->findOne(['_id' => $user_id]);
        echo "Profile updated successfully!";
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
    <title>Vintagure</title>
    <link rel="stylesheet" href="/Kostas_Armando_E20073/my_files/repeated/styles.css" />
    <link rel="stylesheet" href="/Kostas_Armando_E20073/my_files/styles/profile.css" />
</head>


<body style="background-color: #e4e2d6;">
    <div id="header-placeholder"></div>

    <h2>User information</h2>
    <!-- User information -->
    <p style="text-align: center;">Name: <?php echo htmlspecialchars($user['name']); ?></p>
    <p style="text-align: center;">Username: <?php echo htmlspecialchars($user['username']); ?></p>
    <p style="text-align: center;">Email: <?php echo htmlspecialchars($user['email']); ?></p>

    <!-- Profile -->
    <div class="centered-form">
        <h2 style="color: black;">Edit Profile</h2>
        <form id="fr" method="post" action="profile.php">
            <label for="new_name">New Name:</label>
            <input type="text" name="new_name" placeholder="Enter new name">
            <label for="new_email">New Email:</label>
            <input type="email" name="new_email" placeholder="Enter new email">
            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" placeholder="Enter new password">
            <button id="btn" type="submit">Update Profile</button>
        </form>
    </div>

    <div id="footer-placeholder"></div>
    <script src="/Kostas_Armando_E20073/my_files/repeated/script.js"></script>
</body>


</html>