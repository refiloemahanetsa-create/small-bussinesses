
<?php

session_start();
require_once "config/db.php";

// Check customer login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$customer_id = (int) $_SESSION["user_id"];

// Get message from add_to_cart.php
$message = $_SESSION["cart_message"] ?? "";
unset($_SESSION["cart_message"]);

// Find customer's cart
$cart_sql = "SELECT id FROM carts WHERE customer_id = ?";
$cart_stmt = $conn->prepare($cart_sql);
$cart_stmt->bind_param("i", $customer_id);
$cart_stmt->execute();

$cart_result = $cart_stmt->get_result();

$cart_id = null;
$items = [];
$shop_name = "";

if ($cart_result->num_rows > 0) {

    $cart = $cart_result->fetch_assoc();
    $cart_id = (int) $cart["id"];

    // Get cart products and their business information
    $items_sql = "SELECT
                    ci.id AS cart_item_id,
                    ci.quantity,
                    p.id AS product_id,
                    p.name,
                    p.description,
                    p.price,
                    p.image,
                    p.stock_quantity,
                    p.business_id,
                    b.business_name
                  FROM cart_items ci
                  INNER JOIN products p
                    ON ci.product_id = p.id
                  INNER JOIN businesses b
                    ON p.business_id = b.id
                  WHERE ci.cart_id = ?
                  ORDER BY ci.id DESC";

    $items_stmt = $conn->prepare($items_sql);
    $items_stmt->bind_param("i", $cart_id);
    $items_stmt->execute();

    $items_result = $items_stmt->get_result();

    while ($row = $items_result->fetch_assoc()) {
        $items[] = $row;
    }

    // Get shop name from the first cart item
    if (!empty($items)) {
        $shop_name = $items[0]["business_name"];
    }
}

$total = 0;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Shopping Cart - Small Business Shop</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 1100px;
            margin: 40px auto;
        }

        h1 {
            margin-bottom: 25px;
        }

        .cart {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
        }

        .message {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .shop-heading {
            background: #e8f5e9;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .cart-item {
            display: flex;
            align-items: center;
            gap: 20px;
            border-bottom: 1px solid #ddd;
            padding: 20px 0;
        }

        .cart-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 6px;
        }

        .item-details {
            flex: 1;
        }

        .item-details h3 {
            margin: 0 0 8px 0;
        }

        .item-details p {
            margin: 5px 0;
            color: #555;
        }

        .price,
        .subtotal {
            font-weight: bold;
        }

        .remove-button {
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 5px;
        }

        .remove-button:hover {
            background-color: #c82333;
        }

        .total-section {
            text-align: right;
            margin-top: 25px;
        }

        .continue-button {
            display: inline-block;
            background-color: #00ad2e;
            color: white;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 5px;
            margin-right: 10px;
        }

        .checkout-button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 5px;
        }

        .empty-cart {
            text-align: center;
            padding: 50px 20px;
        }

        @media (max-width: 650px) {
            .cart-item {
                flex-wrap: wrap;
            }

            .total-section {
                text-align: center;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <h1>My Shopping Cart</h1>

    <?php if ($message !== ""): ?>
        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="cart">

        <?php if (empty($items)): ?>

            <div class="empty-cart">
                <h2>Your cart is empty</h2>

                <p>You have not added any products to your cart yet.</p>

                <a class="continue-button" href="shop.php">
                    Browse Products
                </a>
            </div>

        <?php else: ?>

            <div class="shop-heading">
                <strong>Shopping from:</strong>
                <?php echo htmlspecialchars($shop_name); ?>

                <p>
                    Your order will be placed with this shop only.
                </p>
            </div>

            <?php foreach ($items as $item): ?>

                <?php
                $subtotal = $item["price"] * $item["quantity"];
                $total += $subtotal;
                ?>

                <div class="cart-item">

                    <?php if (!empty($item["image"])): ?>

                        <img
                            src="uploads/products/<?php echo htmlspecialchars($item["image"]); ?>"
                            alt="<?php echo htmlspecialchars($item["name"]); ?>"
                        >

                    <?php else: ?>

                        <div style="
                            width:100px;
                            height:100px;
                            background:#ddd;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                        ">
                            No Image
                        </div>

                    <?php endif; ?>

                    <div class="item-details">

                        <h3>
                            <?php echo htmlspecialchars($item["name"]); ?>
                        </h3>

                        <p>
                            Price:
                            M <?php echo number_format($item["price"], 2); ?>
                        </p>

                        <form action="update_cart.php" method="POST"
                              style="margin-top:10px;">

                            <input
                                type="hidden"
                                name="cart_item_id"
                                value="<?php echo (int) $item["cart_item_id"]; ?>"
                            >

                            <button
                                type="button"
                                onclick="changeQuantity(this, -1)"
                            >
                                −
                            </button>

                            <input
                                type="number"
                                name="quantity"
                                value="<?php echo (int) $item["quantity"]; ?>"
                                min="1"
                                max="<?php echo (int) $item["stock_quantity"]; ?>"
                                style="width:50px;text-align:center;"
                                required
                            >

                            <button
                                type="button"
                                onclick="changeQuantity(this, 1)"
                            >
                                +
                            </button>

                            <button type="submit">
                                Update
                            </button>

                        </form>

                    </div>

                    <div class="subtotal">
                        M <?php echo number_format($subtotal, 2); ?>
                    </div>

                    <a
                        class="remove-button"
                        href="remove_from_cart.php?id=<?php echo (int) $item["cart_item_id"]; ?>"
                    >
                        Remove
                    </a>

                </div>

            <?php endforeach; ?>

            <div class="total-section">

                <h2>
                    Total: M <?php echo number_format($total, 2); ?>
                </h2>

                <a class="continue-button" href="shop.php">
                    Continue Shopping
                </a>

                <a class="checkout-button" href="checkout.php">
                    Checkout
                </a>

            </div>

        <?php endif; ?>

    </div>

</div>

<script>
function changeQuantity(button, amount) {

    const form = button.closest("form");
    const input = form.querySelector('input[name="quantity"]');

    let quantity = parseInt(input.value) || 1;
    let max = parseInt(input.max) || 1;

    quantity += amount;

    if (quantity < 1) {
        quantity = 1;
    }

    if (quantity > max) {
        quantity = max;
    }

    input.value = quantity;
}
</script>

</body>
</html>
