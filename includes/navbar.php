<?php
// includes/navbar.php (Corrected & Final Version)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// คำนวณจำนวนสินค้าในตะกร้า
$cartCount = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantity'];
    }
}
?>
<nav class="navbar navbar-expand-lg navbar-main sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="../pages/index.php">
            <img src="../assets/logo/2.png" alt="Logo" style="width:50px; height:50px;">
            <span class="ms-2 fs-4 fw-bold">The Bookmark</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <div class="position-relative flex-grow-1 mx-lg-5">
                <input id="searchInput" class="form-control" type="text" placeholder="ค้นหาหนังสือ, ผู้แต่ง..." autocomplete="off">
                <div id="searchResults" class="list-group position-absolute w-100 shadow-sm mt-1" style="z-index: 2000; display: none; max-height: 300px; overflow-y: auto;"></div>
            </div>
            
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="../pages/all_products.php">สินค้าทั้งหมด</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../pages/promotions.php">โปรโมชั่น</a>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a href="../cart/cart.php" class="nav-link position-relative">
                            <i class="fas fa-shopping-cart fa-lg"></i>
                            <span id="cartCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6em;"><?= $cartCount ?></span>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img src="https://i.pravatar.cc/40" alt="avatar" width="32" height="32" class="rounded-circle">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="../pages/profile.php">โปรไฟล์ของฉัน</a></li>
                            <li><a class="dropdown-item" href="../pages/order_history.php">ประวัติการสั่งซื้อ</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../auth/logout.php">ออกจากระบบ</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item ms-lg-2">
                        <a href="../auth/login.php" class="btn btn-outline-primary btn-sm">เข้าสู่ระบบ</a>
                    </li>
                    <li class="nav-item">
                        <a href="../auth/sign-up.php" class="btn btn-primary btn-sm">สมัครสมาชิก</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){ /* ... โค้ด search ... */ });
</script>