<?php
session_start();
include '../config/connectdb.php';
$is_logged_in = isset($_SESSION['role']);
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>สินค้าทั้งหมด - The Bookmark Society</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../includes/css/style.css"> </head>
<body>

<?php include '../includes/navbar.php'; ?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">หนังสือทั้งหมด</h1>
        
        <form id="sort-form" class="d-flex align-items-center">
            <label for="sort-select" class="form-label mb-0 me-2">เรียงตาม:</label>
            <select name="sort" id="sort-select" class="form-select" style="width: 200px;">
                <option value="newest">ใหม่ล่าสุด</option>
                <option value="price_asc">ราคา: น้อยไปมาก</option>
                <option value="price_desc">ราคา: มากไปน้อย</option>
                <option value="name_asc">ชื่อ: A-Z</option>
                <option value="name_desc">ชื่อ: Z-A</option>
            </select>
        </form>
    </div>

    <div id="product-list">
        <?php include 'fetch_products.php'; // โหลดข้อมูลหน้าแรก ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ฟังก์ชันสำหรับโหลดสินค้า
function loadProducts(url) {
    fetch(url)
        .then(res => res.text())
        .then(html => {
            document.getElementById('product-list').innerHTML = html;
            window.scrollTo({top: 0, behavior: 'smooth'});
        })
        .catch(err => console.error(err));
}

// AJAX สำหรับ Pagination
document.addEventListener('click', function(e){
    const link = e.target.closest('.pagination a.page-link');
    if(link){
        e.preventDefault();
        loadProducts(link.href);
    }
});

// AJAX สำหรับ Sort
document.getElementById('sort-select').addEventListener('change', function(){
    const sortValue = this.value;
    const url = `fetch_products.php?page=1&sort=${sortValue}`;
    loadProducts(url);
});
</script>
</body>
</html>
<?php mysqli_close($conn); ?>