<?php
include '../config/connectdb.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // ลบข้อมูลผู้ใช้
    $stmt = $conn->prepare("DELETE FROM user WHERE user_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // กลับไปหน้าหลัก
    header("Location: users.php");
    exit();
} else {
    echo "ไม่พบข้อมูลผู้ใช้ที่ต้องการลบ";
}
?>
