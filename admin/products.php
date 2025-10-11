<?php
session_start();
include '../config/connectdb.php';

// ✅ ลบสินค้า
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // ดึงชื่อไฟล์รูปเก่า
    $stmt = $conn->prepare("SELECT image FROM products WHERE id=?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && !empty($row['image'])) {
        $imgPath = "../uploads/" . $row['image'];
        if (file_exists($imgPath)) unlink($imgPath);
    }

    // ลบข้อมูลใน DB
    $del = $conn->prepare("DELETE FROM products WHERE id=?");
    $del->execute([$id]);

    $_SESSION['success'] = "ลบสินค้าสำเร็จ!";
    header("Location: products.php");
    exit();
}

// ✅ ดึงข้อมูลสินค้า
$sql = "SELECT p.*, c.name AS category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.id DESC";
$products = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">📦 จัดการสินค้า</h4>
            <a href="add_product.php" class="btn btn-light btn-sm">➕ เพิ่มสินค้า</a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="productTable" class="table table-striped table-bordered align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>รูปภาพ</th>
                            <th>ชื่อสินค้า</th>
                            <th>หมวดหมู่</th>
                            <th>ราคา</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $index => $p): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td>
                                <?php if (!empty($p['image'])): ?>
                                    <img src="../uploads/<?= htmlspecialchars($p['image']) ?>" alt="img" width="70" class="rounded">
                                <?php else: ?>
                                    <span class="text-muted">ไม่มีรูป</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($p['title']) ?></td>
                            <td><?= htmlspecialchars($p['category_name'] ?? '-') ?></td>
                            <td><?= number_format($p['price'], 2) ?> ฿</td>
                            <td>
                                <a href="edit_product.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">แก้ไข</a>
                                <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $p['id'] ?>" data-name="<?= htmlspecialchars($p['title']) ?>">ลบ</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ✅ SweetAlert แจ้งเตือนสำเร็จ -->
<?php if (isset($_SESSION['success'])): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'สำเร็จ!',
    text: '<?= addslashes($_SESSION['success']) ?>',
    showConfirmButton: false,
    timer: 1800
});
</script>
<?php unset($_SESSION['success']); endif; ?>

<!-- ✅ DataTable & SweetAlert Delete Confirm -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // 🧾 DataTable
    $('#productTable').DataTable({
        language: {
            search: "ค้นหา:",
            lengthMenu: "แสดง _MENU_ รายการ",
            info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
            paginate: { previous: "ก่อนหน้า", next: "ถัดไป" }
        },
        pageLength: 10
    });

    // 🗑️ SweetAlert ยืนยันก่อนลบ
    $('.btn-delete').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');

        Swal.fire({
            title: `ลบสินค้า: ${name}?`,
            text: "การลบนี้ไม่สามารถย้อนกลับได้!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ลบเลย',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = 'products.php?delete=' + id;
            }
        });
    });
});
</script>

</body>
</html>