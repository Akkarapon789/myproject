<?php
// pages/fetch_products.php (Updated with Quick View)
session_start();
include '../config/connectdb.php';

$is_logged_in = isset($_SESSION['role']);
$limit = 12; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$sort = $_GET['sort'] ?? 'newest';

switch ($sort) {
    case 'price_asc':  $order_sql = "ORDER BY price ASC"; break;
    case 'price_desc': $order_sql = "ORDER BY price DESC"; break;
    case 'name_asc':   $order_sql = "ORDER BY title ASC"; break;
    case 'name_desc':  $order_sql = "ORDER BY title DESC"; break;
    case 'newest':
    default:           $order_sql = "ORDER BY id DESC"; break;
}

$query = "SELECT id, title, price, image_url FROM products $order_sql LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

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