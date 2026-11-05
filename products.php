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

$message = "";
$message_type = "";

/*
|--------------------------------------------------------------------------
| Get the business belonging to the logged-in owner
|--------------------------------------------------------------------------
*/

$business_stmt = $conn->prepare(
    "SELECT id, business_name
     FROM businesses
     WHERE owner_id = ?"
);

$business_stmt->bind_param("i", $owner_id);
$business_stmt->execute();

$business_result = $business_stmt->get_result();
$business = $business_result->fetch_assoc();

$business_stmt->close();


/*
|--------------------------------------------------------------------------
| Add Product
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!$business) {

        $message = "Please register your business first.";
        $message_type = "error";

    } else {

        $business_id = $business["id"];

        $category_id = !empty($_POST["category_id"])
            ? (int) $_POST["category_id"]
            : null;

        $name = trim($_POST["name"]);
        $description = trim($_POST["description"]);
        $price = $_POST["price"];
        $stock_quantity = $_POST["stock_quantity"];

        // Check required fields
        if (
            empty($name) ||
            $price === "" ||
            $stock_quantity === ""
        ) {

            $message = "Please fill in all required fields.";
            $message_type = "error";

        } elseif (!is_numeric($price) || $price < 0) {

            $message = "Please enter a valid price.";
            $message_type = "error";

        } elseif (!is_numeric($stock_quantity) || $stock_quantity < 0) {

            $message = "Please enter a valid stock quantity.";
            $message_type = "error";

        } else {

            /*
            |--------------------------------------------------------------------------
            | Handle product image
            |--------------------------------------------------------------------------
            */

            $image = null;

            if (
                isset($_FILES["image"]) &&
                $_FILES["image"]["error"] == 0
            ) {

                $allowed_extensions = [
                    "jpg",
                    "jpeg",
                    "png",
                    "gif",
                    "webp"
                ];

                $file_name = $_FILES["image"]["name"];
                $file_tmp = $_FILES["image"]["tmp_name"];

                $file_extension = strtolower(
                    pathinfo($file_name, PATHINFO_EXTENSION)
                );

                if (in_array($file_extension, $allowed_extensions)) {

                    $image_folder = "uploads/products/";

                    if (!is_dir($image_folder)) {
                        mkdir($image_folder, 0777, true);
                    }

                    $new_file_name =
                        time() . "_" .
                        preg_replace(
                            "/[^A-Za-z0-9._-]/",
                            "_",
                            $file_name
                        );

                    $image_path =
                        $image_folder . $new_file_name;

                    if (move_uploaded_file(
                        $file_tmp,
                        $image_path
                    )) {

                        $image = $image_path;

                    }

                } else {

                    $message = "Invalid image format. Please use JPG, JPEG, PNG, GIF or WEBP.";
                    $message_type = "error";
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Insert product
            |--------------------------------------------------------------------------
            */

            if (empty($message)) {

                $stmt = $conn->prepare(
                    "INSERT INTO products
                    (
                        business_id,
                        category_id,
                        name,
                        description,
                        price,
                        stock_quantity,
                        image,
                        status
                    )
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'active')"
                );

                $stmt->bind_param(
                    "iissdis",
                    $business_id,
                    $category_id,
                    $name,
                    $description,
                    $price,
                    $stock_quantity,
                    $image
                );

                if ($stmt->execute()) {

                    $message = "Product added successfully!";
                    $message_type = "success";

                } else {

                    $message = "Failed to add product. Please try again.";
                    $message_type = "error";
                }

                $stmt->close();
            }
        }
    }
}


/*
|--------------------------------------------------------------------------
| Get categories
|--------------------------------------------------------------------------
*/

$categories = [];

$category_result = $conn->query(
    "SELECT id, name
     FROM categories
     ORDER BY name ASC"
);

if ($category_result) {

    while ($row = $category_result->fetch_assoc()) {

        $categories[] = $row;

    }
}


/*
|--------------------------------------------------------------------------
| Get products belonging to this business
|--------------------------------------------------------------------------
*/

$products = [];

