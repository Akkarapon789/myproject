<?php
// order_details.php (Corrected)
include 'header.php';

$order_id = $_GET['id'] ?? 0;

// --- ส่วนจัดการอัปเดตสถานะ ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    $stmt_update = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt_update->bind_param("si", $new_status, $order_id);
    if ($stmt_update->execute()) {
        // [แก้ไข] เปลี่ยนเป็น JavaScript Redirect
        echo "<script>
                alert('อัปเดตสถานะเรียบร้อยแล้ว!');
                window.location.href = 'orders.php';
              </script>";
        exit(); // exit() ยังคงสำคัญ เพื่อให้แน่ใจว่าสคริปต์หยุดทำงาน
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการอัปเดตสถานะ');</script>";
    }
    $stmt_update->close();
}

// --- ดึงข้อมูลหลักของ Order (ข้อมูลลูกค้า, ที่อยู่) ---
// [แก้ไข] เพิ่ม ` ` ครอบ `user`
$stmt_order = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt_order->bind_param("i", $order_id);
$stmt_order->execute();
$order = $stmt_order->get_result()->fetch_assoc();
$stmt_order->close();

if (!$order) {
    echo "ไม่พบคำสั่งซื้อนี้";
    exit;
}

// --- ดึงข้อมูลสินค้าทั้งหมดใน Order นี้ ---
$stmt_items = $conn->prepare("SELECT oi.*, p.title as product_title, p.image_url FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$order_items_result = $stmt_items->get_result();
$stmt_items->close();

?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">รายละเอียดคำสั่งซื้อ #<?= $order_id ?></h1>
    <a href="orders.php" class="btn btn-secondary"><i class="fas fa-arrow-left fa-sm me-2"></i>กลับไปหน้ารายการ</a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">สินค้าในคำสั่งซื้อ</h6></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>สินค้า</th>
                                <th class="text-center">จำนวน</th>
                                <th class="text-end">ราคารวม</th>
                                <th class="text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($item = $order_items_result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <img src="../uploads/<?= htmlspecialchars($item['image_url'] ?? 'no-image.jpg') ?>" width="60" class="img-thumbnail me-3 float-start">
                                    <?= htmlspecialchars($item['product_title']) ?><br>
                                    <small class="text-muted">ราคา: ฿<?= number_format($item['price'], 2) ?> / ชิ้น</small>
                                </td>
                                <form action="order_actions.php" method="POST">
                                    <input type="hidden" name="action" value="update_item">
                                    <input type="hidden" name="order_id" value="<?= $order_id ?>">
                                    <input type="hidden" name="order_item_id" value="<?= $item['id'] ?>">
                                    <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                    <input type="hidden" name="old_quantity" value="<?= $item['quantity'] ?>">
                                    
                                    <td class="text-center align-middle">
                                        <input type="number" name="new_quantity" class="form-control form-control-sm text-center" value="<?= $item['quantity'] ?>" min="0" style="width: 80px; margin: auto;">
                                    </td>
                                    <td class="text-end align-middle">฿<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                                    <td class="text-center align-middle">
                                        <button type="submit" class="btn btn-primary btn-sm" title="อัปเดตจำนวน">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </td>
                                </form>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td colspan="3" class="text-end">ยอดรวมสุทธิ:</td>
                                <td class="text-end fs-5">฿<?= number_format($order['total'], 2) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                 <div class="alert alert-info mt-3">
                    <strong>คำแนะนำ:</strong> หากต้องการ **ลบ** สินค้ารายการใด ให้เปลี่ยนจำนวนเป็น **0** แล้วกดปุ่มอัปเดต
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">ข้อมูลสรุป</h6></div>
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
                    <button type="submit" name="update_status" class="btn btn-primary w-100" ><i class="fas fa-sync-alt me-2"></i>อัปเดตสถานะ</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>