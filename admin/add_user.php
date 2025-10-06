<?php
include '../config/connectdb.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = $_POST['firstname'];
    $lastname  = $_POST['lastname'];
    $email     = $_POST['email'];
    $phone     = $_POST['phone'];
    $role      = $_POST['role'];
    $password  = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO user (firstname, lastname, email, password, phone, role) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("ssssss", $firstname, $lastname, $email, $password, $phone, $role);
    $stmt->execute();
    header("Location: users.php");
    exit();
}
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>เพิ่มผู้ใช้</title>
  <?php include 'layout.php'; ?>
</head>
<body>
<div class="container mt-5">
  <h2>เพิ่มผู้ใช้ใหม่</h2>
  <form method="post">
    <div class="mb-3">
      <label>ชื่อ</label>
      <input type="text" name="firstname" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>นามสกุล</label>
      <input type="text" name="lastname" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Email</label>
      <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>เบอร์โทร</label>
      <input type="text" name="phone" class="form-control">
    </div>
    <div class="mb-3">
      <label>สิทธิ์</label>
      <select name="role" class="form-control">
        <option value="user">User</option>
        <option value="admin">Admin</option>
      </select>
    </div>
    <div class="mb-3">
      <label>รหัสผ่าน</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <button class="btn btn-success">บันทึก</button>
    <a href="users.php" class="btn btn-secondary">ยกเลิก</a>
  </form>
</div>
</body>
</html>
