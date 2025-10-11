<?php
// cart/checkout_process.php
session_start();
include '../config/connectdb.php';

// ตรวจสอบว่ามาจากฟอร์มและมีของในตะกร้าจริง
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['cart'])) {
    header('Location: ../pages/index.php');
    exit();
}

// 1. รับข้อมูลจากฟอร์ม
$user_id     = $_SESSION['user_id'];
$fullname    = $_POST['fullname'];
$email       = $_POST['email'];
$phone       = $_POST['phone'];
$address     = $_POST['address'];
$total_price = $_POST['total_price'];
$payment     = 'cod'; // สมมติว่าเป็นการเก็บเงินปลายทาง

// ใช้ Transaction เพื่อความปลอดภัยของข้อมูล
$conn->begin_transaction();

try {
    // 2. INSERT ข้อมูลหลักลงตาราง `orders`
    $sql_order = "INSERT INTO orders (user_id, fullname, email, phone, address, payment, total) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->bind_param("isssssd", $user_id, $fullname, $email, $phone, $address, $payment, $total_price);
    $stmt_order->execute();

    // 3. ดึง ID ของออเดอร์ล่าสุดที่เพิ่งสร้าง
    $last_order_id = $conn->insert_id;

    // 4. เตรียมคำสั่งสำหรับ INSERT ลงตาราง `order_items`
    $sql_items = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt_items = $conn->prepare($sql_items);

    // 5. วนลูปสินค้าในตะกร้า แล้ว INSERT ลง `order_items`
    foreach ($_SESSION['cart'] as $product_id => $item) {
        $stmt_items->bind_param("iiid", $last_order_id, $product_id, $item['quantity'], $item['price']);
        $stmt_items->execute();
    }

    // ถ้าทุกอย่างสำเร็จ ให้ยืนยันการทำรายการ
    $conn->commit();

    // 6. เคลียร์ตะกร้า และส่งไปหน้าขอบคุณ
    unset($_SESSION['cart']);
    // เปลี่ยนจาก thank_you.php ไปที่หน้า loading-car.php แทน
    header('Location: loading-car.php?order_id=' . $last_order_id);
    exit();

} catch (mysqli_sql_exception $exception) {
    // หากเกิดข้อผิดพลาด ให้ยกเลิกการทำรายการทั้งหมด
    $conn->rollback();
    
    // สามารถบันทึก log หรือแสดงข้อความ Error ที่นี่ได้
    // echo "Error: " . $exception->getMessage();
    header('Location: checkout.php?error=1');
    exit();
}