<?php
// pages/categories.php (Corrected & Final Version)

/**
 * ฟังก์ชันสำหรับดึงข้อมูลหมวดหมู่ทั้งหมดจากฐานข้อมูล
 * @param mysqli $conn - Object การเชื่อมต่อฐานข้อมูล
 * @return array - Array ของข้อมูลหมวดหมู่
 */
function getAllCategories($conn): array
{
    // 1. ตรวจสอบว่าการเชื่อมต่อถูกต้องหรือไม่
    if (!$conn || $conn->connect_error) {
        return [];
    }

    // 2. ดึงข้อมูลที่จำเป็นทั้งหมด รวมถึง image_url
    $sql = "SELECT id, title, slug, image_url FROM categories ORDER BY id ASC";
    $result = $conn->query($sql);
    
    $categories = [];
    // 3. ตรวจสอบว่ามีผลลัพธ์และมีข้อมูลอย่างน้อย 1 แถวหรือไม่
    if ($result && $result->num_rows > 0) {
        // 4. ดึงข้อมูลทั้งหมดมาเก็บใน array
        $categories = $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // 5. คืนค่า array ที่มีข้อมูล (หรือ array ว่าง ถ้าไม่เจอ)
    return $categories;
}