<?php
session_start();
include '../config/connectdb.php';
include 'header.php';

// ดึงข้อมูล orders ทั้งหมด พร้อม JOIN ตาราง user เพื่อเอาชื่อมาด้วย
// [แก้ไข] เพิ่มการ JOIN กับตาราง user และใช้ Backticks (`) ครอบชื่อตาราง
$sql = "SELECT o.*, u.firstname, u.lastname 
        FROM `orders` o
        LEFT JOIN `user` u ON o.user_id = u.user_id
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">จัดการคำสั่งซื้อ</h1>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">รายการคำสั่งซื้อทั้งหมด</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="ordersTable" class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ชื่อลูกค้า</th>
                        <th>วันที่สั่งซื้อ</th>
                        <th>ยอดรวม</th>
                        <th>สถานะ</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?= $row['id'] ?></td>
                        <td>
                            <?php
                                // ถ้า user_id มีค่า (ลูกค้าที่เป็นสมาชิก) ให้ใช้ชื่อจากตาราง user
                                // ถ้าไม่มี (เป็นแขก) ให้ใช้ชื่อ fullname ที่กรอกตอนสั่งซื้อ
                                $customer_name = (!empty($row['firstname'])) ? $row['firstname'] . ' ' . $row['lastname'] : $row['fullname'];
                                echo htmlspecialchars($customer_name);
                            ?>
                        </td>
                        <td><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></td>
                        <td class="text-end"><?= number_format($row['total'], 2) ?> ฿</td>
                        <td class="text-center">
                            <?php
                                $status = $row['status'] ?? 'pending'; // กำหนดค่า default
                                $badge_class = 'bg-secondary'; // Default
                                if ($status == 'completed') $badge_class = 'bg-success';
                                if ($status == 'pending') $badge_class = 'bg-warning text-dark';
                                if ($status == 'processing') $badge_class = 'bg-info text-dark';
                                if ($status == 'shipped') $badge_class = 'bg-primary';
                                if ($status == 'cancelled') $badge_class = 'bg-danger';
                            ?>
                            <span class="badge rounded-pill <?= $badge_class ?>">
                                <?= ucfirst($status) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="order_details.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> ดูรายละเอียด
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#ordersTable').DataTable({
        "order": [[0, "desc"]] // เรียงจาก IDล่าสุดไปเก่าสุดเป็นค่าเริ่มต้น
    });
});
</script>

<?php 
// เพิ่มส่วนท้ายของเทมเพลตเข้ามา
include 'footer.php'; 
?>