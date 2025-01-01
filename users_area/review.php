<?php 
include('../include/connect.php');
include('../functions/common_function.php');
@session_start();


// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Get UserID from session
$username = $_SESSION['username'];
$get_user = "SELECT * FROM `user_table` WHERE UserName = '$username'";
$result_user = mysqli_query($con, $get_user);
$row_user = mysqli_fetch_assoc($result_user);
$UserID = $row_user['UserID'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $OrderID = $_POST['OrderID'];
    $Rating = $_POST['Rating'];
    $Comments = mysqli_real_escape_string($con, $_POST['Comments']);

    // Insert review into the database
    $insert_review = "INSERT INTO `reviews` (`OrderID`, `UserID`, `Rating`, `Comments`) 
                      VALUES ('$OrderID', '$UserID', '$Rating', '$Comments')";
    if (mysqli_query($con, $insert_review)) {
        echo "<script>
                alert('Review submitted successfully!');
                window.location.href = 'http://localhost/MeowBrew/users_area/profile.php?my_orders';
              </script>";
        exit();
    } else {
        echo "<script>alert('Failed to submit review. Please try again.');</script>";
    }
}

// Get OrderID from URL
if (!isset($_GET['orderID']) || empty($_GET['orderID'])) {
    echo "<script>alert('Invalid Order ID'); window.location.href = 'my_orders.php';</script>";
    exit();
}
$OrderID = $_GET['orderID'];
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

        .form-outline label {
            font-weight: bold;
        }

        .review-container {
            margin-top: 8rem;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .review-form {
            background-color: #f9f9f9;
            padding: 2rem;
            border-radius: 8px;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .review-form h2 {
            text-align: center;
            margin-bottom: 1rem;
            font-family: 'Sansita One', sans-serif;
            color: #333;
        }
        .review-form label {
            font-weight: bold;
        }
        .review-form .form-select,
        .review-form .form-control {
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 10px;
        }
        .review-form .form-select:focus,
        .review-form .form-control:focus {
            border-color: #CCAB8C;
            box-shadow: 0 0 0 0.2rem rgba(204, 171, 140, 0.25);
        }
        .review-form .btn-primary {
            background-color: #CCAB8C;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            width: 100%;
            font-weight: bold;
        }
        .review-form .btn-primary:hover {
            background-color: #b8997c;
        }
        .review-form .btn-secondary {
            width: 100%;
            margin-top: 10px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-center">
            <a href="../index.php" style="text-decoration: none; color: inherit;">
                <img src="../images/logo.jpg" alt="MeowBrew Logo" style="vertical-align: middle;">
                <h1 style="display: inline; margin: 0; font-family: 'Sansita One', Arial, sans-serif;">MeowBrew</h1>
            </a>
        </div>
        <div class="header-right">
            <a href="../cart.php" class="btn btn-light me-2"><i class="fa-solid fa-cart-shopping"></i> 
                <sup>
                    <?php cartItem(); ?>
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
                <a href="../users_area/profile.php" style="text-decoration: none;">
                    <img src="./users_images/<?php echo $userprofileimage; ?>" alt="Profile Picture" 
                        style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                </a>
            <?php else: ?>
                <!-- Sign In Button -->
                <a href="../users_area/user_login.php" class="btn btn-light">Sign In</a>
            <?php endif; ?>
        </div>
    </header>

    <!-- Sidebar -->
    <button class="sidebar-toggle">&#9776;</button>
    <aside class="sidebar">
        <ul>
            <li><a href="../index.php">Home</a></li>
            <li><a href="../displayAll.php">Products</a></li>
            <li><a href="../cart.php">Shopping Cart</a></li>
            <li><a href="../contact.php">Contact</a></li>
            <?php if (isset($_SESSION['username'])): ?>
                <li><a href="../users_area/logout.php" style="color: red; font-weight: bold;">Logout</a></li>
            <?php endif; ?>
        </ul>
    </aside>

    <!-- review -->
    <div class="container review-container">
        <form class="review-form" action="review.php" method="POST" style="padding-bottom: 50px;">
            <h2 class="text-center" style="font-family: 'Poppins', sans-serif; font-weight: bold;">Leave a Review</h2>
            <input type="hidden" name="OrderID" value="<?php echo $OrderID; ?>">
            <!-- Rating -->
            <div class="mb-3">
                <label for="Rating" class="form-label">Rating (1-5):</label>
                <select name="Rating" id="Rating" class="form-select" required>
                    <option value="">Select Rating</option>
                    <option value="1">1 - Very Bad</option>
                    <option value="2">2 - Bad</option>
                    <option value="3">3 - Average</option>
                    <option value="4">4 - Good</option>
                    <option value="5">5 - Excellent</option>
                </select>
            </div>
            <!-- Comments -->
            <div class="mb-3">
                <label for="Comments" class="form-label">Comments:</label>
                <textarea name="Comments" id="Comments" rows="4" class="form-control" placeholder="Write your feedback here..." required></textarea>
            </div>
            <!-- Submit and Cancel Buttons -->
            <button type="submit" class="btn btn-primary">Submit Review</button>
            <a href="http://localhost/MeowBrew/users_area/profile.php?my_orders" class="btn btn-secondary mt-3">Cancel</a>
        </form>
    </div>
    <script src="../path/to/bootstrap.bundle.min.js"></script>

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
    </script>
</body>
</html>
