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


// Check order ID
if (!isset($_GET["order_id"])) {
    header("Location: my_orders.php");
    exit();
}

$order_id = intval($_GET["order_id"]);


// Get order
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


if ($order_result->num_rows == 0) {

    echo "Order not found.";

    exit();
}

$order = $order_result->fetch_assoc();


// Get order items
$items_sql = "SELECT
                oi.quantity,
                oi.unit_price,
                p.name
              FROM order_items oi
              INNER JOIN products p
              ON oi.product_id = p.id
              WHERE oi.order_id = ?";

$items_stmt = $conn->prepare($items_sql);

$items_stmt->bind_param(
    "i",
    $order_id
);

$items_stmt->execute();

$items_result = $items_stmt->get_result();

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Order Details</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
        }

        .container {
            width: 90%;
            max-width: 900px;
            margin: 40px auto;
        }

        .box {
            background-color: white;
            padding: 25px;
            border-radius: 8px;
        }

        h1 {
            margin-top: 0;
        }

        .order-info {
            background-color: #f8f8f8;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 5px;
        }

        .order-info p {
            margin: 8px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f5f5f5;
        }

        .total {
            text-align: right;
            font-size: 20px;
            font-weight: bold;
            margin-top: 20px;
        }

        .back {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #00ad2e;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

    </style>

</head>

<body>

<div class="container">

    <div class="box">

        <h1>
            Order #<?php echo $order["id"]; ?>
        </h1>


        <div class="order-info">

            <p>
                <strong>Status:</strong>
                <?php echo ucfirst($order["status"]); ?>
            </p>

            <p>
                <strong>Date:</strong>
                <?php echo $order["created_at"]; ?>
            </p>

            <p>
                <strong>Delivery Address:</strong>
                <?php echo nl2br(htmlspecialchars($order["delivery_address"])); ?>
            </p>

        </div>


        <h2>Products</h2>


        <table>

            <tr>

                <th>Product</th>

                <th>Quantity</th>

                <th>Unit Price</th>

                <th>Subtotal</th>

            </tr>


            <?php while ($item = $items_result->fetch_assoc()): ?>

                <tr>

                    <td>
                        <?php echo htmlspecialchars($item["name"]); ?>
                    </td>

                    <td>
                        <?php echo $item["quantity"]; ?>
                    </td>

                    <td>
                        M <?php echo number_format($item["unit_price"], 2); ?>
                    </td>

                    <td>
                        M
                        <?php
                        echo number_format(
                            $item["quantity"] * $item["unit_price"],
                            2
                        );
                        ?>
                    </td>

                </tr>

            <?php endwhile; ?>

        </table>


        <div class="total">

            Total:
            M <?php echo number_format($order["total_amount"], 2); ?>

        </div>


        <a href="my_orders.php" class="back">
            ← Back to My Orders
        </a>

    </div>

</div>

</body>

</html>
```
