<?php
session_start();

// ✅ ตรวจสอบสิทธิ์ผู้ใช้
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

include '../config/connectdb.php';

// ✅ ตรวจสอบว่า form ถูกส่งมาหรือยัง
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // รับค่าจาก Form
    $title       = trim($_POST['title']);
    $category_id = intval($_POST['category_id']);
    $price       = floatval($_POST['price']);
    $stock       = intval($_POST['stock']);

    // ✅ สร้าง slug จากชื่อสินค้า (กันซ้ำ)
    $slug = strtolower(str_replace(' ', '-', $title));

    // ✅ จัดการอัปโหลดรูปภาพ
    $image_url = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image_name = time() . rand(1000, 9999) . '.' . $ext;
        $target_file = $target_dir . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            // ✅ เก็บ path ที่จะใช้เรียกในเว็บ
            $image_url = "uploads/" . $image_name;
        } else {
            $_SESSION['error'] = "❌ ไม่สามารถอัปโหลดรูปภาพได้";
            header("Location: products.php");
            exit();
        }
    }

    // ✅ ใช้ Prepared Statement ปลอดภัย
    $sql = "INSERT INTO products (category_id, title, slug, price, stock, image_url) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    // i = integer, s = string, d = double
    $stmt->bind_param("issdis", $category_id, $title, $slug, $price, $stock, $image_url);

    if ($stmt->execute()) {
        $_SESSION['success'] = "✅ เพิ่มสินค้าเรียบร้อยแล้ว!";
    } else {
        $_SESSION['error'] = "❌ เกิดข้อผิดพลาด: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    header("Location: products.php");
    exit();
}
?>

<!-- ✅ ฟอร์มเพิ่มสินค้า -->
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มสินค้า</title>
    <style>
        body { font-family: sans-serif; background: #f8f9fa; padding: 20px; }
        form {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            width: 400px; margin: auto;
        }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input, select {
            width: 100%; padding: 8px; margin-top: 5px;
            border: 1px solid #ccc; border-radius: 6px;
        }
        button {
            background-color: #2155CD; color: white;
            border: none; padding: 10px; margin-top: 15px;
            width: 100%; border-radius: 8px; font-size: 16px;
            cursor: pointer; transition: background 0.3s;
        }
        button:hover { background-color: #1a47a0; }
    </style>
</head>
<body>
    <h2 align="center">เพิ่มสินค้าใหม่</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>หมวดหมู่ (category_id):</label>
        <input type="number" name="category_id" required>

        <label>ชื่อสินค้า:</label>
        <input type="text" name="title" required>

        <label>ราคา:</label>
        <input type="number" name="price" step="0.01" required>

        <label>จำนวนคงเหลือ (Stock):</label>
        <input type="number" name="stock" required>

        <label>รูปภาพสินค้า:</label>
        <input type="file" name="image" accept="image/*" required>

        <button type="submit">เพิ่มสินค้า</button>
    </form>
</body>
</html>