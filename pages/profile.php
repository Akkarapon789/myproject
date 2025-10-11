<?php
// pages/profile.php
session_start();
include '../config/connectdb.php';

// 1. ตรวจสอบว่าผู้ใช้ล็อกอินหรือยัง
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php?redirect=pages/profile.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$is_logged_in = true;
$success_message = '';
$error_message = '';

// 2. ส่วนจัดการการอัปเดตข้อมูล (เมื่อมีการกดบันทึก)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // เตรียมคำสั่ง SQL เพื่ออัปเดตข้อมูล
    $stmt = $conn->prepare("UPDATE `user` SET firstname = ?, lastname = ?, email = ?, phone = ?, address = ? WHERE user_id = ?");
    $stmt->bind_param("sssssi", $firstname, $lastname, $email, $phone, $address, $user_id);

    if ($stmt->execute()) {
        $success_message = "อัปเดตข้อมูลโปรไฟล์สำเร็จ!";
    } else {
        $error_message = "เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . $stmt->error;
    }
    $stmt->close();
}

// 3. ดึงข้อมูลล่าสุดของผู้ใช้มาแสดงในฟอร์ม
$stmt_select = $conn->prepare("SELECT * FROM `user` WHERE user_id = ?");
$stmt_select->bind_param("i", $user_id);
$stmt_select->execute();
$user = $stmt_select->get_result()->fetch_assoc();
$stmt_select->close();

?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>โปรไฟล์ของฉัน - The Bookmark Society</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../includes/css/style.css">
</head>
<body>

<?php include '../includes/navbar.php'; ?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-3">
            <div class="list-group">
                <a href="profile.php" class="list-group-item list-group-item-action active" aria-current="true">
                    <i class="fas fa-user-edit me-2"></i>โปรไฟล์ของฉัน
                </a>
                <a href="order_history.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-history me-2"></i>ประวัติการสั่งซื้อ
                </a>
                <a href="../auth/logout.php" class="list-group-item list-group-item-action text-danger">
                    <i class="fas fa-sign-out-alt me-2"></i>ออกจากระบบ
                </a>
            </div>
        </div>
        
        <div class="col-md-9">
            <h1 class="mb-4">โปรไฟล์ของฉัน</h1>

            <?php if ($success_message): ?>
                <div class="alert alert-success"><?= $success_message ?></div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div class="alert alert-danger"><?= $error_message ?></div>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form action="profile.php" method="POST">
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
                            <textarea class="form-control" id="address" name="address" rows="4"><?= htmlspecialchars($user['address']) ?></textarea>
                            <small class="form-text text-muted">*ที่อยู่นี้จะถูกใช้เป็นค่าเริ่มต้นในการจัดส่งสินค้า</small>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>