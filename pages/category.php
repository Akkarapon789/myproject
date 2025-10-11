<?php
// pages/category.php (Updated with Quick View)
session_start();
include '../config/connectdb.php';
$is_logged_in = isset($_SESSION['role']);

$category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
// ... (โค้ดดึงข้อมูล category และ products เหมือนเดิม) ...
?>
<!doctype html>
<html lang="th">
<head>
    </head>
<body>

<?php include '../includes/navbar.php'; ?>

<div class="container my-5">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        <?php while ($product = $products_result->fetch_assoc()): ?>
        <div class="col">
            <div class="card product-card h-100">
                <a href="product_detail.php?id=<?= $product['id'] ?>" class="text-decoration-none">
                    <img src="../<?= htmlspecialchars($product['image_url'] ?? 'assets/default.jpg') ?>" class="card-img-top" alt="<?= htmlspecialchars($product['title']) ?>">
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
        <?php endwhile; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>