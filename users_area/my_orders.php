<?php
$username = $_SESSION['username'];
$get_user = "SELECT * FROM `user_table` WHERE UserName = '$username'";
$result = mysqli_query($con, $get_user);

$row_fetch = mysqli_fetch_assoc($result);
$userID = $row_fetch['UserID'];
?>

<h3 class="mt-3 mb-4 text-center" style="font-weight: bold; margin-top: 1rem;">My Orders</h3>

<div class="container">
    <div class="row">
        <?php
        $get_order_details = "SELECT * FROM `user_orders` WHERE UserID = '$userID' ORDER BY OrderDate DESC";
        $result_orders = mysqli_query($con, $get_order_details);

        if (mysqli_num_rows($result_orders) > 0) {
            while ($row_orders = mysqli_fetch_assoc($result_orders)) {
                $OrderID = $row_orders['OrderID'];
                $AmountDue = $row_orders['AmountDue'];
                $InvoiceNum = $row_orders['InvoiceNum'];
                $TotalDrinks = $row_orders['TotalDrinks'];
                $OrderDate = $row_orders['OrderDate'];
                $OrderStatus = $row_orders['OrderStatus'];
                $DeliveryMethod = ucfirst($row_orders['DeliveryMethod']);
                $ShippingAddress = $row_orders['ShippingAddress'];

                // Badge class based on order status
                $badge_class = $OrderStatus === 'pending' ? 'bg-warning text-dark' : 'bg-success text-white';
                $status_text = $OrderStatus === 'pending' ? 'Incomplete' : 'Complete';

                // Get order items for this invoice
                $get_order_items = "
                    SELECT op.Quantity, d.DrinkName, d.DrinkPrice, od.IceLevel, od.SugarLevel
                    FROM `orders_pending` op
                    JOIN `drink` d ON op.DrinkID = d.DrinkID
                    LEFT JOIN `order_drink_details` od ON op.OrderID = od.OrderID AND d.DrinkID = od.DrinkID
                    WHERE op.InvoiceNum = '$InvoiceNum'";
                $result_items = mysqli_query($con, $get_order_items);

                ?>

                <!-- Bootstrap Card for Each Order -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm" style="background-color: #FFEBD8; border: none;">
                        <div class="card-header" style="background-color: #CCAB8C; color: white;">
                            <h5 class="mb-0">Invoice #<?php echo $InvoiceNum; ?></h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Order Date:</strong> <?php echo date('d M Y, h:i A', strtotime($OrderDate)); ?></p>
                            <p><strong>Total Drinks:</strong> <?php echo $TotalDrinks; ?></p>
                            <p><strong>Amount Due:</strong> RM <?php echo number_format($AmountDue, 2); ?></p>
                            <p><strong>Status:</strong> 
                                <span class="badge <?php echo $badge_class; ?>">
                                    <?php echo $status_text; ?>
                                </span>
                            </p>
                            <p><strong>Delivery Method:</strong> <?php echo $DeliveryMethod; ?></p>
                            <?php if ($DeliveryMethod === 'Delivery') { ?>
                                <p><strong>Shipping Address:</strong> <?php echo $ShippingAddress; ?></p>
                            <?php } ?>
                            <button class="btn btn-sm mt-2" style="background-color: #CCAB8C; color: white;" data-bs-toggle="modal" data-bs-target="#orderDetails<?php echo $OrderID; ?>">
                                View Details
                            </button>
                            <!-- Review/Feedback Button -->
                            <?php if ($OrderStatus !== 'pending') { ?>
                                <a href="review.php?orderID=<?php echo $OrderID; ?>" class="btn btn-sm mt-2" style="background-color: #CCAB8C; color: white;">
                                    Leave a Review
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <!-- Order Details Modal -->
                <div class="modal fade" id="orderDetails<?php echo $OrderID; ?>" tabindex="-1" aria-labelledby="orderDetailsLabel<?php echo $OrderID; ?>" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="orderDetailsLabel<?php echo $OrderID; ?>">Order Details - Invoice #<?php echo $InvoiceNum; ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Drink</th>
                                            <th>Price (RM)</th>
                                            <th>Quantity</th>
                                            <th>Sugar Level</th>
                                            <th>Ice Level</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (mysqli_num_rows($result_items) > 0) {
                                            while ($row_items = mysqli_fetch_assoc($result_items)) { ?>
                                                <tr>
                                                    <td><?php echo $row_items['DrinkName']; ?></td>
                                                    <td><?php echo number_format($row_items['DrinkPrice'], 2); ?></td>
                                                    <td><?php echo $row_items['Quantity']; ?></td>
                                                    <td><?php echo ucfirst($row_items['SugarLevel']); ?></td>
                                                    <td><?php echo ucfirst($row_items['IceLevel']); ?></td>
                                                </tr>
                                            <?php }
                                        } else { ?>
                                            <tr>
                                                <td colspan="5" class="text-center">No items found for this order.</td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<p class="text-center">You have no orders yet.</p>';
        }
        ?>
    </div>
</div>
