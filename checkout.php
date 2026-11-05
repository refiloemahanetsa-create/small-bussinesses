
<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

session_start();

require_once "config/db.php";

// Check customer login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$customer_id = (int) $_SESSION["user_id"];

$error = "";
$items = [];
$total = 0;
$business_id = 0;
$business_name = "";

// Find customer's cart
$cart_sql = "SELECT id FROM carts WHERE customer_id = ?";
$cart_stmt = $conn->prepare($cart_sql);
$cart_stmt->bind_param("i", $customer_id);
$cart_stmt->execute();

$cart_result = $cart_stmt->get_result();

if ($cart_result->num_rows === 0) {
    header("Location: cart.php");
    exit();
}

$cart = $cart_result->fetch_assoc();
$cart_id = (int) $cart["id"];

// Get cart products and their shop information
$items_sql = "SELECT
                ci.product_id,
                ci.quantity,
                p.name,
                p.price,
                p.stock_quantity,
                p.business_id,
                b.business_name
              FROM cart_items ci
              INNER JOIN products p
                ON ci.product_id = p.id
              INNER JOIN businesses b
                ON p.business_id = b.id
              WHERE ci.cart_id = ?";

$items_stmt = $conn->prepare($items_sql);
$items_stmt->bind_param("i", $cart_id);
$items_stmt->execute();

$items_result = $items_stmt->get_result();

while ($item = $items_result->fetch_assoc()) {

    $items[] = $item;

    // Identify the shop
    if ($business_id === 0) {
        $business_id = (int) $item["business_id"];
        $business_name = $item["business_name"];
    }

    // Prevent mixed-shop orders
    if ((int) $item["business_id"] !== $business_id) {
        $error = "Your cart contains products from different shops. "
               . "Please remove products until only one shop remains.";
        break;
    }

    // Check stock
    if ((int) $item["quantity"] > (int) $item["stock_quantity"]) {
        $error = "There is not enough stock for "
               . htmlspecialchars($item["name"])
               . ". Please update your cart.";
        break;
    }

    $total += $item["price"] * $item["quantity"];
}

// Check empty cart
if (empty($items)) {
    header("Location: cart.php");
    exit();
}

