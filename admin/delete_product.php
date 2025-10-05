<?php
include '../config/connectdb.php';
$id = $_GET['id'];
$conn->query("DELETE FROM products WHERE id=$id");
header("Location: products.php");
exit();
?>
