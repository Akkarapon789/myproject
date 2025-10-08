<?php
// success.php
require_once '../config/connectdb.php';

// ดึง order_id จาก URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$order = null;

if ($order_id > 0) {
    $sql = "SELECT fullname, total_price, created_at FROM orders WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>สั่งซื้อสำเร็จ - The Bookmark Society</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .success-card {
      background: #ffffff;
      border-radius: 20px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.08);
      padding: 50px 30px;
      max-width: 600px;
      margin: 80px auto;
    }
    .emoji {
      font-size: 4rem;
      animation: pop 0.4s ease-in-out;
    }
    @keyframes pop {
      0% { transform: scale(0.5); opacity: 0; }
      100% { transform: scale(1); opacity: 1; }
    }
    .btn-primary {
      background-color: #2155CD;
      border: none;
      font-weight: 500;
    }
    .btn-primary:hover {
      background-color: #173b96;
    }
  </style>
</head>
<body class="bg-light">

  <div class="container">
    <div class="success-card text-center">
      <div class="emoji mb-3">✅</div>
      <h1 class="text-success mb-3">สั่งซื้อสำเร็จ!</h1>
      <p class="text-muted mb-4">ขอบคุณที่ใช้บริการกับเรา ระบบได้รับคำสั่งซื้อของคุณเรียบร้อยแล้ว</p>
      <a href="../pages/index.php" class="btn btn-primary btn-lg px-5">กลับไปหน้าหลัก</a>
    </div>
  </div>

</body>
</html>
