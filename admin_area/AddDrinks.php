<?php 
   include('../include/connect.php'); 
   if(isset($_POST['add_drink']))
   {
      $drink_name = $_POST['drink_name'];
      $drink_description = $_POST['drink_description'];
      $drink_category = $_POST['drink_category'];
      $drink_recommend = isset($_POST['drink_recommend']) ? $_POST['drink_recommend'] : 0; // Default to 0 if not set
      $drink_price = $_POST['drink_price'];
      $drink_status = "true";

      // accessing image
      $drink_image01 = $_FILES['drink_image01']['name'];

      // accessing image temp name
      $tmp_drink_image01 = $_FILES['drink_image01']['tmp_name'];

      // checking empty condition
      if($drink_name == '' || $drink_description == '' || $drink_category == '' || $drink_price == '' || $drink_image01 == '')
      {
         echo "<script>alert('Please fill in all the available fields')</script>";
         exit();
      }else
      {
         move_uploaded_file($tmp_drink_image01,"./DrinkImages/$drink_image01");

         // insert query
         $add_drinks = "INSERT INTO `drink` (DrinkName, DrinkDesc, CategoryID, RecommendID, DrinkImage01, DrinkPrice, Date, DrinkStatus) 
                        VALUES ('$drink_name', '$drink_description', '$drink_category', '$drink_recommend', '$drink_image01', '$drink_price', NOW(), '$drink_status')";

         $result_query = mysqli_query($con, $add_drinks);
         if($result_query)
         {
            echo  "<script>alert('New drink is added successfully!')</script>";
         }
      }
   }
?>

<h2 class="text-center mt-2 mb-3">Add a New Drink</h2>
<div class="container">
    <form action="" method="post" enctype="multipart/form-data" class="shadow p-4 rounded bg-light">
        <!-- Drink Name -->
        <div class="form-group mb-3">
            <label for="drink_name" class="form-label">Drink Name</label>
            <input type="text" name="drink_name" id="drink_name" class="form-control" placeholder="Enter drink name" required>
        </div>

        <!-- Drink Description -->
        <div class="form-group mb-3">
            <label for="drink_description" class="form-label">Drink Description</label>
            <textarea name="drink_description" id="drink_description" class="form-control" rows="3" placeholder="What's special about it" required></textarea>
        </div>

        <!-- Category -->
        <div class="form-group mb-3">
            <label for="drink_category" class="form-label">Category</label>
            <select name="drink_category" id="drink_category" class="form-select" required>
                <option value="">Select a category</option>
                <?php 
                $select_query = "SELECT * FROM `category`";
                $result_query = mysqli_query($con, $select_query);
                while($row = mysqli_fetch_assoc($result_query))
                {
                    $category_name = $row['CategoryName'];
                    $category_id = $row['CategoryID'];
                    echo "<option value='$category_id'>$category_name</option>";
                }
                ?>
            </select>
        </div>

        <!-- Recommendation -->
        <div class="form-group mb-3">
            <label for="drink_recommend" class="form-label">Recommendation</label>
            <select name="drink_recommend" id="drink_recommend" class="form-select">
                <option value="0" selected>None</option> <!-- Default to "None" -->
                <?php 
                $select_query = "SELECT * FROM `recommend`";
                $result_query = mysqli_query($con, $select_query);
                while($row = mysqli_fetch_assoc($result_query))
                {
                    $recommend_type = $row['RecommendType'];
                    $recommend_id = $row['RecommendID'];
                    echo "<option value='$recommend_id'>$recommend_type</option>";
                }
                ?>
            </select>
        </div>

        <!-- Drink Image -->
        <div class="form-group mb-3">
            <label for="drink_image01" class="form-label">Drink Image</label>
            <input type="file" name="drink_image01" id="drink_image01" class="form-control" required>
        </div>

        <!-- Drink Price -->
        <div class="form-group mb-3">
            <label for="drink_price" class="form-label">Drink Price</label>
            <input type="number" step="0.01" name="drink_price" id="drink_price" class="form-control" placeholder="Enter drink price" required>
        </div>

        <!-- Submit Button -->
        <div class="text-center mb-4">
            <button type="submit" name="add_drink" class="btn" style="background-color: #CCAB8C; color: white;">Add Drink</button>
        </div>
    </form>
</div>