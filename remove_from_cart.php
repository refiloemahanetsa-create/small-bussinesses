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

// Check if cart item ID was provided
if (!isset($_GET["id"])) {
    header("Location: cart.php");
    exit();
}

$cart_item_id = intval($_GET["id"]);


// Get the customer's cart
$cart_sql = "SELECT id FROM carts WHERE customer_id = ?";

$cart_stmt = $conn->prepare($cart_sql);
$cart_stmt->bind_param("i", $customer_id);
$cart_stmt->execute();

$cart_result = $cart_stmt->get_result();


// Check if customer has a cart
if ($cart_result->num_rows == 0) {
    header("Location: cart.php");
    exit();
}

$cart = $cart_result->fetch_assoc();

$cart_id = $cart["id"];


// Remove the item only if it belongs to this customer's cart
$delete_sql = "DELETE FROM cart_items
               WHERE id = ?
               AND cart_id = ?";

$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("ii", $cart_item_id, $cart_id);
$delete_stmt->execute();


// Return to cart
header("Location: cart.php");
exit();

?>
```
