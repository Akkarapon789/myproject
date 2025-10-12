<?php
// pages/product_detail.php (Upgraded with Image Gallery)
session_start();
include '../config/connectdb.php';
$is_logged_in = isset($_SESSION['user_id']);

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($product_id === 0) die("ไม่พบสินค้า");

// 1. ดึงข้อมูลสินค้าหลัก
$stmt_prod = $conn->prepare("SELECT p.*, c.title as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt_prod->bind_param("i", $product_id);
$stmt_prod->execute();
$product = $stmt_prod->get_result()->fetch_assoc();
$stmt_prod->close();

if (!$product) die("ไม่พบข้อมูลสินค้า");

// ⭐️⭐️⭐️ 2. ดึงรูปภาพทั้งหมดจากตาราง `product_images` ⭐️⭐️⭐️
$stmt_gallery = $conn->prepare("SELECT image_url FROM product_images WHERE product_id = ? ORDER BY id ASC");
$stmt_gallery->bind_param("i", $product_id);
$stmt_gallery->execute();
$gallery_result = $stmt_gallery->get_result();
$gallery_images = $gallery_result->fetch_all(MYSQLI_ASSOC);
$stmt_gallery->close();

include '../includes/navbar.php';
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['title']); ?> - The Bookmark Society</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../includes/css/style.css">
    <style>
        .product-thumbnails img {
            cursor: pointer;
            border: 2px solid transparent;
            transition: border-color 0.2s;
            aspect-ratio: 1/1;
            object-fit: cover;
        }
        .product-thumbnails img:hover, .product-thumbnails img.active {
            border-color: var(--primary-color);
        }
    </style>
</head>
<body>

<div class="container my-5">
    <div class="row">
        <div class="col-md-5">
            <img id="mainProductImage" src="../<?= htmlspecialchars($product['image_url'] ?? 'assets/default.jpg') ?>" class="img-fluid rounded shadow-sm w-100 mb-3" style="aspect-ratio: 3/4; object-fit: cover;">
            
            <?php if (!empty($gallery_images)): ?>
            <div class="product-thumbnails d-flex gap-2">
                <?php foreach ($gallery_images as $index => $img): ?>
                    <img src="../<?= htmlspecialchars($img['image_url']) ?>" class="img-fluid rounded w-25 <?= $index == 0 ? 'active' : '' ?>" onclick="changeImage('../<?= htmlspecialchars($img['image_url']) ?>', this)">
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-md-7">
            <span class="badge bg-primary mb-2"><?= htmlspecialchars($product['category_name'] ?? 'ไม่มีหมวดหมู่') ?></span>
            <h1><?= htmlspecialchars($product['title']) ?></h1>
            <p class="text-muted">โดย: ผู้แต่งสมมติ</p>
            
            <p class="lead"><?= nl2br(htmlspecialchars($product['description'] ?? 'ไม่มีคำอธิบายสำหรับสินค้านี้')) ?></p>
            
            <div class="my-4">
                <span class="product-price-new fs-2">฿<?= number_format($product['price'], 2) ?></span>
            </div>
            
            <?php if ($is_logged_in): ?>
                <form action="../cart/cart_actions.php" method="POST" class="d-flex gap-2">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?= $product_id ?>">
                    <input type="number" name="quantity" value="1" min="1" class="form-control" style="width: 80px;">
                    <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-cart-plus"></i> เพิ่มลงตะกร้า</button>
                </form>
            <?php else: ?>
                <a href="../auth/login.php" class="btn btn-primary btn-lg">ล็อกอินเพื่อสั่งซื้อ</a>
            <?php endif; ?>
        </div>
    </div>
    </div>

<?php include '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
function changeImage(newSrc, clickedElement) {
    // เปลี่ยนรูปภาพหลัก
    document.getElementById('mainProductImage').src = newSrc;

    // จัดการ active class บน thumbnails
    document.querySelectorAll('.product-thumbnails img').forEach(thumb => {
        thumb.classList.remove('active');
    });
    clickedElement.classList.add('active');
}
</script>
</body>
</html>