// Process checkout
if ($_SERVER["REQUEST_METHOD"] === "POST" && $error === "") {

    $delivery_address = trim($_POST["delivery_address"] ?? "");

    if ($delivery_address === "") {

        $error = "Please enter your delivery address.";

    } else {

        $conn->begin_transaction();

        try {

            // Recheck cart products and stock inside transaction
            $verify_sql = "SELECT
                            ci.product_id,
                            ci.quantity,
                            p.name,
                            p.price,
                            p.stock_quantity,
                            p.business_id
                           FROM cart_items ci
                           INNER JOIN products p
                            ON ci.product_id = p.id
                           WHERE ci.cart_id = ?
                           FOR UPDATE";

            $verify_stmt = $conn->prepare($verify_sql);
            $verify_stmt->bind_param("i", $cart_id);
            $verify_stmt->execute();

            $verify_result = $verify_stmt->get_result();

            $verified_items = [];
            $verified_total = 0;
            $verified_business_id = 0;

            while ($item = $verify_result->fetch_assoc()) {

                $item_business_id = (int) $item["business_id"];

                if ($verified_business_id === 0) {
                    $verified_business_id = $item_business_id;
                }

                if ($item_business_id !== $verified_business_id) {
                    throw new Exception(
                        "Your cart contains products from different shops."
                    );
                }

                if ((int) $item["quantity"] < 1 ||
                    (int) $item["quantity"] > (int) $item["stock_quantity"]) {

                    throw new Exception(
                        "There is not enough stock for "
                        . $item["name"] . "."
                    );
                }

                $verified_total +=
                    $item["price"] * $item["quantity"];

                $verified_items[] = $item;
            }

            if (empty($verified_items)) {
                throw new Exception("Your cart is empty.");
            }

            if ($verified_business_id !== $business_id) {
                throw new Exception(
                    "Your cart has changed. Please review it again."
                );
            }

            // Create order with the selected shop ID
            $order_sql = "INSERT INTO orders
                          (customer_id, business_id, total_amount,
                           status, delivery_address)
                          VALUES (?, ?, ?, 'pending', ?)";

            $order_stmt = $conn->prepare($order_sql);

            $order_stmt->bind_param(
                "iids",
                $customer_id,
                $verified_business_id,
                $verified_total,
                $delivery_address
            );

            $order_stmt->execute();

            $order_id = $conn->insert_id;

            // Insert order items and reduce stock
            foreach ($verified_items as $item) {

                $order_item_sql = "INSERT INTO order_items
                                   (order_id, product_id,
                                    quantity, unit_price)
                                   VALUES (?, ?, ?, ?)";

                $order_item_stmt = $conn->prepare($order_item_sql);

                $order_item_stmt->bind_param(
                    "iiid",
                    $order_id,
                    $item["product_id"],
                    $item["quantity"],
                    $item["price"]
                );

                $order_item_stmt->execute();

                // Reduce product stock
                $new_stock =
                    (int) $item["stock_quantity"]
                    - (int) $item["quantity"];

                $stock_sql = "UPDATE products
                              SET stock_quantity = ?
                              WHERE id = ?";

                $stock_stmt = $conn->prepare($stock_sql);

                $stock_stmt->bind_param(
                    "ii",
                    $new_stock,
                    $item["product_id"]
                );

                $stock_stmt->execute();
            }

            // Empty customer's cart
            $delete_sql = "DELETE FROM cart_items WHERE cart_id = ?";

            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("i", $cart_id);
            $delete_stmt->execute();

            // Complete database transaction
            $conn->commit();

            // Go to order confirmation
            header(
                "Location: order_confirmation.php?order_id="
                . $order_id
            );

            exit();

        } catch (Throwable $e) {

            $conn->rollback();

            $error = $e->getMessage();

            // Log technical details instead of displaying them
            error_log("Checkout error: " . $e->getMessage());
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Checkout - Small Business Shop</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
        }

        .container {
            width: 90%;
            max-width: 900px;
            margin: 40px auto;
        }

        .checkout-box {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
        }

        h1 {
            margin-bottom: 25px;
        }

        .shop-box {
            background-color: #e8f5e9;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #ddd;
        }

        .total {
            text-align: right;
            font-size: 22px;
            font-weight: bold;
            margin-top: 20px;
        }

        label {
            display: block;
            margin-top: 25px;
            margin-bottom: 8px;
            font-weight: bold;
        }

        textarea {
            width: 100%;
            min-height: 100px;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: vertical;
        }

        .error {
            background-color: #f8d7da;
            color: #842029;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .place-order {
            margin-top: 25px;
            background-color: #00ad2e;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .place-order:hover {
            background-color: #008f25;
        }

        .back {
            display: inline-block;
            margin-top: 20px;
            color: #333;
            text-decoration: none;
        }

    </style>

</head>

<body>

<div class="container">

    <div class="checkout-box">

        <h1>Checkout</h1>

        <?php if ($error !== ""): ?>

            <div class="error">
                <?php echo htmlspecialchars($error); ?>
            </div>

            <a href="cart.php" class="back">
                Return to Cart
            </a>

        <?php else: ?>

            <div class="shop-box">
                <strong>Shop:</strong>
                <?php echo htmlspecialchars($business_name); ?>

                <p>
                    This order will be placed with this shop only.
                </p>
            </div>

            <h2>Order Summary</h2>

            <?php foreach ($items as $item): ?>

                <div class="order-item">

                    <span>
                        <?php echo htmlspecialchars($item["name"]); ?>
                        ×
                        <?php echo (int) $item["quantity"]; ?>
                    </span>

                    <span>
                        M
                        <?php echo number_format(
                            $item["price"] * $item["quantity"],
                            2
                        ); ?>
                    </span>

                </div>

            <?php endforeach; ?>

            <div class="total">
                Total: M <?php echo number_format($total, 2); ?>
            </div>

            <form method="POST">

                <label for="delivery_address">
                    Delivery Address
                </label>

                <textarea
                    id="delivery_address"
                    name="delivery_address"
                    placeholder="Enter your delivery address"
                    required
                ></textarea>

                <button
                    type="submit"
                    class="place-order"
                >
                    Place Order
                </button>

            </form>

        <?php endif; ?>

    </div>

</div>

</body>
</html>
