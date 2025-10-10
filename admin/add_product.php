<?php
session_start();

// ตรวจสอบสิทธิ์ผู้ใช้ (ต้องเป็น admin)
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// เชื่อมต่อฐานข้อมูล
include '../config/connectdb.php'; 

// ดึงข้อมูลหมวดหมู่สำหรับ Dropdown
$categories_result = $conn->query("SELECT id, title FROM categories ORDER BY title ASC");

// ตรวจสอบว่า form ถูกส่งมาหรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // รับค่าจาก Form และป้องกัน Injection
    $title       = trim($_POST['title']);
    $category_id = intval($_POST['category_id']);
    $price       = floatval($_POST['price']);
    $stock       = intval($_POST['stock']);

    // --- จัดการอัปโหลดรูปภาพ ---
    $image_url = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/"; // โฟลเดอร์สำหรับเก็บรูปภาพ
        // สร้างโฟลเดอร์ถ้ายังไม่มี
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // ตั้งชื่อไฟล์ใหม่เพื่อป้องกันการซ้ำกัน
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image_name = "product_" . time() . rand(1000, 9999) . '.' . $ext;
        $target_file = $target_dir . $image_name;

        // อัปโหลดไฟล์
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            // เก็บ path ที่จะใช้แสดงผลในเว็บ
            $image_url = "uploads/" . $image_name; 
        } else {
            $_SESSION['error'] = "❌ ไม่สามารถอัปโหลดรูปภาพได้";
            header("Location: add_product.php"); // กลับไปหน้าฟอร์มเดิม
            exit();
        }
    } else {
        $_SESSION['error'] = "❌ กรุณาเลือกรูปภาพสินค้า";
        header("Location: add_product.php");
        exit();
    }

    // --- บันทึกข้อมูลลงฐานข้อมูล ---
    // ❌ BUG FIX: ลบคอลัมน์ `slug` ที่ไม่มีในฐานข้อมูลออก
    $sql = "INSERT INTO products (category_id, title, price, stock, image_url) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    // ❌ BUG FIX: แก้ไขประเภทข้อมูลใน bind_param ให้ตรงกับ query (ลบ 's' ของ slug ออก)
    // i = integer, s = string, d = double
    $stmt->bind_param("isdis", $category_id, $title, $price, $stock, $image_url);

    if ($stmt->execute()) {
        $_SESSION['success'] = "✅ เพิ่มสินค้า '" . htmlspecialchars($title) . "' เรียบร้อยแล้ว!";
    } else {
        $_SESSION['error'] = "❌ เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    header("Location: products.php"); // กลับไปหน้าแสดงรายการสินค้า
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มสินค้าใหม่</title>
    <style>
        body { font-family: 'Tahoma', sans-serif; background-color: #f4f7f6; margin: 0; padding: 2rem; }
        .container { max-width: 600px; margin: auto; background: #fff; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; margin-bottom: 1.5rem; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: bold; color: #555; }
        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 1rem;
        }
        input[type="file"] { padding: 0.5rem; }
        button {
            width: 100%;
            padding: 0.8rem;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h2>เพิ่มสินค้าใหม่</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="category_id">หมวดหมู่สินค้า:</label>
                <select id="category_id" name="category_id" required>
                    <option value="">-- กรุณาเลือกหมวดหมู่ --</option>
                    <?php
                    if ($categories_result->num_rows > 0) {
                        while ($row = $categories_result->fetch_assoc()) {
                            echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['title']) . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="title">ชื่อสินค้า:</label>
                <input type="text" id="title" name="title" required>
            </div>

            <div class="form-group">
                <label for="price">ราคา (บาท):</label>
                <input type="number" id="price" name="price" step="0.01" min="0" required>
            </div>

            <div class="form-group">
                <label for="stock">จำนวนคงคลัง (Stock):</label>
                <input type="number" id="stock" name="stock" min="0" required>
            </div>

            <div class="form-group">
                <label for="image">รูปภาพสินค้า:</label>
                <input type="file" id="image" name="image" accept="image/png, image/jpeg, image/gif" required>
            </div>

            <button type="submit">บันทึกสินค้า</button>
        </form>
    </div>
</body>
</html>