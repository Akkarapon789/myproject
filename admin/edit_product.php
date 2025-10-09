<?php
include '../config/connectdb.php';

$id = $_POST['id'];
$title = $_POST['title'];
$category_id = $_POST['category_id'];
$price = $_POST['price'];
$stock = $_POST['stock'];

$sql = "UPDATE products SET title=?, category_id=?, price=?, stock=?";
$params = [$title, $category_id, $price, $stock];
$types = "sidi";

if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $image = uniqid().'.'.$ext;
    move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/'.$image);
    $sql .= ", image=?";
    $types .= "s";
    $params[] = $image;
}

$sql .= " WHERE id=?";
$types .= "i";
$params[] = $id;

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();

header("Location: products.php");
exit();
?>
