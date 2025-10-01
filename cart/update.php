<?php
session_start();

if (!isset($_GET['action']) || !isset($_GET['id'])) {
    header("Location: cart.php");
    exit;
}

$action = $_GET['action'];
$id = intval($_GET['id']);

if (!isset($_SESSION['cart'][$id])) {
    header("Location: cart.php");
    exit;
}

switch ($action) {
    case 'increase':
        $_SESSION['cart'][$id]['qty']++;
        break;
    case 'decrease':
        $_SESSION['cart'][$id]['qty']--;
        if ($_SESSION['cart'][$id]['qty'] <= 0) {
            unset($_SESSION['cart'][$id]);
        }
        break;
    case 'remove':
        unset($_SESSION['cart'][$id]);
        break;
}

header("Location: cart.php");
exit;
