<?php

session_start();

require_once "language.php";
require_once "config/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (empty($email) || empty($password)) {

        $message = t("login_fill_fields");

    } else {

        $sql = "SELECT id, full_name, email, password, role
                FROM users
                WHERE email = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows == 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user["password"])) {

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_name"] = $user["full_name"];
                $_SESSION["user_email"] = $user["email"];
                $_SESSION["user_role"] = $user["role"];

                if ($user["role"] == "business_owner") {

                    header("Location: business_owner_dashboard.php");

                } elseif ($user["role"] == "admin") {

                    header("Location: admin_dashboard.php");

                } else {

                    header("Location: dashboard.php");
                }

                exit();

            } else {

                $message = t("incorrect_password");
            }

        } else {

            $message = t("no_account_email");
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="<?php echo $_SESSION["language"] === "st" ? "st" : "en"; ?>">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo htmlspecialchars(t("login")); ?> -
        <?php echo htmlspecialchars(t("site_name")); ?>
    </title>

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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background-color: white;
            width: 400px;
            max-width: 100%;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 25px;
        }

        .home-link {
            color: #00ad2e;
            text-decoration: none;
            font-weight: bold;
        }

        .language-select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background: white;
            cursor: pointer;
        }

        h1 {
            text-align: center;
            margin-bottom: 10px;
            color: #222;
        }

        .subtitle {
            text-align: center;
            color: #777;
            margin-bottom: 30px;
            line-height: 1.5;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 15px;
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
            background-color: #ffe5e5;
            color: #b00000;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .register {
            text-align: center;
            margin-top: 20px;
            line-height: 1.8;
        }

        .register a {
            color: #00ad2e;
            text-decoration: none;
            font-weight: bold;
        }

        .back {
            text-align: center;
            margin-top: 15px;
        }

        .back a {
            color: #555;
            text-decoration: none;
        }

    </style>

</head>

<body>

<div class="login-container">

    <div class="top-bar">

        <a href="index.php" class="home-link">
            <?php echo htmlspecialchars(t("home")); ?>
        </a>

    </div>

    <h1>
        <?php echo htmlspecialchars(t("login_welcome")); ?>
    </h1>

    <p class="subtitle">
        <?php echo htmlspecialchars(t("login_subtitle")); ?>
    </p>

    <?php if (!empty($message)): ?>

        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>

    <form method="POST" action="">

        <label for="email">
            <?php echo htmlspecialchars(t("email")); ?>
        </label>

        <input
            type="email"
            id="email"
            name="email"
            placeholder="<?php echo htmlspecialchars(t("enter_email")); ?>"
            autocomplete="email"
            required
        >

        <label for="password">
            <?php echo htmlspecialchars(t("password")); ?>
        </label>

        <input
            type="password"
            id="password"
            name="password"
            placeholder="<?php echo htmlspecialchars(t("enter_password")); ?>"
            autocomplete="current-password"
            required
        >

        <button type="submit">
            <?php echo htmlspecialchars(t("login")); ?>
        </button>

    </form>

    <div class="register">

        <?php echo htmlspecialchars(t("no_account_question")); ?>

        <a href="register.php">
            <?php echo htmlspecialchars(t("register")); ?>
        </a>

    </div>

    <div class="back">

        <a href="index.php">
            ← <?php echo htmlspecialchars(t("back_home")); ?>
        </a>

    </div>

</div>

<script>
function changeLanguage(language) {
    window.location.href = "login.php?lang=" + encodeURIComponent(language);
}
</script>

</body>
</html>
