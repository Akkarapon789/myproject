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
    case 'price_asc': $order_sql = "ORDER BY price ASC"; break;
    case 'price_desc': $order_sql = "ORDER BY price DESC"; break;
    case 'name_asc': $order_sql = "ORDER BY title ASC"; break;
    case 'name_desc': $order_sql = "ORDER BY title DESC"; break;
    default: $order_sql = "ORDER BY id DESC";
}

// นับจำนวนสินค้าทั้งหมด
$count_query = "SELECT COUNT(*) AS total FROM products";
$count_result = mysqli_query($conn, $count_query);
$total_rows = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_rows / $limit);

// ดึงข้อมูลสินค้าตามหน้า
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

        /* Pagination Shopee Style */
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

    <!-- Sort -->
    <form method="GET" class="sort-bar" id="sort-form">
        <input type="hidden" name="page" value="<?= $page ?>">
        <select name="sort" class="form-select" onchange="document.getElementById('sort-form').submit()">
            <option value="newest" <?= $sort == 'newest' ? 'selected' : '' ?>>ใหม่ล่าสุด</option>
            <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>ราคาต่ำ → สูง</option>
            <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>ราคาสูง → ต่ำ</option>
            <option value="name_asc" <?= $sort == 'name_asc' ? 'selected' : '' ?>>ชื่อ A → Z</option>
            <option value="name_desc" <?= $sort == 'name_desc' ? 'selected' : '' ?>>ชื่อ Z → A</option>
        </select>
    </form>

    <!-- Product List (AJAX จะเปลี่ยนเฉพาะส่วนนี้) -->
    <div id="product-list">
        <?php include 'fetch_products.php'; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// AJAX Pagination
document.addEventListener('click', function(e) {
    const link = e.target.closest('.pagination a.page-link');
    if (link) {
        e.preventDefault();
        const url = new URL(link.href);
        const params = new URLSearchParams(url.search);
        const page = params.get('page');
        const sort = params.get('sort');

        // fetch_products.php ต้องอยู่ใน path เดียวกับ all_products.php
        fetch(`fetch_products.php?page=${page}&sort=${sort}`)
            .then(res => res.text())
            .then(html => {
                document.getElementById('product-list').innerHTML = html;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            })
            .catch(err => console.error(err));
    }
});
</script>
</body>
</html>
<?php mysqli_close($conn); ?>
