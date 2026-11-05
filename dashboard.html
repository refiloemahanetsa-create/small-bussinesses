
<?php
session_start();

require_once __DIR__ . '/language.php';

// Make sure the user is logged in.
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Escape text before displaying it.
function e($text) {
    return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
}

$currentLanguage = $_SESSION['language'] ?? 'en';
?>

<!DOCTYPE html>
<html lang="<?= $currentLanguage === 'st' ? 'st' : 'en' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= e(t('customer_dashboard_title')) ?></title>

    <style>
        * {
            box-sizing: border-box;
        }

        .payment-button {
    display: inline-block;
    background-color: #00ad2e;
    color: white;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 5px;
    margin-top: 10px;
}

.payment-button:hover {
    background-color: #008f26;
}

        body {
            margin: 0;
            font-family: Arial, sans-serif;
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
            margin: 0;
        }

        nav a {
            color: #00ad2e;
            text-decoration: none;
            background-color: white;
            padding: 10px 18px;
            border-radius: 5px;
            font-weight: bold;
        }

        .dashboard {
            padding: 50px 7%;
        }

        .welcome {
            background-color: white;
            padding: 35px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .welcome h1 {
            margin-bottom: 10px;
        }

        .welcome p {
            color: #666;
            line-height: 1.6;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
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
            margin-top: 0;
        }

        .card p {
            color: #666;
            line-height: 1.6;
        }

        .card-link {
            display: block;
            color: inherit;
            text-decoration: none;
            transition: transform 0.2s;
        }

        .card-link:hover .card {
            transform: translateY(-3px);
        }

        @media (max-width: 800px) {
            .cards {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 550px) {
            .dashboard {
                padding: 30px 5%;
            }

            .cards {
                grid-template-columns: 1fr;
            }

            nav {
                padding: 15px 5%;
            }

            .welcome {
                padding: 25px;
            }
        }
    </style>
</head>

<body>

<nav>
    <h2><?= e(t('shop_name')) ?></h2>

    <a href="logout.php"><?= e(t('logout')) ?></a>
</nav>

<div class="dashboard">

    <div class="welcome">
        <h1>
            <?= e(t('welcome')) ?>,
            <?= e($_SESSION["user_name"] ?? '') ?>!
        </h1>

        <p><?= e(t('customer_dashboard_message')) ?></p>
    </div>

    <div class="cards">

        <a href="shop.php" class="card-link">
            <div class="card">
                <h3><?= e(t('browse_products')) ?></h3>
                <p><?= e(t('browse_products_description')) ?></p>
            </div>
        </a>

        <a href="my_orders.php" class="card-link">
            <div class="card">
                <h3><?= e(t('my_orders')) ?></h3>
                <p><?= e(t('my_orders_description')) ?></p>
            </div>
        </a>

        <a href="checkout.php" class="card-link">
    <div class="card">
        <h3><?= e(t('payment')) ?></h3>

        <p><?= e(t('payment_description')) ?></p>

        <span class="payment-button">
            <?= e(t('proceed_to_payment')) ?>
        </span>
    </div>
</a>

    </div>
</div>

</body>
</html>
