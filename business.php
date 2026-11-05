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

$message = "";
$message_type = "";

// Handle business registration
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $business_name = trim($_POST["business_name"]);
    $description = trim($_POST["description"]);
    $phone = trim($_POST["phone"]);
    $location = trim($_POST["location"]);

    // Check required field
    if (empty($business_name)) {

        $message = "Please enter your business name.";
        $message_type = "error";

    } else {

        // Check if this owner already has a business
        $check = $conn->prepare(
            "SELECT id FROM businesses WHERE owner_id = ?"
        );

        $check->bind_param("i", $owner_id);
        $check->execute();

        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $message = "You already have a registered business.";
            $message_type = "error";

        } else {

            // Insert business
            $stmt = $conn->prepare(
                "INSERT INTO businesses
                (owner_id, business_name, description, phone, location)
                VALUES (?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "issss",
                $owner_id,
                $business_name,
                $description,
                $phone,
                $location
            );

            if ($stmt->execute()) {

                $message = "Business registered successfully!";
                $message_type = "success";

            } else {

                $message = "Something went wrong. Please try again.";
                $message_type = "error";
            }

            $stmt->close();
        }

        $check->close();
    }
}

// Get owner's business
$stmt = $conn->prepare(
    "SELECT id, business_name, description, phone, location, created_at
     FROM businesses
     WHERE owner_id = ?"
);

$stmt->bind_param("i", $owner_id);
$stmt->execute();

$business_result = $stmt->get_result();

$business = $business_result->fetch_assoc();

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>My Business - Small Business Shop</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f5f7f6;
            min-height: 100vh;
        }

        .navbar {
            background-color: #00ad2e;
            color: white;
            padding: 18px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h2 {
            margin: 0;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }

        .container {
            width: 90%;
            max-width: 900px;
            margin: 40px auto;
        }

        .card {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }

        h1 {
            margin-bottom: 10px;
            color: #222;
        }

        h2 {
            margin-bottom: 20px;
            color: #222;
        }

        .subtitle {
            color: #666;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input,
        textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 15px;
        }

        textarea {
            height: 120px;
            resize: vertical;
        }

        button {
            background-color: #00ad2e;
            color: white;
            border: none;
            padding: 13px 25px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #008f26;
        }

        .message {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .success {
            background-color: #e5f8ea;
            color: #087a25;
        }

        .error {
            background-color: #ffe5e5;
            color: #b00000;
        }

        .business-info {
            line-height: 1.8;
        }

        .business-info strong {
            color: #333;
        }

        .back {
            display: inline-block;
            margin-top: 20px;
            color: #00ad2e;
            text-decoration: none;
            font-weight: bold;
        }

    </style>

</head>

<body>

<div class="navbar">

    <h2>Small Business Shop</h2>

    <div>

        <a href="business_owner_dashboard.php">
            Dashboard
        </a>

        <a href="logout.php">
            Logout
        </a>

    </div>

</div>


<div class="container">

    <div class="card">

        <h1>My Business</h1>

        <p class="subtitle">
            Register and manage your business information.
        </p>


        <?php if (!empty($message)): ?>

            <div class="message <?php echo $message_type; ?>">

                <?php echo htmlspecialchars($message); ?>

            </div>

        <?php endif; ?>


        <?php if (!$business): ?>

            <h2>Register Your Business</h2>

            <form method="POST" action="">

                <label for="business_name">
                    Business Name
                </label>

                <input
                    type="text"
                    id="business_name"
                    name="business_name"
                    placeholder="Enter your business name"
                    required
                >


                <label for="description">
                    Business Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    placeholder="Describe your business"
                ></textarea>


                <label for="phone">
                    Phone Number
                </label>

                <input
                    type="text"
                    id="phone"
                    name="phone"
                    placeholder="Enter your phone number"
                >


                <label for="location">
                    Business Location
                </label>

                <input
                    type="text"
                    id="location"
                    name="location"
                    placeholder="Enter your business location"
                >


                <button type="submit">
                    Register Business
                </button>

            </form>

        <?php else: ?>

            <h2>Business Information</h2>

            <div class="business-info">

                <p>
                    <strong>Business Name:</strong>
                    <?php echo htmlspecialchars($business["business_name"]); ?>
                </p>

                <p>
                    <strong>Description:</strong>
                    <?php
                    echo !empty($business["description"])
                        ? htmlspecialchars($business["description"])
                        : "Not provided";
                    ?>
                </p>

                <p>
                    <strong>Phone:</strong>
                    <?php
                    echo !empty($business["phone"])
                        ? htmlspecialchars($business["phone"])
                        : "Not provided";
                    ?>
                </p>

                <p>
                    <strong>Location:</strong>
                    <?php
                    echo !empty($business["location"])
                        ? htmlspecialchars($business["location"])
                        : "Not provided";
                    ?>
                </p>

                <p>
                    <strong>Registered:</strong>
                    <?php echo htmlspecialchars($business["created_at"]); ?>
                </p>

            </div>

        <?php endif; ?>


        <a class="back" href="business_owner_dashboard.php">
            ← Back to Business Dashboard
        </a>

    </div>

</div>

</body>

</html>