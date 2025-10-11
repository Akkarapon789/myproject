<?php
session_start();
include '../config/connectdb.php';
include 'header.php'; // <-- เรียกใช้ส่วนหัว (ถูกต้องแล้ว)


// ✅ ลบสินค้า (เมื่อยืนยันแล้ว)
if (isset($_GET['delete'])) {
    // ... โค้ดส่วนนี้เหมือนเดิม ...
    $id = $_GET['delete'];
    $stmt_img = $conn->prepare("SELECT image_url FROM products WHERE id = ?");
    $stmt_img->bind_param("i", $id);
    $stmt_img->execute();
    $result_img = $stmt_img->get_result()->fetch_assoc();
    if ($result_img && !empty($result_img['image_url'])) {
        $image_path = '../' . $result_img['image_url'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    $stmt_img->close();
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "ลบสินค้าสำเร็จ!";
    } else {
        $_SESSION['error'] = "ไม่สามารถลบสินค้าได้: " . $stmt->error;
    }
    $stmt->close();
    header("Location: products.php");
    exit();
}

// [แก้ไข] ดึงข้อมูลสินค้าทั้งหมด
$sql = "SELECT p.*, c.title AS category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.id DESC";
$result = $conn->query($sql);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>📦 จัดการสินค้า</h3>
    <a href="add_product.php" class="btn btn-primary">➕ เพิ่มสินค้าใหม่</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <table id="productTable" class="table table-striped table-bordered align-middle">
            <thead class="table-primary">
                <tr class="text-center">
                    <th>#</th>
                    <th>รูปภาพ</th>
                    <th>ชื่อสินค้า</th>
                    <th>ราคา</th>
                    <th>หมวดหมู่</th>
                    <th>การจัดการ</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td class="text-center"><?= $row['id']; ?></td>
                    <td class="text-center">
                        <?php if (!empty($row['image_url'])): ?>
                            <img src="../<?= htmlspecialchars($row['image_url']); ?>" width="80" class="rounded">
                        <?php else: ?>
                            <span class="text-muted">ไม่มีภาพ</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['title']); ?></td>
                    <td class="text-end"><?= number_format($row['price'], 2); ?> ฿</td>
                    <td><?= htmlspecialchars($row['category_name']); ?></td>
                    <td class="text-center">
                        <a href="edit_product.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-warning">แก้ไข</a>
                        <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $row['id']; ?>)">ลบ</button>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() { $('#productTable').DataTable(); });
function confirmDelete(id) {
    Swal.fire({
        title: 'ยืนยันการลบ?',
        text: "คุณแน่ใจหรือไม่ว่าต้องการลบสินค้านี้!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ใช่, ลบเลย!',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'products.php?delete=' + id;
        }
    })
}
</script>

<?php
// ... โค้ด unset session ...
if (isset($_SESSION['success'])) unset($_SESSION['success']);
if (isset($_SESSION['error'])) unset($_SESSION['error']);

include 'footer.php'; // <-- เรียกใช้ส่วนท้าย (สำคัญมาก!)
?>