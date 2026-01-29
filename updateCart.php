<?php
session_start();
require_once 'DBController.php';
require_once 'helpers.php';

if (!isset($_SESSION['member_id'])) {
    // protecție: doar userul logat își poate modifica coșul
    redirectTo('login.php');
}

if (!isset($_POST['cart_id'], $_POST['quantity'])) {
    // fără datele obligatorii revenim în coș
    redirectTo('cart.php');
}

$db       = new DBController();
$cart_id  = (int)$_POST['cart_id'];
$quantity = (int)$_POST['quantity'];

if ($quantity > 0) {
    // actualizăm cantitatea cerută
    $query = "UPDATE tbl_cart SET quantity = ? WHERE id = ?";
    $db->updateDB($query, [$quantity, $cart_id]);
} else {
    // cantitățile zero/negative elimină produsul
    $query = "DELETE FROM tbl_cart WHERE id = ?";
    $db->updateDB($query, [$cart_id]);
}

redirectTo('cart.php');
