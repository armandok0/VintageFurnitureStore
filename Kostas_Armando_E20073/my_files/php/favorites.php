<?php

require 'vendor/autoload.php';
session_start();

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->Store;
$productsCollection = $db->products;

// Check if the favorites session exists
if (!isset($_SESSION['favorites'])) {
    $_SESSION['favorites'] = [];
}

// Remove product from favorites
function removeFromFavorites($productId)
{
    if (isset($_SESSION['favorites'][$productId])) {
        unset($_SESSION['favorites'][$productId]);
        return true;
    }

    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_from_favorites'])) {
    $productId = $_POST['product_id'];
    removeFromFavorites($productId);
}

function getFavoriteProducts()
{
    global $productsCollection;
    $favoriteProducts = [];

    foreach ($_SESSION['favorites'] as $productId => $productArray) {
        $favoriteProducts[] = $productArray;
    }
    return $favoriteProducts;
}

$favoriteProducts = getFavoriteProducts();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Vintagure</title>
    <link rel="stylesheet" href="/Kostas_Armando_E20073/my_files/repeated/styles.css" />

    <style>
        body {
            background-color: #e4e2d6;
            color: #a68c62;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
            color: #3e4349;
        }

        .favorites {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            padding: 20px;
        }

        .product {
            box-sizing: border-box;
            width: calc(30% - 20px);
            margin: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f0eee4;
            cursor: pointer;
            transition: transform 0.3s ease-in-out;
        }

        .product:hover {
            transform: scale(1.05);
        }

        .product img {
            width: 100%;
            border-radius: 4px;
        }

        .product p {
            margin: 10px 0;
        }
    </style>
</head>

<body style="background-color: #e4e2d6; color:#a68c62">
    <div id="header-placeholder"></div>
    <h2>My Favorite Furniture</h2>
    <div class="favorites">
        <?php foreach ($favoriteProducts as $product) : ?>
            <article class="product">
                <img src="<?php echo htmlspecialchars($product['imageFilePath']); ?>" alt="Product Image">
                <p>Bio: <?php echo htmlspecialchars($product['info']); ?></p>
                <p>Name: <?php echo htmlspecialchars($product['name']); ?></p>
                <p>Price: <?php echo htmlspecialchars($product['price']); ?></p>
                <p>Color: <?php echo htmlspecialchars($product['color']); ?></p>
                <p>Quantity: <?php echo htmlspecialchars($product['quantity']); ?></p>
                <form method="post">
                    <input type="hidden" name="product_id" value="<?php echo $product['_id']; ?>">
                    <button type="submit" name="remove_from_favorites">Remove from Favorites</button>
                </form>
            </article>
        <?php endforeach; ?>
    </div>
    <div id="footer-placeholder"></div>
    <script src="/Kostas_Armando_E20073/my_files/repeated/script.js"></script>
</body>

</html>