<?php
// add_product.php (Upgraded for Image Upload)
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}
include '../config/connectdb.php';

$title       = $_POST['title'];
$category_id = $_POST['category_id'];
$price       = $_POST['price'];
$stock       = $_POST['stock'];
$image_name  = ''; // กำหนดค่าเริ่มต้นเป็นค่าว่าง

// --- ส่วนจัดการการอัปโหลดรูปภาพ ---
if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
    $upload_dir = '../uploads/';
    // สร้างชื่อไฟล์ใหม่ที่ไม่ซ้ำกัน โดยใช้ timestamp + ชื่อไฟล์เดิม
    $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $image_name = time() . '_' . uniqid() . '.' . $file_extension;
    $target_file = $upload_dir . $image_name;

    // ย้ายไฟล์ที่อัปโหลดไปยังโฟลเดอร์ uploads
    if(!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)){
        // หากย้ายไฟล์ไม่สำเร็จ ให้ใช้ค่าว่างสำหรับ image_name
        $image_name = '';
        // (Optional) สามารถตั้งค่า session แจ้งเตือนข้อผิดพลาดได้
        $_SESSION['error'] = "ขออภัย, เกิดข้อผิดพลาดในการอัปโหลดไฟล์";
    }
}

// ใช้ Prepared Statement เพื่อบันทึกข้อมูล
$sql = "INSERT INTO products (title, category_id, price, stock, image_url) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sidis", $title, $category_id, $price, $stock, $image_name);

if($stmt->execute()){
    $_SESSION['success'] = "เพิ่มสินค้าเรียบร้อยแล้ว!";
} else {
    $_SESSION['error'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $stmt->error;
}

$stmt->close();
$conn->close();
header("Location: products.php");
exit();
?>