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

$message = "";
$error = "";

// Add new category
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $description = trim($_POST["description"]);

    if (empty($name)) {

        $error = "Category name is required.";

    } else {

        // Check if category already exists
        $check = $conn->prepare(
            "SELECT id FROM categories WHERE name = ?"
        );

        $check->bind_param("s", $name);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {

            $error = "This category already exists.";

        } else {

            // Insert category
            $stmt = $conn->prepare(
                "INSERT INTO categories (name, description)
                 VALUES (?, ?)"
            );

            $stmt->bind_param("ss", $name, $description);

            if ($stmt->execute()) {

                $message = "Category added successfully.";

            } else {

                $error = "Failed to add category.";
            }

            $stmt->close();
        }

        $check->close();
    }
}


// Get all categories
$result = $conn->query(
    "SELECT id, name, description
     FROM categories
     ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Manage Categories</title>

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

        .form-box {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        .form-box h1 {
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 15px;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        button {
            background-color: #00ad2e;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 15px;
            font-weight: bold;
        }

        button:hover {
            background-color: #008f26;
        }

        .success {
            background-color: #dff5e5;
            color: #167a32;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .error {
            background-color: #ffe1e1;
            color: #b30000;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .table-box {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            overflow-x: auto;
        }

        .table-box h2 {
            margin-bottom: 20px;
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

        tr:hover {
            background-color: #f5f5f5;
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


    <!-- Add Category -->

    <div class="form-box">

        <h1>Manage Categories</h1>

        <p style="margin-bottom: 20px; color: #666;">
            Add product categories that business owners can use
            when adding their products.
        </p>


        <?php if (!empty($message)): ?>

            <div class="success">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>


        <?php if (!empty($error)): ?>

            <div class="error">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>


        <form method="POST">

            <div class="form-group">

                <label for="name">
                    Category Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Example: Clothing"
                    required
                >

            </div>


            <div class="form-group">

                <label for="description">
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    placeholder="Describe this category..."
                ></textarea>

            </div>


            <button type="submit">
                Add Category
            </button>

        </form>

    </div>


    <!-- Categories List -->

    <div class="table-box">

        <h2>Existing Categories</h2>

        <table>

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Category Name</th>

                    <th>Description</th>

                </tr>

            </thead>

            <tbody>

                <?php if ($result->num_rows > 0): ?>

                    <?php while ($category = $result->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?php echo $category["id"]; ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($category["name"]); ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $category["description"] ?? ""
                                );
                                ?>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="3">
                            No categories have been added yet.
                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>

</html>