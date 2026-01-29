<?php
session_start();
// Pagina coșului afișează conținutul tbl_cart pentru utilizatorul logat.
require_once 'DBController.php';
require_once 'helpers.php';

if (!isset($_SESSION['member_id'])) {
    // ne asigurăm că doar userii logați văd coșul
    redirectTo('login.php');
}

$db = new DBController();
$member_id = $_SESSION['member_id'];

// Căutăm toate produsele din coșul utilizatorului curent pentru a le lista mai jos
$cart_items = $db->getDBResult(
    "SELECT p.name, p.price, c.quantity, c.id, c.product_id
     FROM tbl_cart c
     JOIN tbl_product p ON c.product_id = p.id
     WHERE c.id_member = ?",
    [$member_id]
);
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Coș de cumpărături</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 2rem;
        }
        .cart-item {
            padding: 0.7rem 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .cart-actions form {
            display: inline-block;
            margin-left: 1rem;
        }
        .links p {
            margin: 0.3rem 0;
        }
        .btnAction {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: #1a4a9f;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <h1>Coș de cumpărături</h1>

    <?php if (empty($cart_items)): ?>
        <p>Coșul este gol.</p>
    <?php else: ?>
        <?php $total = 0; ?>
        <?php foreach ($cart_items as $item): ?>
            <?php $lineTotal = (float)$item['price'] * (int)$item['quantity']; ?>
            <?php $total += $lineTotal; ?>
            <div class="cart-item">
                <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                - <?php echo number_format((float)$item['price'], 2); ?> lei x <?php echo (int)$item['quantity']; ?>
                <span class="cart-actions">
                    <form method="post" action="updateCart.php">
                        <input type="number" name="quantity" value="<?php echo (int)$item['quantity']; ?>" min="1" />
                        <input type="hidden" name="cart_id" value="<?php echo (int)$item['id']; ?>" />
                        <input type="submit" value="Actualizează" />
                    </form>
                    <a href="removeFromCart.php?cart_id=<?php echo (int)$item['id']; ?>">Elimină</a>
                </span>
            </div>
        <?php endforeach; ?>
        <p><strong>Total coș:</strong> <?php echo number_format($total, 2); ?> lei</p>
        <p><a class="btnAction" href="checkout.php">Plătește cu cardul (Stripe)</a></p>
    <?php endif; ?>

    <div class="links">
        <p><a href="emptyCart.php">Golește coșul</a></p>
        <p><a href="categoryIndex.php">Înapoi la categorii</a></p>
        <p><a href="index.php">Înapoi la produse</a></p>
        <p><a href="logout.php">Logout</a></p>
    </div>
</body>
</html>
