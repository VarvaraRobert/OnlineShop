<?php
session_start();
require_once 'DBController.php';

$db = new DBController();

$categoryId = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);
$categoryName = null;

if ($categoryId) {
    $categoryRows = $db->getDBResult(
        "SELECT name FROM tbl_category WHERE id = ?",
        [$categoryId]
    );

    if (!empty($categoryRows)) {
        $categoryName = $categoryRows[0]['name'];
    }
}

$query = "SELECT * FROM tbl_product";
$params = [];

if ($categoryId) {
    $query .= " WHERE category_id = ?";
    $params[] = $categoryId;
}

$query .= " ORDER BY name";
$products = $db->getDBResult($query, $params);

$heading = $categoryName
    ? "Produse din categoria: " . htmlspecialchars($categoryName)
    : "Produse disponibile";
$isLoggedIn = isset($_SESSION['member_id']);
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Listă produse</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 2rem;
        }
        ul {
            padding-left: 1.2rem;
        }
        li {
            margin-bottom: 0.4rem;
        }
        .notice {
            color: #d9534f;
        }
        .links p {
            margin: 0.3rem 0;
        }
    </style>
</head>
<body>
    <h1><?php echo $heading; ?></h1>

    <?php if ($categoryId && !$categoryName): ?>
        <p class="notice">Categorie inexistentă sau ștearsă.</p>
    <?php endif; ?>

    <?php if (empty($products)): ?>
        <p>Nu există produse pentru această selecție.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($products as $product): ?>
                <?php $link = "addToCart.php?product_id=" . (int)$product['id']; ?>
                <li>
                    <?php echo htmlspecialchars($product['name']); ?>
                    - <?php echo number_format((float)$product['price'], 2); ?> lei
                    - <a href="<?php echo $link; ?>">Adaugă în coș</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <div class="links">
        <p><a href="categoryIndex.php">Înapoi la categorii</a></p>
        <?php if (!$isLoggedIn): ?>
            <p><a href="login.php">Autentifică-te pentru a folosi coșul</a></p>
        <?php else: ?>
            <p><a href="cart.php">Vezi coșul</a></p>
            <p><a href="logout.php">Logout</a></p>
        <?php endif; ?>
    </div>
</body>
</html>
