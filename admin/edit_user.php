<?php
include '../config/connectdb.php';

// ตรวจสอบว่ามี id ที่ส่งมาหรือไม่
if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$id = intval($_GET['id']);

// ดึงข้อมูลผู้ใช้เดิม
$result = $conn->prepare("SELECT * FROM user WHERE user_id = ?");
$result->bind_param("i", $id);
$result->execute();
$user = $result->get_result()->fetch_assoc();

// ถ้าไม่พบผู้ใช้
if (!$user) {
    echo "ไม่พบข้อมูลผู้ใช้";
    exit();
}

// ถ้ามีการส่งฟอร์มแก้ไข
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = $_POST['firstname'];
    $lastname  = $_POST['lastname'];
    $email     = $_POST['email'];
    $phone     = $_POST['phone'];
    $role      = $_POST['role'];

    $stmt = $conn->prepare("UPDATE user SET firstname=?, lastname=?, email=?, phone=?, role=? WHERE user_id=?");
    $stmt->bind_param("sssssi", $firstname, $lastname, $email, $phone, $role, $id);
    $stmt->execute();

    header("Location: users.php");
    exit();
}
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>แก้ไขผู้ใช้</title>
  <?php include 'layout.php'; ?>
</head>
<body>
<div class="container mt-5">
  <h2>แก้ไขข้อมูลผู้ใช้</h2>

  <form method="post">
    <div class="mb-3">
      <label>ชื่อ</label>
      <input type="text" name="firstname" class="form-control" value="<?= htmlspecialchars($user['firstname']); ?>" required>
    </div>
    <div class="mb-3">
      <label>นามสกุล</label>
      <input type="text" name="lastname" class="form-control" value="<?= htmlspecialchars($user['lastname']); ?>" required>
    </div>
    <div class="mb-3">
      <label>Email</label>
      <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']); ?>" required>
    </div>
    <div class="mb-3">
      <label>เบอร์โทร</label>
      <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']); ?>">
    </div>
    <div class="mb-3">
      <label>สิทธิ์</label>
      <select name="role" class="form-control">
        <option value="user" <?= $user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
        <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
    <a href="users.php" class="btn btn-secondary">ยกเลิก</a>
  </form>
</div>
</body>
</html>
