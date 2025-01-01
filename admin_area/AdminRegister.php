<?php 
include('../include/connect.php');
include('../functions/common_function.php');
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
        
        <!-- font awesome link -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" 
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" 
        crossorigin="anonymous" referrerpolicy="no-referrer" />

        <style>
            body{
                overflow-x: hidden;
            }
        </style>
</head>
<body>
    <div class="container-fluid m-3">
        <h1 class="text-center mb-5 mt-2">Admin Registeration</h1>
    
        <div class="row d-flex justify-content-center">
            <div class="col-lg-6 col-xl-4">
                <img src="../images/register.jpg" alt="Admin Registration"
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
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email"
                        placeholder="Enter your email" required="required"
                        class="form-control">
                    </div>
                    <div class="form-outline mb-4">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password"
                        placeholder="Enter your password" required="required"
                        class="form-control">
                    </div>
                    <div class="form-outline mb-4">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password"
                        placeholder="Confirm your password" required="required"
                        class="form-control">
                    </div>
                    <!-- button -->
                    <div class="mt-4 pt-2">
                    <input type="submit" value="Register"
                    class="btn px-3 py-2 text-light border-0"
                    style="background-color: #6c5641" name="admin_register">
                    <p class="small fw-bold mt-2 pt-1 mb-0">Already have an account ? <a href="AdminLogin.php"> Log in</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

<!-- php code -->
<?php
if(isset($_POST['admin_register']))
{
    $AdminUsername = $_POST['username'];
    $AdminEmail = $_POST['email'];
    $AdminPassword = $_POST['password'];
    $hash_password = password_hash($AdminPassword,PASSWORD_DEFAULT);
    $confirm_password = $_POST['confirm_password'];

    // select query
    $select_query = "select * from `admin_table` where AdminName = '$AdminUsername' or AdminEmail = '$AdminEmail'";
    $result = mysqli_query($con,$select_query);
    $rows_count = mysqli_num_rows($result);
    if($rows_count>0)
    {
        echo "<script>alert('Username or email already exists')</script>";
    }else if($confirm_password != $AdminPassword)
    {
        echo "<script>alert('Password does not match !')</script>";
    }else
    {
        // insert query
        $insert_query = "insert into `admin_table` (AdminName,AdminEmail,AdminPassword) 
        values ('$AdminUsername','$AdminEmail','$hash_password')";
        $sql_execute = mysqli_query($con,$insert_query);

        if($sql_execute)
        {
            echo "<script>alert('Data inserted successfully!')</script>";
            echo "<script>window.open('AdminLogin.php','_self')</script>";
        }else
        {
            die(mysqli_error($con));
        }
    }
}
?>