<?php
if (isset($_GET['delete_admin'])) {
    $adminID = $_GET['delete_admin'];
    
    // Delete query
    $delete_query = "DELETE FROM `admin_table` WHERE AdminID = $adminID";
    $result = mysqli_query($con, $delete_query);

    if ($result) {
        echo "<script>alert('Admin deleted successfully.')</script>";
        echo "<script>window.open('index.php?list_admins', '_self')</script>";
    } else {
        echo "<script>alert('Failed to delete admin. Please try again.')</script>";
        echo "<script>window.open('index.php?list_admins', '_self')</script>";
    }
}
?>