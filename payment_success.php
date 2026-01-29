<?php
session_start();
require_once 'DBController.php';
require_once 'stripe_config.php';
require_once 'vendor/autoload.php';

use Stripe\Stripe;
use Stripe\PaymentIntent;

if (empty($_GET['pi']) || empty($_SESSION['member_id'])) {
    header('Location: index.php');
    exit;
}

$paymentIntentId = $_GET['pi'];
$memberId = $_SESSION['member_id'];

Stripe::setApiKey(STRIPE_SECRET_KEY);

try {
    $pi = PaymentIntent::retrieve($paymentIntentId);
    $status   = $pi->status;
    $amount   = $pi->amount / 100;
    $currency = strtoupper($pi->currency);
} catch (Exception $e) {
    $status = 'error';
    $amount = 0;
    $currency = STRIPE_CURRENCY;
}

$db = new DBController();

// dacă plata e ok, salvăm și golim coșul
if ($status === 'succeeded') {
    $orderReference = uniqid('ORD-');
    $customerEmail = isset($pi->charges->data[0]->billing_details->email)
        ? $pi->charges->data[0]->billing_details->email
        : '';
    $customerName = isset($pi->charges->data[0]->billing_details->name)
        ? $pi->charges->data[0]->billing_details->name
        : null;

    $db->updateDB(
        "INSERT INTO tbl_payment (order_reference, member_id, payment_intent_id, amount, currency, status, customer_email, customer_name)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
        [$orderReference, $memberId, $paymentIntentId, $amount, $currency, $status, $customerEmail, $customerName]
    );

    $db->updateDB("DELETE FROM tbl_cart WHERE id_member = ?", [$memberId]);
}
?>
<!DOCTYPE html>
<html lang="ro">

<head>
    <meta charset="UTF-8">
    <title>Status plată</title>
</head>

<body>
    <?php if ($status === 'succeeded'): ?>
        <h1>Plata a fost efectuată cu succes!</h1>
        <p>Sumă: <?php echo number_format($amount, 2) . ' ' . $currency; ?></p>
        <p>ID plată Stripe: <?php echo htmlspecialchars($paymentIntentId); ?></p>
    <?php else: ?>
        <h1>Plată nereușită sau eroare.</h1>
        <p>Status: <?php echo htmlspecialchars($status); ?></p>
    <?php endif; ?>

    <p><a href="index.php">Înapoi la magazin</a></p>
</body>

</html>
