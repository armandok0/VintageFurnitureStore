<?php
require 'vendor/autoload.php';
session_start();

// Check if the user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->Store;
$ordersCollection = $db->orders;
$usersCollection = $db->users;
$productsCollection = $db->products;

// Review submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_review"])) {
    $productId = htmlspecialchars($_POST["product_id"]);
    $rating = intval($_POST["rating"]);
    $comment = htmlspecialchars($_POST["comment"]);

    // Additional validation: Check if the product exists
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
                    'user_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id']),
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
$orderHistoryCursor = $ordersCollection->find();
$orderHistory = iterator_to_array($orderHistoryCursor);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Vintagure</title>
    <link rel="stylesheet" href="/Kostas_Armando_E20073/my_files/repeated/styles.css" />
</head>

<body style="background-color: #e4e2d6; color: #a68c62; text-align: center;">
    <div id="header-placeholder"></div>

    <div class="container mt-5">
        <h2>Total Orders: <?php echo count($orderHistory); ?></h2>
        <?php if (!empty($orderHistory)) : ?>
            <div class="card-columns">
                <?php foreach ($orderHistory as $order) : ?>
                    <div class="card" style="background-color: #e4e2d6; color: #a68c62;">
                        <div class="card-body">
                            <h5 class="card-title">Order ID: <?php echo $order['_id']; ?></h5>
                            <p class="card-text">Name: <?php echo isset($order['surname']) ? htmlspecialchars($order['name'] . ' ' . $order['surname']) : 'N/A'; ?></p>
                            <div class="btn-group">
                                <button class="btn btn-primary" onclick="toggleDetails('<?php echo $order['_id']; ?>')">View Details</button>
                                <button class="btn btn-danger" onclick="cancelOrder('<?php echo $order['_id']; ?>')">Cancel Order</button>
                            </div>
                            <div id="details_<?php echo $order['_id']; ?>" class="order-details" style="display: none;">
                                <p>Order Date: <?php echo isset($order['order_date']) ? $order['order_date']->toDateTime()->format('Y-m-d H:i:s') : 'N/A'; ?></p>
                                <p>Total Price: $<?php echo isset($order['total_price']) ? $order['total_price'] : 'N/A'; ?></p>
                                <p>Status: <?php echo isset($order['status']) ? $order['status'] : 'N/A'; ?></p>
                                <p>Products Purchased:</p>

                                <?php foreach ($order['order_details'] as $productId => $productDetails) : ?>
                                    <?php
                                    $product = $productsCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($productId)]);
                                    $productName = isset($productDetails['name']) && is_string($productDetails['name']) ? htmlspecialchars($productDetails['name']) : 'Unknown Product';
                                    $quantity = isset($productDetails['quantity']) ? $productDetails['quantity'] : 1;
                                    ?>
                                    <li><?php echo htmlspecialchars($quantity) . ' x ' . $productName; ?></li>
                                <?php endforeach; ?>

                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p>No order history found.</p>
        <?php endif; ?>
    </div>

    <div id="footer-placeholder"></div>
    <script src="/Kostas_Armando_E20073/my_files/repeated/script.js"></script>
    <script>
        function toggleDetails(orderId) {
            // Hide all order details
            var allDetails = document.querySelectorAll('.order-details');
            allDetails.forEach(function(details) {
                details.style.display = 'none';
            });

            // Show details for the clicked order
            var detailsDiv = document.getElementById('details_' + orderId);
            detailsDiv.style.display = 'block';
        }

        function cancelOrder(orderId) {
            var updateResult = <?php echo json_encode($productsCollection->updateOne(['_id' => new MongoDB\BSON\ObjectId($productId)], ['$set' => ['status' => 'cancelled']])) ?>;
            alert("Order with ID " + orderId + " has been canceled!");
        }
    </script>
</body>

</html>