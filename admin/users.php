<?php
session_start();
include '../config/connectdb.php';
include 'header.php'; // 1. เรียกใช้ส่วนหัวและเมนู

// [แก้ไข] เพิ่ม ` ` (Backticks) ครอบชื่อตาราง `user` เพราะเป็นคำสงวนใน SQL
$result = $conn->query("SELECT * FROM `user` ORDER BY user_id DESC");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">👥 จัดการผู้ใช้งาน</h1>
    <a href="add_user.php" class="btn btn-primary">
        <i class="fas fa-plus fa-sm me-2"></i>เพิ่มผู้ใช้ใหม่
    </a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">รายชื่อผู้ใช้ทั้งหมด</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="userTable" class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>ชื่อ-นามสกุล</th>
                        <th>Email</th>
                        <th>เบอร์โทร</th>
                        <th>สิทธิ์</th>
                        <th class="text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // ตรวจสอบว่ามีข้อมูลหรือไม่ก่อนเริ่มลูป
                    if ($result && $result->num_rows > 0):
                        while ($row = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= $row['user_id'] ?></td>
                        <td><?= htmlspecialchars($row['firstname'] . " " . $row['lastname']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td class="text-center">
                            <span class="badge rounded-pill <?= $row['role'] == 'admin' ? 'bg-danger' : 'bg-success' ?>">
                                <?= ucfirst($row['role']) // ทำให้ตัวอักษรแรกเป็นตัวพิมพ์ใหญ่ ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="edit_user.php?id=<?= $row['user_id'] ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> แก้ไข
                            </a>
                            <a href="delete_user.php?id=<?= $row['user_id'] ?>" class="btn btn-danger btn-sm"
                               onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบผู้ใช้นี้?')">
                                <i class="fas fa-trash"></i> ลบ
                            </a>
                        </td>
                    </tr>
                    <?php
                        endwhile;
                    else: // กรณีไม่พบข้อมูล
                    ?>
                    <tr>
                        <td colspan="6" class="text-center">ไม่พบข้อมูลผู้ใช้งาน</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#userTable').DataTable({
        "order": [[0, "desc"]], // เรียงจาก IDล่าสุดไปเก่าสุดเป็นค่าเริ่มต้น
        "language": {
            "search": "ค้นหา:",
            "lengthMenu": "แสดง _MENU_ รายการ",
            "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
            "infoEmpty": "ไม่พบข้อมูล",
            "zeroRecords": "ไม่พบข้อมูลที่ตรงกับการค้นหา",
            "paginate": {
                "first": "แรกสุด",
                "last": "ท้ายสุด",
                "next": "ถัดไป",
                "previous": "ก่อนหน้า"
            }
        }
    });
});
</script>

<?php
include 'footer.php'; // 2. เรียกใช้ส่วนท้ายเพื่อปิดหน้าเว็บ
?>