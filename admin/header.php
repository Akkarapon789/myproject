<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ระบบจัดการหลังบ้าน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

<div class="d-flex" id="wrapper">
    <div id="sidebar-wrapper">
        <div class="sidebar-heading">Admin Panel</div>
        <div class="list-group list-group-flush">
            <a href="index.php" class="list-group-item list-group-item-action"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="products.php" class="list-group-item list-group-item-action"><i class="fas fa-box"></i> จัดการสินค้า</a>
            <a href="categories.php" class="list-group-item list-group-item-action"><i class="fas fa-tags"></i> จัดการหมวดหมู่</a>
            <a href="promotions.php" class="list-group-item list-group-item-action"><i class="fas fa-percent"></i> จัดการโปรโมชั่น</a>
            <a href="orders.php" class="list-group-item list-group-item-action"><i class="fas fa-shopping-cart"></i> จัดการออเดอร์</a>
            <a href="users.php" class="list-group-item list-group-item-action"><i class="fas fa-users"></i> จัดการผู้ใช้</a>
            <a href="../pages/index.php" class="list-group-item list-group-item-action bg-secondary mt-auto"><i class="fas fa-sign-out-alt"></i> กลับไปหน้าเว็บ</a>
        </div>
    </div>
    <div id="page-content-wrapper">
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
            <div class="container-fluid">
                <button class="btn btn-primary" id="menu-toggle"><i class="fas fa-bars"></i></button>

                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i> Admin
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="#">โปรไฟล์</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="../auth/login.php">ออกจากระบบ</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container-fluid p-4">
<?php if (isset($_SESSION['success']) || isset($_SESSION['error'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            <?php if (isset($_SESSION['success'])): ?>
                Toast.fire({
                    icon: 'success',
                    title: '<?= addslashes($_SESSION['success']); ?>'
                });
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                Toast.fire({
                    icon: 'error',
                    title: '<?= addslashes($_SESSION['error']); ?>'
                });
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        });
    </script>
<?php endif; ?>     