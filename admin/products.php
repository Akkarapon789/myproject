<?php
session_start();
include '../config/connectdb.php';
include 'header.php';


// ✅ ลบสินค้า (เมื่อยืนยันแล้ว)
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // ดึงชื่อไฟล์รูปภาพก่อนลบข้อมูล เพื่อนำไปลบไฟล์จริง
    $stmt_img = $conn->prepare("SELECT image_url FROM products WHERE id = ?");
    $stmt_img->bind_param("i", $id);
    $stmt_img->execute();
    $result_img = $stmt_img->get_result()->fetch_assoc();
    if ($result_img && !empty($result_img['image_url'])) {
        $image_path = '../' . $result_img['image_url'];
        if (file_exists($image_path)) {
            unlink($image_path); // ลบไฟล์รูปภาพจริง
        }
    }
    $stmt_img->close();

    // ลบข้อมูลออกจากฐานข้อมูล
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

// [แก้ไข] ดึงข้อมูลสินค้าทั้งหมด พร้อม JOIN ตาราง categories เพื่อเอาชื่อหมวดหมู่มาด้วย
$sql = "SELECT p.*, c.title AS category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.id DESC";
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
</div>


<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
// JavaScript (เหมือนเดิม)
$(document).ready(function() { $('#productTable').DataTable({ /* ... config ... */ }); });
function confirmDelete(id) { /* ... SweetAlert code ... */ }
</script>

<?php if (isset($_SESSION['success'])): ?>
    <?php unset($_SESSION['success']); endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <?php unset($_SESSION['error']); endif; ?>

</body>
</html>