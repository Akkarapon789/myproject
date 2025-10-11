<?php
session_start();
include '../config/connectdb.php';

$is_logged_in = isset($_SESSION['role']);

if (!isset($_GET['id'])) die("ไม่พบสินค้าที่ระบุ");
$product_id = intval($_GET['id']);

$sql = "SELECT p.*, c.title as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = $product_id";
$result = mysqli_query($conn, $sql);
$product = mysqli_fetch_assoc($result);
if (!$product) die("ไม่พบข้อมูลสินค้า");

// ... โค้ดดึงรีวิว ...
$sql_reviews = "SELECT * FROM reviews WHERE product_id = $product_id ORDER BY created_at DESC";
$result_reviews = mysqli_query($conn, $sql_reviews);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['title']); ?> - The Bookmark Society</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../includes/css/style.css">
</head>
<body>

<?php include '../includes/navbar.php'; ?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-4">
            <img src="../<?= htmlspecialchars($product['image_url'] ?? 'assets/default.jpg') ?>" class="img-fluid rounded shadow-sm w-100" style="aspect-ratio: 3/4; object-fit: cover;">
        </div>
        <div class="col-md-8">
            <span class="badge bg-primary mb-2"><?= htmlspecialchars($product['category_name'] ?? 'ไม่มีหมวดหมู่') ?></span>
            <h1><?= htmlspecialchars($product['title']) ?></h1>
            <p class="text-muted">โดย: ผู้แต่งสมมติ</p>
            
            <div class="d-flex align-items-center mb-3">
                </div>

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

    <div class="review-section mt-5 pt-5 border-top">
        <h3>รีวิวจากลูกค้า</h3>
        <?php if ($result_reviews->num_rows > 0): ?>
            <?php while ($review = $result_reviews->fetch_assoc()): ?>
                <div class="mb-3 border-bottom pb-3">
                    <strong><?= htmlspecialchars($review['user_name']) ?></strong>
                    <p class="mb-0 mt-1"><?= htmlspecialchars($review['comment']) ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-muted">ยังไม่มีรีวิวสำหรับสินค้านี้</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>