<?php 
// auth/sign-up.php (Updated to save birthday)
include("../config/connectdb.php"); 

// เปิด error_reporting ไว้ช่วย debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// เช็กการเชื่อมต่อ DB
if (!$conn) {
    die("❌ Database connection failed: " . mysqli_connect_error());
}
// --- polyfill สำหรับ str_ends_with ถ้าโฮสต์ยังใช้ PHP < 8.0 ---
if (!function_exists('str_ends_with')) {
    function str_ends_with($haystack, $needle) {
        if ($needle === '') return true;
        if (strlen($needle) > strlen($haystack)) return false;
        return substr($haystack, -strlen($needle)) === $needle;
    }
}
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>สมัครสมาชิก</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <img src="../assets/logo/2.png" class="rounded-circle overflow-hidden" style="width:100px; height:100px;">
                    <h2>Sign-up</h2>
                </div>
                <div class="card-body">
                    <form method="post" action="">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">ชื่อ</label>
                                <input type="text" name="firstname" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">นามสกุล</label>
                                <input type="text" name="lastname" class="form-control" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">อีเมล</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">รหัสผ่าน</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ที่อยู่</label>
                            <textarea name="address" class="form-control"></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">เบอร์โทรศัพท์</label>
                                <input type="text" name="phone" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">วันเดือนปีเกิด</label>
                                <input type="date" name="birthday" class="form-control">
                            </div>
                        </div>
                        <label class="mb-3">
                            <input type="checkbox" id="subscribe" name="subscribe" required />
                            ยอมรับเงื่อนไขและข้อตกลงของระบบนี้
                        </label>
                        <button type="submit" name="submit" class="btn btn-primary w-100 mb-2">สมัครสมาชิก</button>
                        <button type="reset" name="reset" class="btn btn-outline-secondary w-100">ล้างข้อมูล</button>
                        <div class="text-center mt-3">
                            <a href="login.php">กลับไปหน้า Login</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
if (isset($_POST['submit'])) {
    $firstname = $_POST['firstname'];
    $lastname  = $_POST['lastname'];
    $email     = $_POST['email'];
    $password  = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $address   = $_POST['address'];
    $phone     = $_POST['phone'];
    // ⭐️ 1. รับค่าวันเกิด (ถ้าไม่มีให้เป็น NULL) ⭐️
    $birthday  = !empty($_POST['birthday']) ? $_POST['birthday'] : NULL;

    // ตรวจสอบอีเมลเพื่อกำหนด role อัตโนมัติ
    if (str_ends_with($email, "@admin.gmail.com")) {
        $role = "admin";
    } elseif (str_ends_with($email, "@gmail.com")) {
        $role = "user";
    } else {
        echo "<script>
                Swal.fire({ icon: 'error', title: 'สมัครได้เฉพาะ Gmail เท่านั้น', showConfirmButton: false, timer: 2000 })
                .then(() => { window.location = 'sign-up.php'; });
              </script>";
        exit;
    }

    // กันซ้ำอีเมล (ใช้ Prepared Statement)
    $stmt_check = $conn->prepare("SELECT user_id FROM user WHERE email = ?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $check_result = $stmt_check->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>
                Swal.fire({ icon: 'error', title: 'อีเมลนี้ถูกใช้งานแล้ว', text: 'กรุณาใช้อีเมลอื่น', showConfirmButton: false, timer: 2000 })
                .then(() => { window.location = 'sign-up.php'; });
              </script>";
        $stmt_check->close();
        exit;
    }
    $stmt_check->close();

    // ถ้าไม่ซ้ำ → บันทึกข้อมูล (ใช้ Prepared Statement)
    // ⭐️ 2. เพิ่ม birthday เข้าไปใน SQL และ bind_param ⭐️
    $sql = "INSERT INTO user (firstname, lastname, email, password, address, phone, birthday, role)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    // ประเภทข้อมูล: s = string, s = string, s = string, s = string, s = string, s = string, s = string (สำหรับ date), s = string
    $stmt->bind_param("ssssssss", $firstname, $lastname, $email, $password, $address, $phone, $birthday, $role);

    if ($stmt->execute()) {
        echo "<script>
                Swal.fire({ icon: 'success', title: 'สมัครสมาชิกสำเร็จ!', showConfirmButton: false, timer: 2000 })
                .then(() => { window.location = 'login.php'; });
              </script>";
    } else {
        echo "<script>
                Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด', text: 'ไม่สามารถบันทึกข้อมูลได้: " . $stmt->error . "', showConfirmButton: true })
                .then(() => { window.location = 'sign-up.php'; });
              </script>";
    }
    $stmt->close();
    $conn->close(); // ปิดการเชื่อมต่อเมื่อเสร็จสิ้น
}
?>
</body>
</html>