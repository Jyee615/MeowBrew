<?php
if(isset($_GET['delete_rec'])) 
{
    $delrecID = $_GET['delete_rec'];
    // delete query
    $del_data = "delete from `recommend` where RecommendID = $delrecID";
    $result = mysqli_query($con,$del_data);
    if($result)
    {
        echo "<script>alert('Recommend deleted successfully.')</script>";
        echo "<script>window.open('index.php?view_rec','_self')</script>";
    }
}
?>