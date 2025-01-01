<?php
include('../include/connect.php');
include('../functions/common_function.php');
require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('User not logged in! Please sign in again.');</script>";
    echo "<script>window.open('./users_area/user_login.php', '_self');</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$delivery_method = isset($_POST['delivery_method']) ? trim($_POST['delivery_method']) : 'pickup'; 
$time_slot_id = isset($_POST['delivery_time']) ? intval($_POST['delivery_time']) : 0; 
$shipping_address = isset($_POST['shipping_address']) ? trim($_POST['shipping_address']) : ''; 
$payment_method = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : '';

$upload_dir = '../uploads/receipts/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true); // Create the directory with appropriate permissions
}

$receipt_image = null;

// Handle file upload for DuitNow and TnG payments
if ($payment_method === 'duitnow' || $payment_method === 'tng') {
    if (isset($_FILES['payment_receipt']) && $_FILES['payment_receipt']['error'] === 0) {
        $file_name = time() . '_' . $_FILES['payment_receipt']['name'];
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['payment_receipt']['tmp_name'], $file_path)) {
            $receipt_image = $file_name;
        } else {
            echo "<script>alert('Failed to upload receipt. Please try again.');</script>";
            echo "<script>window.location.href = 'payment.php?reset_form=true';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Please upload your payment receipt.');</script>";
        echo "<script>window.location.href = 'payment.php?reset_form=true';</script>";
        exit();
    }
}

// Validate required fields
if (empty($time_slot_id) || empty($payment_method) || ($delivery_method === 'delivery' && empty($shipping_address))) {
    echo "<script>alert('Please fill in all required fields.')</script>";
    echo "<script>window.location.href = 'payment.php?reset_form=true';</script>";
    exit();
}

$total_price = 0;
$delivery_charge = 2.00;
$user_ip = getIPAddress();
$cart_query_price = "SELECT * FROM `cart_detail` WHERE IP_address = '$user_ip'";
$result_cart_price = mysqli_query($con, $cart_query_price);
$count_drinks = mysqli_num_rows($result_cart_price);
$invoice_num = mt_rand();
$status = 'pending';

$invoice_num = mt_rand(); 

// Fetch the time slot from the `delivery_time_slots` table
$time_slot_query = "SELECT TimeSlot FROM `delivery_time_slots` WHERE TimeSlotID = $time_slot_id";
$time_slot_result = mysqli_query($con, $time_slot_query);
$time_slot = mysqli_fetch_assoc($time_slot_result)['TimeSlot'] ?? 'Not Selected';

// Check if invoice number already exists in the table
$check_invoice_query = "SELECT * FROM `user_orders` WHERE InvoiceNum = '$invoice_num'";
$check_result = mysqli_query($con, $check_invoice_query);

// Regenerate the invoice number if it's a duplicate
while (mysqli_num_rows($check_result) > 0) {
    $invoice_num = mt_rand();
    $check_result = mysqli_query($con, $check_invoice_query); // Re-run the check
}

while ($row_price = mysqli_fetch_array($result_cart_price)) {
    $drinkID = $row_price['DrinkID'];
    $quantity = $row_price['Quantity'];

    $select_drink = "SELECT * FROM `drink` WHERE DrinkID = '$drinkID'";
    $run_price = mysqli_query($con, $select_drink);

    while ($row_drink_price = mysqli_fetch_array($run_price)) {
        $drink_price = $row_drink_price['DrinkPrice'];
        $total_price += ($drink_price * $quantity);
    }
}

if ($delivery_method === 'delivery') {
    $total_price += $delivery_charge;
}

$shipping_address_escaped = $delivery_method === 'delivery' 
    ? "'" . mysqli_real_escape_string($con, $shipping_address) . "'" 
    : "NULL";

// Insert into `user_orders`
$insert_orders = "
    INSERT INTO user_orders 
    (UserID, AmountDue, InvoiceNum, TotalDrinks, OrderDate, OrderStatus, DeliveryMethod, TimeSlotID, ShippingAddress, PaymentMethod, ReceiptImage) 
    VALUES 
    (
        $user_id, 
        $total_price, 
        $invoice_num, 
        $count_drinks, 
        NOW(), 
        '$status', 
        '$delivery_method', 
        $time_slot_id, 
        $shipping_address_escaped, 
        '$payment_method',
        '$receipt_image'
    )";

