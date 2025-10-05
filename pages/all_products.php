<?php
session_start();
include '../config/connectdb.php';
require_once 'products.php';
$is_logged_in = isset($_SESSION['role']);
?>
<!doctype html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>สินค้าทั้งหมด - The Bookmark Society</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.product-card .card-img-top { height: 200px; object-fit: cover; }
.product-price-new { font-size:1.25em;font-weight:700;color:#FCC61D;margin-right:5px; }
.product-price-old { font-size:0.9em;text-decoration:line-through;color:#6c757d; }
.sort-bar { display:flex; justify-content:end; gap:10px; margin-bottom:20px; }
.sort-bar select { width:200px; }
.pagination { display:inline-flex; justify-content:center; align-items:center; list-style:none; gap:6px; padding:0; }
.pagination .page-item .page-link { border:none; background:#f1f3f5; color:#333; font-weight:500; padding:10px 18px; border-radius:8px; transition:all .25s; box-shadow:0 1px 2px rgba(0,0,0,.05); }
.pagination .page-item .page-link:hover { background:#2155CD; color:#fff; transform:translateY(-2px); box-shadow:0 3px 6px rgba(33,85,205,.3); }
.pagination .page-item.active .page-link { background:#2155CD; color:#fff; font-weight:bold; box-shadow:0 3px 8px rgba(33,85,205,.4); }
.pagination .page-item.disabled .page-link { opacity:.5; cursor:not-allowed; background:#e9ecef; }
nav.pagination-wrapper { display:flex; justify-content:center; margin-top:30px; }
</style>
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="container mt-5 mb-5">
<h1 class="text-center mb-4">สินค้าทั้งหมด</h1>

<form id="sort-form" class="sort-bar">
    <select name="sort" class="form-select">
        <option value="newest">ใหม่ล่าสุด</option>
        <option value="price_asc">ราคาต่ำ → สูง</option>
        <option value="price_desc">ราคาสูง → ต่ำ</option>
        <option value="name_asc">ชื่อ A → Z</option>
        <option value="name_desc">ชื่อ Z → A</option>
    </select>
</form>

<div id="product-list">
    <?php include 'fetch_products.php'; ?>
</div>

</div>
<?php include '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// AJAX Pagination
document.addEventListener('click', function(e){
    const link = e.target.closest('.pagination a.page-link');
    if(link){
        e.preventDefault();
        const url = new URL(link.href);
        const page = url.searchParams.get('page');
        const sort = url.searchParams.get('sort');

        fetch(`fetch_products.php?page=${page}&sort=${sort}`)
            .then(res => res.text())
            .then(html => {
                document.getElementById('product-list').innerHTML = html;
                window.scrollTo({top:0, behavior:'smooth'});
            })
            .catch(err => console.error(err));
    }
});

// AJAX Sort
document.getElementById('sort-form').addEventListener('change', function(){
    const sort = this.sort.value;
    fetch(`fetch_products.php?page=1&sort=${sort}`)
        .then(res => res.text())
        .then(html => {
            document.getElementById('product-list').innerHTML = html;
            window.scrollTo({top:0, behavior:'smooth'});
        })
        .catch(err => console.error(err));
});
</script>
</body>
</html>
<?php mysqli_close($conn); ?>
