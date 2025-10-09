<?php
// users.php (Upgraded)
include 'header.php';
include '../config/connectdb.php';

// ดึงข้อมูลผู้ใช้ทั้งหมด
$result = $conn->query("SELECT * FROM user ORDER BY user_id ASC");
$result = $conn->query($sql);
if (!$result) {
    // ถ้า query ไม่สำเร็จ ให้แสดงข้อผิดพลาดแล้วหยุดทำงาน
    die("SQL Error: " . $conn->error);
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">จัดการผู้ใช้งาน</h1>
    <a href="add_user.php" class="btn btn-success">
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
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ชื่อ-นามสกุล</th>
                        <th>Email</th>
                        <th>เบอร์โทร</th>
                        <th>สิทธิ์</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['user_id'] ?></td>
                        <td><?= htmlspecialchars($row['firstname'] . " " . $row['lastname']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td>
                            <span class="badge rounded-pill <?= $row['role'] == 'admin' ? 'bg-danger' : 'bg-primary' ?>">
                                <?= ucfirst($row['role']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="edit_user.php?id=<?= $row['user_id'] ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="delete_user.php?id=<?= $row['user_id'] ?>" class="btn btn-danger btn-sm" 
                               onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบผู้ใช้นี้?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    $('#userTable').DataTable({
        "order": [[0, "asc"]], // เรียงลำดับตามคอลัมน์แรก (ID) จากน้อยไปมาก
        "language": { // หากต้องการภาษาไทย สามารถใช้ส่วนนี้ได้
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