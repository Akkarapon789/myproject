<?php
// edit_user.php (Corrected)
include 'header.php';

$user_id = $_GET['id'] ?? 0;

// [แก้ไข] เพิ่ม ` ` ครอบ `user`
$sql_select = "SELECT * FROM `user` WHERE user_id = ?";
$stmt_select = $conn->prepare($sql_select);
$stmt_select->bind_param("i", $user_id);
$stmt_select->execute();
$result = $stmt_select->get_result();
$user = $result->fetch_assoc();
$stmt_select->close();

if (!$user) {
    echo "ไม่พบผู้ใช้งาน!";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname  = $_POST['lastname'];
    $email     = $_POST['email'];
    $phone     = $_POST['phone'];
    $role      = $_POST['role'];
    $password  = $_POST['password'];

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        // [แก้ไข] เพิ่ม ` ` ครอบ `user`
        $sql_update = "UPDATE `user` SET firstname=?, lastname=?, email=?, password=?, phone=?, role=? WHERE user_id=?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssssssi", $firstname, $lastname, $email, $hashed_password, $phone, $role, $user_id);
    } else {
        // [แก้ไข] เพิ่ม ` ` ครอบ `user`
        $sql_update = "UPDATE `user` SET firstname=?, lastname=?, email=?, phone=?, role=? WHERE user_id=?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssssi", $firstname, $lastname, $email, $phone, $role, $user_id);
    }

    if ($stmt_update->execute()) {
        echo "<script>alert('อัปเดตข้อมูลผู้ใช้เรียบร้อยแล้ว!'); window.location.href = 'users.php';</script>";
    } else {
         echo "<script>alert('เกิดข้อผิดพลาด: " . htmlspecialchars($stmt_update->error) . "');</script>";
    }
    $stmt_update->close();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">แก้ไขผู้ใช้: <?= htmlspecialchars($user['firstname']) ?></h1>
    <a href="users.php" class="btn btn-secondary"><i class="fas fa-arrow-left fa-sm me-2"></i>กลับไปหน้าผู้ใช้</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="edit_user.php?id=<?= $user_id ?>" method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">ชื่อจริง</label>
                    <input type="text" name="firstname" class="form-control" value="<?= htmlspecialchars($user['firstname']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">นามสกุล</label>
                    <input type="text" name="lastname" class="form-control" value="<?= htmlspecialchars($user['lastname']) ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">อีเมล</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">รหัสผ่านใหม่</label>
                <input type="password" name="password" class="form-control" placeholder="ปล่อยว่างไว้ถ้าไม่ต้องการเปลี่ยน">
            </div>
            <div class="mb-3">
                <label class="form-label">เบอร์โทรศัพท์</label>
                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">สิทธิ์การใช้งาน</label>
                <select name="role" class="form-select">
                    <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                    <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-sync-alt me-2"></i>อัปเดตข้อมูล</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>