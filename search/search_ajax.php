<?php
include('../config/connectdb.php');
header('Content-Type: application/json; charset=utf-8');

$query = isset($_POST['query']) ? trim($_POST['query']) : '';
$categoryFilter = isset($_POST['categories']) ? trim($_POST['categories']) : '';

$response = ['categories' => '', 'results' => ''];

if ($query !== '') {
    $q = mysqli_real_escape_string($conn, $query);
    $catCondition = $categoryFilter ? "AND categories = '".mysqli_real_escape_string($conn, $categoryFilter)."'" : '';

    // 🔸 ดึงหมวดหมู่ที่ตรงกับคำค้น
    $catSql = "SELECT DISTINCT categories FROM products 
               WHERE title LIKE '%$q%' 
                  OR author LIKE '%$q%' 
                  OR description LIKE '%$q%' 
                  OR categories LIKE '%$q%' 
               ORDER BY categories ASC";
    $catResult = mysqli_query($conn, $catSql);

    $categoriesHTML = '<div class="categories-tags">';
    while ($catRow = mysqli_fetch_assoc($catResult)) {
        $active = ($catRow['categories'] === $categoryFilter) ? 'style="background:#2155CD; color:white;"' : '';
        $categoriesHTML .= '<span class="categories-tag" '.$active.' data-categories="'.$catRow['categories'].'">'.$catRow['categories'].'</span>';
    }
    $categoriesHTML .= '</div>';
    $response['categories'] = $categoriesHTML;

    // 🔸 ดึงสินค้าที่ตรง
    $sql = "SELECT id, title, author, price, image_url 
            FROM products 
            WHERE (title LIKE '%$q%' 
                OR author LIKE '%$q%' 
                OR description LIKE '%$q%' 
                OR categories LIKE '%$q%')
            $catCondition
            LIMIT 10";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $resultsHTML = '';
        while ($row = mysqli_fetch_assoc($result)) {
            $resultsHTML .= '
            <a href="../product_detail.php?id='.$row['id'].'" 
               class="list-group-item list-group-item-action d-flex align-items-center">
                <img src="'.$row['image_url'].'" 
                     alt="img" 
                     class="me-2 rounded" 
                     style="width:50px; height:50px; object-fit:cover;">
                <div>
                    <div class="fw-bold text-dark">'.$row['title'].'</div>
                    <small class="text-muted">'.$row['author'].' | ฿'.number_format($row['price'],2).'</small>
                </div>
            </a>';
        }
        $response['results'] = $resultsHTML;
    } else {
        $response['results'] = '<div class="list-group-item text-center text-muted">ไม่พบผลลัพธ์</div>';
    }
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>