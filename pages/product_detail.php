<?php
include '../config/connectdb.php';
session_start();

if (!isset($_GET['id'])) die("ไม่พบสินค้าที่ระบุ");
$product_id = intval($_GET['id']);

// ดึงข้อมูลสินค้า
$sql = "SELECT * FROM products WHERE id = $product_id";
$result = mysqli_query($conn, $sql);
$product = mysqli_fetch_assoc($result);
if (!$product) die("ไม่พบข้อมูลสินค้า");

// ดึงรีวิว
$sql_reviews = "SELECT * FROM reviews WHERE product_id = $product_id ORDER BY created_at DESC";
$result_reviews = mysqli_query($conn, $sql_reviews);

// คะแนนเฉลี่ย
$sql_avg = "SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviews FROM reviews WHERE product_id = $product_id";
$result_avg = mysqli_query($conn, $sql_avg);
$avg_data = mysqli_fetch_assoc($result_avg);
$avg_rating = $avg_data['avg_rating'] ? round($avg_data['avg_rating'],1) : 0;
$total_reviews = $avg_data['total_reviews'];
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title><?php echo htmlspecialchars($product['name']); ?> - รายละเอียดสินค้า</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
/* ⭐ ปรับสวยสำหรับรายละเอียดสินค้า */
.rating-stars { color: gold; font-size: 1rem; }
.review-item { border-bottom: 1px solid #eee; padding: 15px 0; }
.review-item:last-child { border-bottom: none; }
.add-review { margin-top: 20px; }
.product-price { font-size: 1.4rem; font-weight: 700; color: #FCC61D; }
.product-price-old { font-size: 1rem; text-decoration: line-through; color: #6c757d; margin-left: 10px; }
.btn-detail { margin-top: 10px; }
.container { max-width: 1000px; }
</style>
</head>
<body class="bg-light">

<div class="container my-5 bg-white p-4 rounded shadow-sm">
    <div class="row">
        <div class="col-md-5 text-center">
            <img src="<?php echo $product['image']; ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        <div class="col-md-7">
            <h2 class="fw-bold"><?php echo htmlspecialchars($product['name']); ?></h2>
            <!-- ⭐ คะแนนเฉลี่ย -->
            <div class="mb-2">
                <?php
                if ($total_reviews > 0) {
                    $fullStars = floor($avg_rating);
                    $halfStar = ($avg_rating - $fullStars >= 0.5);
                    for ($i=0; $i<$fullStars; $i++) echo "⭐";
                    if ($halfStar) echo "⭐️";
                    echo " <span class='text-muted'>($avg_rating/5 จาก $total_reviews รีวิว)</span>";
                } else {
                    echo "<span class='text-muted'>ยังไม่มีรีวิว</span>";
                }
                ?>
            </div>
            <p class="mb-3"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            <div class="mb-3">
                <span class="product-price">฿ <?php echo number_format($product['price']*0.8,2); ?></span>
                <span class="product-price-old">฿ <?php echo number_format($product['price'],2); ?></span>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="index.php" class="btn btn-outline-secondary">กลับหน้าหลัก</a>
                <a href="add_review.php?product_id=<?php echo $product_id; ?>" class="btn btn-primary btn-detail">เขียนรีวิวสินค้า</a>
            </div>
        </div>
    </div>

    <!-- รีวิวสินค้า -->
    <div class="mt-5">
        <h4 class="mb-3">รีวิวสินค้า</h4>
        <?php if(mysqli_num_rows($result_reviews) > 0): ?>
            <?php while($review = mysqli_fetch_assoc($result_reviews)): ?>
                <div class="review-item">
                    <div class="d-flex justify-content-between">
                        <strong><?php echo htmlspecialchars($review['user_name']); ?></strong>
                        <small class="text-muted"><?php echo $review['created_at']; ?></small>
                    </div>
                    <div class="rating-stars mb-1">
                        <?php for($i=1;$i<=5;$i++): ?>
                            <?php echo ($i <= $review['rating']) ? '⭐' : '☆'; ?>
                        <?php endfor; ?>
                    </div>
                    <p><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-muted">ยังไม่มีรีวิวสำหรับสินค้านี้</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
