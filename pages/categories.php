<?php
// pages/categories.php (Corrected & Final Version)

/**
 * ฟังก์ชันสำหรับดึงข้อมูลหมวดหมู่ทั้งหมดจากฐานข้อมูล
 * @param mysqli $conn - Object การเชื่อมต่อฐานข้อมูล
 * @return array - Array ของข้อมูลหมวดหมู่
 */
function getAllCategories($conn): array
{
    // ตรวจสอบว่าการเชื่อมต่อถูกต้องหรือไม่
    if (!$conn || $conn->connect_error) {
        // ในสถานการณ์จริง ควรบันทึก log แต่สำหรับตอนนี้คืนค่าว่างไปก่อน
        return [];
    }

    // ⭐️ ใช้ Prepared Statement เพื่อความปลอดภัยสูงสุด
    $sql = "SELECT id, title, slug, image_url FROM categories ORDER BY title ASC";
    $result = $conn->query($sql);
    
    $categories = [];
    if ($result && $result->num_rows > 0) {
        // ดึงข้อมูลทั้งหมดมาเก็บใน array
        $categories = $result->fetch_all(MYSQLI_ASSOC);
    }
    
    return $categories;
}