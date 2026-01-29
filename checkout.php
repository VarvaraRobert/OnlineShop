<?php
session_start();
require_once 'DBController.php';
require_once 'stripe_config.php';

if (empty($_SESSION['member_id'])) {
    header('Location: login.php');
    exit;
}

$memberId = $_SESSION['member_id'];
$db = new DBController();

$query = "
    SELECT p.name, p.price, c.quantity
    FROM tbl_cart c
    INNER JOIN tbl_product p ON c.product_id = p.id
    WHERE c.id_member = ?
";
$cartItems = $db->getDBResult($query, [$memberId]);

$total = 0;
foreach ($cartItems as $item) {
    $total += (float)$item['price'] * (int)$item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="ro">

<head>
    <meta charset="UTF-8">
    <title>Plată cu cardul</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 2rem;
        }
        .card {
            max-width: 600px;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 1.5rem;
        }
        ul {
            padding-left: 1.2rem;
        }
        li {
            margin-bottom: 0.3rem;
        }
        #card-element {
            border: 1px solid #ccc;
            padding: 0.75rem;
            border-radius: 4px;
        }
        #pay-button {
            margin-top: 1rem;
            padding: 0.6rem 1.4rem;
            background-color: #1a4a9f;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        #pay-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .back-links {
            margin-top: 1.5rem;
        }
        .back-links a {
            margin-right: 1rem;
        }
        #card-errors {
            color: #d9534f;
            min-height: 1.2rem;
        }
    </style>
</head>

<body>
    <h1>Plată cu cardul (Stripe)</h1>
    <div class="card">
        <?php if (empty($cartItems)): ?>
            <p>Coșul tău este gol.</p>
            <p><a href="index.php">Înapoi la produse</a></p>
        <?php else: ?>
            <h2>Rezumat coș</h2>
            <ul>
                <?php foreach ($cartItems as $item): ?>
                    <li>
                        <?php echo htmlspecialchars($item['name']); ?> -
                        <?php echo number_format($item['price'], 2); ?> lei x
                        <?php echo (int)$item['quantity']; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p><strong>Total:</strong>
                <?php echo number_format($total, 2) . ' ' . strtoupper(STRIPE_CURRENCY); ?>
            </p>

            <h2>Detalii card</h2>
            <div id="card-element"></div>
            <div id="card-errors"></div>
            <button id="pay-button">Plătește acum</button>
        <?php endif; ?>
    </div>

    <div class="back-links">
        <a href="cart.php">Înapoi la coș</a>
        <a href="index.php">Continuă cumpărăturile</a>
    </div>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const payButton = document.getElementById('pay-button');
        if (payButton) {
            const stripe = Stripe('<?php echo STRIPE_PUBLISHABLE_KEY; ?>');
            const elements = stripe.elements();
            const card = elements.create('card');
            card.mount('#card-element');

            const cardErrors = document.getElementById('card-errors');

            payButton.addEventListener('click', async function () {
                payButton.disabled = true;
                cardErrors.textContent = '';

                try {
                    const response = await fetch('create_stripe_payment.php', {
                        method: 'POST'
                    });

                    const data = await response.json();

                    if (!response.ok || data.error) {
                        throw new Error(data.error || 'Eroare la crearea plății.');
                    }

                    const result = await stripe.confirmCardPayment(data.clientSecret, {
                        payment_method: {
                            card: card
                        }
                    });

                    if (result.error) {
                        cardErrors.textContent = result.error.message;
                        payButton.disabled = false;
                    } else if (result.paymentIntent.status === 'succeeded') {
                        window.location.href = 'payment_success.php?pi=' +
                            encodeURIComponent(result.paymentIntent.id);
                    } else {
                        cardErrors.textContent = 'Status plată: ' + result.paymentIntent.status;
                        payButton.disabled = false;
                    }
                } catch (err) {
                    cardErrors.textContent = err.message;
                    payButton.disabled = false;
                }
            });
        }
    </script>
</body>

</html>
