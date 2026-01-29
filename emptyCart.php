<?php
session_start();
require_once 'DBController.php';
require_once 'helpers.php';

if (!isset($_SESSION['member_id'])) {
    // fără user logat nu putem goli un coș
    redirectTo('login.php');
}

$db        = new DBController();
$member_id = $_SESSION['member_id'];

// șterge toate produsele din coșul acestui membru
$db->updateDB("DELETE FROM tbl_cart WHERE id_member = ?", [$member_id]);

redirectTo('cart.php');
