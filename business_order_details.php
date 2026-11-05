<?php

session_start();

require_once "config/db.php";

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Check if user is a business owner
if ($_SESSION["user_role"] !== "business_owner") {
    header("Location: dashboard.php");
    exit();
}

$owner_id = $_SESSION["user_id"];


// Check if order ID was provided
if (!isset($_GET["order_id"])) {
    header("Location: business_orders.php");
    exit();
}

$order_id = intval($_GET["order_id"]);


// Get the business owned by the logged-in user
$business_sql = "SELECT id, business_name
                 FROM businesses
                 WHERE owner_id = ?";

$business_stmt = $conn->prepare($business_sql);
$business_stmt->bind_param("i", $owner_id);
$business_stmt->execute();

$business_result = $business_stmt->get_result();

if ($business_result->num_rows == 0) {
    header("Location: business_owner_dashboard.php");
    exit();
}

$business = $business_result->fetch_assoc();

$business_id = $business["id"];


// Get order information
$order_sql = "SELECT
                o.id,
                o.total_amount,
                o.status,
                o.delivery_address,
                o.created_at,
                u.full_name AS customer_name,
                u.email AS customer_email
              FROM orders o
              INNER JOIN users u
                  ON o.customer_id = u.id
              WHERE o.id = ?
              AND EXISTS (
                  SELECT 1
                  FROM order_items oi
                  INNER JOIN products p
                      ON oi.product_id = p.id
                  WHERE oi.order_id = o.id
                  AND p.business_id = ?
              )";

$order_stmt = $conn->prepare($order_sql);
$order_stmt->bind_param("ii", $order_id, $business_id);
$order_stmt->execute();

$order_result = $order_stmt->get_result();


// Check if order belongs to this business
if ($order_result->num_rows == 0) {
    echo "<h2>Order not found.</h2>";
    echo '<a href="business_orders.php">Back to Orders</a>';
    exit();
}

$order = $order_result->fetch_assoc();


// Get products belonging to this business in this order
$items_sql = "SELECT
                oi.quantity,
                oi.unit_price,
                p.name,
                p.image
              FROM order_items oi
              INNER JOIN products p
                  ON oi.product_id = p.id
              WHERE oi.order_id = ?
              AND p.business_id = ?";

$items_stmt = $conn->prepare($items_sql);
$items_stmt->bind_param("ii", $order_id, $business_id);
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
            max-width: 1000px;
            margin: 40px auto;
        }

        .box {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
        }

        h1 {
            margin-bottom: 25px;
        }

        .customer-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 25px;
        }

        .customer-info p {
            margin: 8px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #00ad2e;
            color: white;
        }

        .total {
            text-align: right;
            font-size: 22px;
            font-weight: bold;
            margin-top: 20px;
        }

        .status-box {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 6px;
        }

        .status-box label {
            display: block;
            font-weight: bold;
            margin-bottom: 10px;
        }

        select {
            padding: 10px;
            width: 250px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .update-button {
            padding: 10px 18px;
            background-color: #00ad2e;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 10px;
        }

        .update-button:hover {
            background-color: #008f25;
        }

        .back {
            display: inline-block;
            margin-top: 25px;
            color: #333;
            text-decoration: none;
        }

    </style>

</head>

<body>

<div class="container">

    <div class="box">

        <h1>
            Order #<?php echo $order["id"]; ?>
        </h1>


        <div class="customer-info">

            <h2>Customer Information</h2>

            <p>
                <strong>Name:</strong>
                <?php echo htmlspecialchars($order["customer_name"]); ?>
            </p>

            <p>
                <strong>Email:</strong>
                <?php echo htmlspecialchars($order["customer_email"]); ?>
            </p>

            <p>
                <strong>Delivery Address:</strong>
                <?php echo nl2br(htmlspecialchars($order["delivery_address"])); ?>
            </p>

            <p>
                <strong>Order Date:</strong>
                <?php echo htmlspecialchars($order["created_at"]); ?>
            </p>

        </div>


        <h2>Ordered Products</h2>

        <table>

            <thead>

                <tr>

                    <th>Product</th>

                    <th>Quantity</th>

                    <th>Unit Price</th>

                    <th>Subtotal</th>

                </tr>

            </thead>

            <tbody>

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
                            $item["unit_price"] * $item["quantity"],
                            2
                        );
                        ?>
                    </td>

                </tr>

            <?php endwhile; ?>

            </tbody>

        </table>


        <div class="total">

            Total:
            M <?php echo number_format($order["total_amount"], 2); ?>

        </div>


        <div class="status-box">

    <form action="update_order_status.php" method="POST">

        <input
            type="hidden"
            name="order_id"
            value="<?php echo $order["id"]; ?>"
        >

        <label for="status">
            Order Status
        </label>

        <select id="status" name="status">

            <option value="pending"
                <?php if ($order["status"] == "pending") echo "selected"; ?>>
                Pending
            </option>

            <option value="confirmed"
                <?php if ($order["status"] == "confirmed") echo "selected"; ?>>
                Confirmed
            </option>

            <option value="processing"
                <?php if ($order["status"] == "processing") echo "selected"; ?>>
                Processing
            </option>

            <option value="completed"
                <?php if ($order["status"] == "completed") echo "selected"; ?>>
                Completed
            </option>

        </select>

        <button
            type="submit"
            class="update-button"
        >
            Update Status
        </button>

    </form>

</div>


        <a href="business_orders.php" class="back">
            ← Back to Orders
        </a>

    </div>

</div>

</body>

</html>