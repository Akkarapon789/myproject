<?php
// order_details.php
include 'header.php';

$order_id = $_GET['id'] ?? 0;

// --- ส่วนจัดการอัปเดตสถานะ ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    
    // ใช้ Prepared Statement เพื่อความปลอดภัย
    $stmt_update = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt_update->bind_param("si", $new_status, $order_id);
    if ($stmt_update->execute()) {
        echo "<script>alert('อัปเดตสถานะเรียบร้อยแล้ว');</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการอัปเดต');</script>";
    }
    $stmt_update->close();
}


// --- ดึงข้อมูลหลักของ Order ---
$stmt_order = $conn->prepare(
    "SELECT o.*, u.firstname, u.lastname 
     FROM orders o 
     LEFT JOIN users u ON o.user_id = u.user_id 
     WHERE o.id = ?"
);
$stmt_order->bind_param("i", $order_id);
$stmt_order->execute();
$order = $stmt_order->get_result()->fetch_assoc();
$stmt_order->close();

if (!$order) {
    echo "ไม่พบคำสั่งซื้อ";
    exit;
}

// --- ดึงข้อมูลสินค้าใน Order ---
$stmt_items = $conn->prepare(
    "SELECT oi.*, p.title as product_title, p.image_url 
     FROM order_items oi
     JOIN products p ON oi.product_id = p.id
     WHERE oi.order_id = ?"
);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$order_items = $stmt_items->get_result();
$stmt_items->close();

?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">รายละเอียดคำสั่งซื้อ #<?= $order_id ?></h1>
    <a href="orders.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left fa-sm me-2"></i>กลับไปหน้ารายการ
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">สินค้าในคำสั่งซื้อ</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <?php while($item = $order_items->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <img src="../uploads/<?= htmlspecialchars($item['image_url']) ?>" width="60" class="img-thumbnail me-3">
                                </td>
                                <td>
                                    <?= htmlspecialchars($item['product_title']) ?><br>
                                    <small class="text-muted">จำนวน: <?= $item['quantity'] ?></small>
                                </td>
                                <td class="text-end">
                                    ฿<?= number_format($item['price'] * $item['quantity'], 2) ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td colspan="2" class="text-end">ยอดรวมสุทธิ:</td>
                                <td class="text-end">฿<?= number_format($order['total'], 2) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">ข้อมูลสรุป</h6>
            </div>
            <div class="card-body">
                <strong>ลูกค้า:</strong>
                <p><?= htmlspecialchars($order['fullname']) ?></p>
                
                <strong>ที่อยู่จัดส่ง:</strong>
                <p><?= nl2br(htmlspecialchars($order['address'])) ?></p>

                <strong>เบอร์โทรศัพท์:</strong>
                <p><?= htmlspecialchars($order['phone']) ?></p>
                
                <hr>

                <form action="order_details.php?id=<?= $order_id ?>" method="POST">
                    <div class="mb-3">
                        <label for="status" class="form-label fw-bold">สถานะคำสั่งซื้อ</label>
                        <select name="status" id="status" class="form-select">
                            <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Pending (รอดำเนินการ)</option>
                            <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>Processing (กำลังเตรียมจัดส่ง)</option>
                            <option value="shipped" <?= $order['status'] == 'shipped' ? 'selected' : '' ?>>Shipped (จัดส่งแล้ว)</option>
                            <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>Completed (สำเร็จ)</option>
                            <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled (ยกเลิก)</option>
                        </select>
                    </div>
                    <button type="submit" name="update_status" class="btn btn-primary w-100">
                        <i class="fas fa-sync-alt me-2"></i>อัปเดตสถานะ
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>


<?php include 'footer.php'; ?>