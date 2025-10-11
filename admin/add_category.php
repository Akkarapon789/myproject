<?php
session_start();
include '../config/connectdb.php';

// --- ส่วนของการบันทึกข้อมูล ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $image_url = null; // กำหนดค่าเริ่มต้นเป็น null

    // --- จัดการการอัปโหลดรูปภาพ ---
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "../uploads/";
        $fileName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFilePath = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            $image_url = "uploads/" . $fileName; // เก็บ Path แบบเต็ม
        }
    }

    // --- บันทึกลงฐานข้อมูล ---
    $stmt = $conn->prepare("INSERT INTO categories (title, description, image_url) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $description, $image_url);

    if ($stmt->execute()) {
        $_SESSION['success'] = "เพิ่มหมวดหมู่ '$title' สำเร็จ!";
        header("Location: categories.php");
        exit();
    } else {
        $_SESSION['error'] = "เกิดข้อผิดพลาด: " . $stmt->error;
    }
    $stmt->close();
}
include 'header.php';
?>

<h1 class="h3 mb-4 text-gray-800">➕ เพิ่มหมวดหมู่ใหม่</h1>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form action="add_category.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">ชื่อหมวดหมู่</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">คำอธิบาย</label>
                <textarea class="form-control" id="description" name="description" rows="4"></textarea>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">รูปภาพหมวดหมู่</label>
                <input class="form-control" type="file" id="image" name="image" accept="image/*">
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="categories.php" class="btn btn-secondary">ยกเลิก</a>
                <button type="submit" class="btn btn-primary">บันทึก</button>
            </div>
        </form>
    </div>
</div>
<?php include 'footer.php'; ?>