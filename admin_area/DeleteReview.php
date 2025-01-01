<?php
include('../include/connect.php'); // Ensure $con is defined and connected

if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

if (isset($_GET['delete_review'])) {
    $review_id = intval($_GET['delete_review']);
    echo "Review ID to delete: $review_id<br>"; // Debugging

    // Disable foreign key checks
    mysqli_query($con, "SET FOREIGN_KEY_CHECKS = 0;");

    // Delete query
    $del_data = "DELETE FROM `reviews` WHERE ReviewID = $review_id";
    echo "Query: $del_data<br>"; // Debugging
    $result = mysqli_query($con, $del_data);

    // Re-enable foreign key checks
    mysqli_query($con, "SET FOREIGN_KEY_CHECKS = 1;");

    if ($result) {
        echo "<script>alert('Review deleted successfully.');</script>";
        echo "<script>window.open('index.php?review', '_self');</script>";
    } else {
        echo "Error: " . mysqli_error($con);
    }
}
?>
