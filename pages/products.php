<?php
// pages/products.php (Corrected & Final Version)

/**
 * ฟังก์ชันสำหรับดึงสินค้าทั้งหมด (พร้อมสุ่ม)
 * @param mysqli $conn - Object การเชื่อมต่อฐานข้อมูล
 * @param int|null $limit - จำนวนสินค้าที่ต้องการ
 * @return array - Array ของข้อมูลสินค้า
 */
function getAllProducts($conn, $limit = null): array
{
    if (!$conn || $conn->connect_error) {
        return [];
    }
    
    // ⭐️ ใช้ Prepared Statement และตรวจสอบ SQL ให้ถูกต้อง
    $sql = "SELECT id, title, price, image_url FROM products ORDER BY RAND()";
    
    if (is_int($limit) && $limit > 0) {
        // ใช้ placeholder (?) เพื่อความปลอดภัย
        $sql .= " LIMIT ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $limit);
    } else {
        $stmt = $conn->prepare($sql);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];
    if ($result && $result->num_rows > 0) {
        $products = $result->fetch_all(MYSQLI_ASSOC);
    }
    
    $stmt->close();
    return $products;
}