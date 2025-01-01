<?php 
include('../include/connect.php');
if(isset($_POST['insert_recommend']))
{
    $RecommendType = trim($_POST['recommend_type']);

    //select data from database
    $select_query = "SELECT * FROM `recommend` WHERE RecommendType = '$RecommendType'";
    $result_select = mysqli_query($con, $select_query);
    $number = mysqli_num_rows($result_select);
    if($number > 0)
    {
        echo "<script>alert('The recommend type is already present in the database.')</script>";
    } else {
        $insert_query = "INSERT INTO `recommend` (RecommendType) VALUES('$RecommendType')";
        $result = mysqli_query($con, $insert_query);
        if($result)
        {
            echo "<script>alert('Recommend type has been inserted successfully!')</script>";
        }
    }  
}
?>

<div class="container mt-3 mb-5">
    <h2 class="text-center mb-4">Add Recommend Type</h2>

    <form action="" method="post" class="w-50 m-auto" style="border: 1px solid #e0e0e0; padding: 20px; border-radius: 8px; background-color: #f9f9f9;">
        <div class="mb-3">
            <label for="recommend_type" class="form-label">Recommend Type</label>
            <input type="text" class="form-control" name="recommend_type" id="recommend_type" placeholder="Enter recommend type" required>
        </div>

        <div class="text-center">
            <button type="submit" name="insert_recommend" class="btn text-light px-4 py-2" style="background-color: #CCAB8C;">Add Recommend</button>
        </div>
    </form>
</div>
