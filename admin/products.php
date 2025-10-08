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
    .dataTables_wrapper .dataTables_paginate .paginate_button {
      padding: 5px 10px;
      border-radius: 6px;
      background: transparent;
      border: 1px solid #000000FF;
      margin: 0 2px;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
      background-color: #2155CD !important;
      color: #fff !important;
      border-color: #2155CD !important;
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
    <h2>📦 สินค้า</h2>
    <a href="add_product.php" class="btn btn-success mb-3">+ เพิ่มสินค้า</a>
    <div class="card p-3">
      <table id="productTable" class="table table-striped table-hover align-middle">
        <thead class="table-dark">
          <tr>
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
          $sql = "SELECT p.*, c.title AS category_name 
                  FROM products p 
                  JOIN categories c ON p.category_id = c.id 
                  ORDER BY p.id DESC";
          $result = $conn->query($sql);
          while($row = $result->fetch_assoc()):
          ?>
          <tr>
            <td><?= $row['id']; ?></td>
            <td><?= htmlspecialchars($row['title']); ?></td>
            <td><?= number_format($row['price'],2); ?> บาท</td>
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
        order: [[0, "desc"]],
        responsive: true
    });
});
</script>

</body>
</html>
