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
    <title>คำถามที่พบบ่อย - The Bookmark Society</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../includes/css/style.css">
</head>
<body>

<div class="container my-5">
    <div class="text-center mb-5">
        <h1 class="display-5">คำถามที่พบบ่อย (FAQ)</h1>
        <p class="lead text-muted">เราได้รวบรวมคำถามที่ลูกค้าสอบถามเข้ามาบ่อยที่สุดไว้ที่นี่</p>
    </div>

    <div class="accordion" id="faqAccordion">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                    มีช่องทางการชำระเงินแบบไหนบ้าง?
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                <div class="accordion-body text-muted">
                    เรามีบริการ 2 ช่องทางหลักคือ:
                    <ul>
                        <li><strong>ชำระเงินปลายทาง (Cash on Delivery):</strong> ชำระเงินสดกับพนักงานจัดส่งเมื่อได้รับสินค้า</li>
                        <li><strong>โอนเงินผ่านธนาคาร / QR Code:</strong> คุณสามารถสแกน QR Code หรือโอนเงินผ่านเลขที่บัญชีที่แสดงในหน้าชำระเงินได้ทันที</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                    ใช้เวลาจัดส่งกี่วัน?
                </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body text-muted">
                    โดยปกติแล้ว สินค้าจะใช้เวลาจัดส่งประมาณ 2-4 วันทำการ (ไม่รวมวันเสาร์-อาทิตย์ และวันหยุดนักขัตฤกษ์) สำหรับพื้นที่กรุงเทพฯ และปริมณฑล และ 3-5 วันสำหรับพื้นที่ต่างจังหวัด
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                    จะตรวจสอบสถานะคำสั่งซื้อได้อย่างไร?
                </button>
            </h2>
            <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body text-muted">
                    หลังจากเข้าสู่ระบบ คุณสามารถไปที่เมนูโปรไฟล์ของคุณ และเลือก "ประวัติการสั่งซื้อ" เพื่อดูสถานะล่าสุดของคำสั่งซื้อทั้งหมดของคุณได้
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>