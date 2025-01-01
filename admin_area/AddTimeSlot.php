<?php 
include('../include/connect.php');
if(isset($_POST['insert_slot']))
{
    $TimeSlot = trim($_POST['time_slot']);

    // Select data from database
    $select_query = "SELECT * FROM `delivery_time_slots` WHERE TimeSlot = '$TimeSlot'";
    $result_select = mysqli_query($con, $select_query);
    $number = mysqli_num_rows($result_select);
    if($number > 0)
    {
        echo "<script>alert('The time slot is already present in the database.')</script>";
    } else {
        $insert_query = "INSERT INTO `delivery_time_slots` (TimeSlot) VALUES('$TimeSlot')";
        $result = mysqli_query($con, $insert_query);
        if($result)
        {
            echo "<script>alert('Time slot has been inserted successfully!')</script>";
        }
    }  
}
?>

<div class="container mt-3 mb-5">
    <h2 class="text-center mb-4">Add New Time Slot</h2>

    <form action="" method="post" class="w-50 m-auto" style="border: 1px solid #e0e0e0; padding: 20px; border-radius: 8px; background-color: #f9f9f9;">
        <div class="mb-3">
            <label for="time_slot" class="form-label">Time Slot</label>
            <input type="text" class="form-control" name="time_slot" id="time_slot" placeholder="Enter time slot" required>
        </div>

        <div class="text-center">
            <button type="submit" name="insert_slot" class="btn text-light px-4 py-2" style="background-color: #CCAB8C;">Add Time Slot</button>
        </div>
    </form>
</div>