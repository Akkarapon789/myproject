<?php
// cart/add.php
session_start();
require_once '../config/connectdb.php';

// ตรวจสอบการส่งค่ามา
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['product_id'])) {
    header("Location: ../pages/index.php");
    exit;
}

$product_id = intval($_POST['product_id']);

// ดึงข้อมูลสินค้าจากฐานข้อมูล
$sql = "SELECT id, title, price, stock FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

// 🔹 ตรวจสอบว่ามีสินค้าหรือไม่
if (!$product) {
    header("Location: ../pages/index.php?error=product_not_found");
    exit;
}

// 🔹 ตรวจสอบว่าสินค้าหมดหรือไม่
if (isset($product['stock']) && $product['stock'] <= 0) {
    header("Location: ../pages/index.php?error=out_of_stock");
    exit;
}

// ถ้ายังไม่มี cart ให้สร้าง array
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// ถ้ามีสินค้าในตะกร้าอยู่แล้ว -> เพิ่มจำนวน
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id]['qty']++;
} else {
    // ถ้าไม่มี -> เพิ่มใหม่
    $_SESSION['cart'][$product_id] = [
        'id'    => $product['id'],
        'title' => $product['title'],
        'price' => $product['price'],
        'qty'   => 1
    ];
}

// เก็บชื่อสินค้าที่เพิ่งเพิ่ม (ไว้โชว์ popup ที่ cart.php)
$_SESSION['added_product'] = $product['title'];

// ✅ (ส่วนใหม่) บันทึก log ลงตารางชั่วคราวหรือเตรียมข้อมูล order
if (!isset($_SESSION['order_temp'])) {
    $_SESSION['order_temp'] = [];
}
$_SESSION['order_temp'][] = [
    'product_id' => $product['id'],
    'title'      => $product['title'],
    'price'      => $product['price'],
    'qty'        => 1,
    'added_at'   => date('Y-m-d H:i:s')
];

// ✅ (ส่วนใหม่) ตัวอย่างการบันทึก "รายการเพิ่มสินค้า" ไว้ log ในฐานข้อมูล (optional)
$log_sql = "INSERT INTO cart_log (product_id, title, added_at) VALUES (?, ?, NOW())";
if ($conn->prepare($log_sql)) {
    $log_stmt = $conn->prepare($log_sql);
    $log_stmt->bind_param("is", $product['id'], $product['title']);
    $log_stmt->execute();
    $log_stmt->close();
}

// เสร็จแล้ว redirect ไป cart.php
header("Location: cart.php");
exit;
