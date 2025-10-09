<?php
// header.php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}
include '../config/connectdb.php';

// หาว่าตอนนี้อยู่ที่หน้าไหน เพื่อให้เมนู active ถูกต้อง
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!doctype html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.datatables.net/2.0.3/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="admin_style.css">
</head>

<body>
    <div class="d-flex" id="wrapper">
        <div class="bg-dark" id="sidebar-wrapper">
            <div class="sidebar-heading text-white">
                <i class="fas fa-cogs me-2"></i> MyProject Admin
            </div>
            <div class="list-group list-group-flush">
                <a href="index.php" class="list-group-item list-group-item-action bg-dark text-light <?= ($current_page == 'index.php') ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt fa-fw me-2"></i>แดชบอร์ด
                </a>
                <a href="products.php" class="list-group-item list-group-item-action bg-dark text-light <?= ($current_page == 'products.php') ? 'active' : '' ?>">
                    <i class="fas fa-box fa-fw me-2"></i>สินค้า
                </a>
                <a href="users.php" class="list-group-item list-group-item-action bg-dark text-light <?= ($current_page == 'users.php') ? 'active' : '' ?>">
                    <i class="fas fa-users fa-fw me-2"></i>ผู้ใช้
                </a>
                <a href="orders.php" class="list-group-item list-group-item-action bg-dark text-light <?= ($current_page == 'orders.php') ? 'active' : '' ?>">
                    <i class="fas fa-shopping-cart fa-fw me-2"></i>คำสั่งซื้อ
                </a>
                <a href="adminout.php" class="list-group-item list-group-item-action bg-dark text-danger mt-auto">
                    <i class="fas fa-sign-out-alt fa-fw me-2"></i>ออกจากระบบ
                </a>
            </div>
        </div>
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
                <div class="container-fluid">
                    <button class="btn btn-light" id="menu-toggle"><i class="fas fa-bars"></i></button>
                    <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-user me-1"></i>
                                <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="#">โปรไฟล์</a>
                                <a class="dropdown-item" href="adminout.php">ออกจากระบบ</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="container-fluid p-4">