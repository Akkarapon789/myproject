<?php
session_start();
include '../config/connectdb.php';
include '../includes/navbar.php'; // เรียกใช้ Navbar ของหน้าบ้าน

$cart_items = $_SESSION['cart'] ?? [];
$total_price = 0;
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ตะกร้าสินค้า - The Bookmark Society</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../includes/css/style.css">
</head>
<body>

<div class="container my-5">
    <h1 class="mb-4">🛒 ตะกร้าสินค้าของคุณ</h1>

    <?php if (!empty($cart_items)): ?>
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <?php foreach ($cart_items as $product_id => $item): ?>
                    <?php $subtotal = $item['price'] * $item['quantity']; $total_price += $subtotal; ?>
                    <div class="row mb-4 align-items-center">
                        <div class="col-md-2">
                            <img src="../<?= htmlspecialchars($item['image_url'] ?? 'assets/default.jpg') ?>" class="img-fluid rounded">
                        </div>
                        <div class="col-md-4">
                            <h5 class="mb-0"><?= htmlspecialchars($item['title']) ?></h5>
                            <small class="text-muted">ราคา: ฿<?= number_format($item['price'], 2) ?></small>
                        </div>
                        <div class="col-md-3">
                            <form action="cart_actions.php" method="POST" class="d-flex">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="product_id" value="<?= $product_id ?>">
                                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" class="form-control form-control-sm me-2" style="width: 70px;" onchange="this.form.submit()">
                            </form>
                        </div>
                        <div class="col-md-2 text-end">
                            <strong>฿<?= number_format($subtotal, 2) ?></strong>
                        </div>
                        <div class="col-md-1 text-end">
                            <a href="cart_actions.php?action=remove&product_id=<?= $product_id ?>" class="text-danger"><i class="fas fa-trash"></i></a>
                        </div>
                    </div>
                    <hr>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h4 class="mb-3">สรุปยอดสั่งซื้อ</h4>
                    <div class="d-flex justify-content-between">
                        <span>ยอดรวม</span>
                        <strong>฿<?= number_format($total_price, 2) ?></strong>
                    </div>
                    <hr>
                    <div class="d-grid">
                        <a href="checkout.php" class="btn btn-primary btn-lg">ดำเนินการสั่งซื้อ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="text-center py-5">
        <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
        <h3>ตะกร้าของคุณว่างเปล่า</h3>
        <p>เลือกซื้อหนังสือดีๆ เพิ่มลงในตะกร้าได้เลย</p>
        <a href="../pages/all_products.php" class="btn btn-primary mt-3">เลือกซื้อสินค้า</a>
    </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>