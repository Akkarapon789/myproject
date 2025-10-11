<?php
// pages/category.php (Updated with Quick View)
session_start();
include '../config/connectdb.php';
$is_logged_in = isset($_SESSION['role']);

$category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($category_id === 0) {
    die("ไม่พบหมวดหมู่ที่ระบุ (URL ต้องมี ?id=... ต่อท้าย)");
}

// ดึงข้อมูลของหมวดหมู่นี้เพื่อแสดงชื่อ
$stmt_cat = $conn->prepare("SELECT title FROM categories WHERE id = ?");
$stmt_cat->bind_param("i", $category_id);
$stmt_cat->execute();
$category_result = $stmt_cat->get_result();
$category = $category_result->fetch_assoc();
$stmt_cat->close();

if (!$category) {
    die("หมวดหมู่ ID: $category_id ไม่ถูกต้อง หรือไม่มีในระบบ");
}

// ดึงสินค้าทั้งหมดที่อยู่ในหมวดหมู่นี้
$stmt_prod = $conn->prepare("SELECT * FROM products WHERE category_id = ? ORDER BY id DESC");
$stmt_prod->bind_param("i", $category_id);
$stmt_prod->execute();
$products_result = $stmt_prod->get_result();
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>หมวดหมู่: <?= htmlspecialchars($category['title']) ?> - The Bookmark Society</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../includes/css/style.css">
</head>
<body>

<?php include '../includes/navbar.php'; ?>

<div class="container my-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">หน้าแรก</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($category['title']) ?></li>
        </ol>
    </nav>
    <h1 class="mb-4">หมวดหมู่: <?= htmlspecialchars($category['title']) ?></h1>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        <?php if ($products_result->num_rows > 0): ?>
            <?php while ($product = $products_result->fetch_assoc()): ?>
            <div class="col">
                <div class="card product-card h-100">
                    <div class="product-card-img-container">
                        <img src="../<?= htmlspecialchars($product['image_url'] ?? 'assets/default.jpg') ?>" class="card-img-top" alt="<?= htmlspecialchars($product['title']) ?>">
                        <div class="quick-view-overlay">
                            <span class="quick-view-btn js-quick-view" data-id="<?= $product['id'] ?>">Quick View</span>
                        </div>
                    </div>
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
        <?php else: ?>
            <div class="col-12">
                <p class="text-center text-muted fs-4 mt-5">ไม่พบสินค้าในหมวดหมู่นี้</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>