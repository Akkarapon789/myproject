<?php
// admin/add_user.php
session_start();
include '../config/connectdb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $birthday = $_POST['birthday']; // <-- **แก้ไข 1**: เปลี่ยนจาก $_POST['date']
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // เข้ารหัสรหัสผ่าน

    // <-- **แก้ไข 2**: เพิ่ม `birthday` และ `?` ตัวที่ 7
    $stmt = $conn->prepare("INSERT INTO `user` (firstname, lastname, email, password, phone, address, birthday, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    // <-- **แก้ไข 3**: เปลี่ยน "sssssss" (7 ตัว) เป็น "ssssssss" (8 ตัว)
    $stmt->bind_param("ssssssss", $firstname, $lastname, $email, $password, $phone, $address, $birthday, $role);

    if ($stmt->execute()) {
        $_SESSION['success'] = "เพิ่มผู้ใช้ใหม่สำเร็จ!";
        header("Location: users.php");
        exit();
    } else {
        $error = "อีเมลนี้มีในระบบแล้ว หรือเกิดข้อผิดพลาด: " . $stmt->error;
    }
    $stmt->close();
}
include 'header.php';
?>

<h1 class="h3 mb-4 text-gray-800">➕ เพิ่มผู้ใช้ใหม่</h1>
<?php if (isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="add_user.php" method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="firstname" class="form-label">ชื่อจริง</label>
                    <input type="text" class="form-control" id="firstname" name="firstname" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="lastname" class="form-label">นามสกุล</label>
                    <input type="text" class="form-control" id="lastname" name="lastname" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">อีเมล</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">รหัสผ่าน</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
             <div class="mb-3">
                <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
                <input type="tel" class="form-control" id="phone" name="phone">
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">ที่อยู่</label>
                <textarea class="form-control" id="address" name="address" rows="3"></textarea>
            </div>

            <div class="mb-3">
                <label for="birthday" class="form-label">วันเดือนปีเกิด</label>
                
                <input type="date" class="form-control" id="birthday" name="birthday">
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">สิทธิ์การใช้งาน</label>
                <select class="form-select" id="role" name="role">
                    <option value="user" selected>User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="users.php" class="btn btn-secondary">ยกเลิก</a>
                <button type="submit" class="btn btn-primary">บันทึก</button>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>