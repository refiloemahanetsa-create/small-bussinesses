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

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard</title>

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
            padding: 50px 7%;
        }

        .welcome {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .welcome h1 {
            margin-bottom: 10px;
        }

        .welcome p {
            color: #666;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .card {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        .card h3 {
            color: #00ad2e;
            margin-bottom: 15px;
        }

        .card p {
            color: #666;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .card a {
            display: inline-block;
            background-color: #00ad2e;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
        }

        .card a:hover {
            background-color: #008f26;
        }

        @media (max-width: 900px) {

            .cards {
                grid-template-columns: repeat(2, 1fr);
            }

        }

        @media (max-width: 600px) {

            .cards {
                grid-template-columns: 1fr;
            }

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

    <div class="welcome">

        <h1>
            Welcome,
            <?php echo htmlspecialchars($_SESSION["user_name"]); ?>!
        </h1>

        <p>
            Manage and monitor the Small Business Shop platform
            from the administrator dashboard.
        </p>

    </div>


    <div class="cards">


        <!-- Users -->

        <div class="card">

            <h3>Users</h3>

            <p>
                View and manage customers and business owners
                registered on the platform.
            </p>

            <a href="admin_users.php">
    Manage Users
</a>

        </div>


        <!-- Businesses -->

        <div class="card">

            <h3>Businesses</h3>

            <p>
                View and manage businesses registered
                on the platform.
            </p>

            <a href="admin_businesses.php">
    Manage Businesses
</a>

        </div>


        <!-- Products -->

        <div class="card">

            <h3>Products</h3>

            <p>
                View and manage products listed by
                business owners.
            </p>

            <a href="admin_products.php">
    Manage Products
</a>

        </div>


        <!-- Categories -->

        <div class="card">

            <h3>Categories</h3>

            <p>
                Manage product categories used by
                businesses.
            </p>

            <a href="admin_categories.php">
    Manage Categories
</a>

        </div>


        <!-- Orders -->

        <div class="card">

            <h3>Orders</h3>

            <p>
                Monitor orders made by customers
                on the platform.
            </p>

            <a href="admin_orders.php">
    Manage Orders
</a>

        </div>


        <!-- Learning Resources -->

        <div class="card">

            <h3>Learning Resources</h3>

            <p>
                Manage educational resources available
                to business owners.
            </p>

            <a href="admin_resources.php">
    Manage Resources
</a>

        </div>


    </div>

</div>

</body>

</html>