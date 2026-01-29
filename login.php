<?php
session_start();
require_once 'DBController.php';
require_once 'helpers.php';

$error = "";

// dacă venim dintr-un link de tip ?product_id= salvăm intenția pentru după login
if (isset($_GET['product_id'])) {
    $_SESSION['pending_cart_product_id'] = (int)$_GET['product_id'];
    $quantityFromQuery = filter_input(INPUT_GET, 'quantity', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $_SESSION['pending_cart_quantity'] = $quantityFromQuery !== null && $quantityFromQuery !== false ? $quantityFromQuery : 1;
    $_SESSION['redirect_after_login'] = 'cart.php';
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $db = new DBController();

    try {
        $user = $db->getDBResult(
            "SELECT id, password FROM users WHERE username = ?",
            [$username]
        );
    } catch (Exception $e) {
        $user = [];
    }

    if ($user && password_verify($password, $user[0]['password'])) {
        // login reușit -> punem id-ul în sesiune
        $memberId = (int)$user[0]['id'];
        $_SESSION['user_id'] = $memberId;
        $_SESSION['member_id'] = $memberId;

        $redirect = 'home.php';

        if (isset($_SESSION['pending_cart_product_id'])) {
            // dacă exista un produs selectat anterior îl adăugăm în coș acum
            $productId = (int)$_SESSION['pending_cart_product_id'];
            $pendingQuantity = isset($_SESSION['pending_cart_quantity'])
                ? max(1, (int)$_SESSION['pending_cart_quantity'])
                : 1;

            try {
                $db->updateDB(
                    "INSERT INTO tbl_cart (product_id, quantity, id_member) VALUES (?, ?, ?)",
                    [$productId, $pendingQuantity, $memberId]
                );
            } catch (Exception $e) {
            }

            unset($_SESSION['pending_cart_product_id'], $_SESSION['pending_cart_quantity']);
            $redirect = 'cart.php';
        }

        if (isset($_SESSION['redirect_after_login'])) {
            // redirect custom păstrat din addToCart/register
            $redirect = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']);
        }

        redirectTo($redirect);
    } else {
        $error = "Username sau parolă greșite.";
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Login utilizator</title>
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
    <h1>Login utilizator</h1>

    <form method="post">
        <label>
            Username:
            <input type="text" name="username" required>
        </label>
        <label>
            Parolă:
            <input type="password" name="password" required>
        </label>
        <button type="submit">Login</button>
    </form>

    <?php if (!empty($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <p><a href="register.php">Nu ai cont? Înregistrează-te</a></p>
</body>
</html>
