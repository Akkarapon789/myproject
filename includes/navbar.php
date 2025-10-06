<?php
// navbar.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// จำนวนสินค้าทั้งหมดในตะกร้า
$cartCount = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
?>
<style>
.navbar-custom {
    background-color: #2155CD;
    border-bottom: 1px solid #2155CD;
    padding: 10px 8px;
}
.navbar-custom1 {
    background-color: #2155CD;
    border-bottom: 1px solid #2155CD;
    padding: 10px 8px;
}
.navbar-custom .nav-link {
    color: #FDDE55;
    font-size: 14px;
    margin-right: 15px;
    padding: 0 10px;
}
.navbar-custom .nav-link:hover {
    color: #fff;
}

/* 🎨 Search bar styling */
#searchInput {
    border: 2px solid #FDDE55;
    border-radius: 30px;
    padding: 10px 18px;
    transition: all 0.2s ease-in-out;
}
#searchInput:focus {
    box-shadow: 0 0 8px rgba(253, 222, 85, 0.7);
    border-color: #fff;
}
#searchResults {
    background-color: #ffffff;
    border: 1px solid #FDDE55;
    border-radius: 10px;
    overflow: hidden;
}
#searchResults a {
    color: #333;
    transition: background 0.15s;
}
#searchResults a:hover {
    background-color: #FFF8DC;
    color: #2155CD;
}
#searchResults img {
    border: 1px solid #eee;
}

/* ปรับ dropdown ให้ดูโปร */
.dropdown-menu {
    border-radius: 10px;
    border: 1px solid #FDDE55;
}
.dropdown-menu a:hover {
    background-color: #FDDE55;
    color: #2155CD;
}

/* 🟡 แถบหมวดหมู่ */
.category-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    padding: 8px 10px;
    border-bottom: 1px solid #f1f1f1;
    background: #fffbea;
}
.category-tag {
    background-color: #FDDE55;
    color: #2155CD;
    font-weight: 500;
    border-radius: 20px;
    padding: 4px 12px;
    cursor: pointer;
    transition: all 0.15s ease-in-out;
}
.category-tag:hover {
    background-color: #2155CD;
    color: #fff;
}
</style>

<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        <!-- ด้านซ้าย -->
        <a class="nav-link" href="#">Seller Centre</a>
        <a class="nav-link" href="#">เปิดร้านค้า</a>
        <a class="nav-link" href="#">ดาวน์โหลด</a>

        <!-- Social -->
        <span class="nav-link">ติดตามเรา</span>

        <!-- ด้านขวา -->
        <div class="ms-auto d-flex align-items-center">
            <a class="nav-link" href="#">การแจ้งเตือน</a>
            <a class="nav-link" href="#">ช่วยเหลือ</a>
        </div>

        <!-- ภาษา -->
        <div class="dropdown me-3">
            <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
                ไทย
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">ไทย</a></li>
                <li><a class="dropdown-item" href="#">English</a></li>
            </ul>
        </div>
    </div>
</nav>

<nav class="navbar navbar-expand-lg navbar-custom1">
  <div class="container-fluid d-flex align-items-center justify-content-between">
      
      <!-- Logo -->
      <a href="index.php" class="d-flex align-items-center text-decoration-none me-3">
          <img src="../assets/logo/2.png" style="width:80px; height:80px;">
          <span class="ms-3 fs-2 fw-bold" style="color:#FDDE55;">The Bookmark</span>
      </a>

      <!-- 🔍 Search bar -->
      <div class="position-relative flex-grow-1 mx-3">
          <input id="searchInput" 
                 class="form-control" 
                 type="text" 
                 placeholder="ค้นหาหนังสือ..." 
                 aria-label="Search" 
                 autocomplete="off">

          <!-- กล่องแสดงผลการค้นหา -->
          <div id="searchResults" 
               class="list-group position-absolute w-100 shadow-sm mt-1"
               style="z-index: 2000; display: none; max-height: 400px; overflow-y: auto;">
              <div id="categoryTagsContainer"></div>
              <div id="searchListContainer"></div>
          </div>
      </div>

      <!-- User Section -->
      <div class="text-end d-flex align-items-center gap-3">
          <?php if (isset($_SESSION['user_id'])): ?>
              <button id="cartButton"
                      class="cart-btn btn btn-outline-light position-relative"
                      aria-controls="cartOffcanvas"
                      aria-expanded="false"
                      aria-label="Shopping cart"
                      onclick="window.location.href='../cart/cart.php'">
                  <svg viewBox="0 0 26.6 25.6" width="24" height="24">
                      <polyline fill="none" points="2 1.7 5.5 1.7 9.6 18.3 21.2 18.3 24.6 6.1 7 6.1"
                                  stroke="white" stroke-linecap="round" stroke-linejoin="round" 
                                  stroke-miterlimit="10" stroke-width="2.5"></polyline>
                      <circle cx="10.7" cy="23" r="2.2" fill="white"></circle>
                      <circle cx="19.7" cy="23" r="2.2" fill="white"></circle>
                  </svg>
                  <span id="cartCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                      <?= $cartCount ?>
                  </span>
              </button>

              <div class="dropdown">
                  <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="avatarDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                      <img src="https://down-th.img.susercontent.com/file/6109d8ed7204998f787c35686d70229e_tn" 
                           alt="avatar" width="40" height="40" class="rounded-circle">
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="avatarDropdown">
                      <li><a class="dropdown-item" href="#">โปรไฟล์</a></li>
                      <li><a class="dropdown-item" href="#">การตั้งค่า</a></li>
                      <li><hr class="dropdown-divider"></li>
                      <li><a class="dropdown-item" href="../auth/logout.php">ออกจากระบบ</a></li>
                  </ul>
              </div>
          <?php else: ?>
              <a href="../auth/login.php" class="btn btn-warning">Login</a>
              <a href="../auth/sign-up.php" class="btn btn-outline-warning">Sign-up</a>
          <?php endif; ?>
      </div>

  </div>
</nav>

<!-- ✅ JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){

    // 🛒 อัปเดตตะกร้า
    $('.add-to-cart-btn').click(function(){
        var productId = $(this).data('id');
        $.post('../cart/add_to_cart.php', {product_id: productId}, function(response){
            var data = JSON.parse(response);
            $('#cartCount').text(data.count);
        });
    });

    // 🔍 Real-time search + Category Tags
    $('#searchInput').on('keyup', function(){
        let query = $(this).val().trim();
        if(query.length === 0){
            $('#searchResults').hide();
            return;
        }

        $.ajax({
            url: '../search/search_ajax.php',
            method: 'POST',
            data: {query: query},
            success: function(data){
                $('#searchResults').show();
                $('#categoryTagsContainer').html(data.categories);
                $('#searchListContainer').html(data.results);
            },
            dataType: 'json'
        });
    });

    // 🟡 เมื่อคลิกแท็บหมวดหมู่
    $(document).on('click', '.categories-tag', function(){
        let categories = $(this).data('categories');
        let query = $('#searchInput').val().trim();

        $.ajax({
            url: '../search/search_ajax.php',
            method: 'POST',
            data: {query: query, categories: categories},
            success: function(data){
                $('#categoriesTagsContainer').html(data.categories);
                $('#searchListContainer').html(data.results);
            },
            dataType: 'json'
        });
    });

    // คลิกข้างนอกให้ปิดผลลัพธ์
    $(document).click(function(e){
        if (!$(e.target).closest('#searchInput, #searchResults').length) {
            $('#searchResults').hide();
        }
    });
});
</script>