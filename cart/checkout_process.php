<?php
// cart/checkout_process.php (Corrected & Final Version)

// ⭐️ เรียกใช้ session_start() ก่อนทำอย่างอื่นเสมอ ⭐️
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config/connectdb.php';

// 1. ตรวจสอบเงื่อนไขทั้งหมดให้ครบถ้วน
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['cart']) || !isset($_SESSION['user_id'])) {
    // ถ้าเงื่อนไขไม่ครบ ให้ส่งกลับไปหน้าแรก
    header('Location: ../pages/index.php');
    exit();
}

// 2. รับข้อมูลจากฟอร์มและ Session
$user_id     = $_SESSION['user_id'];
$fullname    = $_POST['fullname'];
$email       = $_POST['email'];
$phone       = $_POST['phone'];
$address     = $_POST['address'];
$payment     = $_POST['payment'];

// 3. ดึงยอดรวมสุทธิจาก Session ที่คำนวณส่วนลดไว้แล้ว
$final_total = $_SESSION['final_total'] ?? array_sum(array_map(function($item) {
    return $item['price'] * $item['quantity'];
}, $_SESSION['cart']));

$conn->begin_transaction();

try {
    // 4. บันทึกออเดอร์หลัก
    $sql_order = "INSERT INTO orders (user_id, fullname, email, phone, address, payment, total, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->bind_param("isssssd", $user_id, $fullname, $email, $phone, $address, $payment, $final_total);
    $stmt_order->execute();
    $last_order_id = $conn->insert_id;

    // 5. บันทึกรายการสินค้า
    $sql_items = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt_items = $conn->prepare($sql_items);
    foreach ($_SESSION['cart'] as $product_id => $item) {
        $stmt_items->bind_param("iiid", $last_order_id, $product_id, $item['quantity'], $item['price']);
        $stmt_items->execute();
    }
    
    $conn->commit();

    // 6. เคลียร์ Session ทั้งหมดที่เกี่ยวข้อง
    unset($_SESSION['cart']);
    unset($_SESSION['promo_id']);
    unset($_SESSION['promo_discount']);
    unset($_SESSION['final_total']);

    // 7. แยกการส่งต่อไปยังหน้าที่ถูกต้อง
    if ($payment == 'bank') {
        header('Location: payment.php?order_id=' . $last_order_id);
    } else {
        header('Location: loading-car.php?order_id=' . $last_order_id);
    }
    exit();

} catch (mysqli_sql_exception $exception) {
    $conn->rollback();
    $_SESSION['checkout_error'] = "เกิดข้อผิดพลาด: " . $exception->getMessage(); // แสดง Error เพื่อ Debug
    header('Location: checkout.php');
    exit();
}