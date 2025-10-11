<?php
// cart/cart.php (Upgraded with SweetAlert2)
session_start();
include '../config/connectdb.php';

$cart_items = $_SESSION['cart'] ?? [];
$cart_total = 0;

$today = date('Y-m-d');
$promo_result = $conn->query("SELECT * FROM promotions WHERE start_date <= '{$today}' AND end_date >= '{$today}'");

include '../includes/navbar.php'; 
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ตะกร้าสินค้า - The Bookmark Society</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../includes/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
                    <?php $subtotal = $item['price'] * $item['quantity']; $cart_total += $subtotal; ?>
                    <div class="row mb-4 align-items-center">
                        <div class="col-md-2"><img src="../<?= htmlspecialchars($item['image_url'] ?? 'assets/default.jpg') ?>" class="img-fluid rounded"></div>
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
                        <div class="col-md-2 text-end"><strong>฿<?= number_format($subtotal, 2) ?></strong></div>
                        <div class="col-md-1 text-end"><a href="cart_actions.php?action=remove&product_id=<?= $product_id ?>" class="text-danger"><i class="fas fa-trash"></i></a></div>
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
                    
                    <div class="mb-3">
                        <label for="promo_select" class="form-label">เลือกโปรโมชั่น</label>
                        <select id="promo_select" class="form-select">
                            <option value="0">-- ไม่ใช้โปรโมชั่น --</option>
                            <?php while($promo = $promo_result->fetch_assoc()): ?>
                                <option value="<?= $promo['id'] ?>"><?= htmlspecialchars($promo['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <hr>

                    <div class="d-flex justify-content-between">
                        <span>ยอดรวม</span>
                        <strong id="cart-total">฿<?= number_format($cart_total, 2) ?></strong>
                    </div>
                    
                    <div class="d-flex justify-content-between text-danger" id="discount-row" style="display: none;">
                        <span>ส่วนลดโปรโมชั่น</span>
                        <strong id="discount-amount">- ฿0.00</strong>
                    </div>
                    <hr>

                    <div class="d-flex justify-content-between fw-bold fs-5">
                        <span>ยอดรวมสุทธิ</span>
                        <strong id="final-total">฿<?= number_format($cart_total, 2) ?></strong>
                    </div>
                    
                    <div class="d-grid mt-3">
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
            <a href="../pages/all_products.php" class="btn btn-primary mt-3">เลือกซื้อสินค้า</a>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    $('#promo_select').on('change', function() {
        var promo_id = $(this).val();

        $.ajax({
            url: 'apply_promo.php',
            type: 'POST',
            data: { promo_id: promo_id },
            dataType: 'json',
            success: function(response) {
                // อัปเดตตัวเลขในหน้าเว็บ
                $('#discount-amount').text(response.discount_formatted);
                $('#final-total').text(response.final_total_formatted);
                
                // แสดง/ซ่อนแถวส่วนลด
                if (response.discount_value > 0) {
                    $('#discount-row').show();
                    // แสดง popup สวยๆ บอกว่าใช้ส่วนลดสำเร็จ
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'ใช้ส่วนลดสำเร็จ!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    $('#discount-row').hide();
                }
            },
            error: function() {
                // เปลี่ยนจาก alert() ธรรมดา มาเป็น SweetAlert
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถใช้โปรโมชั่นได้ กรุณาลองใหม่อีกครั้ง'
                });
            }
        });
    });
});
</script>

</body>
</html>