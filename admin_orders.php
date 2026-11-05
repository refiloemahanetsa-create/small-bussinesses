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


// Get all orders
$sql = "
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
    ORDER BY o.created_at DESC
";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Manage Orders</title>

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
            width: 90%;
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
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            overflow-x: auto;
        }

        h1 {
            margin-bottom: 10px;
        }

        .description {
            color: #666;
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
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

        tr:hover {
            background-color: #f5f5f5;
        }

        .status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 5px;
            font-size: 13px;
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

        .view-button {
            background-color: #00ad2e;
            color: white;
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 5px;
            display: inline-block;
        }

        .view-button:hover {
            background-color: #008f26;
        }

        .no-orders {
            padding: 20px;
            text-align: center;
            color: #666;
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

    <a href="admin_dashboard.php" class="back">
        ← Back to Admin Dashboard
    </a>


    <div class="box">

        <h1>Manage Orders</h1>

        <p class="description">
            View and monitor all orders placed by customers.
        </p>


        <?php if ($result && $result->num_rows > 0): ?>

            <table>

                <thead>

                    <tr>

                        <th>Order ID</th>

                        <th>Customer</th>

                        <th>Email</th>

                        <th>Total</th>

                        <th>Status</th>

                        <th>Delivery Address</th>

                        <th>Order Date</th>

                        <th>Action</th>

                    </tr>

                </thead>

                <tbody>

                    <?php while ($order = $result->fetch_assoc()): ?>

                        <tr>

                            <td>
                                #<?php echo $order["id"]; ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $order["customer_name"]
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $order["customer_email"]
                                );
                                ?>
                            </td>

                            <td>
                                M<?php
                                echo number_format(
                                    $order["total_amount"],
                                    2
                                );
                                ?>
                            </td>

                            <td>

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

                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $order["delivery_address"]
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $order["created_at"]
                                );
                                ?>
                            </td>

                            <td>

                                <a
                                    href="admin_order_details.php?id=<?php echo $order["id"]; ?>"
                                    class="view-button"
                                >
                                    View
                                </a>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        <?php else: ?>

            <div class="no-orders">
                No orders have been placed yet.
            </div>

        <?php endif; ?>

    </div>

</div>

</body>

</html>