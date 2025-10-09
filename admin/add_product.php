<?php
include '../config/connectdb.php';

$title = $_POST['title'];
$category_id = $_POST['category_id'];
$price = $_POST['price'];
$stock = $_POST['stock'];

$image_name = '';
if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $image_name = time().rand(1000,9999).'.'.$ext;
    move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/'.$image_name);
}

$sql = "INSERT INTO products (title, category_id, price, stock, image) 
        VALUES ('$title', '$category_id', '$price', '$stock', '$image_name')";

if($conn->query($sql)){
    header("Location: products.php");
    exit();
}else{
    echo "Error: ".$conn->error;
}
?>
