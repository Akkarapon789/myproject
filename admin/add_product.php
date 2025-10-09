<?php
include '../config/connectdb.php';

$title = $_POST['title'];
$category_id = $_POST['category_id'];
$price = $_POST['price'];
$stock = $_POST['stock'];
$image = '';

if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $image = uniqid().'.'.$ext;
    move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/'.$image);
}

$sql = "INSERT INTO products (title, category_id, price, stock, image, created_at) 
        VALUES (?, ?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sidis", $title, $category_id, $price, $stock, $image);
$stmt->execute();

header("Location: products.php");
exit();
?>
