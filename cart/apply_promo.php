<?php
// cart/apply_promo.php (Clean & Final Version)

// ⭐️ สำคัญ: ไฟล์นี้ต้องไม่มี HTML, echo, หรือข้อความใดๆ ก่อนหน้านี้เลย ⭐️

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/connectdb.php';

// ตั้งค่า Header เป็น JSON ก่อนจะแสดงผลอะไรทั้งสิ้น
header('Content-Type: application/json');

// --- ฟังก์ชันสำหรับส่ง Error กลับไปอย่างปลอดภัย ---
function send_json_error($message) {
    echo json_encode(['success' => false, 'error' => $message]);
    exit();
}

// --- ตรวจสอบการเชื่อมต่อ ---
if ($conn->connect_error) {
    send_json_error('Database connection failed');
}

// --- คำนวณราคารวม ---
$cart_total = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        if (is_array($item) && isset($item['price'], $item['quantity'])) {
            $cart_total += floatval($item['price']) * intval($item['quantity']);
        }
    }
}

// --- รับค่าและคำนวณส่วนลด ---
$promo_id = isset($_POST['promo_id']) ? intval($_POST['promo_id']) : 0;
$discount = 0;
$final_total = $cart_total;
$promo_name = null;

if ($promo_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM promotions WHERE id = ?");
    if (!$stmt) {
        send_json_error('SQL prepare failed');
    }
    
    $stmt->bind_param("i", $promo_id);
    $stmt->execute();
    $promo_result = $stmt->get_result();
    
    if ($promo_result->num_rows > 0) {
        $promo = $promo_result->fetch_assoc();
        $promo_name = $promo['name'];

        if ($promo['discount_type'] == 'percentage') {
            $discount = ($cart_total * floatval($promo['discount_value'])) / 100;
        } elseif ($promo['discount_type'] == 'fixed') {
            $discount = floatval($promo['discount_value']);
        }

        $final_total = $cart_total - $discount;
        if ($final_total < 0) {
            $final_total = 0;
            $discount = $cart_total;
        }
    }
    $stmt->close();
}

// --- บันทึกผลลัพธ์ลง Session ---
$_SESSION['promo_id'] = $promo_id;
$_SESSION['promo_discount'] = $discount;
$_SESSION['final_total'] = $final_total;

// --- ส่งข้อมูลกลับไปให้ JavaScript ---
echo json_encode([
    'success'               => true,
    'cart_total_formatted'  => '฿' . number_format($cart_total, 2),
    'discount_formatted'    => '- ฿' . number_format($discount, 2),
    'final_total_formatted' => '฿' . number_format($final_total, 2),
    'discount_value'        => $discount,
    'promo_name'            => $promo_name
]);

exit();