<?php
// admin/edit_product.php (Corrected & Final Version)
session_start();
include '../config/connectdb.php';

$id_to_edit = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_to_edit === 0) {
    die("ไม่ได้ระบุ ID สินค้า");
}

// --- ส่วนของการอัปเดตข้อมูล ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->begin_transaction();
    try {
        // 1. อัปเดตข้อมูล Text พื้นฐาน
        $title = $_POST['title'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];
        $category_id = $_POST['category_id'];
        // ⭐️ ป้องกัน Error ถ้าตารางไม่มีคอลัมน์ description ⭐️
        $description = isset($_POST['description']) ? $_POST['description'] : null;
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

        // เตรียม SQL โดยขึ้นอยู่กับว่ามี description หรือไม่
        if ($description !== null) {
            $stmt_update_prod = $conn->prepare("UPDATE products SET title=?, slug=?, price=?, stock=?, category_id=?, description=? WHERE id=?");
            $stmt_update_prod->bind_param("ssdiisi", $title, $slug, $price, $stock, $category_id, $description, $id_to_edit);
        } else {
            $stmt_update_prod = $conn->prepare("UPDATE products SET title=?, slug=?, price=?, stock=?, category_id=? WHERE id=?");
            $stmt_update_prod->bind_param("ssdiii", $title, $slug, $price, $stock, $category_id, $id_to_edit);
        }
        $stmt_update_prod->execute();
        $stmt_update_prod->close();

        // ⭐️⭐️⭐️ 2. แก้ไขจุดผิดพลาด: ตรวจสอบก่อนว่ามีการเลือกลบรูปภาพหรือไม่ ⭐️⭐️⭐️
        if (!empty($_POST['delete_images']) && is_array($_POST['delete_images'])) {
            $images_to_delete = $_POST['delete_images'];
            // สร้าง placeholder (?,?,?) สำหรับ prepared statement
            $placeholders = implode(',', array_fill(0, count($images_to_delete), '?'));
            $types = str_repeat('i', count($images_to_delete));

            // ดึง URL ของไฟล์ที่จะลบก่อน
            $result_urls = $conn->execute_query("SELECT image_url FROM product_images WHERE id IN ($placeholders)", $images_to_delete);
            while($row = $result_urls->fetch_assoc()) {
                if (file_exists('../' . $row['image_url'])) {
                    unlink('../' . $row['image_url']);
                }
            }
            
            // ลบข้อมูลออกจากฐานข้อมูลอย่างปลอดภัย
            $stmt_delete = $conn->prepare("DELETE FROM product_images WHERE id IN ($placeholders)");
            $stmt_delete->bind_param($types, ...$images_to_delete);
            $stmt_delete->execute();
            $stmt_delete->close();
        }

        // 3. จัดการการอัปโหลดรูปภาพใหม่ (เหมือนเดิม)
        if (isset($_FILES['new_images']) && !empty($_FILES['new_images']['name'][0])) {
            $stmt_images = $conn->prepare("INSERT INTO product_images (product_id, image_url) VALUES (?, ?)");
            foreach ($_FILES['new_images']['name'] as $i => $name) {
                if ($_FILES['new_images']['error'][$i] === UPLOAD_ERR_OK) {
                    $targetDir = "../uploads/";
                    $fileName = time() . "_" . basename($name);
                    $targetFilePath = $targetDir . $fileName;
                    if (move_uploaded_file($_FILES["new_images"]["tmp_name"][$i], $targetFilePath)) {
                        $image_path = "uploads/" . $fileName;
                        $stmt_images->bind_param("is", $id_to_edit, $image_path);
                        $stmt_images->execute();
                    }
                }
            }
            $stmt_images->close();
        }

        // 4. อัปเดตรูปภาพหลัก (Main Image) ให้เป็นรูปแรกในแกลเลอรีเสมอ
        $result_first_img = $conn->query("SELECT image_url FROM product_images WHERE product_id = $id_to_edit ORDER BY id ASC LIMIT 1");
        $new_main_image = ($result_first_img->num_rows > 0) ? $result_first_img->fetch_assoc()['image_url'] : null;
        
        $stmt_update_main_img = $conn->prepare("UPDATE products SET image_url = ? WHERE id = ?");
        $stmt_update_main_img->bind_param("si", $new_main_image, $id_to_edit);
        $stmt_update_main_img->execute();
        $stmt_update_main_img->close();

        $conn->commit();
        $_SESSION['success'] = "แก้ไขสินค้า '$title' สำเร็จ!";
        header("Location: products.php");
        exit();

    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        $error = "เกิดข้อผิดพลาด: " . $exception->getMessage();
    }
}

// --- ดึงข้อมูลเดิมมาแสดงในฟอร์ม (เหมือนเดิม) ---
// (โค้ดส่วนนี้ถูกต้องแล้ว ไม่ต้องแก้ไข)

include 'header.php'; // เรียก header หลังจาก Logic ทั้งหมด
?>