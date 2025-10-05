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

// ดึงรีวิวสินค้า
$sql_reviews = "SELECT * FROM reviews WHERE product_id = $product_id ORDER BY created_at DESC";
$result_reviews = mysqli_query($conn, $sql_reviews);

// คำนวณคะแนนเฉลี่ย
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
.rating-stars { color: gold; }
.review-item { border-bottom: 1px solid #eee; padding: 10px 0; }
.add-review { margin-top: 20px; }
</style>
</head>
<body>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-4">
            <img src="<?php echo $product['image']; ?>" class="img-fluid" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        <div class="col-md-8">
            <h2><?php echo htmlspecialchars($product['name']); ?></h2>
            <!-- ⭐ คะแนนเฉลี่ย -->
            <div class="mb-2">
                <?php
                if ($total_reviews > 0) {
                    $fullStars = floor($avg_rating);
                    $halfStar = ($avg_rating - $fullStars >= 0.5);
                    for ($i=0; $i<$fullStars; $i++) echo "⭐";
                    if ($halfStar) echo "⭐️";
                    echo " <small>($avg_rating/5 จาก $total_reviews รีวิว)</small>";
                } else {
                    echo "<small class='text-muted'>ยังไม่มีรีวิว</small>";
                }
                ?>
            </div>
            <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            <p><strong>ราคา:</strong> <?php echo number_format($product['price'],2); ?> บาท</p>
            <a href="index.php" class="btn btn-secondary">กลับหน้าหลัก</a>
        </div>
    </div>

    <!-- รีวิวสินค้า -->
    <div class="mt-5">
        <h4>รีวิวสินค้า</h4>
        <?php if(mysqli_num_rows($result_reviews) > 0): ?>
            <?php while($review = mysqli_fetch_assoc($result_reviews)): ?>
                <div class="review-item">
                    <strong><?php echo htmlspecialchars($review['user_name']); ?></strong>
                    <div class="rating-stars">
                        <?php for($i=1; $i<=5; $i++): ?>
                            <?php echo ($i <= $review['rating']) ? '⭐' : '☆'; ?>
                        <?php endfor; ?>
                    </div>
                    <p><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                    <small><?php echo $review['created_at']; ?></small>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>ยังไม่มีรีวิวสำหรับสินค้านี้</p>
        <?php endif; ?>

        <div class="add-review">
            <a href="add_review.php?product_id=<?php echo $product_id; ?>" class="btn btn-primary">เขียนรีวิวสินค้า</a>
        </div>
    </div>
</div>
</body>
</html>
