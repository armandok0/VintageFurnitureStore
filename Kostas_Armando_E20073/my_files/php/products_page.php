<?php
require 'vendor/autoload.php';
session_start();

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->Store;
$productsCollection = $db->products;

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Function to add a product to cart
function addToCart($productId)
{
    global $productsCollection;
    $product = $productsCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($productId)]);
    if ($product) {
        $productArray = iterator_to_array($product);
        $serializedProduct = serialize($productArray);
        if (is_string($serializedProduct)) {
            $serializedProduct = unserialize($serializedProduct);
        }
        $_SESSION['cart'][$productId] = $serializedProduct;
        return true;
    }
    return false;
}

// Check if the add to cart form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $productId = $_POST['product_id'];
    addToCart($productId);
}

// Check if the favorites session exists
if (!isset($_SESSION['favorites'])) {
    $_SESSION['favorites'] = [];
}

// Function to add a product to favorites
function addToFavorites($productId)
{
    global $productsCollection;
    $product = $productsCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($productId)]);
    if ($product) {
        $productArray = iterator_to_array($product);
        $serializedProduct = serialize($productArray);
        if (is_string($serializedProduct)) {
            $serializedProduct = unserialize($serializedProduct);
        }
        $_SESSION['favorites'][$productId] = $serializedProduct;
        return true;
    }

    return false;
}

// Check if the add to favorites form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_favorites'])) {
    $productId = $_POST['product_id'];
    addToFavorites($productId);
}

function getUniqueCategories()
{
    global $productsCollection;
    return $productsCollection->distinct('category');
}

function getProductsByCategory($category)
{
    global $productsCollection;
    return $productsCollection->find(['category' => $category]);
}

$categories = getUniqueCategories();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Furniture Store</title>
</head>

<link rel="stylesheet" href="/Kostas_Armando_E20073/my_files/repeated/styles.css" />

<style>
    .categories {
        padding: 1em;
    }

    .category {
        margin-bottom: 20px;
    }

    .category-title {
        font-size: 24px;
        font-weight: bold;
    }

    .products {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
    }

    .product {
        box-sizing: border-box;
        width: calc(25% - 2em);
        margin: 1em;
        padding: 1em;
        border: 1px solid #ddd;
        border-radius: 8px;
        background-color: #f0eee4;
        cursor: pointer;
    }

    .product:hover {
        transform: scale(1.05);
    }

    .comments {
        margin-top: 1em;
    }

    .product img {
        width: 100%;
        border-radius: 4px;
    }

    /* Desktop View */
    @media (min-width: 1100px) {
        .products {
            justify-content: space-between;
        }
    }

    /* Tablet View */
    @media (max-width: 1100px) {
        .categories {
            grid-template-columns: repeat(3, 1fr);
        }

        .products {
            justify-content: space-around;
        }

        .product {
            width: calc(50% - 2em);
        }
    }

    /* Mobile View */
    @media (max-width: 780px) {
        .categories {
            grid-template-columns: repeat(2, 1fr);
        }

        .product {
            width: calc(100% - 2em);
        }
    }
</style>

<body style="background-color: #e4e2d6; color: #a68c62;">
    <div id="header-placeholder"></div>
    <div class="categories">
        <?php foreach ($categories as $category) : ?>
            <section class="category">
                <div class="category-title"><?php echo $category; ?></div>
                <div class="products">
                    <?php
                    $categoryProducts = getProductsByCategory($category);
                    foreach ($categoryProducts as $product) : ?>
                        <article class="product">
                            <img src="<?php echo htmlspecialchars($product['imageFilePath']); ?>" alt="Product Image">
                            <p>Bio: <?php echo htmlspecialchars($product['info']); ?></p>
                            <p>Name: <?php echo htmlspecialchars($product['name']); ?></p>
                            <p>Price: <?php echo htmlspecialchars($product['price']); ?></p>
                            <p>Color: <?php echo htmlspecialchars($product['color']); ?></p>
                            <p>Quantity: <?php echo htmlspecialchars($product['quantity']); ?></p>
                            <form method="post" onsubmit="return addToCartAlert()">
                                <input type="hidden" name="product_id" value="<?php echo $product['_id']; ?>">
                                <button type="submit" name="add_to_cart" style="background-color: #a68c62; border: none; color: white; padding: 10px 20px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer;">Add to Cart</button>
                            </form>

                            <?php if (isset($_SESSION['user_id'])) : ?>
                                <form method="post" onsubmit="return addToFavoritesAlert()">
                                    <input type="hidden" name="product_id" value="<?php echo $product['_id']; ?>">
                                    <button type="submit" name="add_to_favorites" style="background-color: #a68c62; border: none; color: white; padding: 10px 20px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer;">Add to Favorites</button>
                                </form>
                            <?php endif; ?>

                            <?php if (!empty($product['reviews'])) : ?>
                                <div class="comments">
                                    <h4>Reviews:</h4>
                                    <?php
                                    $reviewsPerPage = 2;
                                    $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
                                    $startIndex = ($currentPage - 1) * $reviewsPerPage;

                                    $reviewsArray = iterator_to_array($product['reviews']);
                                    $paginatedReviews = array_slice($reviewsArray, $startIndex, $reviewsPerPage);

                                    foreach ($paginatedReviews as $review) : ?>
                                        <div class="card mb-2" style="background-color: #e4e2d6; color: #a68c62;">
                                            <div class="card-body">
                                                <p class="card-text"><strong>User:</strong> <?php echo htmlspecialchars($review['user_id']); ?></p>
                                                <p class="card-text"><strong>Rating:</strong> <?php echo htmlspecialchars($review['rating']); ?></p>
                                                <p class="card-text"><strong>Comment:</strong> <?php echo htmlspecialchars($review['comment']); ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>

                                    <nav>
                                        <ul class="pagination">
                                            <?php
                                            $totalPages = ceil(count($reviewsArray) / $reviewsPerPage);

                                            for ($i = 1; $i <= $totalPages; $i++) {
                                                echo '<li class="page-item ' . ($currentPage == $i ? 'active' : '') . '"><a class="page-link" style="background-color: #e4e2d6; color: #a68c62 !important;" href="?page=' . $i . '&product_id=' . $product['_id'] . '">' . $i . '</a></li>';
                                            }
                                            ?>
                                        </ul>
                                    </nav>

                                    <?php
                                    $total_ratings = 0;
                                    $count_ratings = count($reviewsArray);

                                    foreach ($reviewsArray as $review) {
                                        $total_ratings += $review['rating'];
                                    }

                                    if ($count_ratings > 0) {
                                        $mean_rating = $total_ratings / $count_ratings;
                                    ?>
                                        <div class="card" style="background-color: #e4e2d6; color: #a68c62;">
                                            <div class="card-body">
                                                <p class="card-text"><strong>Total Rating:</strong> <?php echo number_format($mean_rating, 1); ?></p>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php endif; ?>

                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
    </div>

    <div id="footer-placeholder"></div>
    <script src="/Kostas_Armando_E20073/my_files/repeated/script.js"></script>

    <script>
        function addToCartAlert() {
            alert('Item added to cart!');
            return true;
        }

        function addToFavoritesAlert() {
            alert('Item added to favorites!');
            return true;
        }
    </script>
</body>


</html>