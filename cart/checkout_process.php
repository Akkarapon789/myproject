<?php
// cart/checkout_process.php
session_start();
include '../config/connectdb.php';

// 1. ตรวจสอบเบื้องต้นว่ามาจากฟอร์มและมีของในตะกร้าจริง
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['cart'])) {
    header('Location: ../pages/index.php');
    exit();
}

// 2. รับข้อมูลจากฟอร์มและ Session
$user_id     = $_SESSION['user_id'];
$fullname    = $_POST['fullname'];
$email       = $_POST['email'];
$phone       = $_POST['phone'];
$address     = $_POST['address'];
$payment     = 'cod'; // สมมติว่าเป็นการเก็บเงินปลายทาง

// ⭐️ 3. ดึงยอดรวมสุทธิจาก Session ที่คำนวณส่วนลดไว้แล้ว ⭐️
// ถ้าไม่มีค่า (คือไม่ได้เลือกโปรโมชั่น) ให้ใช้ยอดรวมปกติจากตะกร้า
$total_price_from_cart = array_sum(array_map(function($item) {
    return $item['price'] * $item['quantity'];
}, $_SESSION['cart']));
$final_total = $_SESSION['final_total'] ?? $total_price_from_cart;


// 4. ใช้ Transaction เพื่อความปลอดภัยของข้อมูล (ถ้ามีขั้นตอนไหนพลาด จะยกเลิกทั้งหมด)
$conn->begin_transaction();

try {
    // 5. INSERT ข้อมูลหลักลงตาราง `orders` (ใช้ยอดรวมสุทธิ)
    $sql_order = "INSERT INTO orders (user_id, fullname, email, phone, address, payment, total) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->bind_param("isssssd", $user_id, $fullname, $email, $phone, $address, $payment, $final_total);
    $stmt_order->execute();

    // 6. ดึง ID ของออเดอร์ล่าสุดที่เพิ่งสร้าง
    $last_order_id = $conn->insert_id;

    // 7. เตรียมคำสั่งสำหรับ INSERT ลงตาราง `order_items`
    $sql_items = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt_items = $conn->prepare($sql_items);

    // 8. วนลูปสินค้าในตะกร้า แล้ว INSERT ลง `order_items` ทีละรายการ
    foreach ($_SESSION['cart'] as $product_id => $item) {
        $stmt_items->bind_param("iiid", $last_order_id, $product_id, $item['quantity'], $item['price']);
        $stmt_items->execute();
    }

    // 9. ถ้าทุกอย่างสำเร็จ ให้ยืนยันการทำรายการทั้งหมด
    $conn->commit();

    // 10. เคลียร์ตะกร้าและข้อมูลโปรโมชั่นออกจาก Session
    unset($_SESSION['cart']);
    unset($_SESSION['promo_id']);
    unset($_SESSION['promo_discount']);
    unset($_SESSION['final_total']);

    // 11. ส่งไปหน้า loading พร้อมแนบ ID ออเดอร์
    header('Location: loading-car.php?order_id=' . $last_order_id);
    exit();

} catch (mysqli_sql_exception $exception) {
    // 12. หากเกิดข้อผิดพลาดใดๆ ให้ยกเลิกการทำรายการทั้งหมด
    $conn->rollback();
    
    // สามารถบันทึก log หรือแสดงข้อความ Error ที่นี่ได้ (สำหรับ debug)
    // error_log("Checkout Error: " . $exception->getMessage());
    
    // ส่งผู้ใช้กลับไปหน้า checkout พร้อมข้อความแจ้งเตือน
    $_SESSION['checkout_error'] = "เกิดข้อผิดพลาดในการบันทึกคำสั่งซื้อ กรุณาลองใหม่อีกครั้ง";
    header('Location: checkout.php');
    exit();
}