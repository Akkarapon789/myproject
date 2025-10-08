<?php
// fetch_products.php
session_start();
include '../config/connectdb.php';

$is_logged_in = isset($_SESSION['role']);

$limit = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $limit;
$sort = $_GET['sort'] ?? 'random'; // ✅ ค่าเริ่มต้นคือสุ่ม

// ✅ เลือกเงื่อนไขเรียงสินค้า
switch ($sort) {
    case 'price_asc':  $order_sql = "ORDER BY price ASC"; break;
    case 'price_desc': $order_sql = "ORDER BY price DESC"; break;
    case 'name_asc':   $order_sql = "ORDER BY title ASC"; break;
    case 'name_desc':  $order_sql = "ORDER BY title DESC"; break;
    case 'newest':     $order_sql = "ORDER BY id DESC"; break;
    case 'random':     // ✅ สุ่มลำดับสินค้า
    default:           $order_sql = "ORDER BY RAND()"; break;
}

// ✅ ดึงข้อมูลสินค้า
$query = "SELECT * FROM products $order_sql LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);

// ✅ ฟังก์ชันภาพสินค้า
function getProductImageUrl(int $product_id, mysqli $conn): string {
    // ดึงชื่อไฟล์รูปจาก DB
    $stmt = $conn->prepare("SELECT image_url, title FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row && !empty($row['image_url'])) {
        // ถ้ามีรูป ให้ใช้ URL ของไฟล์ใน uploads
        return "../uploads/" . $row['image_url'];
    } elseif ($row) {
        // ถ้าไม่มีรูป ใช้ placeholder แบบเดิม
        return "https://picsum.photos/300/200?random=" . crc32($row['title']);
    } else {
        // ถ้า product ไม่เจอ ให้ใช้ placeholder เฉย ๆ
        return "https://picsum.photos/300/200?random=" . time();
    }
}
?>

<?php if (!empty($products)): ?>
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mb-4">
    <?php foreach ($products as $product): ?>
        <div class="col">
            <div class="card product-card h-100 shadow-sm">
                <img src="<?= getProductImageUrl($product['title']) ?>" 
                     class="card-img-top" 
                     alt="<?= htmlspecialchars($product['title']) ?>">
                <div class="card-body">
                    <h5 class="card-title fs-6"><?= htmlspecialchars($product['title']) ?></h5>
                    <div class="mt-2 mb-3">
                        <span class="product-price-new">฿ <?= number_format($product['price'] * 0.8, 2) ?></span>
                        <span class="product-price-old">฿ <?= number_format($product['price'], 2) ?></span>
                    </div>

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mb-5">
    <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>

            <?php
            // ดึงคะแนนเฉลี่ยจากตาราง reviews
            $pid = $product['id'];
            $review_sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews FROM reviews WHERE product_id = $pid";
            $review_result = mysqli_query($conn, $review_sql);
            $review_data = mysqli_fetch_assoc($review_result);

            $avg_rating = round($review_data['avg_rating'], 1);
            $total_reviews = $review_data['total_reviews'];
            ?>

            <div class="col">
                <div class="card product-card h-100 shadow-sm">
                    <img src="<?= getProductImageUrl($product['title']) ?>" 
                        class="card-img-top" 
                        alt="<?= htmlspecialchars($product['title']) ?>">
                    
                    <div class="card-body">
                        <h5 class="card-title fs-6">
                            <a href="product_detail.php?id=<?= $product['id'] ?>" class="text-decoration-none text-dark">
                                <?= htmlspecialchars($product['title']) ?>
                            </a>
                        </h5>

                        <!-- 🔹 แสดงคะแนนรีวิวเฉลี่ย -->
                        <div class="rating-stars mb-2">
                            <?php
                            if ($total_reviews > 0) {
                                $fullStars = floor($avg_rating);
                                $halfStar = ($avg_rating - $fullStars >= 0.5);
                                for ($i = 0; $i < $fullStars; $i++) echo "⭐";
                                if ($halfStar) echo "⭐️";
                                echo " <small>(" . $avg_rating . "/5 จาก " . $total_reviews . " รีวิว)</small>";
                            } else {
                                echo "<small class='text-muted'>ยังไม่มีรีวิว</small>";
                            }
                            ?>
                        </div>

                        <div class="mt-2 mb-3">
                            <span class="product-price-new">฿ <?= number_format($product['price'] * 0.8, 2) ?></span>
                            <span class="product-price-old">฿ <?= number_format($product['price'], 2) ?></span>
                        </div>

                        <?php if ($is_logged_in): ?>
                            <form action="../cart/add.php" method="POST" class="d-grid gap-2">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <button type="submit" class="btn btn-primary">เพิ่มลงตะกร้า</button>
                            </form>
                        <?php else: ?>
                            <a href="../auth/login.php" class="btn btn-outline-primary d-grid gap-2">
                                ล็อกอินเพื่อสั่งซื้อ
                            </a>
                        <?php endif; ?>

                        <!-- 🔹 ปุ่มดูรายละเอียดสินค้า -->
                        <a href="product_detail.php?id=<?= $product['id'] ?>" class="btn btn-outline-secondary mt-2 d-grid gap-2">
                            ดูรายละเอียดสินค้า
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>ไม่พบสินค้าในระบบ</p>
    <?php endif; ?>
</div>

<div class="text-center my-5">
    <a href="all_products.php" class="btn btn-lg btn-primary" 
       style="padding: 12px 20px; font-size: 1.2em; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
        ดูสินค้าเพิ่มเติม
    </a>
</div>

<?php
// ✅ Pagination
$count_query = "SELECT COUNT(*) AS total FROM products";
$count_result = mysqli_query($conn, $count_query);
$total_rows = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_rows / $limit);

$visible_pages = 5;
$half = floor($visible_pages / 2);
$start_page = max(1, $page - $half);
$end_page = min($total_pages, $page + $half);
if ($page <= $half) $end_page = min($visible_pages, $total_pages);
if ($page > $total_pages - $half) $start_page = max(1, $total_pages - $visible_pages + 1);
?>

<nav class="pagination-wrapper">
    <ul class="pagination">
        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= max(1,$page-1) ?>&sort=<?= $sort ?>">«</a>
        </li>

        <?php
        if ($start_page > 1) {
            echo '<li class="page-item"><a class="page-link" href="?page=1&sort='.$sort.'">1</a></li>';
            if ($start_page > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        for ($i=$start_page; $i<=$end_page; $i++) {
            $active = ($i==$page)?'active':'';
            echo '<li class="page-item '.$active.'"><a class="page-link" href="?page='.$i.'&sort='.$sort.'">'.$i.'</a></li>';
        }
        if ($end_page < $total_pages) {
            if ($end_page < $total_pages-1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            echo '<li class="page-item"><a class="page-link" href="?page='.$total_pages.'&sort='.$sort.'">'.$total_pages.'</a></li>';
        }
        ?>
        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= min($total_pages,$page+1) ?>&sort=<?= $sort ?>">»</a>
        </li>
    </ul>
</nav>

<?php else: ?>
<p class="text-center">ไม่พบสินค้าในระบบ</p>
<?php endif; ?>

<?php mysqli_close($conn); ?>
