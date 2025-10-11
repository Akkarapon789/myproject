<?php
// admin/orders.php (Upgraded with DataTables)
session_start();
include '../config/connectdb.php';
include 'header.php';

$sql = "SELECT o.*, u.firstname, u.lastname 
        FROM `orders` o
        LEFT JOIN `user` u ON o.user_id = u.user_id
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">🛒 จัดการคำสั่งซื้อ</h1>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <table id="ordersTable" class="table table-bordered table-hover align-middle" style="width:100%">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>ชื่อลูกค้า</th>
                    <th>วันที่สั่งซื้อ</th>
                    <th class="text-end">ยอดรวม</th>
                    <th class="text-center">สถานะ</th>
                    <th class="text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>#<?= $row['id'] ?></td>
                    <td><?= htmlspecialchars((!empty($row['firstname'])) ? $row['firstname'].' '.$row['lastname'] : $row['fullname']) ?></td>
                    <td><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></td>
                    <td class="text-end">฿<?= number_format($row['total'], 2) ?></td>
                    <td class="text-center">
                        <?php
                            $status = $row['status'] ?? 'pending';
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
                        <a href="order_details.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> ดูรายละเอียด</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    $('#ordersTable').DataTable({
        "order": [[ 0, "desc" ]],
        "language": { "search": "ค้นหา:", "lengthMenu": "แสดง _MENU_ รายการ", "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ", "paginate": { "previous": "ก่อนหน้า", "next": "ถัดไป" } }
    });
});
</script>