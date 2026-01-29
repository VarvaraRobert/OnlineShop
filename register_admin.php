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

    if ($username === '' || $password === '') {
        $error = 'Completează toate câmpurile.';
    } else {
        $db = new DBController();
        $hash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $db->updateDB(
                "INSERT INTO admin_users (username, password) VALUES (?, ?)",
                [$username, $hash]
            );
            $_SESSION['admin_id'] = (int)$db->getConnection()->lastInsertId();
            redirectTo('admin_home.php');
        } catch (Exception $e) {
            $error = 'Nu am putut înregistra adminul: ' . htmlspecialchars($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Înregistrare admin</title>
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
    <h1>Înregistrare administrator</h1>
    <form method="post">
        <label>
            Username:
            <input type="text" name="username" required>
        </label>
        <label>
            Parolă:
            <input type="password" name="password" required>
        </label>
        <button type="submit">Creează cont</button>
    </form>

    <?php if ($error): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <p><a href="login_admin.php">Ai deja cont? Login admin</a></p>
</body>
</html>
