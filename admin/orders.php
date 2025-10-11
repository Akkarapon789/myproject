<?php
// orders.php (Corrected)
include 'header.php';

// [แก้ไข] เพิ่ม ` ` ครอบ `user`
$result = $conn->query("SELECT * FROM `orders` ORDER BY user_id ASC");
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
                        <th>ID คำสั่งซื้อ</th>
                        <th>ชื่อลูกค้า</th>
                        <th>วันที่สั่งซื้อ</th>
                        <th>ยอดรวม (บาท)</th>
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
                        <td><?= number_format($row['total'], 2) ?></td>
                        <td>
                            <?php
                                $status = $row['status'];
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
                        <td>
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



<script>
$(document).ready(function() {
    $('#ordersTable').DataTable({
        "order": [[0, "desc"]]
    });
});
</script>