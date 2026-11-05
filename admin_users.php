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


// Get all users
$sql = "SELECT id, full_name, email, role, created_at
        FROM users
        ORDER BY created_at DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Manage Users</title>

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
        }

        tr:hover {
            background-color: #f5f7f6;
        }

        .role {
            padding: 6px 10px;
            border-radius: 15px;
            font-size: 13px;
            font-weight: bold;
        }

        .customer {
            background-color: #e8f8ed;
            color: #008f26;
        }

        .business_owner {
            background-color: #fff3cd;
            color: #856404;
        }

        .admin {
            background-color: #e2e3e5;
            color: #383d41;
        }

        @media (max-width: 700px) {

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

        <h1>Manage Users</h1>

        <p>
            View customers, business owners and administrators
            registered on the platform.
        </p>

    </div>


    <div class="table-container">

        <table>

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Full Name</th>

                    <th>Email</th>

                    <th>Role</th>

                    <th>Registered Date</th>

                </tr>

            </thead>


            <tbody>

                <?php if ($result->num_rows > 0): ?>

                    <?php while ($user = $result->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?php echo $user["id"]; ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($user["full_name"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($user["email"]); ?>
                            </td>

                            <td>

                                <span class="role <?php echo htmlspecialchars($user["role"]); ?>">

                                    <?php
                                    echo ucfirst(
                                        str_replace(
                                            "_",
                                            " ",
                                            $user["role"]
                                        )
                                    );
                                    ?>

                                </span>

                            </td>

                            <td>
                                <?php echo htmlspecialchars($user["created_at"]); ?>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="5">
                            No users found.
                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>

</html>