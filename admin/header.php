<?php
// session_start() ถูกเรียกที่ไฟล์หลักแล้ว ไม่จำเป็นต้องเรียกซ้ำ
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet"> <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body id="page-top">

    <div id="wrapper">

        <?php // include 'sidebar.php'; // โครงสร้างเว็บของคุณอาจมี sidebar ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
            
            <?php // include 'topbar.php'; // โครงสร้างเว็บของคุณอาจมี topbar ?>

                <div class="container-fluid pt-4">

                <?php if (isset($_SESSION['success']) || isset($_SESSION['error'])): ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
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

                ```