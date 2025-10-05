<?php
// products.php - ต้องมีการแก้ไขโค้ดส่วนนี้

/**
 * ดึงสินค้าจากฐานข้อมูลด้วย MySQLi
 * @param mixed $conn - Object การเชื่อมต่อ MySQLi
 * @param int|null $limit - จำนวนสินค้าที่ต้องการดึง (null คือทั้งหมด)
 */
function getAllProducts($conn, $limit = null): array
{
    if (!$conn) return [];
    
    try {
        $sql = "
            SELECT 
                p.id, p.title, p.slug, p.price, p.stock, p.image_url, 
                c.title AS category_title, c.slug AS category_slug
            FROM 
                products p
            INNER JOIN 
                categories c ON p.category_id = c.id
            ORDER BY 
                RAND() /* เพิ่มการสุ่มสินค้าที่นี่ */
        ";
        
        // เพิ่ม LIMIT เข้าไปในคำสั่ง SQL ถ้ามีการระบุค่า limit
        if (is_int($limit) && $limit > 0) {
            $sql .= " LIMIT {$limit}";
        }

        $result = mysqli_query($conn, $sql);
        
        $products = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $products[] = $row;
            }
            mysqli_free_result($result);
        } else {
            error_log("All Products Query Error: " . mysqli_error($conn));
        }
        
        return $products;
    } catch (\Throwable $e) {
        error_log("All Products Fetch Error: " . $e->getMessage());
        return [];
    }
}

/**
 * ดึงสินค้าตาม ID หมวดหมู่ (ต้องทำการ Escape ข้อมูลก่อน)
 */
function getProductsByCategory($conn, int $categoryId): array
{
    if (!$conn) return [];
    
    try {
        // สำคัญ: ต้องทำการ Escape ค่าเพื่อความปลอดภัยก่อนนำไปใช้ใน Query
        // ในกรณีที่เป็น Int มักจะไม่ต้อง escape แต่ควรทำเพื่อความปลอดภัยทางอ้อม
        $safeCategoryId = mysqli_real_escape_string($conn, $categoryId);
        
        $sql = "
            SELECT 
                p.id, p.title, p.slug, p.price, p.stock, p.image_url, 
                c.title AS category_title
            FROM 
                products p
            INNER JOIN 
                categories c ON p.category_id = c.id
            WHERE 
                p.category_id = {$safeCategoryId}
            ORDER BY 
                p.created_at DESC
        ";
        
        $result = mysqli_query($conn, $sql);
        
        $products = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $products[] = $row;
            }
            mysqli_free_result($result);
        } else {
            error_log("Products By Category Query Error: " . mysqli_error($conn));
        }
        
        return $products;
    } catch (\Throwable $e) {
        error_log("Products By Category Fetch Error: " . $e->getMessage());
        return [];
    }
}
