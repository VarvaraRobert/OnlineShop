<?php
session_start();
require_once 'DBController.php';

$db = new DBController();

try {
    // toate categoriile pe care le poate alege utilizatorul
    $categories = $db->getDBResult("SELECT id, name, description FROM tbl_category ORDER BY name");
} catch (Exception $e) {
    die("Nu pot încărca categoriile: " . htmlspecialchars($e->getMessage()));
}

$pageTitle = "Categorii produse";
?>
<!DOCTYPE html>
<html lang="ro">

<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
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
    </style>
</head>

<body>
    <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
    <p>Alege o categorie pentru a fi redirecționat către lista de produse aferentă.</p>

    <?php if (empty($categories)): ?>
        <p>Momentan nu există categorii definite.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($categories as $category): ?>
                <li>
                    <a href="index.php?category_id=<?php echo (int)$category['id']; ?>">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </a>
                    <?php if (!empty($category['description'])): ?>
                        - <?php echo htmlspecialchars($category['description']); ?>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <p><a href="index.php">Vezi toate produsele</a></p>
    <p><a href="cart.php">Cos de cumparaturi</a></p>
    <p><a href="contact.php">Contact</a></p>

    <?php if (isset($_SESSION['member_id'])): ?>
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="login.php">Login</a>
    <?php endif; ?>
</body>

</html>