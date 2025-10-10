<?php
// delete_user.php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}
include '../config/connectdb.php';

$user_id_to_delete = $_GET['id'] ?? 0;

// --- ป้องกันแอดมินลบบัญชีตัวเอง ---
// ตรวจสอบว่ามี user_id ใน session หรือไม่ก่อนใช้งาน
if (isset($_SESSION['user_id']) && $user_id_to_delete == $_SESSION['user_id']) {
    echo "<script>
            alert('ไม่สามารถลบบัญชีของตัวเองได้!');
            window.location.href = 'users.php';
          </script>";
    exit();
}


// ใช้ Prepared Statement เพื่อความปลอดภัยสูงสุด
$sql = "DELETE FROM user WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id_to_delete);

if ($stmt->execute()) {
    // สามารถตั้งค่า session เพื่อแสดงข้อความแจ้งเตือนที่หน้า users.php ได้
    $_SESSION['success_message'] = "ลบผู้ใช้งานเรียบร้อยแล้ว";
} else {
    $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการลบ: " . $stmt->error;
}

$stmt->close();
$conn->close();

// กลับไปหน้า users.php
header("Location: users.php");
exit();
?>