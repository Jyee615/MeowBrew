<?php 
include('../include/connect.php');
include('../functions/common_function.php');
@session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Register</title>

    <!-- bootstrap css link -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
        rel="stylesheet" 
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" 
        crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/css2?family=Sansita+One&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
        
        <!-- font awesome link -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" 
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" 
        crossorigin="anonymous" referrerpolicy="no-referrer" />

        <style>
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
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1002;
        }
        header h1 {
            font-family: 'Sansita One', Arial, sans-serif;
            font-size: 1.8em;
            margin: 0;
        }
        header .profile {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        header .profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid white;
        }
        
        .sidebar {
            position: fixed;
            top: 80px;
            left: 0;
            width: 250px;
            height: 100%;
            background: #fff;
            box-shadow: 2px 0 6px rgba(0, 0, 0, 0.1);
            padding-top: 20px;
            z-index: 1003;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar ul li {
            margin: 10px 0;
            position: relative;
        }
        .sidebar ul li a {
            color: #333;
            text-decoration: none;
            font-weight: 500;
            padding: 10px 20px;
            display: block;
            cursor: pointer;
            transition: background 0.3s, color 0.3s;
        }
        .sidebar ul li a:hover {
            background: #CCAB8C;
            color: white;
        }
        .sidebar ul li ul {
            list-style: none;
            padding-left: 20px;
            display: none;
        }
        .sidebar ul li.active ul {
            display: block;
        }
        .sidebar ul li ul li a {
            font-weight: normal;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            margin-top: 100px;
        }
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        .dashboard-card {
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            transition: transform 0.3s;
        }
        .dashboard-card:hover {
            transform: scale(1.05);
        }
        footer {
            background-color: #CCAB8C;
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        .drink_img{
            width: 100px;
            object-fit: contain;
        }

        .user_img{
            width: 100px;
            height: 100px;
            object-fit: contain;
        }

        .AdminImage{
            width: 100px;
            height: 100px;
            object-fit: contain;
        }
        </style>
</head>
<body>
    <!-- Header -->
    <header>
            <h1>Admin Dashboard</h1>
            <div class="header-center" style="text-align: center; margin: 0 auto;">
            <a href="index.php" style="text-decoration: none; color: inherit;">
                <img src="../images/logo.jpg" alt="MeowBrew Logo" style="vertical-align: middle; width: 60px; height: auto;">
                <h1 style="display: inline; margin: 0; font-family: 'Sansita One', Arial, sans-serif; color: white;">MeowBrew</h1>
            </a>
        </div>
        <div class="profile">
            <?php if (isset($_SESSION['username']) && !empty($_SESSION['username'])): ?>
                <img src="../images/admin.jpg" alt="Admin Profile">
                <span>Welcome, <?php echo $_SESSION['username']; ?></span>
            <?php else: ?>
                <div style="display: flex; gap: 10px;">
                    <!-- Admin Login Button -->
                    <a href="AdminLogin.php" class="btn btn-light" style="background-color: #CCAB8C; color: white; text-decoration: none; padding: 5px 10px; border-radius: 5px;">
                        Admin Login
                    </a>
                    <!-- User Login Button -->
                    <a href="../users_area/user_login.php" class="btn btn-light" style="background-color: #fff; color: #CCAB8C; text-decoration: none; padding: 5px 10px; border-radius: 5px; border: 1px solid #CCAB8C;">
                        User Login
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <div class="container-fluid m-3">
        <h1 class="text-center mb-5 mt-2">Admin Login</h1>
    
        <div class="row d-flex justify-content-center">
            <div class="col-lg-6 col-xl-4">
                <img src="../images/login.jpg" alt="Admin Login"
                class="img-fluid">
            </div>
            <div class="col-lg-6 col-xl-5">
                <form action="" method="post">
                    <div class="form-outline mb-4">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" id="username" name="username"
                        placeholder="Enter your username" required="required"
                        class="form-control">
                    </div>
                    <div class="form-outline mb-4">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password"
                        placeholder="Enter your password" required="required"
                        class="form-control">
                    </div>
                    <!-- button -->
                    <div class="mt-4 pt-2">
                    <input type="submit" value="Login"
                    class="btn px-3 py-2 text-light border-0"
                    style="background-color: #CCAB8C" name="admin_login">
                    <p class="small fw-bold mt-2 pt-1 mb-0">Don't have an account ? <a href="AdminRegister.php"> Register Now</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        &copy; 2024 MeowBrew. All Rights Reserved.
    </footer>
</body>
</html>

<?php
if(isset($_POST['admin_login']))
{
    $AdminUsername = $_POST['username'];
    $AdminPassword = $_POST['password'];

    $select_query = "select * from `admin_table` where AdminName = '$AdminUsername'";
    $result = mysqli_query($con,$select_query);
    $row_count = mysqli_num_rows($result);
    $row_data = mysqli_fetch_assoc($result);

    if($row_count>0)
    {
        $_SESSION['username'] = $AdminUsername;
        if(password_verify($AdminPassword,$row_data['AdminPassword']))
        {
            
            $_SESSION['username'] = $AdminUsername;
            echo "<script>alert('Login successful')</script>";
            echo "<script>window.open('index.php','_self')</script>";
        }else
        {
            echo "<script>alert('Invalid Password')</script>";
        }
    }else
    {
        echo "<script>alert('Invalid Credentials')</script>";
    }
}
?>