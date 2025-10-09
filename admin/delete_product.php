<?php
include '../config/connectdb.php';
$id = $_GET['id'];
// ดึงชื่อรูป
$res = $conn->query("SELECT image FROM products WHERE id=$id")->fetch_assoc();
if(!empty($res['image'])) unlink('../uploads/'.$res['image']);
$conn->query("DELETE FROM products WHERE id=$id");
header("Location: products.php");
exit();
?>
