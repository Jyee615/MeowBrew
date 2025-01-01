<?php
if (isset($_GET['delete_order'])) {
    $order_id = $_GET['delete_order'];

    // Delete from order_drink_details table (child table)
    $delete_order_drink_details = "DELETE FROM `order_drink_details` WHERE OrderID = $order_id";
    if (!mysqli_query($con, $delete_order_drink_details)) {
        die("Error deleting from order_drink_details: " . mysqli_error($con));
    }

    // Delete from orders_pending table (child table)
    $delete_orders_pending = "DELETE FROM `orders_pending` WHERE OrderID = $order_id";
    if (!mysqli_query($con, $delete_orders_pending)) {
        die("Error deleting from orders_pending: " . mysqli_error($con));
    }

    // Delete from user_payments table (child table)
    $delete_user_payments = "DELETE FROM `user_payments` WHERE OrderID = $order_id";
    if (!mysqli_query($con, $delete_user_payments)) {
        die("Error deleting from user_payments: " . mysqli_error($con));
    }

    // Delete from user_orders table (parent table)
    $delete_user_orders = "DELETE FROM `user_orders` WHERE OrderID = $order_id";
    if (!mysqli_query($con, $delete_user_orders)) {
        die("Error deleting from user_orders: " . mysqli_error($con));
    }

    // Redirect with success message
    echo "<script>alert('Order and related data deleted successfully.');</script>";
    echo "<script>window.open('index.php?list_orders', '_self');</script>";
} else {
    echo "<script>alert('No order selected for deletion.'); window.location.href='index.php?list_orders';</script>";
}
?>
