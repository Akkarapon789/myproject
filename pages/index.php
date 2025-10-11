<?php
// pages/index.php (Updated with Quick View)
session_start();
include '../config/connectdb.php'; 
require_once 'categories.php';
require_once 'products.php'; 
$is_logged_in = isset($_SESSION['role']);
$categories = getAllCategories($conn); 
$products = getAllProducts($conn, 8);
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

<section class="featured-products-section">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">สินค้าแนะนำสำหรับคุณ</h2>
        <a href="all_products.php" class="btn btn-outline-primary">ดูสินค้าทั้งหมด <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        <?php foreach ($products as $product): ?>
        <div class="col">
            <div class="card product-card h-100">
                <a href="product_detail.php?id=<?= $product['id'] ?>" class="text-decoration-none">
                    <img src="../<?= htmlspecialchars($product['image_url'] ?? 'assets/default-product.png') ?>" class="card-img-top" alt="<?= htmlspecialchars($product['title']) ?>">
                    <div class="quick-view-overlay">
                        <span class="quick-view-btn">Quick View</span>
                    </div>
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
    </div>
</section>

</div>

<?php include '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>