<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}
include '../config/connectdb.php';
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>สินค้า</title>
  <?php include 'layout.php'; ?>

  <!-- ✅ DataTables CSS (Bootstrap 5 Integration) -->
  <link href="https://cdn.datatables.net/2.0.3/css/dataTables.bootstrap5.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #f8f9fa;
      font-family: "Sarabun", sans-serif;
    }
    .content {
      padding: 20px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      margin: 20px;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
      padding: 6px 12px;
      border-radius: 8px;
      background: transparent;
      border: 1px solid #dee2e6;
      margin: 0 2px;
      font-weight: 500;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
      background-color: #2155CD !important;
      color: #fff !important;
      border-color: #2155CD !important;
    }
    .table thead th {
      background-color: #2155CD !important;
      color: white;
      text-align: center;
      vertical-align: middle;
    }
    .table tbody td {
      vertical-align: middle;
      text-align: center;
    }
    .table img {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 8px;
      border: 1px solid #ddd;
    }
    .btn {
      border-radius: 6px;
    }
  </style>
</head>
<body>
<div class="d-flex">
  <div class="sidebar p-3">
    <h4>Admin Panel</h4>
    <a href="index.php">แดชบอร์ด</a>
    <a href="users.php">ผู้ใช้</a>
    <a href="products.php" class="active">สินค้า</a>
    <a href="orders.php">คำสั่งซื้อ</a>
    <a href="reports.php">รายงาน</a>
    <a href="adminout.php" class="text-danger">ออกจากระบบ</a>
  </div>

  <div class="content flex-grow-1">
    <h2>📦 จัดการสินค้า</h2>
    <a href="add_product.php" class="btn btn-success mb-3">+ เพิ่มสินค้า</a>

    <div class="card shadow-sm p-3">
      <table id="productTable" class="table table-bordered table-hover align-middle">
        <thead>
          <tr>
            <th>รูป</th>
            <th>ID</th>
            <th>ชื่อสินค้า</th>
            <th>ราคา</th>
            <th>สต็อก</th>
            <th>หมวดหมู่</th>
            <th>การจัดการ</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $sql = "SELECT p.*, c.title AS category_name FROM products p 
                  JOIN categories c ON p.category_id = c.id 
                  ORDER BY p.id DESC";
          $result = $conn->query($sql);
          while($row = $result->fetch_assoc()):
              $imgUrl = !empty($row['image']) ? "../uploads/" . $row['image'] : "https://picsum.photos/60?random=" . $row['id'];
          ?>
          <tr>
            <td><img src="<?= htmlspecialchars($imgUrl) ?>" alt="product"></td>
            <td><?= $row['id']; ?></td>
            <td><?= htmlspecialchars($row['title']); ?></td>
            <td><?= number_format($row['price'], 2); ?> ฿</td>
            <td><?= $row['stock']; ?></td>
            <td><?= htmlspecialchars($row['category_name']); ?></td>
            <td>
              <a href="edit_product.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-warning">แก้ไข</a>
              <a href="delete_product.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-danger" 
                 onclick="return confirm('ยืนยันการลบสินค้า?');">ลบ</a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- ✅ DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.0.3/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
  $('#productTable').DataTable({
    language: {
      search: "🔍 ค้นหา:",
      lengthMenu: "แสดง _MENU_ รายการต่อหน้า",
      info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
      infoEmpty: "ไม่มีข้อมูล",
      zeroRecords: "ไม่พบข้อมูลที่ค้นหา",
      paginate: {
        first: "หน้าแรก",
        last: "หน้าสุดท้าย",
        next: "ถัดไป",
        previous: "ก่อนหน้า"
      }
    },
    pageLength: 10,
    order: [[1, "desc"]],
    responsive: true
  });
});
</script>

</body>
</html>
