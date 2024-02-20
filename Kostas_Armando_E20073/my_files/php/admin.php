<?php
// admin.php
require 'vendor/autoload.php';
session_start();

// Check if the user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Redirect to the login page
    header("Location: login.php");
    exit;
}

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->Store;
$usersCollection = $db->users;

$allUsers = $usersCollection->find();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="/Kostas_Armando_E20073/my_files/styles/admin.css" />
    <link rel="stylesheet" href="/Kostas_Armando_E20073/my_files/repeated/styles.css" />
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #e4e2d6;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        h2 {
            text-align: center;
            padding: 20px 0;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            background-color: #e4e2d6;
        }

        th {

            background-color: #a68c62;
        }

        td a {
            text-decoration: none;
            color: #007bff;
        }

        td a:hover {
            text-decoration: underline;
        }

        .no-history {
            color: #888;
        }
    </style>
</head>

<body style="background-color: #e4e2d6;">
    <div id="header-placeholder"></div>
    <h2 style="background-color: #a68c62;">Admin Dashboard</h2>
    <table style="background-color: #e4e2d6;">
        <thead style="background-color: #e4e2d6;">
            <tr style="background-color: #e4e2d6;">
                <th>User ID</th>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Action</th>
                <th>Login History</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($allUsers as $user) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['_id']); ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <a href="edit_user.php?id=<?php echo htmlspecialchars($user['_id']); ?>">Edit</a> |
                        <a href="delete_user.php?id=<?php echo htmlspecialchars($user['_id']); ?>">Delete</a>
                    </td>
                    <td>
                        <?php if (isset($user['login_history'])) : ?>
                            <table>
                                <tr>
                                    <th>Login Time</th>
                                    <th>Logout Time</th>
                                    <th>Duration</th>
                                </tr>
                                <?php foreach ($user['login_history'] as $loginRecord) : ?>
                                    <tr>
                                        <td><?php echo $loginRecord['loginTime']; ?></td>
                                        <td><?php echo $loginRecord['logoutTime'] ?? 'Still logged in'; ?></td>
                                        <td><?php echo $loginRecord['durationFormatted'] ?? '-'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        <?php else : ?>
                            <span class="no-history">No login history</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div id="footer-placeholder"></div>
    <script src="/Kostas_Armando_E20073/my_files/repeated/script.js"></script>
</body>

</html>