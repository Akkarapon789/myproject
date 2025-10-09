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
$title       = $_POST['title'];
$category_id = $_POST['category_id'];
$price       = $_POST['price'];
$stock       = $_POST['stock'];

// อัปโหลดรูป
$image_name = '';
if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $image_name = time().rand(1000,9999).'.'.$ext;
    move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/'.$image_name);
}

// Insert ลงฐานข้อมูล
$sql = "INSERT INTO products (title, category_id, price, stock, image_url) 
        VALUES ('$title', '$category_id', '$price', '$stock', '$image_name')";

if($conn->query($sql)){
    header("Location: products.php");
    exit();
}else{
    echo "เกิดข้อผิดพลาด: ".$conn->error;
}
?>
