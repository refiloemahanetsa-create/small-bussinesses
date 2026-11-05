
<?php

// Start the session and load the language system.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/language.php";
require_once __DIR__ . "/config/db.php";

$message = "";
$message_type = "";

// Preserve entered values after validation errors.
$full_name = "";
$email = "";
$selected_role = "customer";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $selected_role = $_POST["role"] ?? "customer";

    // Only these roles may be selected publicly.
    $allowed_roles = ["customer", "business_owner"];

    if (
        $full_name === "" ||
        $email === "" ||
        $password === ""
    ) {
        $message = t("fill_all_fields");
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = t("invalid_email");
        $message_type = "error";

    } elseif (!in_array($selected_role, $allowed_roles, true)) {

        $message = t("invalid_role");
        $message_type = "error";

    } elseif (strlen($password) < 8) {

        $message = t("password_too_short");
        $message_type = "error";

    } else {

        // Check whether the email is already registered.
        $check = $conn->prepare(
            "SELECT id FROM users WHERE email = ?"
        );

        $check->bind_param("s", $email);
        $check->execute();

        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $message = t("email_exists");
            $message_type = "error";

        } else {

            // Securely hash the password.
            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // Save the selected role in the database.
            $stmt = $conn->prepare(
                "INSERT INTO users
                    (full_name, email, password, role)
                 VALUES (?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "ssss",
                $full_name,
                $email,
                $hashed_password,
                $selected_role
            );

            if ($stmt->execute()) {

                $message = t("registration_success");
                $message_type = "success";

                // Clear the form after successful registration.
                $full_name = "";
                $email = "";
                $selected_role = "customer";

            } else {

                error_log("Registration failed: " . $stmt->error);

                $message = t("registration_failed");
                $message_type = "error";
            }

            $stmt->close();
        }

        $check->close();
    }
}

?>

<!DOCTYPE html>
<html lang="<?= ($_SESSION["language"] ?? "en") === "st" ? "st" : "en" ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= htmlspecialchars(t("register"), ENT_QUOTES, "UTF-8") ?>
        -
        <?= htmlspecialchars(t("site_name"), ENT_QUOTES, "UTF-8") ?>
    </title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background:
                radial-gradient(
                    circle at top left,
                    rgba(37, 102, 54, 0.12),
                    transparent 40%
                ),
                linear-gradient(135deg, #f4fff6, #ffffff, #e8f7ec);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 25px 15px;
        }

        .register-container {
            background-color: white;
            width: 100%;
            max-width: 440px;
            padding: 35px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .home-link,
        .login a {
            color: #00ad2e;
            text-decoration: none;
            font-weight: bold;
        }

        h1 {
            text-align: center;
            margin-bottom: 10px;
            color: #222;
        }

        .subtitle {
            text-align: center;
            color: #777;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        input,
        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 15px;
            background-color: white;
        }

        input:focus,
        select:focus {
            outline: 2px solid rgba(0, 173, 46, 0.25);
            border-color: #00ad2e;
        }

        button {
            width: 100%;
            padding: 13px;
            background-color: #00ad2e;
            color: white;
            border: none;
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
            text-align: center;
        }

        .success {
            background-color: #e5f8ea;
            color: #087a25;
        }

        .error {
            background-color: #ffe5e5;
            color: #b00000;
        }

        .login {
            text-align: center;
            margin-top: 20px;
            line-height: 1.6;
        }

        .back {
            text-align: center;
            margin-top: 15px;
        }

        .back a {
            color: #555;
            text-decoration: none;
        }

        .role-help {
            color: #666;
            font-size: 13px;
            margin-top: -12px;
            margin-bottom: 20px;
            line-height: 1.5;
        }
    </style>
</head>

<body>

<div class="register-container">

    <div class="top-bar">
        <a href="index.php" class="home-link">
            <?= htmlspecialchars(t("home"), ENT_QUOTES, "UTF-8") ?>
        </a>
    </div>

    <h1>
        <?= htmlspecialchars(t("create_account"), ENT_QUOTES, "UTF-8") ?>
    </h1>

    <p class="subtitle">
        <?= htmlspecialchars(t("join_shop"), ENT_QUOTES, "UTF-8") ?>
    </p>

    <?php if ($message !== ""): ?>
        <div class="message <?= $message_type ?>">
            <?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">

        <label for="full_name">
            <?= htmlspecialchars(t("full_name"), ENT_QUOTES, "UTF-8") ?>
        </label>

        <input
            type="text"
            id="full_name"
            name="full_name"
            value="<?= htmlspecialchars($full_name, ENT_QUOTES, "UTF-8") ?>"
            placeholder="<?= htmlspecialchars(t("enter_full_name"), ENT_QUOTES, "UTF-8") ?>"
            autocomplete="name"
            required
        >

        <label for="email">
            <?= htmlspecialchars(t("email"), ENT_QUOTES, "UTF-8") ?>
        </label>

        <input
            type="email"
            id="email"
            name="email"
            value="<?= htmlspecialchars($email, ENT_QUOTES, "UTF-8") ?>"
            placeholder="<?= htmlspecialchars(t("enter_email"), ENT_QUOTES, "UTF-8") ?>"
            autocomplete="email"
            required
        >

        <label for="password">
            <?= htmlspecialchars(t("password"), ENT_QUOTES, "UTF-8") ?>
        </label>

        <input
            type="password"
            id="password"
            name="password"
            placeholder="<?= htmlspecialchars(t("create_password"), ENT_QUOTES, "UTF-8") ?>"
            autocomplete="new-password"
            minlength="8"
            required
        >

        <label for="role">
            <?= htmlspecialchars(t("register_as"), ENT_QUOTES, "UTF-8") ?>
        </label>

        <select name="role" id="role" required>
            <option
                value="customer"
                <?= $selected_role === "customer" ? "selected" : "" ?>
            >
                <?= htmlspecialchars(t("customer"), ENT_QUOTES, "UTF-8") ?>
            </option>

            <option
                value="business_owner"
                <?= $selected_role === "business_owner" ? "selected" : "" ?>
            >
                <?= htmlspecialchars(t("business_owner"), ENT_QUOTES, "UTF-8") ?>
            </option>
        </select>

        <p class="role-help">
            <?= htmlspecialchars(t("role_help"), ENT_QUOTES, "UTF-8") ?>
        </p>

        <button type="submit">
            <?= htmlspecialchars(t("register"), ENT_QUOTES, "UTF-8") ?>
        </button>

    </form>

    <div class="login">
        <?= htmlspecialchars(t("already_account"), ENT_QUOTES, "UTF-8") ?>

        <a href="login.php">
            <?= htmlspecialchars(t("login"), ENT_QUOTES, "UTF-8") ?>
        </a>
    </div>

    <div class="back">
        <a href="index.php">
            &larr; <?= htmlspecialchars(t("back_home"), ENT_QUOTES, "UTF-8") ?>
        </a>
    </div>

</div>

</body>
</html>
