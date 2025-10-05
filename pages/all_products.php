<?php
session_start();
include '../config/connectdb.php';
require_once 'products.php';

// ตรวจสอบสถานะการล็อกอิน
$is_logged_in = isset($_SESSION['role']);

// ---- Pagination setup ----
$limit = 20; // ✅ แสดง 20 ชิ้นต่อหน้า
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $limit;

// ---- Sorting setup ----
$sort = $_GET['sort'] ?? 'newest';
$order_sql = '';

switch ($sort) {
    case 'price_asc':
        $order_sql = "ORDER BY price ASC";
        break;
    case 'price_desc':
        $order_sql = "ORDER BY price DESC";
        break;
    case 'name_asc':
        $order_sql = "ORDER BY title ASC";
        break;
    case 'name_desc':
        $order_sql = "ORDER BY title DESC";
        break;
    default:
        $order_sql = "ORDER BY id DESC"; // newest
        break;
}

// ---- นับจำนวนสินค้าทั้งหมด ----
$count_query = "SELECT COUNT(*) AS total FROM products";
$count_result = mysqli_query($conn, $count_query);
$total_rows = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_rows / $limit);

// ---- ดึงข้อมูลสินค้าตามหน้าและการเรียง ----
$query = "SELECT * FROM products $order_sql LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);

// ---- ฟังก์ชันภาพสินค้า ----
function getProductImageUrl(string $title): string {
    return "https://picsum.photos/300/200?random=" . crc32($title);
}
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>สินค้าทั้งหมด - The Bookmark Society</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .product-card .card-img-top { height: 200px; object-fit: cover; }
        .product-price-new { font-size: 1.25em; font-weight: 700; color: #FCC61D; margin-right: 5px;}
        .product-price-old { font-size: 0.9em; text-decoration: line-through; color: #6c757d;}
        .sort-bar { display: flex; justify-content: end; gap: 10px; margin-bottom: 20px; }
        .sort-bar select { width: 200px; }
    </style>
</head>
<body>

<?php include '../includes/navbar.php'; ?>

<div class="container mt-5 mb-5">
    <h1 class="text-center mb-4">สินค้าทั้งหมด</h1>

    <!-- 🔽 ปุ่มเรียงตาม -->
    <form method="GET" class="sort-bar">
        <input type="hidden" name="page" value="<?= $page ?>">
        <select name="sort" class="form-select" onchange="this.form.submit()">
            <option value="newest" <?= $sort == 'newest' ? 'selected' : '' ?>>ใหม่ล่าสุด</option>
            <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>ราคาต่ำ → สูง</option>
            <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>ราคาสูง → ต่ำ</option>
            <option value="name_asc" <?= $sort == 'name_asc' ? 'selected' : '' ?>>ชื่อ A → Z</option>
            <option value="name_desc" <?= $sort == 'name_desc' ? 'selected' : '' ?>>ชื่อ Z → A</option>
        </select>
    </form>

    <!-- 🔽 แสดงสินค้า -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mb-4">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="col">
                    <div class="card product-card h-100 shadow-sm">
                        <img src="<?= getProductImageUrl($product['title']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['title']) ?>">
                        <div class="card-body">
                            <h5 class="card-title fs-6">
                                <?= htmlspecialchars($product['title']) ?>
                            </h5>
                            <div class="mt-2 mb-3">
                                <span class="product-price-new">฿ <?= number_format($product['price'] * 0.8, 2) ?></span>
                                <span class="product-price-old">฿ <?= number_format($product['price'], 2) ?></span>
                            </div>
                            <?php if ($is_logged_in): ?>
                                <form action="../cart/add.php" method="POST" class="d-grid gap-2">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <button type="submit" class="btn btn-primary">🛒 เพิ่มลงตะกร้า</button>
                                </form>
                            <?php else: ?>
                                <a href="../auth/login.php" class="btn btn-outline-primary d-grid gap-2">
                                    ล็อกอินเพื่อสั่งซื้อ
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">ไม่พบสินค้าในระบบ</p>
        <?php endif; ?>
    </div>

    <!-- 🔽 Pagination -->
    <nav>
        <ul class="pagination justify-content-center">
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page - 1 ?>&sort=<?= $sort ?>">«</a>
            </li>

            <?php
            // แสดงแค่บางหน้ากลาง ๆ เพื่อไม่ให้ยาวเกิน
            $start = max(1, $page - 2);
            $end = min($total_pages, $page + 2);
            for ($i = $start; $i <= $end; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&sort=<?= $sort ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page + 1 ?>&sort=<?= $sort ?>">»</a>
            </li>
        </ul>
    </nav>

</div>

<?php include '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>