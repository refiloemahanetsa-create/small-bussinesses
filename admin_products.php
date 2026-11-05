<?php

session_start();

require_once "config/db.php";

// Make sure the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Only administrators can access this page
if ($_SESSION["user_role"] !== "admin") {
    header("Location: dashboard.php");
    exit();
}


// Get all products
$sql = "SELECT
            p.id,
            p.name,
            p.description,
            p.price,
            p.stock_quantity,
            p.image,
            p.status,
            p.created_at,
            b.business_name,
            c.name AS category_name
        FROM products p
        INNER JOIN businesses b
            ON p.business_id = b.id
        LEFT JOIN categories c
            ON p.category_id = c.id
        ORDER BY p.created_at DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Manage Products</title>

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
            padding: 40px 7%;
        }

        .back {
            display: inline-block;
            margin-bottom: 20px;
            color: #00ad2e;
            text-decoration: none;
            font-weight: bold;
        }

        .title {
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        .title h1 {
            margin-bottom: 10px;
        }

        .title p {
            color: #666;
        }

        .table-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            overflow-x: auto;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #00ad2e;
            color: white;
            padding: 14px;
            text-align: left;
        }

        td {
            padding: 14px;
            border-bottom: 1px solid #ddd;
            vertical-align: middle;
        }

        tr:hover {
            background-color: #f5f7f6;
        }

        .product-name {
            font-weight: bold;
            color: #00ad2e;
        }

        .description {
            max-width: 250px;
            line-height: 1.5;
            color: #555;
        }

        .product-image {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 6px;
        }

        .no-image {
            color: #999;
            font-size: 13px;
        }

        .status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 15px;
            font-size: 13px;
            font-weight: bold;
        }

        .active {
            background-color: #e8f8ed;
            color: #008f26;
        }

        .inactive {
            background-color: #f8d7da;
            color: #721c24;
        }

        .low-stock {
            color: #c0392b;
            font-weight: bold;
        }

        @media (max-width: 800px) {

            .container {
                padding: 30px 5%;
            }

            nav {
                padding: 15px 5%;
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

    <a href="admin_dashboard.php" class="back">
        ← Back to Admin Dashboard
    </a>


    <div class="title">

        <h1>Manage Products</h1>

        <p>
            View products listed by all business owners
            on the platform.
        </p>

    </div>


    <div class="table-container">

        <table>

            <thead>

                <tr>

                    <th>Image</th>

                    <th>Product</th>

                    <th>Business</th>

                    <th>Category</th>

                    <th>Price</th>

                    <th>Stock</th>

                    <th>Status</th>

                    <th>Added Date</th>

                </tr>

            </thead>


            <tbody>

                <?php if ($result->num_rows > 0): ?>

                    <?php while ($product = $result->fetch_assoc()): ?>

                        <tr>

                            <td>

                                <?php if (!empty($product["image"])): ?>

                                    <img
                                        src="uploads/products/<?php echo htmlspecialchars($product["image"]); ?>"
                                        alt="Product Image"
                                        class="product-image"
                                    >

                                <?php else: ?>

                                    <span class="no-image">
                                        No image
                                    </span>

                                <?php endif; ?>

                            </td>


                            <td>

                                <span class="product-name">
                                    <?php echo htmlspecialchars($product["name"]); ?>
                                </span>

                            </td>


                            <td>
                                <?php echo htmlspecialchars($product["business_name"]); ?>
                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $product["category_name"] ?? "No category"
                                );
                                ?>

                            </td>


                            <td>

                                M
                                <?php echo number_format(
                                    $product["price"],
                                    2
                                ); ?>

                            </td>


                            <td>

                                <?php if ($product["stock_quantity"] <= 5): ?>

                                    <span class="low-stock">
                                        <?php echo $product["stock_quantity"]; ?>
                                    </span>

                                <?php else: ?>

                                    <?php echo $product["stock_quantity"]; ?>

                                <?php endif; ?>

                            </td>


                            <td>

                                <span class="status <?php echo htmlspecialchars($product["status"]); ?>">

                                    <?php
                                    echo ucfirst(
                                        $product["status"]
                                    );
                                    ?>

                                </span>

                            </td>


                            <td>
                                <?php echo htmlspecialchars($product["created_at"]); ?>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="8">
                            No products have been added yet.
                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>

</html>