<?php

session_start();

require_once "config/db.php";

// Make sure the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Only admins can access this page
if ($_SESSION["user_role"] !== "admin") {
    header("Location: dashboard.php");
    exit();
}


// Check if an order ID was provided
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: admin_orders.php");
    exit();
}

$order_id = (int) $_GET["id"];


// Get order information
$order_sql = "
    SELECT
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
";

$order_stmt = $conn->prepare($order_sql);
$order_stmt->bind_param("i", $order_id);
$order_stmt->execute();

$order_result = $order_stmt->get_result();


// Make sure the order exists
if ($order_result->num_rows === 0) {
    header("Location: admin_orders.php");
    exit();
}

$order = $order_result->fetch_assoc();


// Get products in this order
$items_sql = "
    SELECT
        oi.quantity,
        oi.unit_price,
        p.name AS product_name,
        b.business_name
    FROM order_items oi
    INNER JOIN products p
        ON oi.product_id = p.id
    INNER JOIN businesses b
        ON p.business_id = b.id
    WHERE oi.order_id = ?
";

$items_stmt = $conn->prepare($items_sql);
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();

$items_result = $items_stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Order Details</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f5f7f6;
        }

        nav {
            background-color: #00ad2e;
            padding: 18px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav h2 {
            color: white;
        }

        nav a {
            color: #00ad2e;
            background-color: white;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 5px;
            font-weight: bold;
        }

        .container {
            width: 86%;
            margin: 40px auto;
        }

        .back {
            display: inline-block;
            margin-bottom: 20px;
            color: #00ad2e;
            text-decoration: none;
            font-weight: bold;
        }

        .box {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        h1 {
            margin-bottom: 25px;
        }

        h2 {
            margin-bottom: 20px;
        }

        .details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .detail {
            background-color: #f5f7f6;
            padding: 15px;
            border-radius: 6px;
        }

        .detail strong {
            display: block;
            margin-bottom: 7px;
            color: #555;
        }

        .status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 5px;
            font-weight: bold;
        }

        .pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .confirmed {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .processing {
            background-color: #cce5ff;
            color: #004085;
        }

        .completed {
            background-color: #d4edda;
            color: #155724;
        }

        .cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 14px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #00ad2e;
            color: white;
        }

        .total {
            text-align: right;
            font-size: 20px;
            font-weight: bold;
            margin-top: 20px;
        }

        @media (max-width: 700px) {

            .details {
                grid-template-columns: 1fr;
            }

            .container {
                width: 92%;
            }

        }

    </style>

</head>

<body>

<nav>

    <h2>Small Business Shop - Admin</h2>

    <a href="logout.php">
        Logout
    </a>

</nav>


<div class="container">

    <a href="admin_orders.php" class="back">
        ← Back to Orders
    </a>


    <!-- Order Information -->

    <div class="box">

        <h1>
            Order #<?php echo $order["id"]; ?>
        </h1>


        <div class="details">

            <div class="detail">

                <strong>Customer</strong>

                <?php
                echo htmlspecialchars(
                    $order["customer_name"]
                );
                ?>

            </div>


            <div class="detail">

                <strong>Email</strong>

                <?php
                echo htmlspecialchars(
                    $order["customer_email"]
                );
                ?>

            </div>


            <div class="detail">

                <strong>Delivery Address</strong>

                <?php
                echo htmlspecialchars(
                    $order["delivery_address"]
                );
                ?>

            </div>


            <div class="detail">

                <strong>Order Date</strong>

                <?php
                echo htmlspecialchars(
                    $order["created_at"]
                );
                ?>

            </div>


            <div class="detail">

                <strong>Status</strong>

                <span class="status
                    <?php
                    echo strtolower(
                        $order["status"]
                    );
                    ?>
                ">

                    <?php
                    echo htmlspecialchars(
                        ucfirst($order["status"])
                    );
                    ?>

                </span>

            </div>


            <div class="detail">

                <strong>Total Amount</strong>

                M<?php
                echo number_format(
                    $order["total_amount"],
                    2
                );
                ?>

            </div>

        </div>

    </div>


    <!-- Products -->

    <div class="box">

        <h2>Products in This Order</h2>


        <table>

            <thead>

                <tr>

                    <th>Product</th>

                    <th>Business</th>

                    <th>Quantity</th>

                    <th>Unit Price</th>

                    <th>Subtotal</th>

                </tr>

            </thead>


            <tbody>

                <?php
                $calculated_total = 0;
                ?>


                <?php while ($item = $items_result->fetch_assoc()): ?>

                    <?php

                    $subtotal =
                        $item["quantity"] *
                        $item["unit_price"];

                    $calculated_total += $subtotal;

                    ?>


                    <tr>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $item["product_name"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $item["business_name"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo $item["quantity"];
                            ?>
                        </td>

                        <td>
                            M<?php
                            echo number_format(
                                $item["unit_price"],
                                2
                            );
                            ?>
                        </td>

                        <td>
                            M<?php
                            echo number_format(
                                $subtotal,
                                2
                            );
                            ?>
                        </td>

                    </tr>

                <?php endwhile; ?>

            </tbody>

        </table>


        <div class="total">

            Order Total:
            M<?php
            echo number_format(
                $calculated_total,
                2
            );
            ?>

        </div>

    </div>

</div>

</body>

</html>