<?php

require 'vendor/autoload.php';
session_start();

// JSON response
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'User is not logged in.']);
    exit;
}

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->Store;
$usersCollection = $db->users;


if (isset($_SESSION['login_history']) && is_array($_SESSION['login_history']) && !empty($_SESSION['login_history'])) {
    $lastLogin = array_pop($_SESSION['login_history']);

    // Update logout details
    $lastLogin['logoutTime'] = date('Y-m-d H:i:s');
    $lastLogin['duration'] = time() - strtotime($lastLogin['loginTime']);

    // Readable Duration
    $lastLogin['durationFormatted'] = formatDuration($lastLogin['duration']);


    $usersCollection->findOneAndUpdate(
        [
            '_id' => new MongoDB\BSON\ObjectID($_SESSION['user_id']),
            'login_history.loginTime' => $lastLogin['loginTime'],
        ],
        [
            '$set' => [
                'login_history.$.logoutTime' => $lastLogin['logoutTime'],
                'login_history.$.duration' => $lastLogin['duration'],
                'login_history.$.durationFormatted' => $lastLogin['durationFormatted'],
            ]
        ]
    );
}

$_SESSION = array();

session_destroy();


header('Location: \Kostas_Armando_E20073\my_files\interfaces\users.html');
exit;

exit;

// Readable Duration Function
function formatDuration($durationInSeconds)
{
    $duration = '';
    $timeUnits = [
        'day' => 24 * 60 * 60,
        'hour' => 60 * 60,
        'minute' => 60,
        'second' => 1,
    ];

    foreach ($timeUnits as $unit => $seconds) {
        if ($durationInSeconds >= $seconds) {
            $value = floor($durationInSeconds / $seconds);
            $durationInSeconds %= $seconds;
            $duration .= ($duration === '' ? '' : ' ') . $value . ' ' . $unit . ($value > 1 ? 's' : '');
        }
    }
    return $duration === '' ? '0 seconds' : $duration;
}
