<?php
session_start();
require_once 'helpers.php';

// curățăm toate datele din sesiune pentru a deconecta utilizatorul
$_SESSION = [];

session_destroy();

redirectTo('categoryIndex.php');
exit;
