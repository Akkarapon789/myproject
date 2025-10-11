<?php
// pages/index.php (Corrected Path)
session_start();
include '../config/connectdb.php'; 
require_once 'categories.php';
require_once 'products.php'; 

$is_logged_in = isset($_SESSION['role']);
$categories = getAllCategories($conn); 
$products = getAllProducts($conn, 8);
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>The Bookmark Society - ร้านหนังสือออนไลน์สำหรับคนรักการอ่าน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="../includes/css/style.css"> 
</head>
<body>

<?php include '../includes/navbar.php'; ?>

<section class="hero-section">
    </section>

<div class="container my-5">
    <section class="category-section text-center mb-5 py-4">
        </section>
    <hr class="my-5">
    <section class="featured-products-section">
        </section>
</div>

<?php include '../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>