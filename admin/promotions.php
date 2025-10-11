<?php
session_start();
include '../config/connectdb.php';
include 'header.php';

// --- ส่วนของการลบข้อมูล ---
if (isset($_GET['delete'])) {
    $id_to_delete = intval($_GET['delete']);
    $stmt_delete = $conn->prepare("DELETE FROM promotions WHERE id = ?");
    $stmt_delete->bind_param("i", $id_to_delete);
    if ($stmt_delete->execute()) {
        $_SESSION['success'] = "ลบโปรโมชั่นสำเร็จ!";
    } else {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบ: " . $stmt_delete->error;
    }
    $stmt_delete->close();
    header("Location: promotions.php");
    exit();
}

// --- ดึงข้อมูลโปรโมชั่นทั้งหมด ---
$result = $conn->query("SELECT pr.*, p.title AS product_title FROM promotions pr LEFT JOIN products p ON pr.product_id = p.id ORDER BY pr.id DESC");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">✨ จัดการโปรโมชั่น</h1>
    <a href="add_promotion.php" class="btn btn-primary">
        <i class="fas fa-plus fa-sm me-2"></i>เพิ่มโปรโมชั่นใหม่
    </a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">รายการโปรโมชั่นทั้งหมด</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ชื่อโปรโมชั่น</th>
                        <th>ส่วนลด</th>
                        <th>ใช้ได้ถึง</th>
                        <th>สำหรับสินค้า</th>
                        <th class="text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td>
                            <?php 
                                echo ($row['discount_type'] == 'percentage') 
                                ? $row['discount_value'] . '%' 
                                : '฿' . number_format($row['discount_value']);
                            ?>
                        </td>
                        <td><?= date('d M Y', strtotime($row['end_date'])) ?></td>
                        <td><?= htmlspecialchars($row['product_title'] ?? 'ทุกชิ้นที่ร่วมรายการ') ?></td>
                        <td class="text-center">
                            <a href="edit_promotion.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> แก้ไข</a>
                            <a href="promotions.php?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('ยืนยันการลบโปรโมชั่นนี้?')"><i class="fas fa-trash"></i> ลบ</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>