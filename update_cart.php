```php
<?php

session_start();

require_once "config/db.php";

// Check if customer is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION["user_id"];


// Check required values
if (!isset($_POST["cart_item_id"]) || !isset($_POST["quantity"])) {
    header("Location: cart.php");
    exit();
}

$cart_item_id = intval($_POST["cart_item_id"]);
$quantity = intval($_POST["quantity"]);


// Quantity cannot be less than 1
if ($quantity < 1) {
    $quantity = 1;
}


// Get customer's cart
$cart_sql = "SELECT id FROM carts WHERE customer_id = ?";

$cart_stmt = $conn->prepare($cart_sql);
$cart_stmt->bind_param("i", $customer_id);
$cart_stmt->execute();

$cart_result = $cart_stmt->get_result();

if ($cart_result->num_rows == 0) {
    header("Location: cart.php");
    exit();
}

$cart = $cart_result->fetch_assoc();

$cart_id = $cart["id"];


// Check stock for this product
$stock_sql = "SELECT p.stock_quantity
              FROM cart_items ci
              INNER JOIN products p
              ON ci.product_id = p.id
              WHERE ci.id = ?
              AND ci.cart_id = ?";

$stock_stmt = $conn->prepare($stock_sql);
$stock_stmt->bind_param("ii", $cart_item_id, $cart_id);
$stock_stmt->execute();

$stock_result = $stock_stmt->get_result();

if ($stock_result->num_rows == 0) {
    header("Location: cart.php");
    exit();
}

$product = $stock_result->fetch_assoc();


// Do not allow quantity greater than stock
if ($quantity > $product["stock_quantity"]) {
    $quantity = $product["stock_quantity"];
}


// Update quantity
$update_sql = "UPDATE cart_items
               SET quantity = ?
               WHERE id = ?
               AND cart_id = ?";

$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("iii", $quantity, $cart_item_id, $cart_id);
$update_stmt->execute();


// Return to cart
header("Location: cart.php");
exit();

?>
```
