<h3 class="mb-4 mt-5 text-center">Are you sure you want to delete your account?</h3>
<form action="" method="post" class="mt-5">
    <div class="text-center mb-3">
        <button type="submit" name="delete" class="btn btn-danger">YES, Delete Account</button>
    </div>
    <div class="text-center">
        <button type="submit" name="dont_delete" class="btn btn-secondary">NO, Don't Delete</button>
    </div>
</form>

<?php
// Check if session is already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the database connection file
include('../include/connect.php'); // Adjust the path as needed

if (!isset($_SESSION['username'])) {
    echo "<script>alert('No active session found. Please log in first.')</script>";
    echo "<script>window.open('../login.php', '_self')</script>";
    exit;
}

$username_session = $_SESSION['username'];

if (isset($_POST['delete'])) {
    // Sanitize input to prevent SQL injection
    $username_session = mysqli_real_escape_string($con, $username_session);
    
    $delete_query = "DELETE FROM `user_table` WHERE `UserName` = '$username_session'";
    $result = mysqli_query($con, $delete_query);

    if ($result) {
        session_destroy();
        echo "<script>alert('Account deleted successfully')</script>";
        echo "<script>window.open('../index.php', '_self')</script>";
    } else {
        echo "<script>alert('Error deleting account. Please try again later.')</script>";
    }
}

if (isset($_POST['dont_delete'])) {
    echo "<script>window.open('profile.php', '_self')</script>";
}
?>
