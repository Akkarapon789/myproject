<?php
// admin/edit_user.php
session_start();
include '../config/connectdb.php';

$id_to_edit = isset($_GET['id']) ? intval($_GET['id']) : 0;

// --- ส่วนของการอัปเดตข้อมูล ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $role = $_POST['role']; // รับค่า role

    $stmt = $conn->prepare("UPDATE `user` SET firstname=?, lastname=?, email=?, phone=?, address=?, role=? WHERE user_id=?");
    $stmt->bind_param("ssssssi", $firstname, $lastname, $email, $phone, $address, $role, $id_to_edit);

    if ($stmt->execute()) {
        $_SESSION['success'] = "แก้ไขข้อมูลผู้ใช้สำเร็จ!";
        header("Location: users.php");
        exit();
    } else {
        $error = "เกิดข้อผิดพลาด: " . $stmt->error;
    }
    $stmt->close();
}

// --- ดึงข้อมูลเดิมมาแสดงในฟอร์ม ---
$stmt_select = $conn->prepare("SELECT * FROM `user` WHERE user_id = ?");
$stmt_select->bind_param("i", $id_to_edit);
$stmt_select->execute();
$user = $stmt_select->get_result()->fetch_assoc();
$stmt_select->close();

if (!$user) {
    die("ไม่พบข้อมูลผู้ใช้");
}
include 'header.php';
?>

<h1 class="h3 mb-4 text-gray-800">✏️ แก้ไขข้อมูลผู้ใช้: <?= htmlspecialchars($user['firstname']) ?></h1>
<?php if (isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="edit_user.php?id=<?= $id_to_edit ?>" method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="firstname" class="form-label">ชื่อจริง</label>
                    <input type="text" class="form-control" id="firstname" name="firstname" value="<?= htmlspecialchars($user['firstname']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="lastname" class="form-label">นามสกุล</label>
                    <input type="text" class="form-control" id="lastname" name="lastname" value="<?= htmlspecialchars($user['lastname']) ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">อีเมล</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
                <input type="tel" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">ที่อยู่</label>
                <textarea class="form-control" id="address" name="address" rows="3"><?= htmlspecialchars($user['address']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">สิทธิ์การใช้งาน</label>
                <select class="form-select" id="role" name="role">
                    <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                    <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="users.php" class="btn btn-secondary">ยกเลิก</a>
                <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
            </div>
        </form>
    </div>
</div>
<?php include 'footer.php'; ?>