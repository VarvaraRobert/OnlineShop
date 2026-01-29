<?php
session_start();
require_once 'helpers.php';

// pagina de bun venit este disponibilă doar după login
if (!isset($_SESSION['user_id'])) {
    redirectTo('login.php');
}

$userId = (int)$_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Acasă</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 2rem;
        }
        .panel {
            border: 1px solid #ccc;
            padding: 1.5rem;
            border-radius: 6px;
            max-width: 500px;
        }
        a {
            color: #1a4a9f;
        }
    </style>
</head>
<body>
    <div class="panel">
        <h1>Bine ai venit!</h1>
        <p>Utilizator #<?php echo htmlspecialchars($userId); ?></p>
        <p><a href="categoryIndex.php">Mergi la magazin (categorii & coș)</a></p>
        <p><a href="logout.php">Logout</a></p>
    </div>
</body>
</html>
