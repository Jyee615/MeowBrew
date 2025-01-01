<?php 
include('include/connect.php');
include('functions/common_function.php');
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MeowBrew</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Sansita+One&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <!-- font awesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" 
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" 
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        /* General styles */
        body {
            font-family: 'Poppins', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            color: #333;
            overflow-x: hidden;
        }
        header {
            background-color: #CCAB8C;
            color: white;
            height: 80px;
            display: flex;
            justify-content: center;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1002;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        header .header-center {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        header .header-center img {
            height: 60px;
        }
        header .header-center h1 {
            font-family: 'Sansita One', Arial, sans-serif;
            font-size: 1.8em;
            margin: 0;
        }
        header .header-right {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
        }
        .navbar {
            background-color: #CCAB8C;
            margin-top: 80px;
        }
        .navbar a {
            color: white !important;
            font-weight: 500;
        }

        .profile-img {
            width: 40px; 
            height: 40px; 
            border-radius: 50%;
            object-fit: cover; 
            border: 2px solid #CCAB8C; 
            cursor: pointer; 
        }

        /* Sidebar styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: -250px;
            width: 250px;
            height: 100%;
            background: #fff;
            box-shadow: 2px 0 6px rgba(0, 0, 0, 0.1);
            padding-top: 100px;
            z-index: 1003;
            transition: left 0.3s ease-in-out;
        }
        .sidebar.open {
            left: 0;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar ul li {
            margin: 20px 0;
        }
        .sidebar ul li a {
            color: #333;
            text-decoration: none;
            font-weight: 500;
            padding: 10px 20px;
            display: block;
            transition: background 0.3s, color 0.3s;
        }
        .sidebar ul li a:hover {
            background: #CCAB8C;
            color: white;
        }
        .sidebar-toggle {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1004;
            background-color: #CCAB8C;
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Search bar styles */
        .search-bar {
            position: relative;
            top: 80px; /* Adjust as needed to align with the bottom of the header */
            margin: 0 auto;
            text-align: center;
            z-index: 1001;
            background-color: #f3f4f6;
            padding: 10px 0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .search-bar form {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }
        .search-bar input[type="text"] {
            width: 80%;
            max-width: 500px;
            padding: 10px;
            font-size: 1em;
            border: 1px solid #ddd;
            border-radius: 20px;
            outline: none;
            transition: border-color 0.3s;
        }
        .search-bar input[type="text"]:focus {
            border-color: #CCAB8C;
        }
        .search-bar button {
            background-color: #CCAB8C;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.2rem;
        }
        .search-bar button:hover {
            background-color: #b8997c;
        }

        /* Navbar styles */
        .nav-bar-container {
            display: flex;
            justify-content: center;
            border-top: 3px solid #000;
            border-bottom: 3px solid #000;
            margin-top: 80px; /* Adjust to match the header */
            z-index: 1001;
            position: relative;
        }

        .nav-tabs {
            display: flex;
            gap: 30px;
            padding: 15px 0;
        }

        .nav-tab {
            text-decoration: none;
            font-size: 1rem;
            color: #333;
            font-weight: bold;
            padding: 10px 15px;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .nav-tab:hover {
            color: #CCAB8C;
            border-bottom: 3px solid #CCAB8C;
        }

        .nav-tab.active {
            color: #CCAB8C;
            font-weight: bold;
            border-bottom: 3px solid #CCAB8C;
        }


        /* Product card styles */
        .product-card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            background: white;
            transition: transform 0.3s;
        }
        .product-card:hover {
            transform: scale(1.05);
        }

        /* Footer styles */
        footer {
            background-color: #CCAB8C;
            color: white;
            text-align: center;
            padding: 20px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
    <div class="header-center">
            <a href="index.php" style="text-decoration: none; color: inherit;">
                <img src="./images/logo.jpg" alt="MeowBrew Logo" style="vertical-align: middle;">
                <h1 style="display: inline; margin: 0; font-family: 'Sansita One', Arial, sans-serif;">MeowBrew</h1>
            </a>
        </div>
        <div class="header-right">
        <a href="cart.php" class="btn btn-light me-2"><i class="fa-solid fa-cart-shopping"></i> 
            <sup>
                <?php
                cartItem(); 
                ?>
            </sup>
        </a>
        <?php if (isset($_SESSION['username'])): ?>
            <?php
            // Fetching the user's profile picture and linking the profile page
            $username = $_SESSION['username'];
            $userprofileimage_query = "SELECT * FROM `user_table` WHERE UserName = '$username'";
            $userprofileimage_result = mysqli_query($con, $userprofileimage_query);
            $row_profileimage = mysqli_fetch_array($userprofileimage_result);
            $userprofileimage = !empty($row_profileimage['UserImage']) ? $row_profileimage['UserImage'] : 'default.jpg'; // Default image if UserImage is empty
            ?>
            <!-- Profile Picture with Link -->
            <a href="./users_area/profile.php" style="text-decoration: none;">
                <img src="./users_area/users_images/<?php echo $userprofileimage; ?>" alt="Profile Picture" 
                    style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
            </a>
        <?php else: ?>
            <!-- Sign In Button -->
            <a href="./users_area/user_login.php" class="btn btn-light">Sign In</a>
        <?php endif; ?>
        </div>
    </header>

    <!-- Sidebar -->
    <button class="sidebar-toggle">&#9776;</button>
    <aside class="sidebar">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="displayAll.php">Products</a></li>
            <li><a href="cart.php">Shopping Cart</a></li>
            <li><a href="contact.php">Contact</a></li>
            <?php if (isset($_SESSION['username'])): ?>
                <li><a href="./users_area/logout.php" style="color: red; font-weight: bold;">Logout</a></li>
            <?php endif; ?>
        </ul>
    </aside>

    <!-- Search Bar -->
    <div class="search-bar">
        <form action="SearchDrinks.php" method="get">
            <input type="text" name="searchData" placeholder="Search for products..." />
            <button type="submit" name="searchDataDrink">
                <span class="material-icons">search</span>
            </button>
        </form>
    </div>

    <!-- Navbar -->
    <div class="nav-bar-container">
    <div class="nav-tabs">
        <!-- Highlight "All" if no specific category or recommend is selected -->
        <a href="displayAll.php" class="nav-tab <?php echo !isset($_GET['category']) && !isset($_GET['recommend']) ? 'active' : ''; ?>">All</a>
        <?php 
        // Highlight the recommend tabs
        $recommend_result = mysqli_query($con, "Select * from `recommend`");
        while ($row_data = mysqli_fetch_assoc($recommend_result)) {
            $recommendID = $row_data['RecommendID'];
            $recommendtype = $row_data['RecommendType'];
            $isActive = isset($_GET['recommend']) && $_GET['recommend'] == $recommendID ? 'active' : '';
            echo "<a href='index.php?recommend=$recommendID' class='nav-tab $isActive'>$recommendtype</a>";
        }

        // Highlight the category tabs
        $category_result = mysqli_query($con, "Select * from `category`");
        while ($row_data = mysqli_fetch_assoc($category_result)) {
            $categoryID = $row_data['CategoryID'];
            $category = $row_data['CategoryName'];
            $isActive = isset($_GET['category']) && $_GET['category'] == $categoryID ? 'active' : '';
            echo "<a href='index.php?category=$categoryID' class='nav-tab $isActive'>$category</a>";
        }
        ?>
    </div>
    </div>

    <!-- Cart -->
    <div class="container">
    <div class="row">
    <h2 class="text-center mb-4 mt-5" style="font-family: 'Poppins', sans-serif; font-weight: bold;">Your Order</h2>
        <form action="" method="post">
            <?php 
                $get_IPAdress = getIPAddress(); 
                $total = 0;
                $cart_query = "SELECT * FROM `cart_detail` WHERE IP_address = '$get_IPAdress'";
                $result_query = mysqli_query($con, $cart_query);
                $result_count = mysqli_num_rows($result_query);

                if ($result_count > 0) {
                    while ($row = mysqli_fetch_array($result_query)) {
                        $cartID = $row['CartID']; // Use CartID for unique identification
                        $drinkID = $row['DrinkID'];
                        $quantity = $row['Quantity'];
                        $sugar_level = $row['SugarLevel'];
                        $ice_level = $row['IceLevel'];
                        $select_drinks = "SELECT * FROM `drink` WHERE DrinkID = '$drinkID'";
                        $result_drinks = mysqli_query($con, $select_drinks);

                        while ($row_drink = mysqli_fetch_array($result_drinks)) {
                            $drink_name = $row_drink['DrinkName'];
                            $drink_image = $row_drink['DrinkImage01'];
                            $drink_price = $row_drink['DrinkPrice'];
                            $total_price = $drink_price * $quantity;
                            $total += $total_price;
            ?>
            <div class="d-flex justify-content-between align-items-start p-3 mb-3" 
    style="border-radius: 10px; background-color: #F8F9FA; padding: 15px; border: 1px solid #ddd;">
                <!-- Drink Image -->
                <img src="./images/<?php echo $drink_image; ?>" alt="<?php echo $drink_name; ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                
                <!-- Drink Details -->
                <div class="d-flex flex-column flex-grow-1 ms-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="fw-bold mb-0"><?php echo $drink_name; ?></h5>
                        <a href="drinkDetails.php?CartID=<?php echo $cartID; ?>" class="btn btn-sm" style="background-color: white; color: black; border: 1px solid black;">Edit</a>
                    </div>
                    <p class="mb-2">Qty: <?php echo $quantity; ?></p>
                    <p class="mb-4"><?php echo ucfirst($sugar_level); ?> | <?php echo ucfirst($ice_level); ?></p>
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="fw-bold mb-0" style="font-size: 1.2rem;">Price: RM<?php echo $total_price; ?></p>
                        <button type="submit" name="delete_cart[<?php echo $cartID; ?>]" class="btn p-0" style="background: none; border: none;">
                            <i class="fas fa-trash-alt" style="font-size: 1.5rem; color: black;"></i>
                        </button>
                    </div>
                </div>
            </div>
            <?php }}} else { ?>
            <h4 class="text-center text-danger">Your cart is empty.</h4>
            <?php } ?>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <!-- Add More Items Button -->
                <button type="button" class="btn btn-light border" onclick="window.location.href='index.php'">+ Add More Items</button>
                
                <!-- Total Price -->
                <h5 class="mb-0 ms-auto" style="margin-right: 1rem;">Total: RM<?php echo number_format($total, 2); ?></h5>
            </div>

            <?php if ($result_count > 0): ?>
                <!-- Order Now Button -->
                <div class="d-flex justify-content-center mt-3">
                    <a href="./users_area/payment.php" class="btn text-center" 
                    style="background-color: #CCAB8C; color: white; width: 200px; font-weight: bold; text-decoration: none;">
                        Order Now
                    </a>
                </div>
            <?php endif; ?>

            <!-- Space Below the Order Now Button -->
            <div class="mb-5"></div>

        </form>
    </div>
</div>

<?php
// Function to handle cart operations
function handle_cart_operations()
{
    global $con;

    // Handle delete action
    if (isset($_POST['delete_cart'])) {
        foreach ($_POST['delete_cart'] as $cartID => $value) {
            $delete_query = "DELETE FROM `cart_detail` WHERE CartID = $cartID"; // Delete by CartID
            $run_delete = mysqli_query($con, $delete_query);

            if ($run_delete) {
                echo "<script>alert('Item removed from cart.');</script>";
                echo "<script>window.open('cart.php', '_self');</script>";
            } else {
                echo "<script>alert('Failed to remove item.');</script>";
            }
        }
    }

    // Handle edit/update action (if applicable)
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['update_cart'] as $cartID => $value) {
            $new_quantity = $_POST['quantity'][$cartID]; // Get new quantity from the form
            $update_query = "UPDATE `cart_detail` SET Quantity = $new_quantity WHERE CartID = $cartID";
            $run_update = mysqli_query($con, $update_query);

            if ($run_update) {
                echo "<script>alert('Cart updated successfully.');</script>";
                echo "<script>window.open('cart.php', '_self');</script>";
            } else {
                echo "<script>alert('Failed to update cart.');</script>";
            }
        }
    }
}

// Call the function to handle cart operations
handle_cart_operations();
?>

    <!-- Footer -->
    <footer>
        &copy; 2024 MeowBrew. All Rights Reserved.
    </footer>

    <script>
        const sidebarToggle = document.querySelector('.sidebar-toggle');
        const sidebar = document.querySelector('.sidebar');
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });

        function decreaseQuantity(drinkID) {
            const quantityInput = document.querySelector(`input[name="quantity[${drinkID}]"]`);
            if (quantityInput.value > 1) {
                quantityInput.value = parseInt(quantityInput.value) - 1;
            }
        }

        function increaseQuantity(drinkID) {
            const quantityInput = document.querySelector(`input[name="quantity[${drinkID}]"]`);
            quantityInput.value = parseInt(quantityInput.value) + 1;
        }
    </script>
</body>
</html>
