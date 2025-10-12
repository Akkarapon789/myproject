<footer class="footer-main pt-5 pb-4">
    </footer>

<div class="modal fade" id="quickViewModal" tabindex="-1">
    </div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function(){

    // --- ระบบค้นหา ---
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
    $(document).click(function(e){
        if (!$(e.target).closest('#searchInput, #searchResults').length) {
            $('#searchResults').hide();
        }
    });

    // --- ระบบ Quick View ---
    $(document).on('click', '.js-quick-view', function(e) {
        // ... (โค้ด Quick View เหมือนเดิม) ...
    });
});
</script>

</body>
</html>