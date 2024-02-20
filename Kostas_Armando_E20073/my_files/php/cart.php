<?php

require 'vendor/autoload.php';
session_start();

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->Store;
$productsCollection = $db->products;

// Get product details by ID
function getProductById($productId, $collection)
{
    return $collection->findOne(['_id' => new MongoDB\BSON\ObjectId($productId)]);
}

// Display items
function displayCart()
{
    if (!empty($_SESSION['cart'])) {
        $totalPrice = 0;
        foreach ($_SESSION['cart'] as $productId => $cartItem) {
            $product = getProductById($productId, $GLOBALS['productsCollection']);
            if ($product) {
                $price = $product['price'];
                $quantity = $cartItem['quantity'] ?? 1;
                $subtotal = $price * $quantity;
                $totalPrice += $subtotal;

                echo '<div class="product-card">';
                echo '<h3>' . htmlspecialchars($product['name']) . '</h3>';
                echo '<p>Price: $' . htmlspecialchars($price) . '</p>';
                echo '<p>Quantity: ' . htmlspecialchars($quantity) . '</p>';
                echo '<p>Subtotal: $' . htmlspecialchars($subtotal) . '</p>';
                echo '<form method="post">';
                echo '<input type="hidden" name="product_id" value="' . $productId . '">';
                echo '<label for="quantity">Update Quantity:</label>';
                echo '<input type="number" name="quantity" value="' . $quantity . '" min="1">';
                echo '<button type="submit" name="update_quantity">Update Quantity</button>';
                echo '</form>';
                echo '<form method="post">';
                echo '<input type="hidden" name="product_id" value="' . $productId . '">';
                echo '<button type="submit" name="delete_item">Delete Item</button>';
                echo '</form>';
                echo '</div>';
            }
        }

        // Display total price
        echo '<div class="total-price">';
        echo '<h4>Total Price: $' . htmlspecialchars($totalPrice) . '</h4>';
        echo '</div>';
        $_SESSION['cart_total'] = $totalPrice;
    } else {
        echo '<p>Your cart is empty</p>';
    }
}

// Reserve products in the cart for 24 hours
function reserveProducts($collection)
{
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $productId => &$cartItem) {
            if (is_array($cartItem)) {
                $cartItem['reserved_until'] = time() + (24 * 3600);
            }
        }
    }
}

