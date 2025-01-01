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

        .contact-header {
            background-color: #CCAB8C;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
            margin-bottom: 20px;
        }
        .contact-container {
            max-width: 600px;
            margin: 90px auto 20px;
            padding: 20px;
            background: white;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            position: relative;
        }
        .contact-card {
            background: #fafafa;
            padding: 20px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }
        .contact-card h3 {
            margin: 0 0 10px;
            color: #CCAB8C;
        }
        .contact-card p {
            margin: 5px 0;
            color: #555;
        }
        .contact-card a {
            color: #CCAB8C;
            text-decoration: none;
        }
        .contact-card a:hover {
            text-decoration: underline;
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

    <div class="contact-container">
        <div class="contact-header">
            <h1>Contact Information</h1>
        </div>
        <div class="contact-card">
            <h3>Angela Hiew Jia Yee</h3>
            <p><strong>Matric No:</strong> 83236</p>
            <p><strong>Email:</strong> <a href="mailto:83236@siswa.unimas.my">83236@siswa.unimas.my</a></p>
        </div>
        <div class="contact-card">
            <h3>Chieng Hui Ru</h3>
            <p><strong>Matric No:</strong> 83478</p>
            <p><strong>Email:</strong> <a href="mailto:83478@siswa.unimas.my">83478@siswa.unimas.my</a></p>
        </div>
        <div class="contact-card">
            <h3>Shawn Fum Jun Xuan</h3>
            <p><strong>Matric No:</strong> 85669</p>
            <p><strong>Email:</strong> <a href="mailto:85669@siswa.unimas.my">85669@siswa.unimas.my</a></p>
        </div>
        <div class="contact-card">
            <h3>Edwin Wong Kiing En</h3>
            <p><strong>Matric No:</strong> 86414</p>
            <p><strong>Email:</strong> <a href="mailto:86414@siswa.unimas.my">86414@siswa.unimas.my</a></p>
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