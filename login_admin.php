<?php
session_start();
require_once 'DBController.php';
require_once 'helpers.php';

if (isset($_SESSION['admin_id'])) {
    redirectTo('admin_home.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $db = new DBController();

    try {
        $rows = $db->getDBResult(
            "SELECT id, password FROM admin_users WHERE username = ? LIMIT 1",
            [$username]
        );
    } catch (Exception $e) {
        $rows = [];
    }

    if ($rows && password_verify($password, $rows[0]['password'])) {
        $_SESSION['admin_id'] = (int)$rows[0]['id'];
        redirectTo('admin_home.php');
    } else {
        $error = 'Username sau parolă invalide.';
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Login admin</title>
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
    <h1>Login administrator</h1>
    <form method="post">
        <label>
            Username:
            <input type="text" name="username" required>
        </label>
        <label>
            Parolă:
            <input type="password" name="password" required>
        </label>
        <button type="submit">Autentificare</button>
    </form>

    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <p><a href="register_admin.php">Creează cont admin</a></p>
</body>
</html>
