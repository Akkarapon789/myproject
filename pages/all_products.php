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

        /* 🔽 Pagination Shopee Style - ปรับสี #2155CD */
        .pagination {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            list-style: none;
            gap: 6px;
            padding: 0;
        }
        .pagination .page-item .page-link {
            border: none;
            background-color: #f1f3f5;
            color: #333;
            font-weight: 500;
            padding: 10px 18px;
            border-radius: 8px;
            transition: all 0.25s ease-in-out;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
        .pagination .page-item .page-link:hover {
            background-color: #2155CD;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 3px 6px rgba(33, 85, 205, 0.3);
        }
        .pagination .page-item.active .page-link {
            background-color: #2155CD;
            color: white;
            font-weight: bold;
            box-shadow: 0 3px 8px rgba(33, 85, 205, 0.4);
        }
        .pagination .page-item.disabled .page-link {
            opacity: 0.5;
            cursor: not-allowed;
            background-color: #e9ecef;
        }
        nav.pagination-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }
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
                            <h5 class="card-title fs-6"><?= htmlspecialchars($product['title']) ?></h5>
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

    <!-- 🔽 Pagination (Shopee-style เต็มรูปแบบ) -->
    <nav class="pagination-wrapper">
        <ul class="pagination">
            <!-- ปุ่มก่อนหน้า -->
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= max(1, $page - 1) ?>&sort=<?= $sort ?>">«</a>
            </li>

        <?php
            // --- ตั้งค่าการแสดงเลขหน้า ---
            $visible_pages = 5; // จำนวนหน้าที่จะแสดงตรงกลาง
            $half = floor($visible_pages / 2);

            $start_page = max(1, $page - $half);
            $end_page = min($total_pages, $page + $half);

            // ปรับกรณีอยู่ต้น/ท้าย
            if ($page <= $half) {
                $end_page = min($visible_pages, $total_pages);
            }
            if ($page > $total_pages - $half) {
                $start_page = max(1, $total_pages - $visible_pages + 1);
            }

            // --- แสดงหน้าแรก + จุด ... ---
            if ($start_page > 1) {
                echo '<li class="page-item"><a class="page-link" href="?page=1&sort=' . $sort . '">1</a></li>';
                if ($start_page > 2) {
                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }
            }

            // --- แสดงเลขหน้าหลัก ---
            for ($i = $start_page; $i <= $end_page; $i++) {
                $active = ($i == $page) ? 'active' : '';
                echo '<li class="page-item ' . $active . '">
                        <a class="page-link" href="?page=' . $i . '&sort=' . $sort . '">' . $i . '</a>
                      </li>';
            }

            // --- แสดงจุด ... + หน้าสุดท้าย ---
            if ($end_page < $total_pages) {
                if ($end_page < $total_pages - 1) {
                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }
                echo '<li class="page-item"><a class="page-link" href="?page=' . $total_pages . '&sort=' . $sort . '">' . $total_pages . '</a></li>';
            }
        ?>

            <!-- ปุ่มถัดไป -->
            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= min($total_pages, $page + 1) ?>&sort=<?= $sort ?>">»</a>
            </li>
    </ul>
    </nav>
</div>

<?php include '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>

