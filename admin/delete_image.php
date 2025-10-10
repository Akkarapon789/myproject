<?php
include '../config/connectdb.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // ดึงชื่อไฟล์
    $q = $conn->query("SELECT image_url FROM product_images WHERE id=$id");
    if ($q->num_rows > 0) {
        $img = $q->fetch_assoc();
        $path = "../assets/product/" . $img['image_url'];
        if (file_exists($path)) unlink($path);

        // ลบข้อมูลออกจากฐานข้อมูล
        $conn->query("DELETE FROM product_images WHERE id=$id");
        echo "success";
        exit;
    }
}
echo "error";
