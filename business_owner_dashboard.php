
<?php
session_start();

require_once __DIR__ . '/language.php';
require_once __DIR__ . '/config/db.php';

// Make sure the user is logged in.
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Only business owners can access this page.
if (($_SESSION["user_role"] ?? '') !== "business_owner") {
    header("Location: dashboard.php");
    exit();
}

// Safely display translated text.
function e($text) {
    return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="<?= $_SESSION['language'] === 'st' ? 'st' : 'en' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= e(t('owner_dashboard_title')) ?></title>

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
            gap: 15px;
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
            grid-template-columns: repeat(4, minmax(0, 1fr));
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

        @media (max-width: 1000px) {
            .cards {
                grid-template-columns: repeat(2, minmax(0, 1fr));
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
    <h2><?= e(t('shop_name')) ?></h2>

    <a href="logout.php">
        <?= e(t('logout')) ?>
    </a>
</nav>

<div class="container">

    <div class="welcome">
        <h1>
            <?= e(t('welcome')) ?>,
            <?= e($_SESSION['user_name'] ?? '') ?>!
        </h1>

        <p><?= e(t('dashboard_intro')) ?></p>
    </div>

    <div class="cards">

        <div class="card">
            <h3><?= e(t('my_business')) ?></h3>

            <p><?= e(t('business_description')) ?></p>

            <a href="business.php">
                <?= e(t('manage_business')) ?>
            </a>
        </div>

        <div class="card">
            <h3><?= e(t('my_products')) ?></h3>

            <p><?= e(t('products_description')) ?></p>

            <a href="products.php">
                <?= e(t('manage_products')) ?>
            </a>
        </div>

        <div class="card">
            <h3><?= e(t('orders')) ?></h3>

            <p><?= e(t('orders_description')) ?></p>

            <a href="business_orders.php">
                <?= e(t('view_orders')) ?>
            </a>
        </div>

        <div class="card">
            <h3><?= e(t('learning_resources')) ?></h3>

            <p><?= e(t('learning_description')) ?></p>

            <a href="educational_resources.php">
                <?= e(t('start_learning')) ?>
            </a>
        </div>

        <div class="card">
            <h3><?= e(t('ai_assistant')) ?></h3>

            <p><?= e(t('ai_description')) ?></p>

            <a href="ai_assistant.php">
                <?= e(t('open_ai_assistant')) ?>
            </a>
        </div>

    </div>
</div>

</body>
</html>
