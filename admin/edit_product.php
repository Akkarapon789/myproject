<?php
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

$image_sql_part = "";
$params = [$title, $category_id, $price, $stock];
$types = "sidi";

// ตรวจสอบและจัดการรูปภาพใหม่
if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
    // ลบรูปเก่าก่อน
    $stmt = $conn->prepare("SELECT image_url FROM products WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    if(!empty($res['image_url']) && file_exists('../uploads/'.$res['image_url'])){
        unlink('../uploads/'.$res['image_url']);
    }
    $stmt->close();

    // อัปโหลดรูปใหม่
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $image_name = time().rand(1000,9999).'.'.$ext;
    move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/'.$image_name);

    $image_sql_part = ", image_url=?";
    $params[] = $image_name;
    $types .= "s";
}

$params[] = $id;
$types .= "i";

$sql = "UPDATE products SET title=?, category_id=?, price=?, stock=? $image_sql_part WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);

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