<?php
session_start();
include '../config/connectdb.php';
include 'header.php';

// --- ส่วนของการลบข้อมูล ---
if (isset($_GET['delete'])) {
    $id_to_delete = intval($_GET['delete']);
    // ป้องกันการลบตัวเอง (ถ้าจำเป็น)
    // if ($id_to_delete == $_SESSION['user_id']) { ... } 
    
    $stmt_delete = $conn->prepare("DELETE FROM `user` WHERE user_id = ?");
    $stmt_delete->bind_param("i", $id_to_delete);
    if ($stmt_delete->execute()) {
        $_SESSION['success'] = "ลบผู้ใช้สำเร็จ!";
    } else {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบ: " . $stmt_delete->error;
    }
    $stmt_delete->close();
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
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">รายชื่อผู้ใช้ทั้งหมด</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ชื่อ-นามสกุล</th>
                        <th>Email</th>
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
                        <td><span class="badge bg-<?= $row['role'] == 'admin' ? 'danger' : 'success' ?>"><?= ucfirst($row['role']) ?></span></td>
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
</div>

<?php include 'footer.php'; ?>