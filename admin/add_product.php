<?php
//--- STEP 1: SETUP & ERROR REPORTING ---
// เปิดการแสดงผลข้อผิดพลาดทั้งหมดบนหน้าจอ (สำคัญมากสำหรับการ Debug)
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

//--- STEP 2: SECURITY CHECK ---
// ตรวจสอบสิทธิ์ผู้ใช้ (ต้องเป็น admin เท่านั้น)
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    // ถ้าไม่ใช่ admin ให้ส่งกลับไปหน้า login พร้อมข้อความแจ้งเตือน
    $_SESSION['error'] = 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้';
    header("Location: ../auth/login.php");
    exit(); // หยุดการทำงานของสคริปต์ทันที
}

//--- STEP 3: DATABASE CONNECTION ---
// เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล
include '../config/connectdb.php';

// ตรวจสอบว่าการเชื่อมต่อสำเร็จหรือไม่
if ($conn->connect_error) {
    // หากเชื่อมต่อไม่ได้ ให้หยุดทำงานและแสดงข้อผิดพลาด
    die("Connection failed: " . $conn->connect_error);
}

//--- STEP 4: FORM SUBMISSION HANDLING ---
// ตรวจสอบว่ามีการส่งข้อมูลมาจากฟอร์มด้วยเมธอด POST หรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //--- 4.1: รับค่าและทำความสะอาดข้อมูล ---
    // trim() ตัดช่องว่าง, intval() แปลงเป็นเลขจำนวนเต็ม, floatval() แปลงเป็นเลขทศนิยม
    $title       = isset($_POST['title']) ? trim($_POST['title']) : '';
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
    $price       = isset($_POST['price']) ? floatval($_POST['price']) : 0;
    $stock       = isset($_POST['stock']) ? intval($_POST['stock']) : 0;

    // ตรวจสอบข้อมูลพื้นฐานว่าครบถ้วนหรือไม่
    if (empty($title) || $category_id <= 0 || $price < 0 || $stock < 0) {
        $_SESSION['error'] = 'กรุณากรอกข้อมูลสินค้าให้ครบถ้วนและถูกต้อง';
        header("Location: products.php");
        exit();
    }

    // สร้าง slug ที่ปลอดภัยสำหรับ URL จากชื่อสินค้า
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

    //--- 4.2: จัดการการอัปโหลดรูปภาพอย่างปลอดภัย ---
    $image_url = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        
        $target_dir = "../uploads/"; // โฟลเดอร์สำหรับเก็บไฟล์
        // ตรวจสอบและสร้างโฟลเดอร์หากยังไม่มี
        if (!is_dir($target_dir)) {
            if (!mkdir($target_dir, 0755, true)) {
                $_SESSION['error'] = 'ไม่สามารถสร้างโฟลเดอร์สำหรับอัปโหลดได้';
                header("Location: products.php");
                exit();
            }
        }

        $image_name = $_FILES['image']['name'];
        $tmp_name = $_FILES['image']['tmp_name'];
        $image_size = $_FILES['image']['size'];
        
        // ตรวจสอบประเภทไฟล์ที่อนุญาต (MIME Type)
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = mime_content_type($tmp_name);
        if (!in_array($file_type, $allowed_types)) {
            $_SESSION['error'] = 'อนุญาตให้อัปโหลดเฉพาะไฟล์รูปภาพ (JPG, PNG, GIF) เท่านั้น';
            header("Location: products.php");
            exit();
        }

        // ตรวจสอบขนาดไฟล์ (ไม่เกิน 2MB)
        if ($image_size > 2 * 1024 * 1024) {
            $_SESSION['error'] = 'ขนาดไฟล์รูปภาพต้องไม่เกิน 2 MB';
            header("Location: products.php");
            exit();
        }

        // สร้างชื่อไฟล์ใหม่ที่ไม่ซ้ำกันเพื่อป้องกันการเขียนทับ
        $ext = pathinfo($image_name, PATHINFO_EXTENSION);
        $new_image_name = "product_" . time() . uniqid() . '.' . $ext;
        $target_file = $target_dir . $new_image_name;

        // ย้ายไฟล์ไปยังโฟลเดอร์เป้าหมาย
        if (move_uploaded_file($tmp_name, $target_file)) {
            // เก็บ Path ที่จะใช้บันทึกลง DB
            $image_url = "uploads/" . $new_image_name;
        } else {
            $_SESSION['error'] = 'เกิดข้อผิดพลาดระหว่างการอัปโหลดไฟล์';
            header("Location: products.php");
            exit();
        }
    } else {
        // กรณีไม่เลือกไฟล์ หรือไฟล์มีปัญหา
        $_SESSION['error'] = 'กรุณาเลือกรูปภาพสินค้า';
        header("Location: products.php");
        exit();
    }

    //--- 4.3: บันทึกข้อมูลลงฐานข้อมูลด้วย Prepared Statement ---
    // ใช้ Prepared Statement เพื่อป้องกัน SQL Injection
    $sql = "INSERT INTO products (category_id, title, slug, price, stock, image_url) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // ตรวจสอบว่า prepare statement สำเร็จหรือไม่
    if ($stmt === false) {
        $_SESSION['error'] = 'SQL Error: ' . $conn->error;
        header("Location: products.php");
        exit();
    }
    
    // ผูกตัวแปรกับ placeholder ใน SQL
    // i = integer, s = string, d = double
    $stmt->bind_param("issdis", $category_id, $title, $slug, $price, $stock, $image_url);

    // สั่งให้ statement ทำงาน
    if ($stmt->execute()) {
        $_SESSION['success'] = "เพิ่มสินค้า '" . htmlspecialchars($title) . "' เรียบร้อยแล้ว!";
    } else {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $stmt->error;
    }

    // ปิด statement และ connection
    $stmt->close();
    $conn->close();

    //--- 4.4: Redirect กลับไปหน้าแสดงรายการสินค้า ---
    header("Location: products.php");
    exit(); // สำคัญมาก: ต้อง exit() ทุกครั้งหลัง header()

} else {
    // ถ้าไม่ได้เข้ามาผ่านฟอร์ม POST ให้ส่งกลับไปหน้าหลัก
    header("Location: products.php");
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