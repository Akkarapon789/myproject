<?php
session_start();
include '../config/connectdb.php';
include 'header.php';

// --- ส่วนของการลบข้อมูล ---
if (isset($_GET['delete'])) {
    $id_to_delete = intval($_GET['delete']);

    // 1. ดึง URL รูปภาพเก่าเพื่อนำไปลบไฟล์ออกจาก server
    $stmt_img = $conn->prepare("SELECT image_url FROM categories WHERE id = ?");
    $stmt_img->bind_param("i", $id_to_delete);
    $stmt_img->execute();
    $result_img = $stmt_img->get_result()->fetch_assoc();
    if ($result_img && !empty($result_img['image_url'])) {
        if (file_exists('../' . $result_img['image_url'])) {
            unlink('../' . $result_img['image_url']);
        }
    }
    $stmt_img->close();

    // 2. ลบข้อมูลออกจากฐานข้อมูล
    $stmt_delete = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt_delete->bind_param("i", $id_to_delete);
    if ($stmt_delete->execute()) {
        $_SESSION['success'] = "ลบหมวดหมู่สำเร็จ!";
    } else {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบ: " . $stmt_delete->error;
    }
    $stmt_delete->close();

    header("Location: categories.php"); // Redirect เพื่อล้างค่า GET
    exit();
}

// --- ดึงข้อมูลทั้งหมดมาแสดง ---
$result = $conn->query("SELECT * FROM categories ORDER BY id DESC");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">🏷️ จัดการหมวดหมู่สินค้า</h1>
    <a href="add_category.php" class="btn btn-primary">
        <i class="fas fa-plus fa-sm me-2"></i>เพิ่มหมวดหมู่ใหม่
    </a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">รายการหมวดหมู่ทั้งหมด</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="categoryTable" class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>รูปภาพ</th>
                        <th>ชื่อหมวดหมู่</th>
                        <th>คำอธิบาย</th>
                        <th class="text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td class="text-center">
                            <img src="../<?= htmlspecialchars($row['image_url'] ?? 'default.jpg') ?>" width="100" class="img-thumbnail">
                        </td>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars(substr($row['description'], 0, 100)) . '...' ?></td>
                        <td class="text-center">
                            <a href="edit_category.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> แก้ไข
                            </a>
                            <button onclick="confirmDelete(<?= $row['id'] ?>)" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i> ลบ
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#categoryTable').DataTable({"order": [[0, "desc"]]});
});

function confirmDelete(id) {
    Swal.fire({
        title: 'ยืนยันการลบ?',
        text: "ข้อมูลหมวดหมู่นี้จะถูกลบอย่างถาวร!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ใช่, ลบเลย!',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'categories.php?delete=' + id;
        }
    })
}
</script>

<?php include 'footer.php'; ?>