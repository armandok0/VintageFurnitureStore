<?php
session_start();
$userLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Vintagure</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
</head>

<body>
    <header class="header">
        <div class="container">
            <div class="logo">
                <img src="/Kostas_Armando_E20073/my_files/images/companylogo.png" alt="Company's Image" />
            </div>
            <div class="search-bar">
                <form action="/Kostas_Armando_E20073/my_files/php/search.php" method="GET">
                    <input type="text" placeholder="Search for furniture" name="search" />
                    <button type="submit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                        </svg>
                    </button>
                </form>
            </div>

            <input type="checkbox" id="menu-toggle" class="hidden-checkbox" />
            <nav>
                <label for="menu-toggle" class="menu-icon">
                    <span></span>
                    <span></span>
                    <span></span>
                </label>
                <ul class="menu">
                    <li><a href="/Kostas_Armando_E20073/my_files/interfaces/home.html">Home</a></li>
                    <li><a href="/Kostas_Armando_E20073/my_files/php/products_page.php">Products</a></li>
                    <li><a href="/Kostas_Armando_E20073/my_files/interfaces/aboutUs.html">About Us</a></li>
                    <li><a href="/Kostas_Armando_E20073/my_files/php/cart.php"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart3" viewBox="0 0 16 16">
                                <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.415 11H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l.84 4.479 9.144-.459L13.89 4zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2" />
                            </svg></a></li>
                    <?php if ($userLoggedIn) : ?>
                        <?php if ($_SESSION['role'] == 'admin') : ?>
                            <li><a href="/Kostas_Armando_E20073/my_files/php/admin.php">Admin Dashboard</a></li>
                            <li><a href="/Kostas_Armando_E20073/my_files/php/products.php">Edit Products</a></li>
                            <li><a href="/Kostas_Armando_E20073/my_files/php/edit_orders.php">Edit Orders</a></li>
                        <?php else : ?>
                            <li><a href="/Kostas_Armando_E20073/my_files/php/favorites.php"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                                        <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.920 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15" />
                                    </svg></a></li>
                            <li><a href="/Kostas_Armando_E20073/my_files/php/profile.php">Profile</a></li>
                            <li><a href="/Kostas_Armando_E20073/my_files/php/orderhistory.php">Order History</a></li>
                        <?php endif; ?>
                        <li><a href="/Kostas_Armando_E20073/my_files/php/logout.php">Logout</a></li>
                    <?php else : ?>
                        <li><a href="/Kostas_Armando_E20073/my_files/interfaces/users.html"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z" />
                                </svg></a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>