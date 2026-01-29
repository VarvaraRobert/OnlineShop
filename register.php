<?php
session_start();
require_once 'DBController.php';
require_once 'helpers.php';

$error = "";

// permitem accesul direct cu ?product_id= pentru a seta produsul dorit înainte de cont
if (isset($_GET['product_id'])) {
    $_SESSION['pending_cart_product_id'] = (int)$_GET['product_id'];
    $quantityFromQuery = filter_input(INPUT_GET, 'quantity', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $_SESSION['pending_cart_quantity'] = $quantityFromQuery !== null && $quantityFromQuery !== false ? $quantityFromQuery : 1;
    $_SESSION['redirect_after_login'] = 'cart.php';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $db = new DBController();
    $query = "INSERT INTO users (username, password) VALUES (?, ?)";

    try {
        $db->updateDB($query, [$username, $password]);
        $member_id = (int)$db->getConnection()->lastInsertId();

        // după înregistrare autentificăm automat utilizatorul
        $_SESSION['user_id'] = $member_id;
        $_SESSION['member_id'] = $member_id;

        $redirect = 'home.php';

        if (isset($_SESSION['pending_cart_product_id'])) {
            // produsul selectat anterior este adăugat imediat în coș
            $product_id = (int)$_SESSION['pending_cart_product_id'];
            $pendingQuantity = isset($_SESSION['pending_cart_quantity'])
                ? max(1, (int)$_SESSION['pending_cart_quantity'])
                : 1;

            try {
                $db->updateDB(
                    "INSERT INTO tbl_cart (product_id, quantity, id_member) VALUES (?, ?, ?)",
                    [$product_id, $pendingQuantity, $member_id]
                );
            } catch (Exception $e) {
            }

            unset($_SESSION['pending_cart_product_id'], $_SESSION['pending_cart_quantity']);
            $redirect = 'cart.php';
        }

        if (isset($_SESSION['redirect_after_login'])) {
            $redirect = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']);
        }

        redirectTo($redirect);
    } catch (Exception $e) {
        $error = "Eroare: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Înregistrare utilizator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 2rem;
        }
        form {
            max-width: 320px;
        }
        label {
            display: block;
            margin-bottom: 0.6rem;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 0.4rem;
        }
        button {
            padding: 0.5rem 1rem;
            margin-top: 0.5rem;
        }
        .error {
            color: #d9534f;
        }
    </style>
</head>
<body>
    <h1>Înregistrare</h1>
    <form method="post">
        <label>
            Username:
            <input type="text" name="username" required>
        </label>
        <label>
            Parolă:
            <input type="password" name="password" required>
        </label>
        <button type="submit">Register</button>
    </form>

    <?php if (!empty($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <p><a href="login.php">Ai deja cont? Login</a></p>
</body>
</html>
