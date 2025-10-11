<?php
session_start();
include '../config/connectdb.php';

// ✅ ลบสินค้า (เมื่อยืนยันแล้ว)
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    if (!$stmt) {
        die("SQL Prepare Error: " . $conn->error);
    }

    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "ลบสินค้าสำเร็จ!";
    } else {
        $_SESSION['error'] = "ไม่สามารถลบสินค้าได้: " . $stmt->error;
    }

    header("Location: products.php");
    exit();
}

// ✅ ดึงข้อมูลสินค้าทั้งหมด
$sql = "SELECT * FROM products ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการสินค้า</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">

<div class="container py-4">
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
                        <th>ชื่อสินค้า</th>
                        <th>ราคา</th>
                        <th>หมวดหมู่</th>
                        <th>รูปภาพ</th>
                        <th>การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="text-center"><?= $row['id']; ?></td>
                        <td><?= htmlspecialchars($row['title']); ?></td>
                        <td class="text-end"><?= number_format($row['price'], 2); ?> ฿</td>
                        <td><?= htmlspecialchars($row['category']); ?></td>
                        <td class="text-center">
                            <?php if (!empty($row['image'])): ?>
                                <img src="../uploads/<?= htmlspecialchars($row['image']); ?>" width="80" class="rounded">
                            <?php else: ?>
                                <span class="text-muted">ไม่มีภาพ</span>
                            <?php endif; ?>
                        </td>
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
</div>

<!-- ✅ Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#productTable').DataTable({
        "language": {
            "lengthMenu": "แสดง _MENU_ รายการต่อหน้า",
            "zeroRecords": "ไม่พบข้อมูล",
            "info": "แสดงหน้า _PAGE_ จาก _PAGES_",
            "infoEmpty": "ไม่มีข้อมูล",
            "search": "ค้นหา:",
            "paginate": {
                "first": "หน้าแรก",
                "last": "หน้าสุดท้าย",
                "next": "ถัดไป",
                "previous": "ก่อนหน้า"
            }
        }
    });
});

// ✅ SweetAlert ยืนยันก่อนลบ
function confirmDelete(id) {
    Swal.fire({
        title: "คุณแน่ใจหรือไม่?",
        text: "หากลบแล้วจะไม่สามารถกู้คืนได้!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "ลบเลย",
        cancelButtonText: "ยกเลิก",
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location = "products.php?delete=" + id;
        }
    });
}
</script>

<!-- ✅ SweetAlert แจ้งเตือน -->
<?php if (isset($_SESSION['success'])): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'สำเร็จ!',
    text: '<?= $_SESSION['success']; ?>',
    timer: 1500,
    showConfirmButton: false
});
</script>
<?php unset($_SESSION['success']); endif; ?>

<?php if (isset($_SESSION['error'])): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'เกิดข้อผิดพลาด!',
    text: '<?= $_SESSION['error']; ?>'
});
</script>
<?php unset($_SESSION['error']); endif; ?>

</body>
</html>