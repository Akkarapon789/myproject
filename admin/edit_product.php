<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

include '../config/connectdb.php';

// รับค่าจาก Form
$id          = $_POST['id'];
$title       = $_POST['title'];
$category_id = $_POST['category_id'];
$price       = $_POST['price'];
$stock       = $_POST['stock'];

// ตรวจสอบว่ามีรูปใหม่หรือไม่
$image_sql = '';
$image_name = '';
if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
    // ลบรูปเก่าก่อน
    $res = $conn->query("SELECT image_url FROM products WHERE id=$id")->fetch_assoc();
    if(!empty($res['image_url']) && file_exists('../uploads/'.$res['image_url'])){
        unlink('../uploads/'.$res['image_url']);
    }

    // อัปโหลดรูปใหม่
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $image_name = time().rand(1000,9999).'.'.$ext;
    move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/'.$image_name);

    $image_sql = ", image_url='$image_name'";
}

// Update ข้อมูล
$sql = "UPDATE products SET title='$title', category_id='$category_id', price='$price', stock='$stock' $image_sql WHERE id=$id";

if($conn->query($sql)){
    header("Location: products.php");
    exit();
}else{
    echo "เกิดข้อผิดพลาด: ".$conn->error;
}
?>
