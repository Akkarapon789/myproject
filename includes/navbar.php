<?php
// navbar.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 🔹 เชื่อมฐานข้อมูลจริง
include '../config/connectdb.php';

// จำนวนสินค้าทั้งหมดในตะกร้า
$cartCount = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
?>
<style>
/* .navbar-custom {
    background-color: #2155CD;
    border-bottom: 1px solid #2155CD;
    padding: 10px 8px;
}
.navbar-custom1 {
    background-color: #2155CD;
    border-bottom: 1px solid #2155CD;
    padding: 10px 8px;
    position: relative;
    overflow: visible !important;
}
.navbar-custom .nav-link {
    color: #FDDE55;
    font-size: 14px;
    margin-right: 15px;
    padding: 0 10px;
}
.navbar-custom .nav-link:hover {
    color: #fff;
} */

/* ✅ ปรับ Search Dropdown ใหม่ ให้ดูเหมือน Shopee และอยู่เหนือ navbar */
#searchResults {
  display: none;
  position: absolute;
  top: 105%;
  left: 0;
  right: 0;
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.15);
  z-index: 99999; /* ← อยู่เหนือ navbar แล้ว */
  max-height: 350px;
  overflow-y: auto;
  padding: 8px 0;
}
#searchResults a.item {
  display: flex;
  align-items: center;
  justify-content: start;
  gap: 10px;
  padding: 10px 15px;
  color: #333;
  text-decoration: none;
  border-bottom: 1px solid #f1f1f1;
  transition: background 0.25s ease;
}
#searchResults a.item:hover {
  background: #f7f9fc;
}
#searchResults img {
  width: 60px;
  height: 60px;
  border-radius: 8px;
  object-fit: cover;
  flex-shrink: 0;
}
#searchResults .info {
  flex: 1;
}
#searchResults .title {
  font-size: 14px;
  font-weight: 500;
  color: #333;
  margin-bottom: 4px;
  line-height: 1.3;
}
#searchResults .price {
  font-size: 13px;
  color: #f77f00;
  font-weight: bold;
}
#searchResults .no-result {
  padding: 15px;
  text-align: center;
  color: #999;
  font-size: 14px;
}
</style>

<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        <a class="nav-link" href="#">Seller Centre</a>
        <a class="nav-link" href="#">เปิดร้านค้า</a>
        <a class="nav-link" href="#">ดาวน์โหลด</a>
        <span class="nav-link">ติดตามเรา</span>
        <div class="ms-auto d-flex align-items-center">
            <a class="nav-link" href="#">การแจ้งเตือน</a>
            <a class="nav-link" href="#">ช่วยเหลือ</a>
        </div>
        <div class="dropdown me-3">
            <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">ไทย</a>
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
      <div class="position-relative flex-grow-1 mx-3" style="max-width: 500px;">
        <input id="searchInput" 
               class="form-control" 
               type="search" 
               placeholder="ค้นหาหนังสือ..." 
               autocomplete="off"
               style="border-radius: 20px; padding: 10px 15px;">
        <div id="searchResults"></div>
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

              <!-- Account Dropdown -->
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

<!-- ✅ JavaScript Section -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){

    // 🛒 อัปเดตตะกร้า (โค้ดเดิม)
    $('.add-to-cart-btn').click(function(){
        var productId = $(this).data('id');
        $.post('../cart/add_to_cart.php', {product_id: productId}, function(response){
            var data = JSON.parse(response);
            $('#cartCount').text(data.count);
        });
    });

    // 🔍 ค้นหาสินค้าแบบเรียลไทม์
    $('#searchInput').on('keyup', function(){
        let query = $(this).val().trim();

        if(query.length < 2){
            $('#searchResults').fadeOut(100);
            return;
        }

        $.ajax({
            url: '',
            method: 'POST',
            data: { ajax_search: true, q: query },
            success: function(data){
                $('#searchResults').html(data).fadeIn(150);
            }
        });
    });

    // 🔘 คลิกข้างนอกเพื่อซ่อน dropdown
    $(document).on('click', function(e){
        if (!$(e.target).closest('#searchInput, #searchResults').length){
            $('#searchResults').fadeOut(150);
        }
    });

});
</script>

<?php
// ✅ ส่วน PHP สำหรับประมวลผล AJAX อยู่ท้ายไฟล์นี้เลย
if (isset($_POST['ajax_search']) && !empty($_POST['q'])) {
    $q = trim($_POST['q']);
    $sql = "SELECT id, title, price FROM products 
            WHERE title LIKE ? OR description LIKE ?
            ORDER BY RAND() LIMIT 6";
    $stmt = $conn->prepare($sql);
    $like = "%$q%";
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0):
        while($row = $result->fetch_assoc()): 
            $img = "https://picsum.photos/seed/" . crc32($row['title']) . "/80/80";
            ?>
            <a href="../products/product_detail.php?id=<?= $row['id'] ?>" class="item">
                <img src="<?= $img ?>" alt="<?= htmlspecialchars($row['title']) ?>">
                <div class="info">
                    <div class="title"><?= htmlspecialchars($row['title']) ?></div>
                    <div class="price">฿<?= number_format($row['price'], 2) ?></div>
                </div>
            </a>
        <?php endwhile;
    else: ?>
        <div class="no-result">ไม่พบสินค้า</div>
    <?php endif;

    $stmt->close();
    $conn->close();
    exit();
}
?>
