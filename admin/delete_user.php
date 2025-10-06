<?php
include '../config/connectdb.php';
$id = $_GET['id'];
$conn->query("DELETE FROM user WHERE id=$id");
header("Location: users.php");
exit();
?>