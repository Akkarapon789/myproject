<?php
// pages/index.php (Corrected & Final Version)
session_start();
include '../config/connectdb.php'; 

// 1. เรียกใช้ไฟล์ฟังก์ชันที่อยู่ในโฟลเดอร์เดียวกัน
require_once 'categories.php';
require_once 'products.php'; 

$is_logged_in = isset($_SESSION['role']);

// 2. เรียกใช้ฟังก์ชันเพื่อดึงข้อมูล
$categories = getAllCategories($conn); 
$products = getAllProducts($conn, 8); // ดึงสินค้าแนะนำมา 8 ชิ้น

?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>The Bookmark Society - ร้านหนังสือออนไลน์สำหรับคนรักการอ่าน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../includes/css/style.css"> 
</head>
<body>

<?php include '../includes/navbar.php'; ?>

<section class="hero-section">
    <div class="container">
        <h1 class="display-4">ค้นพบเรื่องราวใหม่ๆ ได้ทุกวัน</h1>
        <p class="lead">ที่ The Bookmark Society เราเชื่อว่าทุกเล่มมีเรื่องราวรอให้คุณไปสัมผัส</p>
        <a href="all_products.php" class="btn btn-warning btn-lg mt-3">เลือกซื้อหนังสือทั้งหมด</a>
    </div>
</section>

<div class="container my-5">

    <section class="category-section text-center mb-5 py-4">
        <h2 class="mb-4">สำรวจตามหมวดหมู่</h2>
        <div class="d-flex flex-wrap justify-content-center gap-4">
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $cat): ?>
                    <div class="category-item">
                        <a href="category.php?id=<?= $cat['id'] ?>" class="text-decoration-none">
                            <img src="../<?= htmlspecialchars($cat['image_url'] ?? 'assets/default-category.png') ?>" alt="<?= htmlspecialchars($cat['title']) ?>" style="width: 90px; height: 90px; object-fit: cover;">
                            <small class="d-block mt-2"><?= htmlspecialchars($cat['title']) ?></small>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">ยังไม่มีข้อมูลหมวดหมู่ในระบบ</p>
            <?php endif; ?>
        </div>
    </section>

    <hr class="my-5">

    <section class="featured-products-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">สินค้าแนะนำสำหรับคุณ</h2>
            <a href="all_products.php" class="btn btn-outline-primary">ดูสินค้าทั้งหมด <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                <div class="col">
                    <div class="card product-card h-100">
                        <a href="product_detail.php?id=<?= $product['id'] ?>" class="text-decoration-none">
                            <img src="../<?= htmlspecialchars($product['image_url'] ?? 'assets/default-product.png') ?>" class="card-img-top" alt="<?= htmlspecialchars($product['title']) ?>">
                        </a>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">
                                <a href="product_detail.php?id=<?= $product['id'] ?>" class="text-decoration-none text-dark"><?= htmlspecialchars($product['title']) ?></a>
                            </h5>
                            <div class="mt-auto">
                                <div class="mb-2"><span class="product-price-new">฿<?= number_format($product['price'], 2) ?></span></div>
                                <?php if ($is_logged_in): ?>
                                    <form action="../cart/cart_actions.php" method="POST" class="d-grid">
                                        <input type="hidden" name="action" value="add">
                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                        <button type="submit" class="btn btn-primary"><i class="fas fa-cart-plus"></i> เพิ่มลงตะกร้า</button>
                                    </form>
                                <?php else: ?>
                                    <a href="../auth/login.php" class="btn btn-outline-primary d-grid">ล็อกอินเพื่อสั่งซื้อ</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                 <p class="text-muted col-12 text-center">ยังไม่มีสินค้าในระบบ กรุณารอAdminเพิ่มสินค้าในระบบ </p>
            <?php endif; ?>
        </div>
    </section>

</div>

<?php include '../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>