<?php
if(isset($_GET['delete_payment'])) 
{
    $delID = $_GET['delete_payment'];
    // delete query
    $del_data = "delete from `user_payments` where PaymentID = $delID";
    $result = mysqli_query($con,$del_data);
    if($result)
    {
        echo "<script>alert('Payment deleted successfully.')</script>";
        echo "<script>window.open('index.php?list_payments','_self')</script>";
    }
}
?>