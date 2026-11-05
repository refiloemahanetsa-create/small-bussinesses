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


// Add educational resource
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $topic = trim($_POST["topic"]);
    $resource_type = trim($_POST["resource_type"]);
    $source_name = trim($_POST["source_name"]);
    $url = trim($_POST["url"]);


    if (
        empty($title) ||
        empty($topic) ||
        empty($resource_type) ||
        empty($url)
    ) {

        $error = "Please fill in all required fields.";

    } elseif (!filter_var($url, FILTER_VALIDATE_URL)) {

        $error = "Please enter a valid URL.";

    } else {

        $stmt = $conn->prepare(
            "INSERT INTO educational_resources
            (title, description, topic, resource_type, source_name, url)
            VALUES (?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "ssssss",
            $title,
            $description,
            $topic,
            $resource_type,
            $source_name,
            $url
        );

        if ($stmt->execute()) {

            $message = "Educational resource added successfully.";

        } else {

            $error = "Failed to add educational resource.";
        }

        $stmt->close();
    }
}


// Get all educational resources
$result = $conn->query(
    "SELECT
        id,
        title,
        description,
        topic,
        resource_type,
        source_name,
        url,
        status,
        created_at
     FROM educational_resources
     ORDER BY created_at DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Manage Learning Resources</title>

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
            margin-bottom: 30px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        h1 {
            margin-bottom: 10px;
        }

        h2 {
            margin-bottom: 20px;
        }

        .description {
            color: #666;
            margin-bottom: 25px;
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
        textarea,
        select {
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

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
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
            padding: 6px 10px;
            border-radius: 5px;
            font-size: 13px;
            font-weight: bold;
        }

        .active {
            background-color: #d4edda;
            color: #155724;
        }

        .inactive {
            background-color: #f8d7da;
            color: #721c24;
        }

        .view-link {
            color: #00ad2e;
            font-weight: bold;
            text-decoration: none;
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


    <!-- Add Resource -->

    <div class="box">

        <h1>Manage Learning Resources</h1>

        <p class="description">
            Add educational resources that business owners can
            search and use to improve their businesses.
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

                <label for="title">
                    Title *
                </label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    placeholder="Example: How to Market Your Business Online"
                    required
                >

            </div>


            <div class="form-group">

                <label for="topic">
                    Topic *
                </label>

                <input
                    type="text"
                    id="topic"
                    name="topic"
                    placeholder="Example: Online Marketing"
                    required
                >

            </div>


            <div class="form-group">

                <label for="resource_type">
                    Resource Type *
                </label>

                <select
                    id="resource_type"
                    name="resource_type"
                    required
                >

                    <option value="">
                        Select Resource Type
                    </option>

                    <option value="Article">
                        Article
                    </option>

                    <option value="Video">
                        Video
                    </option>

                    <option value="Guide">
                        Guide
                    </option>

                    <option value="Tutorial">
                        Tutorial
                    </option>

                    <option value="Other">
                        Other
                    </option>

                </select>

            </div>


            <div class="form-group">

                <label for="description">
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    placeholder="Briefly explain what the resource teaches..."
                ></textarea>

            </div>


            <div class="form-group">

                <label for="source_name">
                    Source Name
                </label>

                <input
                    type="text"
                    id="source_name"
                    name="source_name"
                    placeholder="Example: Google Digital Garage"
                >

            </div>


            <div class="form-group">

                <label for="url">
                    Resource URL *
                </label>

                <input
                    type="url"
                    id="url"
                    name="url"
                    placeholder="https://example.com"
                    required
                >

            </div>


            <button type="submit">
                Add Resource
            </button>

        </form>

    </div>


    <!-- Existing Resources -->

    <div class="box">

        <h2>Existing Learning Resources</h2>

        <div class="table-container">

            <table>

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Title</th>
                        <th>Topic</th>
                        <th>Type</th>
                        <th>Source</th>
                        <th>Status</th>
                        <th>Resource</th>
                        <th>Date Added</th>

                    </tr>

                </thead>

                <tbody>

                    <?php if ($result && $result->num_rows > 0): ?>

                        <?php while ($resource = $result->fetch_assoc()): ?>

                            <tr>

                                <td>
                                    <?php echo $resource["id"]; ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $resource["title"]
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $resource["topic"]
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $resource["resource_type"]
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $resource["source_name"] ?? ""
                                    );
                                    ?>
                                </td>

                                <td>

                                    <span class="status
                                        <?php
                                        echo strtolower(
                                            $resource["status"]
                                        );
                                        ?>
                                    ">

                                        <?php
                                        echo htmlspecialchars(
                                            ucfirst(
                                                $resource["status"]
                                            )
                                        );
                                        ?>

                                    </span>

                                </td>

                                <td>

                                    <a
                                        href="<?php echo htmlspecialchars($resource["url"]); ?>"
                                        target="_blank"
                                        class="view-link"
                                    >
                                        View
                                    </a>

                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $resource["created_at"]
                                    );
                                    ?>
                                </td>

                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="8">
                                No educational resources have
                                been added yet.
                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</body>

</html>