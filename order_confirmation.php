```php
<?php

session_start();

require_once "config/db.php";

// Check if customer is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION["user_id"];


// Check if order ID was provided
if (!isset($_GET["order_id"])) {
    header("Location: dashboard.php");
    exit();
}

$order_id = intval($_GET["order_id"]);


// Get the order
$order_sql = "SELECT
                id,
                total_amount,
                status,
                delivery_address,
                created_at
              FROM orders
              WHERE id = ?
              AND customer_id = ?";

$order_stmt = $conn->prepare($order_sql);

$order_stmt->bind_param(
    "ii",
    $order_id,
    $customer_id
);

$order_stmt->execute();

$order_result = $order_stmt->get_result();


// Check if order exists
if ($order_result->num_rows == 0) {
    echo "Order not found.";
    exit();
}

$order = $order_result->fetch_assoc();

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Order Confirmation</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
        }

        .container {
            width: 90%;
            max-width: 700px;
            margin: 60px auto;
        }

        .confirmation {
            background-color: white;
            padding: 40px;
            border-radius: 8px;
            text-align: center;
        }

        .success {
            color: #00ad2e;
            font-size: 50px;
            margin-bottom: 10px;
        }

        h1 {
            margin-bottom: 10px;
        }

        .order-details {
            text-align: left;
            background-color: #f8f8f8;
            padding: 20px;
            margin-top: 25px;
            border-radius: 5px;
        }

        .order-details p {
            margin: 10px 0;
        }

        .button {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 20px;
            background-color: #00ad2e;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .button:hover {
            background-color: #008f25;
        }

    </style>

</head>

<body>

<div class="container">

    <div class="confirmation">

        <div class="success">
            ✓
        </div>

        <h1>Order Placed Successfully!</h1>

        <p>
            Thank you for your order.
        </p>


        <div class="order-details">

            <p>
                <strong>Order Number:</strong>
                #<?php echo $order["id"]; ?>
            </p>

            <p>
                <strong>Total Amount:</strong>
                M <?php echo number_format($order["total_amount"], 2); ?>
            </p>

            <p>
                <strong>Status:</strong>
                <?php echo ucfirst($order["status"]); ?>
            </p>

            <p>
                <strong>Delivery Address:</strong><br>
                <?php echo nl2br(htmlspecialchars($order["delivery_address"])); ?>
            </p>

            <p>
                <strong>Date:</strong>
                <?php echo $order["created_at"]; ?>
            </p>

        </div>


        <a href="shop.php" class="button">
            Continue Shopping
        </a>

        <a href="dashboard.php" class="button">
            My Dashboard
        </a>

    </div>

</div>

</body>

</html>
```
