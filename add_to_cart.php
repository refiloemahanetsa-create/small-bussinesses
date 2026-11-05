
<?php

session_start();

require_once "config/db.php";

// Check if customer is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$customer_id = (int) $_SESSION["user_id"];

// Helper for displaying a message after redirect
function cartMessage($message) {
    $_SESSION["cart_message"] = $message;
    header("Location: cart.php");
    exit();
}

// Check if product ID was provided
if (!isset($_GET["product_id"])) {
    header("Location: shop.php");
    exit();
}

$product_id = (int) $_GET["product_id"];

if ($product_id <= 0) {
    cartMessage("Invalid product selected.");
}

// Get product and the business that owns it
$product_sql = "SELECT id, name, price, stock_quantity, business_id
                FROM products
                WHERE id = ?
                AND status = 'active'";

$product_stmt = $conn->prepare($product_sql);
$product_stmt->bind_param("i", $product_id);
$product_stmt->execute();

$product_result = $product_stmt->get_result();

if ($product_result->num_rows === 0) {
    cartMessage("Product not found or is not available.");
}

$product = $product_result->fetch_assoc();

$business_id = (int) $product["business_id"];

// Check stock
if ((int) $product["stock_quantity"] < 1) {
    cartMessage("Sorry, this product is out of stock.");
}

// Find or create customer's cart
$cart_sql = "SELECT id FROM carts WHERE customer_id = ?";

$cart_stmt = $conn->prepare($cart_sql);
$cart_stmt->bind_param("i", $customer_id);
$cart_stmt->execute();

$cart_result = $cart_stmt->get_result();

if ($cart_result->num_rows === 0) {

    $create_cart_sql = "INSERT INTO carts (customer_id) VALUES (?)";

    $create_cart_stmt = $conn->prepare($create_cart_sql);
    $create_cart_stmt->bind_param("i", $customer_id);
    $create_cart_stmt->execute();

    $cart_id = $conn->insert_id;

} else {

    $cart = $cart_result->fetch_assoc();
    $cart_id = (int) $cart["id"];
}

// Check whether the cart already contains products
// from another business
$existing_sql = "SELECT DISTINCT p.business_id
                 FROM cart_items ci
                 INNER JOIN products p
                    ON ci.product_id = p.id
                 WHERE ci.cart_id = ?";

$existing_stmt = $conn->prepare($existing_sql);
$existing_stmt->bind_param("i", $cart_id);
$existing_stmt->execute();

$existing_result = $existing_stmt->get_result();

while ($existing = $existing_result->fetch_assoc()) {

    if ((int) $existing["business_id"] !== $business_id) {

        cartMessage(
            "Your cart already contains products from another shop. "
            . "Please complete that order before shopping from a different shop."
        );
    }
}

// Check if product is already in the cart
$item_sql = "SELECT id, quantity
             FROM cart_items
             WHERE cart_id = ?
             AND product_id = ?";

$item_stmt = $conn->prepare($item_sql);
$item_stmt->bind_param("ii", $cart_id, $product_id);
$item_stmt->execute();

$item_result = $item_stmt->get_result();

if ($item_result->num_rows > 0) {

    $item = $item_result->fetch_assoc();

    $new_quantity = (int) $item["quantity"] + 1;

    // Check available stock
    if ($new_quantity > (int) $product["stock_quantity"]) {
        cartMessage(
            "You cannot add more of this product. There is not enough stock."
        );
    }

    $update_sql = "UPDATE cart_items
                   SET quantity = ?
                   WHERE id = ?";

    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ii", $new_quantity, $item["id"]);
    $update_stmt->execute();

} else {

    // Add product to cart
    $insert_sql = "INSERT INTO cart_items
                   (cart_id, product_id, quantity)
                   VALUES (?, ?, 1)";

    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("ii", $cart_id, $product_id);
    $insert_stmt->execute();
}

// Return to cart
cartMessage("Product added to your cart successfully.");

?>
