<?php
session_start();
// Confirmare și ștergere pentru produse din zona de admin.
require_once 'DBController.php';
require_once 'helpers.php';

if (!isset($_SESSION['admin_id'])) {
    redirectTo('login_admin.php');
}

$productId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$productId) {
    redirectTo('admin_home.php');
}

$db = new DBController();

try {
    // Luăm produsul pentru a arăta numele în confirmare
    $rows = $db->getDBResult(
        "SELECT id, name FROM tbl_product WHERE id = ?",
        [$productId]
    );
} catch (Exception $e) {
    $rows = [];
}

if (!$rows) {
    redirectTo('admin_home.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Butonul confirmă ștergerea și elimină produsul din baza de date
    try {
        $db->updateDB("DELETE FROM tbl_product WHERE id = ?", [$productId]);
    } catch (Exception $e) {
        redirectTo('admin_home.php');
    }
    redirectTo('admin_home.php');
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Șterge produs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 2rem;
        }
        form {
            margin-top: 1rem;
        }
        button {
            padding: 0.5rem 1rem;
        }
        a {
            margin-left: 1rem;
        }
    </style>
</head>
<body>
    <h1>Șterge produs</h1>
    <p>Sigur dorești să ștergi produsul <strong><?php echo htmlspecialchars($rows[0]['name']); ?></strong>?</p>
    <form method="post">
        <button type="submit">Da, șterge</button>
        <a href="admin_home.php">Anulează</a>
    </form>
</body>
</html>
