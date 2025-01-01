<?php
if(isset($_GET['delete_slot'])) 
{
    $delID = $_GET['delete_slot'];
    $del_data = "DELETE FROM `delivery_time_slots` WHERE TimeSlotID = $delID";
    $result = mysqli_query($con, $del_data);
    if($result)
    {
        echo "<script>alert('Time slot deleted successfully.')</script>";
        echo "<script>window.open('index.php?view_slot', '_self')</script>";
    }
}
?>