<?php 
session_start();
include('../include/connect.php');
include('../functions/common_function.php');

// Check if user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    echo "<script>alert('Please sign in to continue.');</script>";
    echo "<script>window.open('../users_area/user_login.php', '_self');</script>";
    exit();
}

$username = $_SESSION['username'];
$user_id = $_SESSION['user_id']; // Ensure user_id is fetched from session

// Define user IP
$user_ip = getIPAddress();
$total_price = 0;
$order_summary = "";

// Fetch user details
$get_user = "SELECT * FROM `user_table` WHERE UserName = '$username'";
$result = mysqli_query($con, $get_user);
$run_query = mysqli_fetch_array($result);

// Fetch cart details for order summary
$cart_query = "SELECT c.Quantity, c.SugarLevel, c.IceLevel, d.DrinkName, d.DrinkPrice 
               FROM `cart_detail` c 
               JOIN `drink` d ON c.DrinkID = d.DrinkID 
               WHERE c.IP_address = '$user_ip'";
$result_cart = mysqli_query($con, $cart_query);

if (!$result_cart) {
    error_log("Cart Query Failed: " . mysqli_error($con));
}

while ($row = mysqli_fetch_assoc($result_cart)) {
    $quantity = $row['Quantity'];
    $sugar_level = ucfirst($row['SugarLevel']);
    $ice_level = ucfirst($row['IceLevel']);
    $drink_name = $row['DrinkName'];
    $drink_price = $row['DrinkPrice'] * $quantity;
    $total_price += $drink_price;

    $order_summary .= "<p>$quantity x $drink_name ($sugar_level, $ice_level) RM" . number_format($drink_price, 2) . "</p>";
}

