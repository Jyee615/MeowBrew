<!-- connect file --> 
<?php 
  include('../include/connect.php');
  session_start();

  if(isset($_GET['OrderID']))
  {
    $OrderID = $_GET['OrderID'];
    
    $select_data = "select * from `user_orders` where OrderID = '$OrderID'";
    $result = mysqli_query($con,$select_data);
    $row_fetch = mysqli_fetch_assoc($result);
    $invoice_num = $row_fetch['InvoiceNum'];
    $amount = $row_fetch['AmountDue'];
  }

  if(isset($_POST['confirm_payment']))
  {
    $invoice_num = $_POST['invoice_num'];
    $amount = $_POST['amount'];
    $payment_mode = $_POST['payment_mode'];

    $insert_query = "insert into `user_payments` (OrderID, InvoiceNum, Amount, PaymentMode)
    values ($OrderID, $invoice_num, $amount, '$payment_mode')";
    $result_ = mysqli_query($con,$insert_query);

    if($result_)
    {
        echo "<script> alert('Successfully completed the payment')</script>";
        echo "<script> window.open('profile.php?my_orders','_self')</script>";
    }

    $update_orders = "update `user_orders` set OrderStatus = 'Complete' where OrderID = '$OrderID'";
    $result_orders = mysqli_query($con,$update_orders);
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Payment Page</title>
    <!-- bootstrap CSS link --> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
        rel="stylesheet" 
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" 
        crossorigin="anonymous">
</head>
<body style = "background-color: #6c5641">
    <h1 class="text-light text-center my-5">Confirm Payment</h1>
    <div class="container my-5">
        <form action="" method = "post">
            <div class="form-outline my-4 text-center w-50 m-auto">
                <label for="" class = "text-light mb-2">Invoice Number</label>
                <input type="text" class = "form-control w-50 m-auto" 
                name = "invoice_num" value = "<?php echo $invoice_num ?>">
            </div>
            <div class="form-outline my-4 text-center w-50 m-auto">
                <label for="" class = "text-light mb-2">Amount (RM)</label>
                <input type="text" class = "form-control w-50 m-auto" 
                name = "amount" value = "<?php echo $amount ?>">
            </div>
            <div class="form-outline my-4 text-center w-50 m-auto">
                <select name="payment_mode" class = "form-select w-50 m-auto">
                    <option>Select Payment Mode</option>
                    <option>UPI</option>
                    <option>Netbanking</option>
                    <option>Paypal</option>
                    <option>Cash on Delivery (COD)</option>
                </select>
            </div>
            <div class = "form-outline my-4 text-center w-50 m-auto">
                <input type="submit" value = "Confirm" class = "px-3 py-2 text-dark border-0"
                style="background-color: #ccab8c" name="confirm_payment">
            </div>
        </form>
    </div>
</body>
</html>