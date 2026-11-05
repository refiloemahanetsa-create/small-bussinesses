<?php

session_start();

require_once "config/db.php";

// Check if customer is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION["user_id"];

// Get customer's orders
$sql = "SELECT
            id,
            total_amount,
            status,
            delivery_address,
            created_at
        FROM orders
        WHERE customer_id = ?
        ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Orders</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
        }

        .container {
            width: 90%;
            max-width: 1000px;
            margin: 40px auto;
        }

        h1 {
            margin-bottom: 25px;
        }

        .order {
            background-color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            border-left: 5px solid #00ad2e;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 12px;
        }

        .order-header h2 {
            margin: 0;
        }

        .status {
            padding: 6px 12px;
            border-radius: 15px;
            background-color: #fff3cd;
            color: #856404;
            font-weight: bold;
        }

        .order-details {
            margin-top: 15px;
        }

        .order-details p {
            margin: 8px 0;
        }

        .view-button {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 15px;
            background-color: #00ad2e;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .view-button:hover {
            background-color: #008f25;
        }

        .empty {
            background-color: white;
            padding: 40px;
            text-align: center;
            border-radius: 8px;
        }

        .shop-button {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 18px;
            background-color: #00ad2e;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .back-button {
            display: inline-block;
            margin-bottom: 20px;
            color: #333;
            text-decoration: none;
        }

    </style>

</head>

<body>

<div class="container">

    <a href="dashboard.php" class="back-button">
        ← Back to Dashboard
    </a>

    <h1>My Orders</h1>


    <?php if ($result->num_rows == 0): ?>

        <div class="empty">

            <h2>No Orders Yet</h2>

            <p>
                You have not placed any orders yet.
            </p>

            <a href="shop.php" class="shop-button">
                Start Shopping
            </a>

        </div>


    <?php else: ?>


        <?php while ($order = $result->fetch_assoc()): ?>

            <div class="order">

                <div class="order-header">

                    <h2>
                        Order #<?php echo $order["id"]; ?>
                    </h2>

                    <span class="status">
                        <?php echo ucfirst($order["status"]); ?>
                    </span>

                </div>


                <div class="order-details">

                    <p>
                        <strong>Total:</strong>
                        M <?php echo number_format($order["total_amount"], 2); ?>
                    </p>

                    <p>
                        <strong>Delivery Address:</strong>
                        <?php echo nl2br(htmlspecialchars($order["delivery_address"])); ?>
                    </p>

                    <p>
                        <strong>Date:</strong>
                        <?php echo $order["created_at"]; ?>
                    </p>


                    <a
                        href="order_details.php?order_id=<?php echo $order["id"]; ?>"
                        class="view-button"
                    >
                        View Order
                    </a>

                </div>

            </div>

        <?php endwhile; ?>


    <?php endif; ?>

</div>

</body>

</html>
```
