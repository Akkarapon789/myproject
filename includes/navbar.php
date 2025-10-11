<?php
// navbar.php (Revamped)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$cartCount = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
?>
<nav class="navbar navbar-expand-lg navbar-main sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
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
                    <a class="nav-link" href="all_products.php">สินค้าทั้งหมด</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="promotions.php">โปรโมชั่น</a>
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
                            <li>
                                <a class="dropdown-item" href="#">โปรไฟล์ของฉัน</a>
                                <a class="dropdown-item" href="../pages/order_history.php">ประวัติการสั่งซื้อ</a>
                            </li>
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
$(document).ready(function(){
    $('#searchInput').on('keyup', function(){
        let query = $(this).val().trim();
        if(query.length < 2){ // เริ่มค้นหาเมื่อพิมพ์ 2 ตัวอักษรขึ้นไป
            $('#searchResults').hide();
            return;
        }
        $.ajax({
            url: '../search/search_ajax.php',
            method: 'POST',
            data: {query: query},
            success: function(data){
                $('#searchResults').html(data).show();
            }
        });
    });
    $(document).click(function(e){
        if (!$(e.target).closest('#searchInput, #searchResults').length) {
            $('#searchResults').hide();
        }
    });
});
</script>