```php
</div> </div>
    </div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>
    // รอให้เอกสารโหลดเสร็จก่อนค่อยทำงาน
    $(document).ready(function(){
        // เมื่อคลิกปุ่มที่มี id="menu-toggle"
        $("#menu-toggle").click(function(e) {
            e.preventDefault();
            // ให้สลับคลาส "toggled" ที่ #wrapper
            $("#wrapper").toggleClass("toggled");
        });
    });
</script>

</body>
</html>