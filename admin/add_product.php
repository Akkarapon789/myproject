<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}
include '../config/connectdb.php';

// รับค่าจาก Form
$title       = $_POST['title'];
$category_id = $_POST['category_id'];
$price       = $_POST['price'];
$stock       = $_POST['stock'];

$image_name = '';
if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $image_name = time().rand(1000,9999).'.'.$ext;
    move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/'.$image_name);
}

// ใช้ Prepared Statement เพื่อความปลอดภัย
$sql = "INSERT INTO products (title, category_id, price, stock, image_url) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
// "sidis" คือ data types: s=string, i=integer, d=double
$stmt->bind_param("sidis", $title, $category_id, $price, $stock, $image_name);

if($stmt->execute()){
    $_SESSION['success'] = "เพิ่มสินค้าเรียบร้อยแล้ว!";
} else {
    $_SESSION['error'] = "เกิดข้อผิดพลาด: " . $stmt->error;
}

$stmt->close();
$conn->close();
header("Location: products.php");
exit();
?>