<?php
if(isset($_GET['delete_customer'])) 
{
    $delID = $_GET['delete_customer'];
    // delete query
    $del_data = "delete from `user_table` where UserID = $delID";
    $result = mysqli_query($con,$del_data);
    if($result)
    {
        echo "<script>alert('Customer deleted successfully.')</script>";
        echo "<script>window.open('index.php?list_customers','_self')</script>";
    }
}
?>