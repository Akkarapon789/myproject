<?php
// cart/checkout_process.php (Upgraded with Payment Logic)
session_start();
include '../config/connectdb.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['cart'])) {
    header('Location: ../pages/index.php');
    exit();
}

$user_id     = $_SESSION['user_id'];
$fullname    = $_POST['fullname'];
$email       = $_POST['email'];
$phone       = $_POST['phone'];
$address     = $_POST['address'];
$payment     = $_POST['payment']; // ⭐️ รับค่าวิธีการชำระเงิน ⭐️

$final_total = $_SESSION['final_total'] ?? array_sum(array_map(function($item) {
    return $item['price'] * $item['quantity'];
}, $_SESSION['cart']));

$conn->begin_transaction();

try {
    // INSERT ข้อมูลลงตาราง `orders` (เหมือนเดิม)
    $sql_order = "INSERT INTO orders (user_id, fullname, email, phone, address, payment, total) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->bind_param("isssssd", $user_id, $fullname, $email, $phone, $address, $payment, $final_total);
    $stmt_order->execute();

    $last_order_id = $conn->insert_id;

    // INSERT ข้อมูลลงตาราง `order_items` (เหมือนเดิม)
    $sql_items = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt_items = $conn->prepare($sql_items);
    foreach ($_SESSION['cart'] as $product_id => $item) {
        $stmt_items->bind_param("iiid", $last_order_id, $product_id, $item['quantity'], $item['price']);
        $stmt_items->execute();
    }
    
    $conn->commit();

    // เคลียร์ Session
    unset($_SESSION['cart']);
    unset($_SESSION['promo_id']);
    unset($_SESSION['promo_discount']);
    unset($_SESSION['final_total']);

    // ⭐️⭐️⭐️ Logic ใหม่: แยกการส่งต่อไปตามวิธีการชำระเงิน ⭐️⭐️⭐️
    if ($payment == 'bank') {
        // ถ้าเลือกโอนเงิน ให้ไปหน้าแสดง QR Code
        header('Location: payment.php?order_id=' . $last_order_id);
    } else {
        // ถ้าเป็น COD หรืออื่นๆ ให้ไปหน้า loading car เหมือนเดิม
        header('Location: loading-car.php?order_id=' . $last_order_id);
    }
    exit();

} catch (mysqli_sql_exception $exception) {
    $conn->rollback();
    $_SESSION['checkout_error'] = "เกิดข้อผิดพลาดในการบันทึกคำสั่งซื้อ";
    header('Location: checkout.php');
    exit();
}