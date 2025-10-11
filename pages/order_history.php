<?php
// pages/order_history.php (Updated Link)
session_start();
include '../config/connectdb.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php?redirect=pages/order_history.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$is_logged_in = true;

$stmt = $conn->prepare("SELECT id, created_at, total, status FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ประวัติการสั่งซื้อ - The Bookmark Society</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../includes/css/style.css">
</head>
<body>

<?php include '../includes/navbar.php'; ?>

<div class="container my-5">
    <h1 class="mb-4">ประวัติการสั่งซื้อ</h1>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>หมายเลขคำสั่งซื้อ</th>
                            <th>วันที่สั่งซื้อ</th>
                            <th class="text-end">ยอดรวม</th>
                            <th class="text-center">สถานะ</th>
                            <th class="text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($orders_result->num_rows > 0): ?>
                            <?php while ($order = $orders_result->fetch_assoc()): ?>
                                <tr>
                                    <td><strong>#<?= $order['id'] ?></strong></td>
                                    <td><?= date('d M Y, H:i', strtotime($order['created_at'])) ?> น.</td>
                                    <td class="text-end">฿<?= number_format($order['total'], 2) ?></td>
                                    <td class="text-center">
                                        <?php
                                            $status = $order['status'] ?? 'pending';
                                            $badge_class = 'bg-secondary';
                                            if ($status == 'completed') $badge_class = 'bg-success';
                                            if ($status == 'pending') $badge_class = 'bg-warning text-dark';
                                            if ($status == 'processing') $badge_class = 'bg-info text-dark';
                                            if ($status == 'shipped') $badge_class = 'bg-primary';
                                            if ($status == 'cancelled') $badge_class = 'bg-danger';
                                        ?>
                                        <span class="badge rounded-pill <?= $badge_class ?>"><?= ucfirst($status) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <a href="order_detail.php?id=<?= $order['id'] ?>" class="btn btn-outline-primary btn-sm">ดูรายละเอียด</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <p>คุณยังไม่มีประวัติการสั่งซื้อ</p>
                                    <a href="all_products.php" class="btn btn-primary">เริ่มเลือกซื้อหนังสือเลย!</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php 
$stmt->close();
mysqli_close($conn); 
?>