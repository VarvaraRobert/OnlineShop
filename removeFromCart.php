<?php
session_start();
require_once 'DBController.php';
require_once 'helpers.php';

if (!isset($_SESSION['member_id'])) {
    // doar userul logat poate modifica propriul coș
    redirectTo('login.php');
}

if (!isset($_GET['cart_id'])) {
    redirectTo('cart.php');
}

$cartId = (int)$_GET['cart_id'];
$memberId = (int)$_SESSION['member_id'];

$db = new DBController();
// ștergem doar item-ul aparținând utilizatorului curent
$db->updateDB("DELETE FROM tbl_cart WHERE id = ? AND id_member = ?", [$cartId, $memberId]);

redirectTo('cart.php');
