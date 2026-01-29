<?php
session_start();
require_once 'helpers.php';

unset($_SESSION['admin_id']);

redirectTo('login_admin.php');
exit;
