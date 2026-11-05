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


// Get all businesses and their owners
$sql = "SELECT
            b.id,
            b.business_name,
            b.description,
            b.phone,
            b.location,
            b.created_at,
            u.full_name AS owner_name,
            u.email AS owner_email
        FROM businesses b
        INNER JOIN users u
            ON b.owner_id = u.id
        ORDER BY b.created_at DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Manage Businesses</title>

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
            vertical-align: top;
        }

        tr:hover {
            background-color: #f5f7f6;
        }

        .business-name {
            font-weight: bold;
            color: #00ad2e;
        }

        .description {
            max-width: 250px;
            line-height: 1.5;
            color: #555;
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

        <h1>Manage Businesses</h1>

        <p>
            View businesses registered by business owners
            on the platform.
        </p>

    </div>


    <div class="table-container">

        <table>

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Business Name</th>

                    <th>Description</th>

                    <th>Owner</th>

                    <th>Owner Email</th>

                    <th>Phone</th>

                    <th>Location</th>

                    <th>Registered Date</th>

                </tr>

            </thead>


            <tbody>

                <?php if ($result->num_rows > 0): ?>

                    <?php while ($business = $result->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?php echo $business["id"]; ?>
                            </td>

                            <td>

                                <span class="business-name">
                                    <?php echo htmlspecialchars($business["business_name"]); ?>
                                </span>

                            </td>

                            <td>

                                <div class="description">
                                    <?php
                                    echo htmlspecialchars(
                                        $business["description"] ?? "No description"
                                    );
                                    ?>
                                </div>

                            </td>

                            <td>
                                <?php echo htmlspecialchars($business["owner_name"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($business["owner_email"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($business["phone"] ?? "Not provided"); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($business["location"] ?? "Not provided"); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($business["created_at"]); ?>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="8">
                            No businesses have been registered yet.
                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>

</html>