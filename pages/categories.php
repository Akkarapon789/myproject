<?php
// pages/categories.php

// ฟังก์ชันนี้จะรับการเชื่อมต่อฐานข้อมูล ($conn)
// และคืนค่าเป็น array ของหมวดหมู่ทั้งหมด
function getAllCategories($conn): array
{
    // ตรวจสอบว่าการเชื่อมต่อถูกต้องหรือไม่
    if (!$conn) {
        return [];
    }

    // ดึงข้อมูลรูปภาพมาด้วย
    $sql = "SELECT id, title, slug, image_url FROM categories ORDER BY title ASC";
    $result = mysqli_query($conn, $sql);
    
    $categories = [];
    if ($result) {
        // ดึงข้อมูลทีละแถวมาเก็บใน array
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row;
        }
    }
    
    return $categories;
}