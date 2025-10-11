<?php
// cart/apply_promo.php (Ultimate Disinfectant Version)

// ⭐️ 1. เปิดเครื่องดูดฝุ่น (Output Buffering) เพื่อดักจับสิ่งแปลกปลอมทั้งหมด ⭐️
ob_start();

// เริ่ม Session และเรียกใช้ไฟล์เชื่อมต่อฐานข้อมูลตามปกติ
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/connectdb.php';

// ⭐️ 2. สั่งให้เครื่องดูดฝุ่น "ล้างทิ้ง" ทุกอย่างที่ดักจับมาได้ ⭐️
ob_end_clean();

// 3. ตั้งค่า Header เป็น JSON หลังจากที่ทุกอย่างสะอาดแล้ว
header('Content-Type: application/json');

// --- ฟังก์ชันสำหรับส่ง Error กลับไปอย่างปลอดภัย ---
function send_json_error($message) {
    echo json_encode(['success' => false, 'error' => $message]);
    exit();
}

// --- ตรวจสอบการเชื่อมต่ออีกครั้ง (เพื่อความปลอดภัย) ---
if ($conn->connect_error) {
    send_json_error('Database connection failed');
}

// --- คำนวณราคารวม (โค้ดส่วนนี้ถูกต้องแล้ว) ---
$cart_total = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        if (is_array($item) && isset($item['price'], $item['quantity'])) {
            $cart_total += floatval($item['price']) * intval($item['quantity']);
        }
    }
}

// --- รับค่าและคำนวณส่วนลด (โค้ดส่วนนี้ถูกต้องแล้ว) ---
$promo_id = isset($_POST['promo_id']) ? intval($_POST['promo_id']) : 0;
$discount = 0;
$final_total = $cart_total;
$promo_name = null;

if ($promo_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM promotions WHERE id = ?");
    if ($stmt) {
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
}

// --- บันทึกผลลัพธ์ลง Session (ถูกต้องแล้ว) ---
$_SESSION['promo_id'] = $promo_id;
$_SESSION['promo_discount'] = $discount;
$_SESSION['final_total'] = $final_total;

// --- ส่งข้อมูลที่สะอาดบริสุทธิ์กลับไป (ถูกต้องแล้ว) ---
echo json_encode([
    'success'               => true,
    'cart_total_formatted'  => '฿' . number_format($cart_total, 2),
    'discount_formatted'    => '- ฿' . number_format($discount, 2),
    'final_total_formatted' => '฿' . number_format($final_total, 2),
    'discount_value'        => $discount,
    'promo_name'            => $promo_name
]);

exit();