// Handles Purchase
function handlePurchase($name, $surname, $deliveryPlace, $email, $phoneNumber, $productsCollection, $ordersCollection)
{
    // Retrieve order details 
    $orderDetails = $_SESSION['cart'] ?? [];
    $totalPrice = $_SESSION['cart_total'] ?? 0;

    // Check if the user is logged in
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    // Check product availability
    $availabilityCheckPassed = true;

    foreach ($orderDetails as $productId => $cartItem) {
        $product = getProductById($productId, $productsCollection);

        if ($product) {
            $availableQuantity = $product['quantity'];

            if ($cartItem['quantity'] > $availableQuantity) {
                $availabilityCheckPassed = false;
                echo '<p>Sorry, the requested quantity for product "' . htmlspecialchars($product['name']) . '" is not available.</p>';
                break;
            }
        }
    }

    // update product quantity
    if ($availabilityCheckPassed) {
        foreach ($orderDetails as $productId => $cartItem) {
            $product = getProductById($productId, $productsCollection);
            if ($product) {
                $newQuantity = $product['quantity'] - $cartItem['quantity'];
                $productsCollection->updateOne(
                    ['_id' => new MongoDB\BSON\ObjectId($productId)],
                    ['$set' => ['quantity' => $newQuantity]]
                );
            }
        }

        // Create a new document with user ID if logged in
        $orderDocument = [
            'name' => $name,
            'surname' => $surname,
            'delivery_place' => $deliveryPlace,
            'email' => $email,
            'phone_number' => $phoneNumber,
            'order_details' => $orderDetails,
            'total_price' => $totalPrice,
            'status' => "ordered",
            'order_date' => new MongoDB\BSON\UTCDateTime(),
        ];

        // Include user ID only if logged in
        if ($userId) {
            $orderDocument['user_id'] = $userId;
        }

        // Insert the order in the database
        $ordersCollection->insertOne($orderDocument);

        // Clear the cart
        $_SESSION['cart'] = [];
        $_SESSION['cart_total'] = 0;

        // Display alert
        echo '<script>alert("Purchase made successfully!");</script>';
        header("Location: /Kostas_Armando_E20073\my_files\php\survay.php");
    } else {
        echo '<p>Failed to complete the purchase due to insufficient product quantity.</p>';
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_quantity'])) {
        $productId = $_POST['product_id'];
        $newQuantity = max(1, $_POST['quantity']);
        $_SESSION['cart'][$productId]['quantity'] = $newQuantity;
    } elseif (isset($_POST['delete_item'])) {
        $productId = $_POST['product_id'];
        unset($_SESSION['cart'][$productId]);
    }

    // Check if the purchase form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['purchase'])) {
        $name = $_POST['name'] ?? '';
        $surname = $_POST['surname'] ?? '';
        $deliveryPlace = $_POST['delivery_place'] ?? '';
        $email = $_POST['email'] ?? '';
        $phoneNumber = $_POST['phone_number'] ?? '';

        // Check if the cart is empty
        if (empty($_SESSION['cart'])) {
            echo '<p>Your cart is empty. Please add items before making a purchase.</p>';
        } elseif (empty($name) || empty($surname) || empty($deliveryPlace) || empty($email) || empty($phoneNumber)) {
            echo '<p>Please provide complete delivery information</p>';
        } else {
            $invalidQuantity = false;
            foreach ($_SESSION['cart'] as $cartItem) {
                if (isset($cartItem['quantity']) && $cartItem['quantity'] < 1) {
                    $invalidQuantity = true;
                    break;
                }
            }
            if ($invalidQuantity) {
                echo '<p>Please ensure that the quantity for each item is at least 1.</p>';
            } else {
                handlePurchase($name, $surname, $deliveryPlace, $email, $phoneNumber, $productsCollection, $db->orders);
            }
        }
    }
}

// Reserve products
reserveProducts($productsCollection);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Vintagure</title>
</head>
<link rel="stylesheet" href="/Kostas_Armando_E20073/my_files/repeated/styles.css" />

<style>
    body {
        font-family: 'Arial', sans-serif;
        margin: 0;
        padding: 0;
    }

    body h1 {
        text-align: center;
    }

    .product-card {
        max-width: 400px;
        margin: 20px auto;
        padding: 20px;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .product-card h2 {

        margin-bottom: 5px;
    }

    #frm {
        display: flex;
        flex-direction: column;
        margin-bottom: 10px;
    }

    label {

        margin-bottom: 5px;
        font-weight: bold;
    }

    input {
        margin-bottom: 10px;
        padding: 5px;
    }

    button {
        background-color: #a68c62;
        color: #fff;
        border: none;
        padding: 8px 16px;
        cursor: pointer;
        margin-right: 10px;
    }

    button:hover {
        background-color: #876f4a;
    }

    .total-price {
        margin-top: 20px;
        text-align: right;
    }
</style>


<body style="background-color: #e4e2d6; color: #a68c62;">

    <header>
        <div id="header-placeholder"></div>
    </header>
    <h1>Card's Products</h1>
    <div class="product-card">
        <?php displayCart(); ?>
        <form method="post" id="frm">
            <h2>Enter Shipping Details</h2>
            <label for="name">Name:</label>
            <input type="text" name="name" required>
            <label for="surname">Surname:</label>
            <input type="text" name="surname" required>
            <label for="delivery_place">Delivery Place:</label>
            <input type="text" name="delivery_place" required>
            <label for="email">Email:</label>
            <input type="email" name="email" required>
            <label for="phone_number">Phone Number:</label>
            <input type="tel" name="phone_number" required>
            <button type="submit" name="purchase">Purchase</button>
        </form>
    </div>
    <div id="footer-placeholder"></div>
    <script src="/Kostas_Armando_E20073/my_files/repeated/script.js"></script>
</body>

</html>