<?php
include '../config/connectdb.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id']);
    $user_name = mysqli_real_escape_string($conn, $_POST['user_name']);
    $rating = intval($_POST['rating']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);

    $sql = "INSERT INTO reviews (product_id, user_name, rating, comment) 
            VALUES ($product_id, '$user_name', $rating, '$comment')";
    mysqli_query($conn, $sql);

    header("Location: product_detail.php?id=$product_id");
    exit;
}
?>

<div class="container mt-5">
    <h3>เขียนรีวิวสินค้า</h3>
    <form method="POST">
        <input type="hidden" name="product_id" value="<?php echo $_GET['product_id']; ?>">
        <div class="mb-3">
            <label>ชื่อของคุณ:</label>
            <input type="text" name="user_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>ให้คะแนน:</label>
            <select name="rating" class="form-select" required>
                <option value="5">⭐⭐⭐⭐⭐</option>
                <option value="4">⭐⭐⭐⭐</option>
                <option value="3">⭐⭐⭐</option>
                <option value="2">⭐⭐</option>
                <option value="1">⭐</option>
            </select>
        </div>
        <div class="mb-3">
            <label>ความคิดเห็น:</label>
            <textarea name="comment" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-success">ส่งรีวิว</button>
        <a href="product_detail.php?id=<?php echo $_GET['product_id']; ?>" class="btn btn-secondary">ยกเลิก</a>
    </form>
</div>
