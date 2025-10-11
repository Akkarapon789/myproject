<footer class="footer-main pt-5 pb-4">
    <div class="container text-center text-md-start">
        <div class="row">
            <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
                <h6 class="text-uppercase fw-bold">The Bookmark Society</h6>
                <hr class="mb-4 mt-0 d-inline-block mx-auto" style="width: 60px; background-color: var(--accent-color); height: 2px"/>
                <p>ร้านหนังสือออนไลน์ที่คัดสรรหนังสือคุณภาพเพื่อนักอ่านทุกคน พร้อมบริการที่เป็นมิตรและจัดส่งรวดเร็ว</p>
            </div>
            <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
                <h6 class="text-uppercase fw-bold">ช่วยเหลือ</h6>
                <hr class="mb-4 mt-0 d-inline-block mx-auto" style="width: 60px; background-color: var(--accent-color); height: 2px"/>
                <p><a href="../pages/how_to_order.php" class="text-reset">วิธีการสั่งซื้อ</a></p>
                <p><a href="../pages/faq.php" class="text-reset">คำถามที่พบบ่อย</a></p>
                <p><a href="../pages/contact.php" class="text-reset">ติดต่อเรา</a></p>
            </div>
            <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
                <h6 class="text-uppercase fw-bold">ติดต่อเรา</h6>
                <hr class="mb-4 mt-0 d-inline-block mx-auto" style="width: 60px; background-color: var(--accent-color); height: 2px"/>
                <p><i class="fas fa-home me-3"></i> Kham Riang, Maha Sarakham, TH</p>
                <p><i class="fas fa-envelope me-3"></i> contact@bookmarksociety.com</p>
                <p><i class="fas fa-phone me-3"></i> +66 12 345 6789</p>
            </div>
        </div>
    </div>
    <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
        © <?= date("Y") ?> Copyright: <a class="text-white" href="../pages/index.php">TheBookmarkSociety.com</a>
    </div>
</footer>

<div class="modal fade" id="quickViewModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-5 text-center">
            <img id="quickViewImage" src="" class="img-fluid rounded" style="aspect-ratio: 3/4; object-fit: cover;">
          </div>
          <div class="col-md-7">
            <h2 id="quickViewTitle" class="fw-bold"></h2>
            <p id="quickViewCategory" class="text-muted"></p>
            <p id="quickViewDesc" class="lead"></p>
            <div class="my-4">
                <span id="quickViewPrice" class="product-price-new fs-2"></span>
            </div>
            <form id="quickViewForm" action="../cart/cart_actions.php" method="POST" class="d-flex gap-2">
                <input type="hidden" name="action" value="add">
                <input type="hidden" id="quickViewProductId" name="product_id" value="">
                <input type="number" name="quantity" value="1" min="1" class="form-control" style="width: 80px;">
                <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-cart-plus"></i> เพิ่มลงตะกร้า</button>
            </form>
            <a href="#" id="quickViewFullDetails" class="btn btn-outline-secondary mt-3">ดูรายละเอียดสินค้าทั้งหมด</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function(){
    $(document).on('click', '.js-quick-view', function(e) {
        e.preventDefault();
        var productId = $(this).data('id');
        var quickViewModal = new bootstrap.Modal(document.getElementById('quickViewModal'));

        $.ajax({
            url: `../pages/ajax/get_product_details.php?id=${productId}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const product = response.data;
                    $('#quickViewImage').attr('src', `../${product.image_url || 'assets/default.jpg'}`);
                    $('#quickViewTitle').text(product.title);
                    $('#quickViewCategory').text(`หมวดหมู่: ${product.category_name || 'ไม่ระบุ'}`);
                    $('#quickViewDesc').text((product.description || 'ไม่มีคำอธิบาย').substring(0, 150) + '...');
                    $('#quickViewPrice').text(`฿${parseFloat(product.price).toFixed(2)}`);
                    $('#quickViewProductId').val(product.id);
                    $('#quickViewFullDetails').attr('href', `../pages/product_detail.php?id=${product.id}`);
                    quickViewModal.show();
                } else {
                    alert('ไม่พบข้อมูลสินค้า');
                }
            },
            error: function() {
                alert('เกิดข้อผิดพลาดในการดึงข้อมูล');
            }
        });
    });
});
</script>

</body>
</html>