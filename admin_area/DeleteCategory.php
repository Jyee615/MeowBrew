<?php
if(isset($_GET['delete_cat'])) 
{
    $delID = $_GET['delete_cat'];
    // delete query
    $del_data = "delete from `category` where CategoryID = $delID";
    $result = mysqli_query($con,$del_data);
    if($result)
    {
        echo "<script>alert('Category deleted successfully.')</script>";
        echo "<script>window.open('index.php?view_cat','_self')</script>";
    }
}
?>