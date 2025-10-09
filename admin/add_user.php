<?php
// add_user.php
include 'header.php';

// ตรวจสอบถ้ามีการส่งข้อมูลฟอร์มมา
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับข้อมูลจากฟอร์ม
    $firstname = $_POST['firstname'];
    $lastname  = $_POST['lastname'];
    $email     = $_POST['email'];
    $password  = $_POST['password']; // รหัสผ่านที่ยังไม่เข้ารหัส
    $phone     = $_POST['phone'];
    $role      = $_POST['role'];

    // --- ส่วนที่สำคัญที่สุด: การเข้ารหัสรหัสผ่าน ---
    // เราจะ *ไม่* เก็บรหัสผ่านตรงๆ ลงฐานข้อมูลเด็ดขาด
    // เราจะใช้ฟังก์ชัน password_hash() เพื่อความปลอดภัยสูงสุด
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // เตรียม SQL Query โดยใช้ Prepared Statement เพื่อป้องกัน SQL Injection
    $sql = "INSERT INTO user (firstname, lastname, email, password, phone, role) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // "ssssss" หมายถึงตัวแปร 6 ตัวเป็นชนิด String ทั้งหมด
    $stmt->bind_param("ssssss", $firstname, $lastname, $email, $hashed_password, $phone, $role);

    // Execute a query
    if ($stmt->execute()) {
        echo "<script>
                alert('เพิ่มผู้ใช้ใหม่เรียบร้อยแล้ว!');
                window.location.href = 'users.php';
              </script>";
    } else {
        echo "<script>
                alert('เกิดข้อผิดพลาด: " . htmlspecialchars($stmt->error) . "');
              </script>";
    }
    $stmt->close();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">เพิ่มผู้ใช้ใหม่</h1>
    <a href="users.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left fa-sm me-2"></i>กลับไปหน้าผู้ใช้
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="add_user.php" method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="firstname" class="form-label">ชื่อจริง</label>
                    <input type="text" class="form-control" id="firstname" name="firstname" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="lastname" class="form-label">นามสกุล</label>
                    <input type="text" class="form-control" id="lastname" name="lastname" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">อีเมล</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">รหัสผ่าน</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
                <input type="text" class="form-control" id="phone" name="phone">
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">สิทธิ์การใช้งาน</label>
                <select class="form-select" id="role" name="role">
                    <option value="user" selected>User (ผู้ใช้ทั่วไป)</option>
                    <option value="admin">Admin (ผู้ดูแลระบบ)</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>บันทึกข้อมูล
            </button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>