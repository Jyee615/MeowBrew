<?php 
include('../include/connect.php');
if(isset($_POST['insert_cat']))
{
    $CategoryName = trim($_POST['cat_name']);

    //select data from database
    $select_query = "SELECT * FROM `category` WHERE CategoryName = '$CategoryName'";
    $result_select = mysqli_query($con, $select_query);
    $number = mysqli_num_rows($result_select);
    if($number > 0)
    {
        echo "<script>alert('The category is already present in the database.')</script>";
    } else {
        $insert_query = "INSERT INTO `category` (CategoryName) VALUES('$CategoryName')";
        $result = mysqli_query($con, $insert_query);
        if($result)
        {
            echo "<script>alert('Category has been inserted successfully!')</script>";
        }
    }  
}
?>

<div class="container mt-3 mb-5">
    <h2 class="text-center mb-4">Add New Category</h2>

    <form action="" method="post" class="w-50 m-auto" style="border: 1px solid #e0e0e0; padding: 20px; border-radius: 8px; background-color: #f9f9f9;">
        <div class="mb-3">
            <label for="cat_name" class="form-label">Category Name</label>
            <input type="text" class="form-control" name="cat_name" id="cat_name" placeholder="Enter category name" required>
        </div>

        <div class="text-center">
            <button type="submit" name="insert_cat" class="btn text-light px-4 py-2" style="background-color: #CCAB8C;">Add Category</button>
        </div>
    </form>
</div>