// Fetch delivery time slots
$time_slots_query = "SELECT * FROM `delivery_time_slots`";
$time_slots_result = mysqli_query($con, $time_slots_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MeowBrew</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Sansita+One&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <!-- font awesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" 
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" 
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        /* General styles */
        body {
            font-family: 'Poppins', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            color: #333;
            overflow-x: hidden;
        }
        header {
            background-color: #CCAB8C;
            color: white;
            height: 80px;
            display: flex;
            justify-content: center;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1002;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        header .header-center {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        header .header-center img {
            height: 60px;
        }
        header .header-center h1 {
            font-family: 'Sansita One', Arial, sans-serif;
            font-size: 1.8em;
            margin: 0;
        }
        header .header-right {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
        }
        .navbar {
            background-color: #CCAB8C;
            margin-top: 80px;
        }
        .navbar a {
            color: white !important;
            font-weight: 500;
        }

        .profile-img {
            width: 40px; 
            height: 40px; 
            border-radius: 50%;
            object-fit: cover; 
            border: 2px solid #CCAB8C; 
            cursor: pointer; 
        }

        /* Sidebar styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: -250px;
            width: 250px;
            height: 100%;
            background: #fff;
            box-shadow: 2px 0 6px rgba(0, 0, 0, 0.1);
            padding-top: 100px;
            z-index: 1003;
            transition: left 0.3s ease-in-out;
        }
        .sidebar.open {
            left: 0;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar ul li {
            margin: 20px 0;
        }
        .sidebar ul li a {
            color: #333;
            text-decoration: none;
            font-weight: 500;
            padding: 10px 20px;
            display: block;
            transition: background 0.3s, color 0.3s;
        }
        .sidebar ul li a:hover {
            background: #CCAB8C;
            color: white;
        }
        .sidebar-toggle {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1004;
            background-color: #CCAB8C;
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Search bar styles */
        .search-bar {
            position: relative;
            top: 80px; /* Adjust as needed to align with the bottom of the header */
            margin: 0 auto;
            text-align: center;
            z-index: 1001;
            background-color: #f3f4f6;
            padding: 10px 0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .search-bar form {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }
        .search-bar input[type="text"] {
            width: 80%;
            max-width: 500px;
            padding: 10px;
            font-size: 1em;
            border: 1px solid #ddd;
            border-radius: 20px;
            outline: none;
            transition: border-color 0.3s;
        }
        .search-bar input[type="text"]:focus {
            border-color: #CCAB8C;
        }
        .search-bar button {
            background-color: #CCAB8C;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.2rem;
        }
        .search-bar button:hover {
            background-color: #b8997c;
        }

        /* Navbar styles */
        .nav-bar-container {
            display: flex;
            justify-content: center;
            border-top: 3px solid #000;
            border-bottom: 3px solid #000;
            margin-top: 80px; /* Adjust to match the header */
            z-index: 1001;
            position: relative;
        }

        .nav-tabs {
            display: flex;
            gap: 30px;
            padding: 15px 0;
        }

        .nav-tab {
            text-decoration: none;
            font-size: 1rem;
            color: #333;
            font-weight: bold;
            padding: 10px 15px;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .nav-tab:hover {
            color: #CCAB8C;
            border-bottom: 3px solid #CCAB8C;
        }

        .nav-tab.active {
            color: #CCAB8C;
            font-weight: bold;
            border-bottom: 3px solid #CCAB8C;
        }


        /* Product card styles */
        .product-card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            background: white;
            transition: transform 0.3s;
        }
        .product-card:hover {
            transform: scale(1.05);
        }

        /* Footer styles */
        footer {
            background-color: #CCAB8C;
            color: white;
            text-align: center;
            padding: 20px;
        }

        .payment_img{
            width: 95%;
            margin: auto;
            display: block;
        } 
    </style>
</head>
<body>
    <!-- Header -->
    <header>
    <div class="header-center">
            <a href="../index.php" style="text-decoration: none; color: inherit;">
                <img src="../images/logo.jpg" alt="MeowBrew Logo" style="vertical-align: middle;">
                <h1 style="display: inline; margin: 0; font-family: 'Sansita One', Arial, sans-serif;">MeowBrew</h1>
            </a>
        </div>
        <div class="header-right">
        <a href="../cart.php" class="btn btn-light me-2"><i class="fa-solid fa-cart-shopping"></i> 
            <sup>
                <?php
                cartItem(); 
                ?>
            </sup>
        </a>
        <?php if (isset($_SESSION['username'])): ?>
            <?php
            // Fetching the user's profile picture and linking the profile page
            $username = $_SESSION['username'];
            $userprofileimage_query = "SELECT * FROM `user_table` WHERE UserName = '$username'";
            $userprofileimage_result = mysqli_query($con, $userprofileimage_query);
            $row_profileimage = mysqli_fetch_array($userprofileimage_result);
            $userprofileimage = !empty($row_profileimage['UserImage']) ? $row_profileimage['UserImage'] : 'default.jpg'; // Default image if UserImage is empty
            ?>
            <!-- Profile Picture with Link -->
            <a href="../users_area/profile.php" style="text-decoration: none;">
                <img src="./users_images/<?php echo $userprofileimage; ?>" alt="Profile Picture" 
                    style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
            </a>
        <?php else: ?>
            <!-- Sign In Button -->
            <a href="../users_area/user_login.php" class="btn btn-light">Sign In</a>
        <?php endif; ?>
        </div>
    </header>

    <!-- Sidebar -->
    <button class="sidebar-toggle">&#9776;</button>
    <aside class="sidebar">
        <ul>
            <li><a href="../index.php">Home</a></li>
            <li><a href="../displayAll.php">Products</a></li>
            <li><a href="../cart.php">Shopping Cart</a></li>
            <li><a href="../contact.php">Contact</a></li>
            <?php if (isset($_SESSION['username'])): ?>
                <li><a href="../users_area/logout.php" style="color: red; font-weight: bold;">Logout</a></li>
            <?php endif; ?>
        </ul>
    </aside>

    <!-- Checkout Page -->
    <div class="container my-5">
        <!-- Header Section -->
        <div class="d-flex align-items-center justify-content-between mb-4" style="margin-top: 7rem;">
            <a href="../cart.php" class="btn btn-light" style="font-size: 1.5rem;">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="text-center flex-grow-1" style="font-family: 'Poppins', sans-serif; font-weight: bold; margin: 0;">Purchase Details</h2>
        </div>
        
        <!-- Checkout Form -->
        <div class="card mt-5 shadow-sm">
            <div class="card-body">
            <form action="order.php?user_id=<?php echo htmlspecialchars($user_id); ?>" method="POST" enctype="multipart/form-data">
                    <!-- Delivery Method -->
                    <div class="mb-4">
                        <h5 class="fw-bold">Delivery Method <span class="text-danger">*</span></h5>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="delivery_method" value="pickup" id="pickup" onclick="toggleShippingAddress()" required>
                            <label class="form-check-label" for="pickup">Pickup (at Kiosk Feng, Unimas)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="delivery_method" value="delivery" id="delivery" onclick="toggleShippingAddress()" required>
                            <label class="form-check-label" for="delivery">Delivery (RM 2.00 will be charged)</label>
                        </div>
                    </div>
        
                    <!-- Pick-up/Delivery Time -->
                    <div class="mb-4">
                        <h5 class="fw-bold">Pick-up / Delivery Time <span class="text-danger">*</span></h5>
                        <select class="form-select" name="delivery_time" required>
                            <option value="">Select time</option>
                            <?php
                            $time_slots_query = "SELECT * FROM `delivery_time_slots`";
                            $time_slots_result = mysqli_query($con, $time_slots_query);
                            while ($time_slot = mysqli_fetch_assoc($time_slots_result)) {
                                echo "<option value='" . $time_slot['TimeSlotID'] . "'>" . $time_slot['TimeSlot'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
        
                    <!-- Shipping Address -->
                    <div class="mb-4" id="shipping-address" style="display: none;">
                        <h5 class="fw-bold">Shipping Address (for delivery only)<span class="text-danger">*</span></h5>
                        <input type="text" class="form-control" name="shipping_address" placeholder="Enter your shipping address">
                    </div>
        
                    <!-- Payment Method -->
                    <div class="mb-4">
                        <h5 class="fw-bold">Payment Method <span class="text-danger">*</span></h5>
                        <select class="form-select" name="payment_method" id="payment_method" required>
                            <option value="">Select payment method</option>
                            <option value="cash_on_delivery">Cash on Delivery</option>
                            <option value="duitnow">DuitNow</option>
                            <option value="tng">TnG</option>
                        </select>
                    </div>
        
                    <!-- Order Summary -->
                    <div class="mb-4">
                        <h5 class="fw-bold">Order Summary</h5>
                        <div class="p-3 bg-light rounded">
                            <p id="order-summary"><?php echo $order_summary; ?></p>
                            <p class="fw-bold mt-2">Total: RM<span id="total-price"><?php echo number_format($total_price, 2); ?></span></p>
                        </div>
                    </div>

                    <!-- QR Code Display -->
                    <div class="mb-4" id="qr-code-container" style="display: none;">
                        <h5 class="fw-bold">Scan QR Code for Payment</h5>
                        <img id="qr-code-image" src="" alt="QR Code" style="max-width: 200px; margin: auto; display: block;">
                    </div>
        
                    <!-- Payment Receipt Upload -->
                    <div class="mb-4" id="payment-receipt" style="display: none;">
                        <h5 class="fw-bold">Upload Payment Receipt</h5>
                        <input type="file" class="form-control" name="payment_receipt" accept="image/*">
                    </div>

                    <script>
                    document.getElementById('payment_method').addEventListener('change', function() {
                        const qrCodeContainer = document.getElementById('qr-code-container');
                        const qrCodeImage = document.getElementById('qr-code-image');
                        const receiptField = document.getElementById('payment-receipt');
                        const selectedMethod = this.value;

                        // Show/hide based on payment method
                        if (selectedMethod === 'duitnow') {
                            qrCodeContainer.style.display = 'block';
                            qrCodeImage.src = '../images/duitnow_qr.jpeg';
                            receiptField.style.display = 'block';
                        } else if (selectedMethod === 'tng') {
                            qrCodeContainer.style.display = 'block';
                            qrCodeImage.src = '../images/tng_qr.jpeg';
                            receiptField.style.display = 'block';
                        } else {
                            qrCodeContainer.style.display = 'none';
                            receiptField.style.display = 'none';
                            qrCodeImage.src = '';
                        }
                    });
                </script>

                    <div class="d-flex justify-content-center mt-3">
                        <button type="submit" class="btn text-center" 
                        style="background-color: #CCAB8C; color: white; width: 200px; font-weight: bold;">Place Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        &copy; 2024 MeowBrew. All Rights Reserved.
    </footer>

    <!-- bootstrap js link -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
    crossorigin="anonymous"></script>

    <script>
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('open');
    });

    const basePrice = <?php echo $total_price; ?>;
    const deliveryCharge = 2.00;

    function toggleShippingAddress() {
        const shippingAddress = document.getElementById('shipping-address');
        const totalPriceElement = document.getElementById('total-price');
        const orderSummaryElement = document.getElementById('order-summary');
        const deliveryMethod = document.querySelector('input[name="delivery_method"]:checked').value;

        if (deliveryMethod === 'delivery') {
            // Show the shipping address field
            shippingAddress.style.display = 'block';

            // Add delivery fee to the total price
            totalPriceElement.textContent = (basePrice + deliveryCharge).toFixed(2);

            // Update the order summary to include the delivery fee
            let deliveryFeeHTML = `<p>Delivery Fee RM${deliveryCharge.toFixed(2)}</p>`;
            if (!orderSummaryElement.innerHTML.includes("Delivery Fee")) {
                orderSummaryElement.innerHTML += deliveryFeeHTML;
            }
        } else {
            // Hide the shipping address field
            shippingAddress.style.display = 'none';

            // Remove the delivery fee from the total price
            totalPriceElement.textContent = basePrice.toFixed(2);

            // Remove the delivery fee from the order summary
            const updatedOrderSummary = orderSummaryElement.innerHTML.replace(/<p class="text-danger">Delivery Fee: RM2.00<\/p>/, '');
            orderSummaryElement.innerHTML = updatedOrderSummary;
        }
    }

    // Add event listeners to update shipping address and order summary based on delivery method
    document.querySelectorAll('input[name="delivery_method"]').forEach(radio => {
        radio.addEventListener('change', toggleShippingAddress);
    });

    // Show/Hide receipt upload field based on payment method
    document.querySelector('[name="payment_method"]').addEventListener('change', function() {
        const receiptField = document.getElementById('payment-receipt');
        const qrCodeContainer = document.getElementById('qr-code-container');
        const qrCodeImage = document.getElementById('qr-code-image');

        if (this.value === 'duitnow') {
            receiptField.style.display = 'block';
            qrCodeContainer.style.display = 'block';
            qrCodeImage.src = '../images/duitnow_qr.jpeg';
        } else if (this.value === 'tng') {
            receiptField.style.display = 'block';
            qrCodeContainer.style.display = 'block';
            qrCodeImage.src = '../images/tng_qr.jpeg';
        } else {
            receiptField.style.display = 'none';
            qrCodeContainer.style.display = 'none';
            qrCodeImage.src = '';
        }
    });

    // Check if the page was loaded due to a failed submission
    window.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const resetForm = urlParams.get('reset_form');

        if (resetForm === 'true') {
            // Reset delivery method radio buttons
            document.querySelectorAll('input[name="delivery_method"]').forEach(radio => {
                radio.checked = false;
            });

            // Hide shipping address and reset fields
            const shippingAddress = document.getElementById('shipping-address');
            shippingAddress.style.display = 'none';
            shippingAddress.querySelector('input').value = '';

            // Reset delivery time select
            const deliveryTimeSelect = document.querySelector('select[name="delivery_time"]');
            deliveryTimeSelect.selectedIndex = 0;

            // Reset payment method select and related fields
            const paymentMethodSelect = document.querySelector('select[name="payment_method"]');
            paymentMethodSelect.selectedIndex = 0;

            const qrCodeContainer = document.getElementById('qr-code-container');
            qrCodeContainer.style.display = 'none';
            document.getElementById('qr-code-image').src = '';

            const receiptField = document.getElementById('payment-receipt');
            receiptField.style.display = 'none';
            receiptField.querySelector('input').value = '';

            // Reset the total price (delivery charge removed)
            const totalPriceElement = document.getElementById('total-price');
            totalPriceElement.textContent = basePrice.toFixed(2);
        }
    });
</script>
</body>
</html>