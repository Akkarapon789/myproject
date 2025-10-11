<?php
// pages/products.php (Corrected & Final Version)

/**
 * ฟังก์ชันดึงสินค้าทั้งหมด (พร้อมสุ่ม)
 * @param mysqli $conn - Object การเชื่อมต่อฐานข้อมูล
 * @param int|null $limit - จำนวนสินค้าที่ต้องการ
 * @return array - Array ของข้อมูลสินค้า
 */
function getAllProducts($conn, $limit = null): array
{
    if (!$conn || $conn->connect_error) {
        return [];
    }
    
    $sql = "SELECT id, title, price, image_url, description, category_id FROM products ORDER BY RAND()";
    
    if (is_int($limit) && $limit > 0) {
        $sql .= " LIMIT ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) return []; // ป้องกัน SQL error
        $stmt->bind_param("i", $limit);
    } else {
        $stmt = $conn->prepare($sql);
        if (!$stmt) return []; // ป้องกัน SQL error
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

/**
 * ฟังก์ชันดึงสินค้าตาม ID หมวดหมู่
 * @param mysqli $conn
 * @param int $categoryId
 * @return array
 */
function getProductsByCategory($conn, int $categoryId): array
{
    if (!$conn || $conn->connect_error) {
        return [];
    }
    
    $sql = "SELECT id, title, price, image_url FROM products WHERE category_id = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return []; // ป้องกัน SQL error
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];
    if ($result && $result->num_rows > 0) {
        $products = $result->fetch_all(MYSQLI_ASSOC);
    }
    
    $stmt->close();
    return $products;
}

// ⭐️ สำคัญ: ต้องไม่มีปีกกาปิด `}` เกินมาตรงนี้ ⭐️