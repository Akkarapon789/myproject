<?php
include '../config/connectdb.php';

// ดึงโปรโมชั่นพร้อมสินค้า
$sql = "
SELECT pr.*, p.name as product_name
FROM promotions pr
LEFT JOIN products p ON pr.product_id = p.id
ORDER BY pr.id DESC
";
$result = $conn->query($sql);
?>
<!doctype html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Promotions</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h1 class="mb-4">โปรโมชั่น</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>ชื่อโปรโมชั่น</th>
                <th>ประเภทส่วนลด</th>
                <th>ค่าลด</th>
                <th>เริ่มต้น</th>
                <th>สิ้นสุด</th>
                <th>สินค้า</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['discount_type'] ?></td>
                <td><?= $row['discount_value'] ?></td>
                <td><?= $row['start_date'] ?></td>
                <td><?= $row['end_date'] ?></td>
                <td><?= $row['product_name'] ? $row['product_name'] : '-' ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <a href="index.php" class="btn btn-primary">ไปหน้าหลัก</a>
</div>
</body>
</html>