<?php
session_start();
include '../config/connectdb.php';

$id_to_edit = isset($_GET['id']) ? intval($_GET['id']) : 0;

// --- ส่วนของการอัปเดตข้อมูล ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $discount_type = $_POST['discount_type'];
    $discount_value = $_POST['discount_value'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $product_id = !empty($_POST['product_id']) ? intval($_POST['product_id']) : NULL;

    $stmt = $conn->prepare("UPDATE promotions SET name=?, discount_type=?, discount_value=?, start_date=?, end_date=?, product_id=? WHERE id=?");
    $stmt->bind_param("ssdssii", $name, $discount_type, $discount_value, $start_date, $end_date, $product_id, $id_to_edit);

    if ($stmt->execute()) {
        $_SESSION['success'] = "แก้ไขโปรโมชั่น '$name' สำเร็จ!";
        header("Location: promotions.php");
        exit();
    } else {
        $error = "เกิดข้อผิดพลาด: " . $stmt->error;
    }
    $stmt->close();
}

// --- ดึงข้อมูลโปรโมชั่นเดิมมาแสดงในฟอร์ม ---
$stmt_select = $conn->prepare("SELECT * FROM promotions WHERE id = ?");
$stmt_select->bind_param("i", $id_to_edit);
$stmt_select->execute();
$promotion = $stmt_select->get_result()->fetch_assoc();
$stmt_select->close();

if (!$promotion) {
    die("ไม่พบโปรโมชั่นที่ต้องการแก้ไข");
}

// 1. ดึงข้อมูลสินค้าทั้งหมดมาใส่ใน dropdown
$products_result = $conn->query("SELECT id, title FROM products ORDER BY title ASC");

include 'header.php';
?>

<h1 class="h3 mb-4 text-gray-800">✏️ แก้ไขโปรโมชั่น: <?= htmlspecialchars($promotion['name']) ?></h1>
<?php if (isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="edit_promotion.php?id=<?= $id_to_edit ?>" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">ชื่อโปรโมชั่น</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($promotion['name']) ?>" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="discount_type" class="form-label">ประเภทส่วนลด</label>
                    <select class="form-select" id="discount_type" name="discount_type">
                        <option value="percentage" <?= $promotion['discount_type'] == 'percentage' ? 'selected' : '' ?>>เปอร์เซ็นต์ (%)</option>
                        <option value="fixed" <?= $promotion['discount_type'] == 'fixed' ? 'selected' : '' ?>>จำนวนเงิน (บาท)</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="discount_value" class="form-label">มูลค่าส่วนลด</label>
                    <input type="number" step="0.01" class="form-control" id="discount_value" name="discount_value" value="<?= htmlspecialchars($promotion['discount_value']) ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="start_date" class="form-label">วันที่เริ่มต้น</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?= htmlspecialchars($promotion['start_date']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="end_date" class="form-label">วันที่สิ้นสุด</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?= htmlspecialchars($promotion['end_date']) ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="product_id" class="form-label">สำหรับสินค้า (ไม่จำเป็น)</label>
                <select class="form-select" id="product_id" name="product_id">
                    <option value="">-- สำหรับสินค้าทุกชิ้นที่ร่วมรายการ --</option>
                    <?php while($product = $products_result->fetch_assoc()): ?>
                        <option value="<?= $product['id'] ?>" <?= $promotion['product_id'] == $product['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($product['title']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="promotions.php" class="btn btn-secondary">ยกเลิก</a>
                <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
            </div>
        </form>
    </div>
</div>
<?php include 'footer.php'; ?>