$result_query = mysqli_query($con, $insert_orders);

if ($result_query) {
    $order_id = mysqli_insert_id($con); // Retrieve the OrderID
    if (!$order_id) {
        echo "<script>alert('Failed to retrieve OrderID. Please try again.');</script>";
        exit();
    }

    // Insert into `order_drink_details` and `orders_pending`
    $cart_items_query = "SELECT * FROM `cart_detail` WHERE IP_address = '$user_ip'";
    $cart_items_result = mysqli_query($con, $cart_items_query);

    while ($cart_item = mysqli_fetch_array($cart_items_result)) {
        $drinkID = $cart_item['DrinkID'];
        $quantity = $cart_item['Quantity'];
        $ice_level = $cart_item['IceLevel'];
        $sugar_level = $cart_item['SugarLevel'];

        // Insert into `order_drink_details`
        $insert_order_drink_details = "
            INSERT INTO `order_drink_details` 
            (OrderID, DrinkID, Quantity, IceLevel, SugarLevel) 
            VALUES 
            ($order_id, $drinkID, $quantity, '$ice_level', '$sugar_level')";

        if (!mysqli_query($con, $insert_order_drink_details)) {
            echo "<script>alert('Failed to insert drink details: " . mysqli_error($con) . "');</script>";
            exit();
        }

        // Insert into `orders_pending`
        $insert_pending_orders = "
            INSERT INTO `orders_pending` 
            (OrderID, UserID, InvoiceNum, DrinkID, Quantity, OrderStatus) 
            VALUES 
            ($order_id, $user_id, $invoice_num, $drinkID, $quantity, '$status')";

        if (!mysqli_query($con, $insert_pending_orders)) {
            echo "<script>alert('Failed to insert pending order: " . mysqli_error($con) . "');</script>";
            exit();
        }
    }

    // Clear cart
    $empty_cart = "DELETE FROM `cart_detail` WHERE IP_address = '$user_ip'";
    if (mysqli_query($con, $empty_cart)) {
        echo "<script>console.log('Cart cleared successfully for IP: $user_ip');</script>";
    } else {
        echo "<script>console.log('Failed to clear cart: " . mysqli_error($con) . "');</script>";
    }

    // Fetch user details
    $user_query = "SELECT UserEmail, UserName FROM `user_table` WHERE UserID = $user_id";
    $user_result = mysqli_query($con, $user_query);

    if ($user_result && mysqli_num_rows($user_result) > 0) {
    $user_data = mysqli_fetch_assoc($user_result);
    $user_email = $user_data['UserEmail'];
    $user_username = $user_data['UserName'];
    
    } else {
        echo "<script>alert('Failed to retrieve user information.');</script>";
        exit();
    }

    $mail = new PHPMailer(true);

    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'cherylchieng02@gmail.com'; // Your Gmail address
        $mail->Password = 'uvii hbmc tegc bvmx'; // Your Gmail app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Sender and recipient
        $mail->setFrom('cherylchieng02@gmail.com', 'MeowBrew');
        $mail->addAddress($user_email, $user_username);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Order Confirmation - ' . $invoice_num;

        $mail->Body = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    margin: 0;
                    padding: 0;
                }
                .email-container {
                    background-color: #d5a986;
                    width: 600px;
                    margin: 20px auto;
                    padding: 20px;
                    color: #000;
                }
                .header {
                    display: flex;
                    align-items: center;
                    margin-bottom: 20px;
                }
                .header img {
                    width: 50px;
                    height: 50px;
                    margin-right: 10px;
                }
                .header h1 {
                    margin: 0;
                    font-size: 20px;
                }
                .header p {
                    margin: 0;
                    font-size: 14px;
                }
                .order-info {
                    margin-bottom: 20px;
                }
                .order-info p {
                    margin: 5px 0;
                }
                .details {
                    background-color: #ffffff;
                    color: #000000;
                    padding: 20px;
                    border-radius: 8px;
                    margin-bottom: 20px;
                }
                .details table {
                    width: 100%;
                    border-collapse: collapse;
                }
                .details table th, .details table td {
                    text-align: left;
                    padding: 8px;
                    border: 1px solid #ddd;
                }
                .footer {
                    text-align: center;
                    font-size: 14px;
                    margin-top: 20px;
                }
            </style>
        </head>
        <body>
            <div class="email-container">
                <div class="header">
                    <img src="https://i.ibb.co/F4yGvTr/logo.png" alt="MeowBrew Logo">
                    <div>
                        <h1>MEOWBREW</h1>
                        <h1>猫斯波饮</h1>
                        <p>Invoice Num: ' . $invoice_num . '<br>Order Date: ' . date("d M Y, H:i:s") . '</p>
                    </div>
                </div>
                <div class="order-info">
                    <p><strong>Delivery Method:</strong> ' . ucfirst($delivery_method) . '</p>
                    <p><strong>Date and Time:</strong> ' . htmlspecialchars($time_slot) . '</p>';
        if ($delivery_method === 'delivery') {
            $mail->Body .= '<p><strong>Address:</strong> ' . htmlspecialchars($shipping_address) . '</p>';
        }
        $mail->Body .= '<p><strong>Payment Method:</strong> ' . ucfirst($payment_method) . '</p>
                </div>
                <div class="details">
                    <h2>Order Details</h2>
                    <table>
                        <tr>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Price</th>
                        </tr>';

        // Fetch order drink details for the email
        $order_details_query = "
        SELECT 
            odd.DrinkID, 
            odd.Quantity, 
            odd.SugarLevel, 
            odd.IceLevel, 
            d.DrinkName, 
            d.DrinkPrice
        FROM 
            `order_drink_details` odd
        JOIN 
            `drink` d 
        ON 
            odd.DrinkID = d.DrinkID
        WHERE 
            odd.OrderID = $order_id";

        $order_details_result = mysqli_query($con, $order_details_query);

        // Check if the query returns any rows
        if (mysqli_num_rows($order_details_result) > 0) {
        while ($order_detail = mysqli_fetch_assoc($order_details_result)) {
            $drink_name = $order_detail['DrinkName'];
            $drink_price = $order_detail['DrinkPrice'];
            $quantity = $order_detail['Quantity'];
            $sugar_level = $order_detail['SugarLevel'];
            $ice_level = $order_detail['IceLevel'];

            // Append each item details to the email body
            $mail->Body .= '
                <tr>
                    <td>' . htmlspecialchars($drink_name) . '<br><small>Sugar: ' . htmlspecialchars($sugar_level) . ', Ice: ' . htmlspecialchars($ice_level) . '</small></td>
                    <td>' . $quantity . '</td>
                    <td>RM ' . number_format($drink_price * $quantity, 2) . '</td>
                </tr>';
        }
        } else {
        // If no items found, show a placeholder message
        $mail->Body .= '
            <tr>
                <td colspan="3" style="text-align: center;">No items found in your order.</td>
            </tr>';
        }

        // Complete the order details section
        $mail->Body .= '
            </table>
            <p><strong>Amount:</strong> RM ' . number_format($total_price - ($delivery_method === 'delivery' ? $delivery_charge : 0), 2) . '</p>
            <p><strong>Delivery Fee:</strong> RM ' . ($delivery_method === 'delivery' ? number_format($delivery_charge, 2) : '0.00') . '</p>
            <p><strong>Subtotal:</strong> RM ' . number_format($total_price, 2) . '</p>
            <p><strong>Grand Total: RM ' . number_format($total_price, 2) . '</strong></p>
        </div>
        <div class="footer">
            <p>- End of Receipt -</p>
        </div>
        </div>
        </body>
        </html>';


        $mail->send();

        echo "<script>alert('Order submitted successfully!');</script>";
        echo "<script>window.open('profile.php', '_self');</script>";
        } catch (Exception $e) {
            echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
    } else {
        echo "<script>alert('Error: " . mysqli_error($con) . "');</script>";
        exit();
    }
?>
