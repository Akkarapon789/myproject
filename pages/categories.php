<?php
// pages/categories.php (Corrected & Final Version)

/**
 * ฟังก์ชันสำหรับดึงข้อมูลหมวดหมู่ทั้งหมดจากฐานข้อมูล
 * @param mysqli $conn - Object การเชื่อมต่อฐานข้อมูล
 * @return array - Array ของข้อมูลหมวดหมู่
 */
function getAllCategories($conn): array
{
    if (!$conn || $conn->connect_error) {
        return [];
    }

    $sql = "SELECT id, title, slug, image_url FROM categories ORDER BY id ASC";
    $result = $conn->query($sql);
    
    $categories = [];
    if ($result && $result->num_rows > 0) {
        $categories = $result->fetch_all(MYSQLI_ASSOC);
    }
    
    return $categories;
}