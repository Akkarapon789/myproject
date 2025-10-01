<?php
session_start();
include '../config/connectdb.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM user WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // เก็บข้อมูลใน SESSION
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['firstname'] = $user['firstname'];
        $_SESSION['lastname'] = $user['lastname'];
        $_SESSION['role'] = $user['role'];

        // เช็ค role
        if ($user['role'] === 'admin') {
            header("Location: ../admin/index.php");
        } else {
            header("Location: ../pages/index.php");
        }
        exit();
    } else {
        $error = "อีเมลหรือรหัสผ่านไม่ถูกต้อง";
    }
}
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>เข้าสู่ระบบ</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow-lg">
        <div class="card-header bg-primary text-white text-center">
          <img src="../assets/logo/2.png" class="rounded-circle overflow-hidden" style="width:100px; heigth:100px;">
          <h2>Login</h2>
        </div>
        <div class="card-body">
          <?php if (!empty($error)) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
          <?php } ?>
          <form method="post" action="">
            <div class="mb-3">
              <label class="form-label">E-mail</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100 mb-3" onclick="window.location.href='../pages/index.php'">Login</button>
            <button type="button" onclick="window.location.href='../auth/sign-up.php'" class="btn btn-outline-primary w-100">Sign-up</button>
          </form>
          <div class="text-center mt-3">
            <a href="#">Forgot Password?</a>
          </div>
          <hr>
            <p class="text-center text-muted">หรือ</p>
            <div class="d-flex justify-content-between">
            <a href="https://www.facebook.com/" class="btn flex-fill me-2" style="background-color:#1877F2; color:white;">
                <i class="bi bi-facebook"></i> Facebook
            </a>
            <a href="https://www.google.com/" class="btn flex-fill ms-2" style="background-color:#DB4437; color:white;">
                <i class="bi bi-google"></i> Google
            </a>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

</body>
</html>