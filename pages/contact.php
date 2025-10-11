<?php
session_start();
include '../config/connectdb.php';
$is_logged_in = isset($_SESSION['user_id']);
include '../includes/navbar.php';
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ติดต่อเรา - The Bookmark Society</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../includes/css/style.css">
</head>
<body>

<div class="container my-5">
    <div class="text-center mb-5">
        <h1 class="display-5">ติดต่อเรา</h1>
        <p class="lead text-muted">เราพร้อมรับฟังทุกข้อเสนอแนะและตอบทุกคำถามของคุณ</p>
    </div>

    <div class="row g-5">
        <div class="col-lg-6">
            <h4 class="mb-3">ข้อมูลการติดต่อ</h4>
            <div class="d-flex mb-3">
                <i class="fas fa-map-marker-alt fa-2x text-primary mt-1"></i>
                <div class="ms-3">
                    <strong>ที่อยู่:</strong><br>
                    <span class="text-muted">Kham Riang, Maha Sarakham, TH</span>
                </div>
            </div>
             <div class="d-flex mb-3">
                <i class="fas fa-envelope fa-2x text-primary mt-1"></i>
                <div class="ms-3">
                    <strong>อีเมล:</strong><br>
                    <a href="mailto:contact@bookmarksociety.com" class="text-muted">contact@bookmarksociety.com</a>
                </div>
            </div>
             <div class="d-flex mb-3">
                <i class="fas fa-phone fa-2x text-primary mt-1"></i>
                <div class="ms-3">
                    <strong>โทรศัพท์:</strong><br>
                    <a href="tel:+66123456789" class="text-muted">+66 12 345 6789</a>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
             <h4 class="mb-3">ส่งข้อความถึงเรา</h4>
             <form action="#" method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">ชื่อของคุณ</label>
                    <input type="text" class="form-control" id="name" required>
                </div>
                 <div class="mb-3">
                    <label for="email" class="form-label">อีเมล</label>
                    <input type="email" class="form-control" id="email" required>
                </div>
                 <div class="mb-3">
                    <label for="message" class="form-label">ข้อความ</label>
                    <textarea class="form-control" id="message" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">ส่งข้อความ</button>
             </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>