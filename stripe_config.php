<?php
// stripe_config.php

// Pune aici cheile tale de TEST din Stripe Dashboard
// (Developers -> API keys)
define('STRIPE_SECRET_KEY', '');      // cheia secretă
define('STRIPE_PUBLISHABLE_KEY', ''); // cheia publică

// Moneda (RON nu e suportat direct de Stripe, folosește EUR sau USD)
define('STRIPE_CURRENCY', 'ron');
?>
