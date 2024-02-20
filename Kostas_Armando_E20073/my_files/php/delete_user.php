<?php

require 'vendor/autoload.php';
session_start();

//intelephense Egatastash mongodb
use MongoDB\Client;
use MongoDB\BSON\ObjectId;

// Check if user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
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

// User deletion
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $deleteResult = $usersCollection->deleteOne(['_id' => new ObjectId($user_id)]);

    if ($deleteResult->getDeletedCount() > 0) {
        echo "User deleted successfully!";
        header("Location: admin.php");
        exit;
    } else {
        echo "Error deleting user.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Vintagure - Delete User</title>
    <link rel="stylesheet" href="/Kostas_Armando_E20073/my_files/styles/profile.css" />
    <link rel="stylesheet" href="/Kostas_Armando_E20073/my_files/repeated/styles.css" />
</head>

<body style="background-color: #e4e2d6;">

    <div id="header-placeholder"></div>
    <h2>Delete User</h2>
    <p style="text-align: center;">Name: <?php echo htmlspecialchars($user['name']); ?></p>
    <p style="text-align: center;">Username: <?php echo htmlspecialchars($user['username']); ?></p>
    <p style="text-align: center;">Email: <?php echo htmlspecialchars($user['email']); ?></p>

    <div class="centered-form">
        <h3>Are you sure you want to delete this user?</h3>
        <form method="post" action="delete_user.php?id=<?php echo $user_id; ?>">
            <button type="submit">Delete User</button>
        </form>
    </div>

    <div id="footer-placeholder"></div>
    <script src="/Kostas_Armando_E20073/my_files/repeated/script.js"></script>
    <script></script>
</body>

</html>