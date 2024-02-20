<?php
require 'vendor/autoload.php';
session_start();

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->Store;
$resultsCollection = $db->survey;

$questions = [
    [
        'question' => 'How satisfied are you with our services?',
        'emojis' => ['😊', '😐', '😕', '😡'],
    ],
    [
        'question' => 'Would you recommend our product to others?',
        'emojis' => ['👍', '👎'],
    ],

    [
        'question' => 'Did you find the information you were looking for?',
        'emojis' => ['✔️', '❌', '🤷‍♂️'],
    ],
];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_survey"])) {
    $surveyData = [];

    foreach ($questions as $key => $question) {
        $answer = isset($_POST["q$key"]) ? $_POST["q$key"] : '';
        $surveyData[] = [
            'question' => $question['question'],
            'answer' => $answer,
        ];
    }

    $resultsCollection->insertOne([
        'user_id' => $_SESSION['user_id'],
        'timestamp' => new MongoDB\BSON\UTCDateTime(),
        'responses' => $surveyData,
    ]);

    echo "<script>alert('Survey submitted successfully.'); window.location.replace('\products_page.php');</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Vintagure</title>
    <link rel="stylesheet" href="/Kostas_Armando_E20073/my_files/repeated/styles.css" />
    <style>
        .containerr {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin-top: 20px;
        }

        .question-container {
            margin: 20px;
            padding: 20px;
            background-color: #fff;
            color: #a68c62;
            border: 1px solid #a68c62;
            border-radius: 8px;
            width: 80%;
            max-width: 600px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .emojis {
            display: flex;
            justify-content: space-around;
            font-size: 24px;
            cursor: pointer;
        }

        .emojis label {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .submit-btn {
            background-color: #a68c62;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 18px;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 20px;
        }

        .submit-btn:hover {
            background-color: #876f4a;
        }
    </style>
</head>

<body style="background-color: #e4e2d6; color:#a68c62">
    <div id="header-placeholder"></div>
    <div class="containerr">
        <h2 class="mb-4">Before You Go...</h2>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <?php foreach ($questions as $key => $question) : ?>
                <div class="question-container">
                    <p><?php echo $question['question']; ?></p>
                    <div class="emojis">
                        <?php foreach ($question['emojis'] as $emoji) : ?>
                            <label>
                                <input type="radio" name="q<?php echo $key; ?>" value="<?php echo $emoji; ?>" />
                                <?php echo $emoji; ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="submit-btn" name="submit_survey">Submit Survey</button>
        </form>
    </div>

    <div id="footer-placeholder"></div>
    <script src="/Kostas_Armando_E20073/my_files/repeated/script.js"></script>
</body>

</html>