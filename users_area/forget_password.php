<?php 
include('../include/connect.php');
include('../functions/common_function.php');
@session_start();

if (isset($_POST['verify_user'])) {
    $user_email = mysqli_real_escape_string($con, $_POST['user_email']);
    $user_mobile = mysqli_real_escape_string($con, $_POST['user_mobile']);

    // Check if the email and mobile number exist in the database
    $query = "SELECT * FROM `user_table` WHERE UserEmail = '$user_email' AND UserMobile = '$user_mobile'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        // Generate a token and save it to the session
        $_SESSION['reset_email'] = $user_email;
        $_SESSION['reset_token'] = bin2hex(random_bytes(16));
        echo "<script>alert('Verification successful. Proceed to reset your password.');</script>";
        echo "<script>window.open('reset_password.php', '_self');</script>";
    } else {
        echo "<script>alert('Invalid email or mobile number.');</script>";
    }
}
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

    <!-- Verify -->
    <div class="container-fluid my-5">
        <h2 class="text-center" style="font-family: 'Poppins', sans-serif; font-weight: bold; margin-top: 8rem;">Verify</h2>
        <div class="row d-flex align-items-center justify-content-center mt-4">
            <div class="col-md-6 col-lg-5">
                <form action="" method="post" style="background-color: #f9f9f9; padding: 20px; border-radius: 8px;">
                    <!-- Email Field -->
                    <div class="form-outline mb-4">
                        <label for="user_email" class="form-label" style="font-weight: bold;">Email <span style="color: red;">*</span></label>
                        <input type="email" id="user_email" class="form-control" 
                            placeholder="Enter your registered email address" 
                            autocomplete="off" required name="user_email" 
                            style="border: 1px solid #ccc; border-radius: 4px; padding: 10px;">
                    </div>
                    <!-- Mobile Number Field -->
                    <div class="form-outline mb-4">
                        <label for="user_mobile" class="form-label" style="font-weight: bold;">Contact Number <span style="color: red;">*</span></label>
                        <input type="text" id="user_mobile" class="form-control" 
                            placeholder="Enter your registered contact number" 
                            autocomplete="off" required name="user_mobile" 
                            style="border: 1px solid #ccc; border-radius: 4px; padding: 10px;">
                    </div>
                    <!-- Buttons -->
                    <div class="d-flex justify-content-between mt-3">
                        <!-- Back Button -->
                        <a href="user_login.php" class="btn text-dark" 
                            style="background-color: white; font-weight: bold; padding: 10px 20px; border: 1px solid #ccc; border-radius: 4px; text-decoration: none;">
                            Back
                        </a>
                        <!-- Verify Button -->
                        <input type="submit" value="Verify" 
                            class="btn text-light" 
                            style="background-color: #CCAB8C; font-weight: bold; padding: 10px 20px; border-radius: 4px;"  
                            name="verify_user">
                    </div>
                </form>     
            </div>
        </div>
    </div>

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


<?php
if (isset($_POST['user_login'])) {
    // Retrieve email and password from form submission
    $user_email = mysqli_real_escape_string($con, $_POST['user_email']);
    $user_password = mysqli_real_escape_string($con, $_POST['user_password']);

    // Fetch user details from the database using email
    $select_query = "SELECT * FROM `user_table` WHERE UserEmail = '$user_email'";
    $result = mysqli_query($con, $select_query);
    $row_count = mysqli_num_rows($result);

    // Get user IP address
    $user_ip = getIPAddress();

    // Fetch cart details for the user's IP
    $select_query_cart = "SELECT * FROM `cart_detail` WHERE IP_address = '$user_ip'";
    $select_cart = mysqli_query($con, $select_query_cart);
    $row_count_cart = mysqli_num_rows($select_cart);

    if ($row_count > 0) {
        $row_data = mysqli_fetch_assoc($result);

        // Verify the entered password with the hashed password from the database
        if (password_verify($user_password, $row_data['UserPassword'])) {
            $_SESSION['username'] = $row_data['UserName'];
            $_SESSION['user_id'] = $row_data['UserID']; // Set user_id in session

            if ($row_count_cart == 0) {
                echo "<script>alert('Login successful');</script>";
                echo "<script>window.open('profile.php', '_self');</script>";
            } else {
                echo "<script>alert('Login successful');</script>";
                echo "<script>window.open('payment.php', '_self');</script>";
            }
        } else {
            // Invalid password
            echo "<script>alert('Invalid Password');</script>";
        }
    } else {
        // Email not found
        echo "<script>alert('Invalid Email Address');</script>";
    }
}
?>
