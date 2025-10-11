<?php
// search/search_ajax.php
include '../config/connectdb.php';

if (isset($_POST['query'])) {
    $query = $_POST['query'];
    // ป้องกัน SQL Injection และเพิ่ม wildcard (%)
    $search_term = "%" . $conn->real_escape_string($query) . "%";

    // ค้นหาจากชื่อสินค้า (title) และจำกัดผลลัพธ์แค่ 5 รายการ
    $stmt = $conn->prepare("SELECT id, title, image_url, price FROM products WHERE title LIKE ? LIMIT 5");
    $stmt->bind_param("s", $search_term);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // สร้าง HTML สำหรับแต่ละรายการผลลัพธ์
            echo '<a href="../pages/product_detail.php?id=' . $row['id'] . '" class="list-group-item list-group-item-action d-flex align-items-center">';
            echo '  <img src="../' . htmlspecialchars($row['image_url'] ?? 'assets/default.jpg') . '" width="40" height="50" class="me-3 object-fit-cover">';
            echo '  <div>';
            echo '    <div class="fw-bold">' . htmlspecialchars($row['title']) . '</div>';
            echo '    <small class="text-primary">฿' . number_format($row['price'], 2) . '</small>';
            echo '  </div>';
            echo '</a>';
        }
    } else {
        // กรณีไม่พบผลลัพธ์
        echo '<div class="list-group-item text-muted">ไม่พบผลการค้นหา...</div>';
    }
    $stmt->close();
}