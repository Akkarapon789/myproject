<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// cart/apply_promo.php
session_start();
include '../config/connectdb.php';

header('Content-Type: application/json'); // บอกให้ browser รู้ว่าเราจะส่งข้อมูลแบบ JSON

// 1. คำนวณราคารวมของสินค้าในตะกร้าก่อน
$cart_total = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_total += $item['price'] * $item['quantity'];
    }
}

$promo_id = isset($_POST['promo_id']) ? intval($_POST['promo_id']) : 0;
$discount = 0;
$final_total = $cart_total;

// 2. ถ้ามีการเลือกโปรโมชั่น (ID ไม่ใช่ 0)
if ($promo_id > 0) {
    // ดึงข้อมูลโปรโมชั่นจาก DB
    $stmt = $conn->prepare("SELECT * FROM promotions WHERE id = ?");
    $stmt->bind_param("i", $promo_id);
    $stmt->execute();
    $promo = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($promo) {
        // 3. คำนวณส่วนลดตามประเภท
        if ($promo['discount_type'] == 'percentage') {
            $discount = ($cart_total * $promo['discount_value']) / 100;
        } elseif ($promo['discount_type'] == 'fixed') {
            $discount = $promo['discount_value'];
        }

        // คำนวณยอดสุทธิ (ป้องกันไม่ให้ส่วนลดมากกว่าราคาสินค้า)
        $final_total = $cart_total - $discount;
        if ($final_total < 0) {
            $final_total = 0;
        }
    }
}

// 4. บันทึกผลลัพธ์ลง Session เพื่อใช้ในหน้า Checkout ต่อไป
$_SESSION['promo_id'] = $promo_id;
$_SESSION['promo_discount'] = $discount;
$_SESSION['final_total'] = $final_total;

// 5. ส่งข้อมูลกลับไปให้ JavaScript ในรูปแบบ JSON
echo json_encode([
    'cart_total_formatted'    => '฿' . number_format($cart_total, 2),
    'discount_formatted'      => '- ฿' . number_format($discount, 2),
    'final_total_formatted'   => '฿' . number_format($final_total, 2),
    'discount_value'          => $discount // ส่งค่าตัวเลขเปล่าๆ ไปด้วย
]);