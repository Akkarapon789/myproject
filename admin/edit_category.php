<?php
session_start();
include '../config/connectdb.php';

$id_to_edit = isset($_GET['id']) ? intval($_GET['id']) : 0;

// --- ส่วนของการอัปเดตข้อมูล ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $current_image_url = $_POST['current_image_url'] ?? null;
    $image_url = $current_image_url;

    // --- จัดการรูปภาพใหม่ (ถ้ามี) ---
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // ลบรูปเก่า (ถ้ามี)
        if (!empty($current_image_url) && file_exists('../' . $current_image_url)) {
            unlink('../' . $current_image_url);
        }
        // อัปโหลดรูปใหม่
        $targetDir = "../uploads/";
        $fileName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            $image_url = "uploads/" . $fileName;
        }
    }

    // --- อัปเดตฐานข้อมูล ---
    $stmt = $conn->prepare("UPDATE categories SET title=?, description=?, image_url=? WHERE id=?");
    $stmt->bind_param("sssi", $title, $description, $image_url, $id_to_edit);
    if ($stmt->execute()) {
        $_SESSION['success'] = "แก้ไขหมวดหมู่ '$title' สำเร็จ!";
        header("Location: categories.php");
        exit();
    } else {
        $_SESSION['error'] = "เกิดข้อผิดพลาด: " . $stmt->error;
    }
    $stmt->close();
}

// --- ดึงข้อมูลเดิมมาแสดงในฟอร์ม ---
$stmt_select = $conn->prepare("SELECT * FROM categories WHERE id = ?");
$stmt_select->bind_param("i", $id_to_edit);
$stmt_select->execute();
$category = $stmt_select->get_result()->fetch_assoc();
$stmt_select->close();

if (!$category) {
    echo "ไม่พบหมวดหมู่!"; exit;
}
include 'header.php';
?>

<h1 class="h3 mb-4 text-gray-800">✏️ แก้ไขหมวดหมู่: <?= htmlspecialchars($category['title']) ?></h1>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form action="edit_category.php?id=<?= $id_to_edit ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="current_image_url" value="<?= htmlspecialchars($category['image_url']) ?>">
            <div class="mb-3">
                <label for="title" class="form-label">ชื่อหมวดหมู่</label>
                <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($category['title']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">คำอธิบาย</label>
                <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($category['description']) ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">รูปภาพปัจจุบัน</label><br>
                <img src="../<?= htmlspecialchars($category['image_url'] ?? 'default.jpg') ?>" width="150" class="img-thumbnail">
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">อัปโหลดรูปภาพใหม่ (หากต้องการเปลี่ยน)</label>
                <input class="form-control" type="file" id="image" name="image" accept="image/*">
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="categories.php" class="btn btn-secondary">ยกเลิก</a>
                <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
            </div>
        </form>
    </div>
</div>
<?php include 'footer.php'; ?>