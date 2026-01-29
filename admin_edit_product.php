<?php
session_start();
// Pagină pentru administratori: editează datele unui produs existent.
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
$error = '';

try {
    // Se încarcă toate categoriile pentru dropdown-ul formularului
    $categories = $db->getDBResult("SELECT id, name FROM tbl_category ORDER BY name");
} catch (Exception $e) {
    $categories = [];
}

try {
    // Citim produsul vizat pentru a precompleta formularul și pentru validări
    $productRows = $db->getDBResult(
        "SELECT id, name, code, image, price, category_id, descriere FROM tbl_product WHERE id = ?",
        [$productId]
    );
} catch (Exception $e) {
    $productRows = [];
}

if (!$productRows) {
    redirectTo('admin_home.php');
}

$product = $productRows[0];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POST -> validăm câmpurile și actualizăm produsul în baza de date
    $name = trim($_POST['name'] ?? '');
    $code = trim($_POST['code'] ?? '');
    $image = trim($_POST['image'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $descriere = trim($_POST['descriere'] ?? '');
    $categoryId = $_POST['category_id'] ?? '';

    if ($name === '' || $code === '' || $price === '') {
        $error = 'Numele, codul și prețul sunt obligatorii.';
    } else {
        $priceValue = (float)$price;
        $categoryValue = $categoryId === '' ? null : (int)$categoryId;

        try {
            $db->updateDB(
                "UPDATE tbl_product
                 SET name = ?, code = ?, image = ?, price = ?, category_id = ?, descriere = ?
                 WHERE id = ?",
                [$name, $code, $image, $priceValue, $categoryValue, $descriere, $productId]
            );
            redirectTo('admin_home.php');
        } catch (Exception $e) {
            $error = 'Nu am putut actualiza produsul: ' . htmlspecialchars($e->getMessage());
        }
    }
} else {
    $name = $product['name'];
    $code = $product['code'];
    $image = $product['image'];
    $price = $product['price'];
    $descriere = $product['descriere'];
    $categoryId = $product['category_id'];
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Editează produs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 2rem;
        }
        form {
            max-width: 480px;
        }
        label {
            display: block;
            margin-bottom: 0.6rem;
        }
        input[type="text"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 0.4rem;
        }
        textarea {
            resize: vertical;
        }
        button {
            margin-top: 0.8rem;
            padding: 0.6rem 1.2rem;
        }
        .error {
            color: #d9534f;
        }
    </style>
</head>
<body>
    <h1>Editează produs</h1>
    <p><a href="admin_home.php">Înapoi la lista de produse</a></p>

    <?php if ($error): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="post">
        <label>
            Nume:
            <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
        </label><br>
        <label>
            Cod:
            <input type="text" name="code" value="<?php echo htmlspecialchars($code); ?>" required>
        </label><br>
        <label>
            Imagine (nume fișier):
            <input type="text" name="image" value="<?php echo htmlspecialchars($image); ?>">
        </label><br>
        <label>
            Preț:
            <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($price); ?>" required>
        </label><br>
        <label>
            Categorie:
            <select name="category_id">
                <option value="">-- fără categorie --</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo (int)$category['id']; ?>"
                        <?php echo $categoryId && (int)$categoryId === (int)$category['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <label>
            Descriere:
            <textarea name="descriere" rows="4" cols="40"><?php echo htmlspecialchars($descriere); ?></textarea>
        </label><br>
        <button type="submit">Salvează modificările</button>
    </form>
</body>
</html>
