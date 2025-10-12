<footer class="footer-main pt-5 pb-4">
    </footer>

<div class="modal fade" id="quickViewModal" tabindex="-1">
    </div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function(){

    // --- ระบบค้นหา (ย้ายมาจาก navbar.php) ---
    $('#searchInput').on('keyup', function(){
        let query = $(this).val().trim();
        if(query.length < 2){
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
    // คลิกข้างนอกเพื่อซ่อนผลการค้นหา
    $(document).click(function(e){
        if (!$(e.target).closest('#searchInput, #searchResults').length) {
            $('#searchResults').hide();
        }
    });

    // --- ระบบ Quick View ---
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