<?php
// cart/apply_promo.php (Corrected & Final Version)

// ⭐️ เพิ่ม 3 บรรทัดนี้ไว้บนสุดเพื่อช่วยหา Error ในอนาคต ⭐️
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include '../config/connectdb.php';

// บอกให้ browser รู้ว่าเราจะส่งข้อมูลกลับไปเป็น JSON
header('Content-Type: application/json'); 

// 1. ตรวจสอบการเชื่อมต่อฐานข้อมูลก่อนเลย
if ($conn->connect_error) {
    // ถ้าเชื่อมต่อไม่ได้ ให้ส่ง Error กลับไปทันที
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// 2. คำนวณราคารวมของสินค้าในตะกร้าก่อน
$cart_total = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_total += $item['price'] * $item['quantity'];
    }
}

// 3. รับค่า promo_id ที่ส่งมาจาก JavaScript
$promo_id = isset($_POST['promo_id']) ? intval($_POST['promo_id']) : 0;
$discount = 0;
$final_total = $cart_total;
$promo_name = null;

// 4. ถ้ามีการเลือกโปรโมชั่น (ID ไม่ใช่ 0)
if ($promo_id > 0) {
    // ดึงข้อมูลโปรโมชั่นจาก DB อย่างปลอดภัย
    $stmt = $conn->prepare("SELECT * FROM promotions WHERE id = ?");
    $stmt->bind_param("i", $promo_id);
    $stmt->execute();
    $promo_result = $stmt->get_result();
    
    if ($promo_result->num_rows > 0) {
        $promo = $promo_result->fetch_assoc();
        $promo_name = $promo['name'];

        // 5. คำนวณส่วนลดตามประเภท
        if ($promo['discount_type'] == 'percentage') {
            $discount = ($cart_total * $promo['discount_value']) / 100;
        } elseif ($promo['discount_type'] == 'fixed') {
            $discount = $promo['discount_value'];
        }

        // 6. คำนวณยอดสุทธิ (ป้องกันไม่ให้ส่วนลดมากกว่าราคาสินค้า)
        $final_total = $cart_total - $discount;
        if ($final_total < 0) {
            $final_total = 0;
            $discount = $cart_total; 
        }
    }
    $stmt->close();
}

// 7. บันทึกผลลัพธ์ลง Session เพื่อใช้ในหน้า Checkout
$_SESSION['promo_id'] = $promo_id;
$_SESSION['promo_discount'] = $discount;
$_SESSION['final_total'] = $final_total;

// 8. ส่งข้อมูลที่คำนวณได้กลับไปให้ JavaScript ในรูปแบบ JSON
echo json_encode([
    'success'                 => true, // เพิ่มสถานะเพื่อบอกว่าสำเร็จ
    'cart_total_formatted'    => '฿' . number_format($cart_total, 2),
    'discount_formatted'      => '- ฿' . number_format($discount, 2),
    'final_total_formatted'   => '฿' . number_format($final_total, 2),
    'discount_value'          => $discount,
    'promo_name'              => $promo_name
]);

exit(); // จบการทำงานของสคริปต์