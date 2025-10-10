<?php
include('../config/connectdb.php');

if(isset($_POST['query'])) {
    $q = mysqli_real_escape_string($conn, $_POST['query']);

    $sql = "SELECT id, title, author, price, image_url 
            FROM products 
            WHERE title LIKE '%$q%' 
               OR author LIKE '%$q%' 
               OR description LIKE '%$q%' 
               OR category LIKE '%$q%' 
            LIMIT 10";

    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            echo '
            <a href="../product_detail.php?id='.$row['id'].'" 
               class="list-group-item list-group-item-action d-flex align-items-center">
                <img src="'.$row['image_url'].'" 
                     alt="img" 
                     class="me-3 rounded" 
                     style="width:50px; height:50px; object-fit:cover;">
                <div>
                    <div class="fw-bold">'.$row['title'].'</div>
                    <small class="text-muted">'.$row['author'].' - ฿'.number_format($row['price'],2).'</small>
                </div>
            </a>';
        }
    } else {
        echo '<div class="list-group-item text-muted text-center">ไม่พบผลลัพธ์</div>';
    }
}
?>