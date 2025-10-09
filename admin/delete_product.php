<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}
include '../config/connectdb.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 1. ดึงชื่อรูปเพื่อลบไฟล์ (ใช้ Prepared Statement)
    $stmt = $conn->prepare("SELECT image_url FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if (!empty($row['image_url']) && file_exists('../uploads/' . $row['image_url'])) {
            unlink('../uploads/' . $row['image_url']);
        }
    }
    $stmt->close();

    // 2. ลบข้อมูลออกจากฐานข้อมูล (ใช้ Prepared Statement)
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if($stmt->execute()){
        $_SESSION['success'] = "ลบสินค้าเรียบร้อยแล้ว!";
    } else {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบ";
    }
    $stmt->close();
}

$conn->close();
header("Location: products.php");
exit();
?>