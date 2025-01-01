<?php 
include('../include/connect.php');
include('../functions/common_function.php');
require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
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

    <!-- Login -->
<div class="container-fluid my-5">
    <h2 class="text-center" style="font-family: 'Poppins', sans-serif; font-weight: bold; margin-top: 8rem;">New User Registeration</h2>
    <div class="row d-flex align-items-center justify-content-center mt-4">
        <div class="col-md-6 col-lg-5">
        <form action="" method="post" enctype="multipart/form-data">
                <!-- username field -->
                <div class="form-outline mb-4">
                    <label for="user_username" class="form-label">Username</label>
                    <input type="text" id="user_username" class="form-control"
                    placeholder="Enter your username (alphabets only, no spaces)" autocomplete="off"
                    required="required" name="user_username"
                    pattern="^[A-Za-z]+$"
                    title="Username must contain only alphabets (uppercase or lowercase), no spaces, and no special characters."/>
                </div>
                <!-- email field -->
                <div class="form-outline mb-4">
                    <label for="user_email" class="form-label">Email</label>
                    <input type="email" id="user_email" class="form-control"
                    placeholder="Enter your email" autocomplete="off"
                    required="required" name="user_email"/>
                </div>
                <!-- image field -->
                <div class="form-outline mb-4">
                    <label for="user_image" class="form-label">User Image</label>
                    <input type="file" id="user_image" class="form-control"
                    required="required" name="user_image"/>
                </div>
                <!-- password field -->
                <div class="form-outline mb-4">
                    <label for="user_password" class="form-label">Password</label>
                    <input type="password" id="user_password" class="form-control"
                    placeholder="Enter a password (6-8 digits, 1 uppercase, 1 number, 1 special character)" autocomplete="off"
                    required="required" name="user_password"
                    pattern="^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,8}$"
                    title="Password must be 6-8 characters, include one uppercase letter, one number, and one special character, and contain no spaces."/>
                </div>
                <!-- confirm password field -->
                <div class="form-outline mb-4">
                    <label for="conf_user_password" class="form-label">Confirm Password</label>
                    <input type="password" id="conf_user_password" class="form-control"
                    placeholder="Enter your password again" autocomplete="off"
                    required="required" name="conf_user_password"/>
                </div>
                <!-- Address field -->
                <div class="form-outline mb-4">
                    <label for="user_address" class="form-label">Address</label>
                    <input type="text" id="user_address" class="form-control"
                    placeholder="Enter your address" autocomplete="off"
                    required="required" name="user_address"/>
                </div>
                <!-- Contact field -->
                <div class="form-outline mb-4">
                    <label for="user_contact" class="form-label">Contact</label>
                    <input type="text" id="user_contact" class="form-control"
                    placeholder="Enter your mobile number" autocomplete="off"
                    required="required" name="user_contact"/>
                </div>
                <!-- button -->
                <div class="mt-4 pt-2">
                <input type="submit" value="Register"
                class="btn text-light"
                style="background-color: #CCAB8C; font-weight: bold; padding: 10px 20px; border-radius: 4px;" name="user_register">
                <p class="small fw-bold mt-2 pt-1 mb-0">Already have an account ? <a href="user_login.php"> Log in</a></p>
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

<!-- php code -->
<?php
if(isset($_POST['user_register']))
{
    $user_username = $_POST['user_username'];
    $user_email = $_POST['user_email'];
    $user_password = $_POST['user_password'];
    $hash_password = password_hash($user_password,PASSWORD_DEFAULT);
    $conf_user_password = $_POST['conf_user_password'];
    $user_address = $_POST['user_address'];
    $user_contact = $_POST['user_contact'];
    $user_image = $_FILES['user_image']['name'];
    $user_image_tmp = $_FILES['user_image']['tmp_name'];
    $user_ip = getIPAddress();

    // select query
    $select_query = "select * from `user_table` where UserName = '$user_username' or UserEmail = '$user_email'";
    $result = mysqli_query($con,$select_query);
    $rows_count = mysqli_num_rows($result);
    if (!preg_match('/^[A-Za-z]+$/', $user_username)) {
        echo "<script>alert('Username must contain only alphabets (uppercase or lowercase), no spaces, and no special characters.')</script>";
    } else if ($rows_count > 0) {
        echo "<script>alert('Username or email already exists')</script>";
    } else if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,8}$/', $user_password)) {
        echo "<script>alert('Password must be 6-8 characters, include one uppercase letter, one number, one special character, and contain no spaces.')</script>";
    } else if ($conf_user_password != $user_password) {
        echo "<script>alert('Passwords do not match!')</script>";
    }
    else
    {
        // insert query
        move_uploaded_file($user_image_tmp,"./users_images/$user_image");
        $insert_query = "insert into `user_table` (UserName,UserEmail,UserPassword,
        UserImage,UserIP,UserAddress,UserMobile) values ('$user_username','$user_email','$hash_password',
        '$user_image','$user_ip','$user_address','$user_contact')";
        $sql_execute = mysqli_query($con,$insert_query);

        if($sql_execute)
        {
            echo "<script>alert('Data inserted successfully!')</script>";
            $mail = new PHPMailer(true);
            try {
                // SMTP Configuration
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Gmail SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = 'cherylchieng02@gmail.com'; // Your Gmail address
                $mail->Password = 'uvii hbmc tegc bvmx'; // Your Gmail app password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
            
                // Sender and recipient
                $mail->setFrom('cherylchieng02@gmail.com', 'MeowBrew');
                $mail->addAddress($user_email, $user_username);
            
                // Email content
                $mail->isHTML(true);
                $mail->Subject = 'Registration Completed';
                $mail->Body = "<h1>Welcome, $user_username!</h1>
                            <p>Thank you for registering at MeowBrew. We're excited to have you on board.</p>
                            <p>Start exploring our website and enjoy exclusive deals.</p>
                            <p>Best regards,<br>MeowBrew Team</p>";
            
                $mail->send();
                echo 'Email sent successfully!';
            } catch (Exception $e) {
                echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else
        {
            die(mysqli_error($con));
        }
    }

   // selecting cart items
    $select_cart_items = "select * from `cart_detail` where IP_address = '$user_ip'";
    $result_cart = mysqli_query($con,$select_cart_items);
    $rows_count = mysqli_num_rows($result_cart);
    if($rows_count>0)
    {
        $_SESSION['username'] = $user_username;
        echo "<script>alert('You have items in your cart.')</script>";
        echo "<script>window.open('../cart.php','_self')</script>";
    }else
    {
        echo "<script>window.open('../index.php','_self')</script>";
    }
}
?>