<?php
include('../config/connectdb.php');
header('Content-Type: application/json; charset=utf-8');

$query = isset($_POST['query']) ? trim($_POST['query']) : '';
$categoryFilter = isset($_POST['category']) ? trim($_POST['category']) : '';

$response = ['categories' => '', 'results' => ''];

if ($query !== '') {
    $q = mysqli_real_escape_string($conn, $query);

    // 🔸 ดึงหมวดหมู่ที่ตรงกับคำค้น โดย join กับตาราง categories
    $catSql = "SELECT DISTINCT c.title AS category_name
               FROM products p
               INNER JOIN categories c ON p.category_id = c.id
               WHERE p.title LIKE '%$q%'
                  OR p.author LIKE '%$q%'
                  OR p.publisher LIKE '%$q%'
                  OR p.description LIKE '%$q%'
                  OR c.title LIKE '%$q%'
               ORDER BY c.title ASC";
    $catResult = mysqli_query($conn, $catSql);

    $categoriesHTML = '<div class=\"category-tags\">';
    while ($catRow = mysqli_fetch_assoc($catResult)) {
        $active = ($catRow['category_name'] === $categoryFilter)
            ? 'style=\"background:#2155CD; color:white;\"'
            : '';
        $categoriesHTML .= '<span class=\"category-tag\" '.$active.' data-category=\"'.$catRow['category_name'].'\">'.$catRow['category_name'].'</span>';
    }
    $categoriesHTML .= '</div>';
    $response['categories'] = $categoriesHTML;

    // 🔸 ดึงสินค้าตามคำค้น + เงื่อนไขหมวดหมู่ที่เลือก
    $catCondition = '';
    if ($categoryFilter !== '') {
        $catCondition = "AND c.title = '".mysqli_real_escape_string($conn, $categoryFilter)."'";
    }

    $sql = "SELECT p.id, p.title, p.author, p.price, p.image_url, c.title AS category_name
            FROM products p
            INNER JOIN categories c ON p.category_id = c.id
            WHERE (p.title LIKE '%$q%'
                OR p.author LIKE '%$q%'
                OR p.publisher LIKE '%$q%'
                OR p.description LIKE '%$q%'
                OR c.title LIKE '%$q%')
            $catCondition
            LIMIT 10";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $resultsHTML = '';
        while ($row = mysqli_fetch_assoc($result)) {
            $resultsHTML .= '
            <a href=\"../product_detail.php?id='.$row['id'].'\" 
               class=\"list-group-item list-group-item-action d-flex align-items-center\">
                <img src=\"'.$row['image_url'].'\" 
                     alt=\"img\" 
                     class=\"me-2 rounded\" 
                     style=\"width:50px; height:50px; object-fit:cover;\">
                <div>
                    <div class=\"fw-bold text-dark\">'.$row['title'].'</div>
                    <small class=\"text-muted\">'.$row['author'].' | '.$row['category_name'].' | ฿'.number_format($row['price'],2).'</small>
                </div>
            </a>';
        }
        $response['results'] = $resultsHTML;
    } else {
        $response['results'] = '<div class=\"list-group-item text-center text-muted\">ไม่พบผลลัพธ์</div>';
    }
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>