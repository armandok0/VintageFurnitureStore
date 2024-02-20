<?php
require 'vendor/autoload.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /Kostas_Armando_E20073/my_files/php/login.php");
    exit;
}

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->Store;
$ordersCollection = $db->orders;
$usersCollection = $db->users;
$productsCollection = $db->products;

$user_id = $_SESSION['user_id'];
$userDetails = $usersCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($user_id)]);
$orderHistory = $ordersCollection->find(['user_id' => new MongoDB\BSON\ObjectId($user_id)]);

// Review submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add_review"])) {
        $productId = htmlspecialchars($_POST["product_id"]);
        $rating = intval($_POST["rating"]);
        $comment = htmlspecialchars($_POST["comment"]);
        $product = $productsCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($productId)]);
        if (!$product) {
            die("Error: Product not found.");
        }

        // New review
        $updateResult = $productsCollection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($productId)],
            [
                '$push' => [
                    'reviews' => [
                        'user_id' => new MongoDB\BSON\ObjectId($user_id),
                        'rating' => $rating,
                        'comment' => $comment,
                    ],
                ],
            ]
        );

        if ($updateResult->getModifiedCount() > 0) {
            // Review added successfully
            echo "<script>alert('Review added successfully.'); window.location.replace('/Kostas_Armando_E20073/my_files/php/orderhistory.php');</script>";
            exit;
        } else {
            // Error adding review
            echo "<script>alert('Error adding review.'); window.location.replace('/Kostas_Armando_E20073/my_files/php/orderhistory.php');</script>";
            exit;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Order History</title>
    <link rel="stylesheet" href="/Kostas_Armando_E20073/my_files/repeated/styles.css" />

</head>

<style>
    #frm {
        max-width: 400px;
        margin: 20px auto;
        padding: 20px;
        background-color: #f8f8f8;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
        color: #333;
    }

    input[type="number"],
    textarea {
        width: 100%;
        padding: 10px;
        margin-bottom: 16px;
        box-sizing: border-box;
        border: 1px solid #ccc;
        border-radius: 4px;
        background-color: #fff;
        transition: border-color 0.3s;
    }

    input[type="number"]:focus,
    textarea:focus {
        border-color: #6fa3ef;
    }

    button {
        background-color: #4caf50;
        color: #fff;
        padding: 12px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s;
    }

    button:hover {
        background-color: #45a049;
    }
</style>

<body style="background-color: #e4e2d6; color: #a68c62; text-align: center;">
    <div id="header-placeholder"></div>

    <?php if ($userDetails) : ?>
        <?php if (!empty($orderHistory)) : ?>
            <?php foreach ($orderHistory as $order) : ?>
                <h2>Order History for <?php echo isset($order['surname']) ? htmlspecialchars($order['name'] . ' ' . $order['surname']) : 'N/A'; ?></h2>
                <p>Order Date: <?php echo isset($order['order_date']) ? $order['order_date']->toDateTime()->format('Y-m-d H:i:s') : 'N/A'; ?></p>
                <p>Total Price: $<?php echo isset($order['total_price']) ? $order['total_price'] : 'N/A'; ?></p>
                <p>Email: <?php echo isset($order['email']) ? htmlspecialchars($order['email']) : 'N/A'; ?></p>
                <p>Phone Number: <?php echo isset($order['phone_number']) ? htmlspecialchars($order['phone_number']) : 'N/A'; ?></p>
                <p>Delivery Place: <?php echo isset($order['delivery_place']) ? htmlspecialchars($order['delivery_place']) : 'N/A'; ?></p>
                <p>Products Purchased:</p>

                <?php foreach ($order['order_details'] as $productId => $productDetails) : ?>

                    <?php
                    $product = $productsCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($productId)]);
                    $productName = isset($productDetails['name']) && is_string($productDetails['name']) ? htmlspecialchars($productDetails['name']) : 'Unknown Product';
                    $quantity = isset($productDetails['quantity']) ? $productDetails['quantity'] : 1;
                    echo htmlspecialchars($quantity) . ' x ' . $productName;
                    ?>
                    <td>

                        <form id="frm" method="POST" action="">
                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['_id']); ?>">
                            <label for="rating">Rating:</label>
                            <input type="number" name="rating" min="1" max="5" required>
                            <br>
                            <label for="comment">Comment:</label>
                            <textarea name="comment" rows="3" required></textarea>
                            <br>
                            <button type="submit" name="add_review">Add Review</button>
                        </form>
                    </td>

                    </li>
                <?php endforeach; ?>

            <?php endforeach; ?>
        <?php else : ?>
            <p>No order history found.</p>
        <?php endif; ?>
    <?php else : ?>
        <p>No user details found.</p>
    <?php endif; ?>

    <div id="footer-placeholder"></div>
    <script src="/Kostas_Armando_E20073/my_files/repeated/script.js"></script>
</body>

</html>