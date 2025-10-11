<?php
// edit_product.php (Upgraded for Image Upload)
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}
include '../config/connectdb.php';

$id          = $_POST['id'];
$title       = $_POST['title'];
$category_id = $_POST['category_id'];
$price       = $_POST['price'];
$stock       = $_POST['stock'];

// --- ส่วนจัดการการอัปโหลดรูปภาพใหม่ ---
if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
    // 1. ดึงชื่อไฟล์รูปเก่าจากฐานข้อมูลเพื่อนำมาลบ
    $stmt_old_img = $conn->prepare("SELECT image_url FROM products WHERE id=?");
    $stmt_old_img->bind_param("i", $id);
    $stmt_old_img->execute();
    $result = $stmt_old_img->get_result();
    $old_data = $result->fetch_assoc();
    $old_image_name = $old_data['image_url'];
    $stmt_old_img->close();

    // 2. อัปโหลดไฟล์รูปใหม่
    $upload_dir = '../uploads/';
    $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $new_image_name = time() . '_' . uniqid() . '.' . $file_extension;
    $target_file = $upload_dir . $new_image_name;

    if(move_uploaded_file($_FILES['image']['tmp_name'], $target_file)){
        // 3. ถ้าอัปโหลดรูปใหม่สำเร็จ ให้ลบรูปเก่า (ถ้ามี)
        if(!empty($old_image_name) && file_exists($upload_dir . $old_image_name)){
            unlink($upload_dir . $old_image_name);
        }

        // 4. อัปเดตฐานข้อมูลด้วย "รูปใหม่"
        $sql = "UPDATE products SET title=?, category_id=?, price=?, stock=?, image_url=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sidisi", $title, $category_id, $price, $stock, $new_image_name, $id);

    } else {
        // หากอัปโหลดรูปใหม่ไม่สำเร็จ ให้อัปเดตเฉพาะข้อมูลอื่น ๆ (ไม่เปลี่ยนรูป)
        $sql = "UPDATE products SET title=?, category_id=?, price=?, stock=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sidis", $title, $category_id, $price, $stock, $id);
    }
} else {
    // ถ้าไม่มีการอัปโหลดรูปใหม่ ให้อัปเดตเฉพาะข้อมูลอื่น ๆ
    $sql = "UPDATE products SET title=?, category_id=?, price=?, stock=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sidi", $title, $category_id, $price, $stock, $id);
}

// Execute the query
if($stmt->execute()){
    $_SESSION['success'] = "อัปเดตข้อมูลสินค้าเรียบร้อย!";
} else {
    $_SESSION['error'] = "เกิดข้อผิดพลาด: " . $stmt->error;
}
$stmt->close();
$conn->close();
header("Location: products.php");
exit();
?>