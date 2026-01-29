<?php
session_start();
// Endpoint care inserează produsele selectate în coșul din tbl_cart.
require_once 'DBController.php';
require_once 'helpers.php';

// fără product_id nu avem ce adăuga, revenim la listă
if (!isset($_GET['product_id'])) {
    redirectTo('index.php');
}

$productId = (int)$_GET['product_id'];
$quantity = filter_input(INPUT_GET, 'quantity', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$quantity = $quantity !== null && $quantity !== false ? $quantity : 1;

if (!isset($_SESSION['member_id'])) {
    // salvăm produsul dorit și trimitem userul la login
    $_SESSION['pending_cart_product_id'] = $productId;
    $_SESSION['pending_cart_quantity'] = $quantity;
    $_SESSION['redirect_after_login'] = 'cart.php';
    redirectTo('login.php');
}

$db = new DBController();
$memberId = (int)$_SESSION['member_id'];

// utilizator logat -> inserăm direct produsul în coș
$db->updateDB(
    "INSERT INTO tbl_cart (product_id, quantity, id_member) VALUES (?, ?, ?)",
    [$productId, $quantity, $memberId]
);

redirectTo('cart.php');
