<?php
// search/search_ajax.php
include '../config/connectdb.php';

// ตรวจสอบว่ามีการส่งคำค้นหามาหรือไม่
if (isset($_POST['query']) && !empty($_POST['query'])) {
    
    $query = $_POST['query'];
    // 1. ป้องกัน SQL Injection และเพิ่ม wildcard (%) เพื่อให้ค้นหาเจอได้ง่ายขึ้น
    $search_term = "%" . $conn->real_escape_string($query) . "%";

    // 2. ค้นหาจาก "ชื่อสินค้า" (title) และจำกัดผลลัพธ์แค่ 5 รายการเพื่อความรวดเร็ว
    $stmt = $conn->prepare("SELECT id, title, image_url, price FROM products WHERE title LIKE ? LIMIT 5");
    $stmt->bind_param("s", $search_term);
    $stmt->execute();
    $result = $stmt->get_result();

    // 3. ตรวจสอบว่ามีผลลัพธ์หรือไม่
    if ($result->num_rows > 0) {
        // 4. ถ้ามี ให้สร้าง HTML สำหรับแต่ละรายการผลลัพธ์
        while ($row = $result->fetch_assoc()) {
            echo '<a href="../pages/product_detail.php?id=' . $row['id'] . '" class="list-group-item list-group-item-action d-flex align-items-center p-2">';
            echo '  <img src="../' . htmlspecialchars($row['image_url'] ?? 'assets/default.jpg') . '" width="40" height="50" class="me-3" style="object-fit: cover;">';
            echo '  <div>';
            echo '    <div class="fw-bold">' . htmlspecialchars($row['title']) . '</div>';
            echo '    <small class="text-primary">฿' . number_format($row['price'], 2) . '</small>';
            echo '  </div>';
            echo '</a>';
        }
    } else {
        // 5. ถ้าไม่เจอ ให้แสดงข้อความ
        echo '<div class="list-group-item text-muted">ไม่พบผลการค้นหา...</div>';
    }
    $stmt->close();
}
?>