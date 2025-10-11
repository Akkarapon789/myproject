<?php
// cart/cart_actions.php
session_start();
include '../config/connectdb.php';

// กำหนดค่าเริ่มต้นให้ตะกร้า ถ้ายังไม่มี
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : (isset($_GET['product_id']) ? intval($_GET['product_id']) : 0);

if (!$product_id) {
    header('Location: ../pages/index.php');
    exit;
}

switch ($action) {
    case 'add':
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
        
        // ถ้ามีสินค้าอยู่แล้ว ให้บวกจำนวนเพิ่ม
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            // ถ้ายังไม่มี ให้ดึงข้อมูลสินค้ามาเก็บ
            $stmt = $conn->prepare("SELECT title, price, image_url FROM products WHERE id = ?");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($product = $result->fetch_assoc()) {
                $_SESSION['cart'][$product_id] = [
                    'quantity'    => $quantity,
                    'title'       => $product['title'],
                    'price'       => $product['price'],
                    'image_url'   => $product['image_url']
                ];
            }
            $stmt->close();
        }
        break;

    case 'update':
        $quantity = intval($_POST['quantity']);
        if ($quantity > 0) {
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        } else {
            // ถ้าจำนวนเป็น 0 หรือน้อยกว่า ให้ลบออก
            unset($_SESSION['cart'][$product_id]);
        }
        break;

    case 'remove':
        unset($_SESSION['cart'][$product_id]);
        break;
}

// หลังจากจัดการเสร็จ ให้กลับไปหน้าตะกร้าสินค้า
header('Location: cart.php');
exit();