if ($business) {

    $product_stmt = $conn->prepare(
        "SELECT
            p.id,
            p.name,
            p.description,
            p.price,
            p.stock_quantity,
            p.image,
            p.status,
            c.name AS category_name
         FROM products p
         LEFT JOIN categories c
            ON p.category_id = c.id
         WHERE p.business_id = ?
         ORDER BY p.created_at DESC"
    );

    $product_stmt->bind_param(
        "i",
        $business["id"]
    );

    $product_stmt->execute();

    $product_result = $product_stmt->get_result();

    while ($row = $product_result->fetch_assoc()) {

        $products[] = $row;

    }

    $product_stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Products - Small Business Shop</title>

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
            width: 90%;
            max-width: 1100px;
            margin: 40px auto;
        }

        .card {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        h1 {
            margin-bottom: 10px;
        }

        h2 {
            margin-bottom: 20px;
        }

        .subtitle {
            color: #666;
            margin-bottom: 25px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 15px;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        button {
            background-color: #00ad2e;
            color: white;
            border: none;
            padding: 13px 25px;
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
        }

        .success {
            background-color: #e5f8ea;
            color: #087a25;
        }

        .error {
            background-color: #ffe5e5;
            color: #b00000;
        }

        .business-name {
            color: #00ad2e;
            font-weight: bold;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .product {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            background-color: white;
        }

        .product img {
            width: 20%;
            height: 100px;
            object-fit: cover;
        }

        .no-image {
            height: 180px;
            background-color: #eee;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #777;
        }

        .product-content {
            padding: 18px;
        }

        .product-content h3 {
            margin-bottom: 8px;
        }

        .category {
            color: #00ad2e;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .price {
            font-size: 20px;
            font-weight: bold;
            margin: 10px 0;
        }

        .stock {
            color: #555;
            margin-bottom: 8px;
        }

        .status {
            font-size: 14px;
            font-weight: bold;
        }

        .active {
            color: #087a25;
        }

        .inactive {
            color: #b00000;
        }

        .back {
            display: inline-block;
            margin-top: 10px;
            color: #00ad2e;
            text-decoration: none;
            font-weight: bold;
        }

    </style>

</head>

<body>


<nav>

    <h2>Small Business Shop</h2>

    <a href="business_owner_dashboard.php">
        Dashboard
    </a>

</nav>


<div class="container">


    <div class="card">

        <h1>Product Management</h1>

        <?php if ($business): ?>

            <p class="subtitle">
                Managing products for:
                <span class="business-name">
                    <?php echo htmlspecialchars($business["business_name"]); ?>
                </span>
            </p>

        <?php else: ?>

            <p class="subtitle">
                You need to register your business before adding products.
            </p>

            <a class="back" href="business.php">
                Register Your Business
            </a>

        <?php endif; ?>


        <?php if (!empty($message)): ?>

            <div class="message <?php echo $message_type; ?>">

                <?php echo htmlspecialchars($message); ?>

            </div>

        <?php endif; ?>


        <?php if ($business): ?>

            <h2>Add New Product</h2>

            <form
                method="POST"
                action=""
                enctype="multipart/form-data"
            >

                <label for="name">
                    Product Name *
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Enter product name"
                    required
                >


                <label for="category_id">
                    Category
                </label>

                <select
                    id="category_id"
                    name="category_id"
                >

                    <option value="">
                        Select a category
                    </option>

                    <?php foreach ($categories as $category): ?>

                        <option
                            value="<?php echo $category["id"]; ?>"
                        >
                            <?php echo htmlspecialchars($category["name"]); ?>
                        </option>

                    <?php endforeach; ?>

                </select>


                <label for="description">
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    placeholder="Describe your product"
                ></textarea>


                <label for="price">
                    Price *
                </label>

                <input
                    type="number"
                    id="price"
                    name="price"
                    step="0.01"
                    min="0"
                    placeholder="Enter product price"
                    required
                >


                <label for="stock_quantity">
                    Stock Quantity *
                </label>

                <input
                    type="number"
                    id="stock_quantity"
                    name="stock_quantity"
                    min="0"
                    placeholder="Enter available quantity"
                    required
                >


                <label for="image">
                    Product Image
                </label>

                <input
                    type="file"
                    id="image"
                    name="image"
                    accept=".jpg,.jpeg,.png,.gif,.webp"
                >


                <button type="submit">
                    Add Product
                </button>

            </form>

        <?php endif; ?>

    </div>


    <?php if ($business): ?>

        <div class="card">

            <h2>My Products</h2>

            <?php if (count($products) == 0): ?>

                <p>
                    You have not added any products yet.
                </p>

            <?php else: ?>

                <div class="product-grid">

                    <?php foreach ($products as $product): ?>

                        <div class="product">

                            <?php if (!empty($product["image"])): ?>

                                <img
                                    src="<?php echo htmlspecialchars($product["image"]); ?>"
                                    alt="<?php echo htmlspecialchars($product["name"]); ?>"
                                >

                            <?php else: ?>

                                <div class="no-image">
                                    No Image
                                </div>

                            <?php endif; ?>


                            <div class="product-content">

                                <h3>
                                    <?php echo htmlspecialchars($product["name"]); ?>
                                </h3>


                                <?php if (!empty($product["category_name"])): ?>

                                    <div class="category">

                                        <?php echo htmlspecialchars($product["category_name"]); ?>

                                    </div>

                                <?php endif; ?>


                                <p>
                                    <?php
                                    echo !empty($product["description"])
                                        ? htmlspecialchars($product["description"])
                                        : "No description available.";
                                    ?>
                                </p>


                                <div class="price">

                                    M
                                    <?php
                                    echo number_format(
                                        $product["price"],
                                        2
                                    );
                                    ?>

                                </div>


                                <div class="stock">

                                    Stock:
                                    <?php echo $product["stock_quantity"]; ?>

                                </div>


                                <div class="status
                                    <?php echo $product["status"] == "active"
                                        ? "active"
                                        : "inactive";
                                    ?>"
                                >

                                    Status:
                                    <?php echo htmlspecialchars($product["status"]); ?>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>

    <?php endif; ?>


    <a class="back" href="business_owner_dashboard.php">
        ← Back to Business Dashboard
    </a>


</div>

</body>

</html>