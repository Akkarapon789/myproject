<?php
// categories.php
// รับ Object การเชื่อมต่อ MySQLi เป็นอาร์กิวเมนต์

function getAllCategories($conn): array
{
    // ตรวจสอบว่ามีการเชื่อมต่อหรือไม่
    if (!$conn) {
        error_log("MySQLi Connection Error: Connection object is invalid.");
        return [];
    }

    try {
        // ใช้ mysqli_query สำหรับการดึงข้อมูล
        $result = mysqli_query($conn, 'SELECT id, title, slug FROM categories ORDER BY title ASC');
        
        $categories = [];
        if ($result) {
            // วนลูปเพื่อดึงข้อมูลทีละแถว
            while ($row = mysqli_fetch_assoc($result)) {
                $categories[] = $row;
            }
            // ปล่อยหน่วยความจำสำหรับชุดผลลัพธ์
            mysqli_free_result($result);
        } else {
            // จัดการข้อผิดพลาดถ้า query ไม่สำเร็จ
            error_log("Category Query Error: " . mysqli_error($conn));
        }
        
        return $categories;
    } catch (\Throwable $e) {
        error_log("Category Fetch Error: " . $e->getMessage());
        return [];
    }
}