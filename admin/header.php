<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการหลังบ้าน</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
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
            <a href="orders.php" class="list-group-item list-group-item-action"><i class="fas fa-shopping-cart"></i> จัดการออเดอร์</a>
            <a href="users.php" class="list-group-item list-group-item-action"><i class="fas fa-users"></i> จัดการผู้ใช้</a>
            <a href="../" class="list-group-item list-group-item-action bg-secondary mt-auto"><i class="fas fa-sign-out-alt"></i> กลับไปหน้าเว็บ</a>
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
                                <a class="dropdown-item" href="#">ออกจากระบบ</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container-fluid p-4"> ```