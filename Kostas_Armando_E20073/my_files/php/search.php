<?php
require 'vendor/autoload.php';

function getProductsByCategory($category)
{
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $db = $client->Store;
    $collection = $db->products;
    $products = $collection->find(['category' => $category]);
    return $products;
}
session_start();
$userLoggedIn = isset($_SESSION['user_id']);

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->Store;
$collection = $db->products;

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $searchQuery = $_GET['search'];

    $product = $collection->findOne(['name' => $searchQuery]);
    if ($product) {
        $productId = (string) $product['_id'];
        header("Location: /Kostas_Armando_E20073/my_files/php/products_page.php");
        exit();
    }

    $categoryProducts = getProductsByCategory($searchQuery);
    if (iterator_count($categoryProducts) > 0) {
        header("Location: /Kostas_Armando_E20073/my_files/php/products_page.php?");
        exit();
    }
}

echo "<script>";
echo "alert('Nothing found. Please try again.');";
echo "window.location.href = '/Kostas_Armando_E20073/my_files/interfaces/home.html';";
echo "</script>";
