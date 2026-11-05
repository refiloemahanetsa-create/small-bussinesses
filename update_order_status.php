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


// Check if required data was sent
if (!isset($_POST["order_id"]) || !isset($_POST["status"])) {
    header("Location: business_orders.php");
    exit();
}

$order_id = intval($_POST["order_id"]);
$new_status = $_POST["status"];


// Only allow these statuses
$allowed_statuses = [
    "pending",
    "confirmed",
    "processing",
    "completed"
];

if (!in_array($new_status, $allowed_statuses)) {
    header("Location: business_orders.php");
    exit();
}


// Get the business owned by the logged-in user
$business_sql = "SELECT id
                 FROM businesses
                 WHERE owner_id = ?";

$business_stmt = $conn->prepare($business_sql);
$business_stmt->bind_param("i", $owner_id);
$business_stmt->execute();

$business_result = $business_stmt->get_result();

if ($business_result->num_rows == 0) {
    header("Location: business_owner_dashboard.php");
    exit();
}

$business = $business_result->fetch_assoc();

$business_id = $business["id"];


// Check that this order contains a product belonging to this business
$check_sql = "SELECT o.id
              FROM orders o
              INNER JOIN order_items oi
                  ON o.id = oi.order_id
              INNER JOIN products p
                  ON oi.product_id = p.id
              WHERE o.id = ?
              AND p.business_id = ?
              LIMIT 1";

$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $order_id, $business_id);
$check_stmt->execute();

$check_result = $check_stmt->get_result();

if ($check_result->num_rows == 0) {
    echo "<h2>You are not allowed to update this order.</h2>";
    echo '<a href="business_orders.php">Back to Orders</a>';
    exit();
}


// Get the current order status
$current_sql = "SELECT status
                FROM orders
                WHERE id = ?";

$current_stmt = $conn->prepare($current_sql);
$current_stmt->bind_param("i", $order_id);
$current_stmt->execute();

$current_result = $current_stmt->get_result();

if ($current_result->num_rows == 0) {
    header("Location: business_orders.php");
    exit();
}

$current_order = $current_result->fetch_assoc();

$current_status = $current_order["status"];


// Define the correct order of statuses
$status_order = [
    "pending" => 1,
    "confirmed" => 2,
    "processing" => 3,
    "completed" => 4
];


// Prevent moving backwards
if ($status_order[$new_status] < $status_order[$current_status]) {

    echo "<h2>Invalid Status Change</h2>";

    echo "<p>
            An order cannot move backwards from
            <strong>"
            . ucfirst($current_status)
            . "</strong>
            to
            <strong>"
            . ucfirst($new_status)
            . "</strong>.
          </p>";

    echo '<a href="business_order_details.php?order_id='
         . $order_id
         . '">Back to Order</a>';

    exit();
}


// Update the order status
$update_sql = "UPDATE orders
               SET status = ?
               WHERE id = ?";

$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("si", $new_status, $order_id);
$update_stmt->execute();


// Send the business owner back to the order
header(
    "Location: business_order_details.php?order_id="
    . $order_id
);

exit();

?>