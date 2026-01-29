<?php


session_start();
// Endpoint AJAX: calculează totalul curent și creează un PaymentIntent Stripe.
require_once 'DBController.php';
require_once 'stripe_config.php';
require_once 'vendor/autoload.php';

use Stripe\Stripe;
use Stripe\PaymentIntent;

header('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Metodă neacceptată']);
    exit;
}


if (empty($_SESSION['member_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Trebuie să fii autentificat pentru plată.']);
    exit;
}

$memberId = $_SESSION['member_id'];
$db = new DBController();


// Preluăm toate produsele din coș pentru a determina suma ce va fi taxată
$query = "
    SELECT p.price, c.quantity
    FROM tbl_cart c
    INNER JOIN tbl_product p ON c.product_id = p.id
    WHERE c.id_member = ?
";
$cartItems = $db->getDBResult($query, [$memberId]);

if (empty($cartItems)) {
    http_response_code(400);
    echo json_encode(['error' => 'Coșul este gol.']);
    exit;
}


$total = 0;
foreach ($cartItems as $item) {
    $total += (float)$item['price'] * (int)$item['quantity'];
}

$amountInMinorUnit = (int) round($total * 100);

Stripe::setApiKey(STRIPE_SECRET_KEY);

try {
    $paymentIntent = PaymentIntent::create([
        'amount' => $amountInMinorUnit,
        'currency' => STRIPE_CURRENCY,
        'metadata' => [
            'member_id' => $memberId,
        ],
    ]);

    echo json_encode([
        'clientSecret'     => $paymentIntent->client_secret,
        'paymentIntentId'  => $paymentIntent->id,
        'amount'           => $total
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Eroare Stripe: ' . $e->getMessage()]);
}
