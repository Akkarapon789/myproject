<?php

session_start();
// 1. เชื่อมต่อฐานข้อมูล: นำเข้าไฟล์เชื่อมต่อ สร้างตัวแปร $conn
include '../config/connectdb.php'; 

// 2. เรียกใช้ไฟล์ฟังก์ชัน 
require_once 'categories.php';
require_once 'products.php'; 

// ตรวจสอบสถานะการล็อกอิน
$is_logged_in = isset($_SESSION['role']);

// 3. ดึงข้อมูลที่ต้องการแสดง 
$categories = getAllCategories($conn); 

// 🔹 Pagination Start
$limit = 12; // จำนวนสินค้าต่อหน้า
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// นับสินค้าทั้งหมด
$count_sql = "SELECT COUNT(*) AS total FROM products";
$count_result = mysqli_query($conn, $count_sql);
$total_items = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_items / $limit);

// ดึงสินค้าตามหน้า
$query = "SELECT * FROM products LIMIT $start, $limit";
$result = mysqli_query($conn, $query);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);
// 🔹 Pagination End

// ฟังก์ชันสำหรับสร้าง URL ของภาพ 
function getCategoryImageUrl(string $slug): string {
    return "https://picsum.photos/100/100?random=" . crc32($slug); 
}
function getProductImageUrl(string $title): string {
    return "https://picsum.photos/300/200?random=" . crc32($title); 
}
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>หน้าแรก - The Bookmark Society</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* CSS สำหรับจัดรูปแบบการ์ดสินค้าและหมวดหมู่ */
        .category-item img { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 1px solid #ddd; transition: transform 0.2s;}
        .category-item img:hover { transform: scale(1.05); box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);}
        .product-card .card-img-top { height: 200px; object-fit: cover;}
        .product-price-new { font-size: 1.25em; font-weight: 700; color: #FCC61D; margin-right: 5px;}
        .product-price-old { font-size: 0.9em; text-decoration: line-through; color: #6c757d;}
        .rating-stars { color: gold; font-size: 0.9em;}
        .card-body { position: relative;}
        .stretched-link-details { position: absolute; top: 0; left: 0; width: 100%; height: 80%; z-index: 1; } 

        /* 🔹 Pagination CSS */
        .pagination .page-item.active .page-link {
            background-color: #FCC61D;
            border-color: #FCC61D;
            color: #fff;
        }
        .pagination .page-link {
            color: #FCC61D;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            text-align: center;
            line-height: 25px;
            font-weight: bold;
        }
        .pagination .page-link:hover {
            background-color: #fff6d5;
        }

        /* 🔹 View All Button */
        .btn-view-all {
            background-color: #FCC61D;
            border: none;
            font-weight: 600;
            color: #000;
            padding: 10px 30px;
            border-radius: 50px;
            transition: 0.3s;
        }
        .btn-view-all:hover {
            background-color: #ffdd57;
            transform: scale(1.05);
            color: #000;
        }
    </style>
</head>
<body>

<?php include '../includes/navbar.php'; ?>

<div class="container mt-5">
    
    <div class="p-5 mb-4 bg-light rounded-3">
        <div class="container-fluid py-5">
            <h1 class="display-5 fw-bold">ยินดีต้อนรับสู่ The Bookmark Society</h1>
            <p class="col-md-8 fs-4">
                <?php if ($is_logged_in): ?>
                    สวัสดีครับ, คุณ<?php echo htmlspecialchars($_SESSION['firstname']); ?> คุณสามารถสั่งซื้อสินค้าได้แล้ว
                <?php endif; ?>
            </p>
        </div>
    </div>
    
    <?php if (!$is_logged_in): ?>
    <div class="alert alert-info text-center mb-5" role="alert">
        กรุณา <a href="../auth/login.php" class="alert-link">ล็อกอิน</a> เพื่อสั่งซื้อสินค้า
    </div>
    <?php endif; ?>

    <h2 class="text-center mb-4">หมวดหมู่หนังสือ</h2>
    <div class="row row-cols-auto justify-content-center g-4 mb-5">
        <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $cat): ?>
                <div class="col text-center">
                    <a href="/category/<?= htmlspecialchars($cat['slug']) ?>" class="d-block text-decoration-none text-dark">
                        <img src="<?= getCategoryImageUrl($cat['slug']) ?>" 
                             alt="<?= htmlspecialchars($cat['title']) ?>" 
                             class="mb-2">
                        <small class="d-block"><?= htmlspecialchars($cat['title']) ?></small>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">ไม่พบหมวดหมู่</p>
        <?php endif; ?>
    </div>
    
    <hr class="my-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">สินค้าแนะนำ</h2>
        <a href="all_products.php" class="btn btn-view-all">ดูสินค้าทั้งหมด</a>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mb-5">
    <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
            <div class="col">
                <div class="card product-card h-100 shadow-sm">
                    <img src="<?= getProductImageUrl($product['title']) ?>" 
                         class="card-img-top" 
                         alt="<?= htmlspecialchars($product['title']) ?>">
                    
                    <div class="card-body">
                        <h5 class="card-title fs-6">
                            <a href="/product/<?= htmlspecialchars($product['slug']) ?>" class="text-decoration-none text-dark">
                                <?= htmlspecialchars($product['title']) ?>
                            </a>
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
        <p>ไม่พบสินค้าในระบบ</p>
    <?php endif; ?>
</div>

<div class="text-center my-5">
    <a href="all_products.php" class="btn btn-lg btn-primary" 
       style="padding: 12px 30px; font-size: 1.2em; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
        ดูสินค้าเพิ่มเติม
    </a>
</div>

</div>

<?php include '../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>