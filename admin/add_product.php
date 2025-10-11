<?php
session_start();
include '../config/connectdb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $category_id = $_POST['category_id'] ?? '';
    $title       = $_POST['title']       ?? '';
    $price       = $_POST['price']       ?? 0;
    $stock       = $_POST['stock']       ?? 0;
    
    // [สำคัญ] สร้าง Slug อัตโนมัติจากชื่อสินค้า
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

    $image_name = ''; // กำหนดค่าเริ่มต้น

    // จัดการการอัปโหลดรูปภาพ
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "../uploads/";
        $fileName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFilePath = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            $image_name = $fileName; // บันทึกแค่ชื่อไฟล์
        } else {
            $_SESSION['error'] = "เกิดข้อผิดพลาดในการอัปโหลดไฟล์ภาพ!";
            header("Location: add_product.php");
            exit;
        }
    }

    // บันทึกข้อมูลลงฐานข้อมูล (รวม slug ด้วย)
    $sql = "INSERT INTO products (category_id, title, slug, price, stock, image_url) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issdis", $category_id, $title, $slug, $price, $stock, $image_name);

    if ($stmt->execute()) {
        $_SESSION['success'] = "เพิ่มสินค้า '$title' สำเร็จแล้ว!";
        header("Location: products.php");
        exit;
    } else {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $stmt->error;
        header("Location: add_product.php");
        exit;
    }
}

// ดึงข้อมูลหมวดหมู่สำหรับ Dropdown
$categories_result = $conn->query("SELECT * FROM categories ORDER BY title ASC");
?>

<!DOCTYPE html>