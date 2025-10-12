<?php
// admin/add_product.php (Corrected & Final Version)
session_start();
include '../config/connectdb.php';
include 'header.php'; // ย้าย header มาไว้ข้างบนสุด

// --- ส่วนของการบันทึกข้อมูล ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->begin_transaction();
    try {
        // ... (โค้ดบันทึกข้อมูลทั้งหมดเหมือนเดิม) ...

        $conn->commit();
        $_SESSION['success'] = "เพิ่มสินค้า '$title' สำเร็จ!";
        header("Location: products.php");
        exit();

    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        $error = "เกิดข้อผิดพลาด: " . $exception->getMessage();
    }
}

// ดึงข้อมูลหมวดหมู่สำหรับ Dropdown
$categories_result = $conn->query("SELECT * FROM categories ORDER BY title ASC");

?>

<h1 class="h3 mb-4 text-gray-800">➕ เพิ่มสินค้าใหม่</h1>
<?php if (isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="add_product.php" method="POST" enctype="multipart/form-data">
            </form>
    </div>
</div>
<?php include 'footer.php'; ?>