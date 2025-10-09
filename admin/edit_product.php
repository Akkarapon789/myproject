<?php
include '../config/connectdb.php';

$id = $_POST['id'];
$title = $_POST['title'];
$category_id = $_POST['category_id'];
$price = $_POST['price'];
$stock = $_POST['stock'];

$image_sql = '';
if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $image_name = time().rand(1000,9999).'.'.$ext;
    move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/'.$image_name);
    $image_sql = ", image='$image_name'";
}

$sql = "UPDATE products SET title='$title', category_id='$category_id', price='$price', stock='$stock' $image_sql WHERE id=$id";

if($conn->query($sql)){
    header("Location: products.php");
    exit();
}else{
    echo "Error: ".$conn->error;
}
?>
