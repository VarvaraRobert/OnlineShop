<?php
session_start();
// Dashboard cu lista de produse pentru administrator.
require_once 'DBController.php';
require_once 'helpers.php';

if (!isset($_SESSION['admin_id'])) {
    redirectTo('login_admin.php');
}

$db = new DBController();

try {
    // Afișăm toate produsele împreună cu categoria aferentă
    $products = $db->getDBResult(
        "SELECT p.id, p.name, p.code, p.price, p.category_id, c.name AS category_name
         FROM tbl_product p
         LEFT JOIN tbl_category c ON p.category_id = c.id
         ORDER BY p.id DESC"
    );
} catch (Exception $e) {
    $products = [];
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Admin - produse</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 2rem;
        }
        table { border-collapse: collapse; width: 100%; margin-top: 1rem; }
        th, td { border: 1px solid #ddd; padding: 0.5rem; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Administrare produse</h1>
    <p>
        <a href="admin_add_product.php">Adaugă produs</a> |
        <a href="categoryIndex.php">Vezi magazinul</a> |
        <a href="logout_admin.php">Logout admin</a>
    </p>

    <?php if (!empty($error ?? '')): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if (empty($products)): ?>
        <p>Nu există produse înregistrate.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nume</th>
                    <th>Cod</th>
                    <th>Preț</th>
                    <th>Categorie</th>
                    <th>Acțiuni</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo (int)$product['id']; ?></td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars($product['code']); ?></td>
                        <td><?php echo number_format((float)$product['price'], 2); ?> lei</td>
                        <td><?php echo htmlspecialchars($product['category_name'] ?? 'Nesetat'); ?></td>
                        <td>
                            <a href="admin_edit_product.php?id=<?php echo (int)$product['id']; ?>">Editează</a> |
                            <a href="admin_delete_product.php?id=<?php echo (int)$product['id']; ?>">Șterge</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
