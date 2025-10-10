<?php
// order_actions.php
session_start();
// ตรวจสอบสิทธิ์แอดมิน
if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}
include '../config/connectdb.php';

// ตรวจสอบว่ามีการส่งข้อมูลมาเพื่ออัปเดตรายการสินค้าหรือไม่
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_item') {
    
    // รับค่าจากฟอร์ม
    $order_id = (int)$_POST['order_id'];
    $order_item_id = (int)$_POST['order_item_id'];
    $product_id = (int)$_POST['product_id'];
    $old_quantity = (int)$_POST['old_quantity'];
    $new_quantity = (int)$_POST['new_quantity'];

    $conn->begin_transaction(); // เริ่มต้น Transaction เพื่อความปลอดภัยของข้อมูล

    try {
        // --- ขั้นตอนที่ 1: อัปเดต/ลบ รายการสินค้า ---
        if ($new_quantity > 0) {
            // ถ้าจำนวนใหม่มากกว่า 0 ให้อัปเดตจำนวน
            $stmt1 = $conn->prepare("UPDATE order_items SET quantity = ? WHERE id = ?");
            $stmt1->bind_param("ii", $new_quantity, $order_item_id);
            $stmt1->execute();
            $stmt1->close();
        } else { 
            // ถ้าจำนวนใหม่เป็น 0 ให้ลบรายการสินค้านี้ทิ้ง
            $stmt1 = $conn->prepare("DELETE FROM order_items WHERE id = ?");
            $stmt1->bind_param("i", $order_item_id);
            $stmt1->execute();
            $stmt1->close();
        }

        // --- ขั้นตอนที่ 2: คืนของเข้าสต็อก หรือ ตัดสต็อกเพิ่ม ---
        $quantity_diff = $old_quantity - $new_quantity; // คำนวณผลต่างของจำนวนเก่า-ใหม่
        $stmt2 = $conn->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
        $stmt2->bind_param("ii", $quantity_diff, $product_id);
        $stmt2->execute();
        $stmt2->close();
        
        // --- ขั้นตอนที่ 3: คำนวณยอดเงินรวมของทั้งออเดอร์ใหม่ทั้งหมด ---
        $total = 0;
        $stmt3 = $conn->prepare("SELECT price, quantity FROM order_items WHERE order_id = ?");
        $stmt3->bind_param("i", $order_id);
        $stmt3->execute();
        $result = $stmt3->get_result();
        while($item = $result->fetch_assoc()) {
            $total += $item['price'] * $item['quantity'];
        }
        $stmt3->close();

        // --- ขั้นตอนที่ 4: อัปเดตยอดเงินรวมใหม่ในตาราง orders ---
        $stmt4 = $conn->prepare("UPDATE orders SET total = ? WHERE id = ?");
        $stmt4->bind_param("di", $total, $order_id);
        $stmt4->execute();
        $stmt4->close();
        
        $conn->commit(); // ยืนยันการเปลี่ยนแปลงทั้งหมดเมื่อทุกอย่างสำเร็จ
        
    } catch (Exception $e) {
        $conn->rollback(); // หากมีข้อผิดพลาดเกิดขึ้น ให้ยกเลิกการกระทำทั้งหมด
        die("เกิดข้อผิดพลาด: " . $e->getMessage());
    }
    
    // กลับไปหน้าเดิม
    header("Location: order_details.php?id=" . $order_id);
    exit();
}
?>