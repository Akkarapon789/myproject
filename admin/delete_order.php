<?php
include '../connectdb.php';
$id = $_GET['id'];
$conn->query("DELETE FROM orders WHERE id=$id");
header("Location: orders.php");
exit();
?>
