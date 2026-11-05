<?php

session_start();

require_once "config/db.php";

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Only business owners can access this page
if ($_SESSION["user_role"] !== "business_owner") {
    header("Location: dashboard.php");
    exit();
}

// Get search keyword
$search = trim($_GET["search"] ?? "");

// Split the search into separate keywords
$keywords = preg_split(
    '/[\s,.;:!?]+/u',
    $search,
    -1,
    PREG_SPLIT_NO_EMPTY
);

// Remove duplicate keywords
$keywords = array_values(array_unique($keywords));

// Build the database search
if (!empty($keywords)) {

    $conditions = [];
    $types = "";
    $params = [];

    foreach ($keywords as $keyword) {

        $conditions[] = "(
            title LIKE ?
            OR description LIKE ?
            OR topic LIKE ?
        )";

        $search_term = "%" . $keyword . "%";

        $types .= "sss";

        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
    }

    // Each keyword must match at least one searchable field
    $sql = "SELECT
                id,
                title,
                description,
                topic,
                resource_type,
                source_name,
                url
            FROM educational_resources
            WHERE status = 'active'
            AND " . implode(" AND ", $conditions) . "
            ORDER BY created_at DESC";

    $stmt = $conn->prepare($sql);

    // Bind all search parameters safely
    $stmt->bind_param($types, ...$params);

} else {

    // Show all active resources when no search is entered
    $sql = "SELECT
                id,
                title,
                description,
                topic,
                resource_type,
                source_name,
                url
            FROM educational_resources
            WHERE status = 'active'
            ORDER BY created_at DESC";

    $stmt = $conn->prepare($sql);
}

$stmt->execute();

$result = $stmt->get_result();

// Create an internet search URL for the entered topic
$internetSearchUrl = "https://www.google.com/search?q="
    . rawurlencode($search . " small business technology");

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Business Learning Centre</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            width: 90%;
            max-width: 1100px;
            margin: 30px auto;
        }

        h1 {
            color: #222;
        }

        .intro,
        .search-box,
        .resource,
        .no-results,
        .internet-box {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .intro {
            line-height: 1.6;
        }

        .search-box form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .search-box input {
            flex: 1;
            min-width: 200px;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 15px;
        }

        .search-button,
        .internet-button {
            display: inline-block;
            padding: 12px 20px;
            background-color: #00ad2e;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 15px;
        }

        .search-button:hover,
        .internet-button:hover {
            background-color: #008f26;
        }

        .clear-button {
            display: inline-block;
            padding: 12px 20px;
            background-color: #777;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .resource {
            box-shadow: 0 2px 5px rgba(0,0,0,0.08);
            overflow-wrap: anywhere;
        }

        .resource h2 {
            margin-top: 0;
            color: #00ad2e;
        }

        .topic,
        .type {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 13px;
            margin-right: 8px;
            margin-bottom: 8px;
        }

        .topic {
            background-color: #e8f8ed;
            color: #008f26;
        }

        .type {
            background-color: #eee;
            color: #555;
        }

        .view-button {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 18px;
            background-color: #00ad2e;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .view-button:hover {
            background-color: #008f26;
        }

        .back-button {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #008f26;
        }

        .internet-box {
            border-left: 5px solid #00ad2e;
            line-height: 1.6;
        }

        .internet-button {
            margin-top: 5px;
        }

        .search-heading {
            overflow-wrap: anywhere;
        }

        @media (max-width: 600px) {
            .container {
                width: 94%;
                margin: 20px auto;
            }

            .search-box form {
                flex-direction: column;
            }

            .search-box input,
            .search-button,
            .clear-button {
                width: 100%;
            }

            .clear-button {
                text-align: center;
            }
        }

    </style>

</head>

<body>

<div class="container">

    <a href="business_owner_dashboard.php" class="back-button">
        &larr; Back to Dashboard
    </a>

    <h1>Business Learning Centre</h1>

    <div class="intro">

        <p>
            Learn practical business and technology skills to help
            you improve, manage, and grow your business.
        </p>

        <p>
            Search your saved learning resources or look for
            additional information on the internet.
        </p>

    </div>

    <!-- Educational search form -->

    <div class="search-box">

        <form method="GET" action="educational_resources.php">

            <input
                type="search"
                name="search"
                placeholder="Try marketing, cybersecurity, stock management..."
                value="<?php echo htmlspecialchars(
                    $search,
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>"
                aria-label="Search educational resources"
            >

            <button type="submit" class="search-button">
                Search Resources
            </button>

            <?php if ($search !== ""): ?>

                <a
                    href="educational_resources.php"
                    class="clear-button"
                >
                    Clear Search
                </a>

            <?php endif; ?>

        </form>

    </div>

    <?php if ($search !== ""): ?>

        <h3 class="search-heading">
            Saved resources matching:
            "<?php echo htmlspecialchars(
                $search,
                ENT_QUOTES,
                'UTF-8'
            ); ?>"
        </h3>

    <?php else: ?>

        <h3>Available Learning Resources</h3>

    <?php endif; ?>

    <!-- Display database results -->

    <?php if ($result->num_rows > 0): ?>

        <?php while ($resource = $result->fetch_assoc()): ?>

            <div class="resource">

                <h2>
                    <?php echo htmlspecialchars(
                        $resource["title"],
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>
                </h2>

                <span class="topic">
                    <?php echo htmlspecialchars(
                        $resource["topic"],
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>
                </span>

                <span class="type">
                    <?php echo htmlspecialchars(
                        $resource["resource_type"],
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>
                </span>

                <p>
                    <?php echo nl2br(htmlspecialchars(
                        $resource["description"] ?? "",
                        ENT_QUOTES,
                        'UTF-8'
                    )); ?>
                </p>

                <?php if (!empty($resource["source_name"])): ?>

                    <p>
                        <strong>Source:</strong>
                        <?php echo htmlspecialchars(
                            $resource["source_name"],
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>
                    </p>

                <?php endif; ?>

                <a
                    href="<?php echo htmlspecialchars(
                        $resource["url"],
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="view-button"
                >
                    View Resource
                </a>

            </div>

        <?php endwhile; ?>

    <?php else: ?>

        <div class="no-results">

            <h3>No matching saved resources found</h3>

            <p>
                Your database does not currently have an active
                resource matching all the search keywords.
                Try fewer or different keywords, or search online below.
            </p>

        </div>

    <?php endif; ?>

    <!-- Internet search option -->

    <?php if ($search !== ""): ?>

        <div class="internet-box">

            <h3>Explore More on the Internet</h3>

            <p>
                Find more information, examples, and guides about
                "<?php echo htmlspecialchars(
                    $search,
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>" from websites across the internet.
            </p>

            <a
                href="<?php echo htmlspecialchars(
                    $internetSearchUrl,
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>"
                target="_blank"
                rel="noopener noreferrer"
                class="internet-button"
            >
                Search the Internet
            </a>

        </div>

    <?php endif; ?>

</div>

</body>

</html>

<?php
$stmt->close();
$conn->close();
?>
