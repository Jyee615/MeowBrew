<?php
include('../include/connect.php'); // Ensure the correct path to your db.php file

if (isset($_GET['confirm_order'])) {
    $order_id = $_GET['confirm_order'];

    // Update the order status in user_orders table
    $update_user_orders = "UPDATE `user_orders` SET OrderStatus = 'completed' WHERE OrderID = $order_id";
    if (!mysqli_query($con, $update_user_orders)) {
        die("Error updating user_orders: " . mysqli_error($con));
    }

    // Update the order status in orders_pending table
    $update_orders_pending = "UPDATE `orders_pending` SET OrderStatus = 'completed' WHERE OrderID = $order_id";
    if (!mysqli_query($con, $update_orders_pending)) {
        die("Error updating orders_pending: " . mysqli_error($con));
    }

    // Fetch order details for payment insertion
    $get_order_details = "SELECT * FROM `user_orders` WHERE OrderID = $order_id";
    $order_result = mysqli_query($con, $get_order_details);
    if (!$order_result) {
        die("Error fetching order details: " . mysqli_error($con));
    }

    $order_data = mysqli_fetch_assoc($order_result);
    $amount_due = $order_data['AmountDue'];
    $invoice_num = $order_data['InvoiceNum'];
    $payment_method = $order_data['PaymentMethod'];

    // Insert payment record into user_payments table
    $insert_payment = "INSERT INTO `user_payments` (OrderID, InvoiceNum, Amount, PaymentMode) 
                       VALUES ($order_id, $invoice_num, $amount_due, '$payment_method')";
    if (!mysqli_query($con, $insert_payment)) {
        die("Error inserting payment record: " . mysqli_error($con));
    }

    // Redirect with success message
    echo "<script>alert('Order confirmed successfully!'); window.location.href='index.php?list_orders';</script>";
} else {
    echo "<script>alert('No order selected for confirmation.'); window.location.href='index.php?list_orders';</script>";
}
?>
