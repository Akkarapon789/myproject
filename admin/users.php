<?php
// admin/users.php (Upgraded with DataTables)
session_start();
include '../config/connectdb.php';
include 'header.php';

// --- ส่วนของการลบข้อมูล (ส่วนนี้ไม่ต้องแก้ไข) ---
if (isset($_GET['delete'])) {
    $id_to_delete = intval($_GET['delete']);
    $stmt_delete = $conn->prepare("DELETE FROM `user` WHERE user_id = ?");
    $stmt_delete->bind_param("i", $id_to_delete);
    $stmt_delete->execute();
    header("Location: users.php");
    exit();
}

$result = $conn->query("SELECT * FROM `user` ORDER BY user_id DESC");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">👥 จัดการผู้ใช้งาน</h1>
    <a href="add_user.php" class="btn btn-primary"><i class="fas fa-plus fa-sm me-2"></i>เพิ่มผู้ใช้ใหม่</a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <table id="usersTable" class="table table-bordered table-hover align-middle" style="width:100%">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>ชื่อ-นามสกุล</th>
                    <th>Email</th>
                    <th>วันเดือนปีเกิด</th>>
                    <th>สิทธิ์</th>
                    <th class="text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['user_id'] ?></td>
                    <td><?= htmlspecialchars($row['firstname'] . " " . $row['lastname']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td class="text-center"><span class="badge bg-<?= $row['role'] == 'admin' ? 'danger' : 'success' ?>"><?= ucfirst($row['role']) ?></span></td>
                    <td class="text-center">
                        <a href="edit_user.php?id=<?= $row['user_id'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> แก้ไข</a>
                        <a href="users.php?delete=<?= $row['user_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('ยืนยันการลบผู้ใช้นี้?')"><i class="fas fa-trash"></i> ลบ</a>
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
    $('#usersTable').DataTable({
        "order": [[ 0, "desc" ]],
        "language": { "search": "ค้นหา:", "lengthMenu": "แสดง _MENU_ รายการ", "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ", "paginate": { "previous": "ก่อนหน้า", "next": "ถัดไป" } }
    });
});
</script>