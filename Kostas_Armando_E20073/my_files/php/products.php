<?php
// products.php
require 'vendor/autoload.php';
session_start();

// Check user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Redirect to the login page
    header("Location: login.php");
    exit;
}

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->Store;
$productsCollection = $db->products;

//Fetch all products
function getAllProducts()
{
    global $productsCollection;
    return $productsCollection->find();
}

function addProduct($category, $name, $info, $price, $color, $quantity, $imageFilePath)
{
    global $productsCollection;

    $productDocument = [
        'category' => $category,
        'name' => $name,
        'info' => $info,
        'price' => $price,
        'color' => $color,
        'quantity' => $quantity,
        'imageFilePath' => $imageFilePath,
        'reviews' => [],
    ];

    $insertResult = $productsCollection->insertOne($productDocument);

    return $insertResult->getInsertedCount() > 0;
}

// Update product
function updateProduct($productId, $category, $name, $info, $price, $color, $quantity, $imageFilePath)
{
    global $productsCollection;

    $updateResult = $productsCollection->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($productId)],
        [
            '$set' => [
                'category' => $category,
                'name' => $name,
                'info' => $info,
                'price' => $price,
                'color' => $color,
                'quantity' => $quantity,
                'imageFilePath' => $imageFilePath,
            ],
        ]
    );

    return $updateResult->getModifiedCount() > 0;
}

// Delete product
function deleteProduct($productId)
{
    global $productsCollection;

    $deleteResult = $productsCollection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($productId)]);

    return $deleteResult->getDeletedCount() > 0;
}

if (isset($_GET["delete"])) {
    $productIdToDelete = $_GET["delete"];
    if (deleteProduct($productIdToDelete)) {
        // Product deleted successfully
        header("Location: products.php");
        exit;
    } else {
        $error_message = "Error deleting product.";
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add_product"])) {
        $category = htmlspecialchars($_POST["category"]);
        $name = htmlspecialchars($_POST["name"]);
        $info = htmlspecialchars($_POST["info"]);
        $price = floatval($_POST["price"]);
        $color = htmlspecialchars($_POST["color"]);
        $quantity = intval($_POST["quantity"]);
        $imageFilePath = htmlspecialchars($_POST["image"]);

        if (addProduct($category, $name, $info, $price, $color, $quantity, $imageFilePath)) {
            // Product added successfully
            header("Location: products.php");
            exit;
        } else {
            // Error 
            $error_message = "Error adding product.";
        }
    } elseif (isset($_POST["update_product"])) {
        $productId = htmlspecialchars($_POST["product_id"]);
        $category = htmlspecialchars($_POST["category"]);
        $name = htmlspecialchars($_POST["name"]);
        $info = htmlspecialchars($_POST["info"]);
        $price = floatval($_POST["price"]);
        $color = htmlspecialchars($_POST["color"]);
        $quantity = intval($_POST["quantity"]);
        $imageFilePath = htmlspecialchars($_POST["image"]);

        if (updateProduct($productId, $category, $name, $info, $price, $color, $quantity, $imageFilePath)) {
            // Product updated successfully
            header("Location: products.php");
            exit;
        } else {
            // Error 
            $error_message = "Error updating product.";
        }
    }
}


// Display
$allProducts = getAllProducts();
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
            font-family: 'Arial', sans-serif;
        }

        body h2 {
            color: black;
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .centered-form {
            max-width: 400px;
            margin: 20px auto;
            background-color: #f0f0f0;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .centered-form label {
            display: block;
            margin-bottom: 12px;
            color: #555;
        }

        .centered-form input[type="text"],
        .centered-form input[type="number"],
        .centered-form textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
        }

        .centered-form button {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #a68c62;
            color: white;
            border: 1px solid #a68c62;
            border-radius: 8px;
            cursor: pointer;
        }

        .centered-form {
            text-align: center;
        }

        .centered-form h2 {
            color: black;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            border-radius: 8px;
        }

        th,
        td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #a68c62;
            color: #fff;
        }

        td a {
            text-decoration: none;
            color: #007bff;
        }

        td a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body style="background-color: #e4e2d6; color: #a68c62;">
    <div id="header-placeholder"></div>
    <h2>Product Management</h2>
    <form method="POST" action="" class="centered-form">
        <?php
        // Check if product editing is requested
        if (isset($_GET["edit"])) {
            $productId = $_GET["edit"];
            $product = $productsCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($productId)]);
            if ($product) {
                echo '<input type="hidden" name="product_id" value="' . $productId . '">';
                echo '<h2>Edit Product</h2>';
            }
        } else {
            echo '<h2>Add Product</h2>';
        }
        ?>

        <label for="category">Category:</label>
        <input type="text" name="category" value="<?php echo $product['category'] ?? ''; ?>" required>

        <label for="name">Name:</label>
        <input type="text" name="name" value="<?php echo $product['name'] ?? ''; ?>" required>

        <label for="info">Product Information:</label>
        <textarea name="info" rows="4" required><?php echo $product['info'] ?? ''; ?></textarea>

        <label for="price">Price:</label>
        <input type="number" name="price" step="0.01" value="<?php echo $product['price'] ?? ''; ?>" required>

        <label for="color">Color:</label>
        <input type="text" name="color" value="<?php echo $product['color'] ?? ''; ?>" required>

        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" value="<?php echo $product['quantity'] ?? ''; ?>" required>

        <label for="image">Image File Path:</label>
        <input type="text" name="image" value="<?php echo $product['imageFilePath'] ?? ''; ?>" required>

        <?php
        if (isset($_GET["edit"])) {
            // Display update button
            echo '<button type="submit" name="update_product">Update Product</button>';
        } else {
            // Display add button
            echo '<button type="submit" name="add_product">Add Product</button>';
        }
        ?>
    </form>

    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Name</th>
                <th>Product Information</th>
                <th>Price</th>
                <th>Color</th>
                <th>Quantity</th>
                <th>Image Path</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($allProducts as $product) : ?>
                <tr>
                    <!-- Display product information -->
                    <td><?php echo htmlspecialchars($product['category']); ?></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['info']); ?></td>
                    <td><?php echo htmlspecialchars($product['price']); ?></td>
                    <td><?php echo htmlspecialchars($product['color']); ?></td>
                    <td><?php echo htmlspecialchars($product['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($product['imageFilePath']); ?></td>
                    <td>
                        <a href="products.php?edit=<?php echo htmlspecialchars($product['_id']); ?>">Edit</a> |
                        <a href="products.php?delete=<?php echo htmlspecialchars($product['_id']); ?>" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div id="footer-placeholder"></div>
    <script src="/Kostas_Armando_E20073/my_files/repeated/script.js"></script>
    <script>
        function deleteProduct(productId) {
            if (confirm('Are you sure you want to delete this product?')) {
                window.location.href = 'delete_products.php?id=' + productId;
            }
        }
    </script>
</body>

</html>