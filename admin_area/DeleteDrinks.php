<?php
if(isset($_GET['delete_drinks'])) 
{
    $delID = $_GET['delete_drinks'];
    // delete query
    $del_data = "delete from `drink` where DrinkID = $delID";
    $result = mysqli_query($con,$del_data);
    if($result)
    {
        echo "<script>alert('Drink deleted successfully.')</script>";
        echo "<script>window.open('index.php?view_drinks','_self')</script>";
    }
}
?>