<?php
$sql = "SELECT p.*, c.title AS category_name FROM products p 
        JOIN categories c ON p.category_id = c.id 
        ORDER BY p.id ASC";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()):
    // ใช้ products.image_url เป็นรูปหลัก
    if(!empty($row['image_url'])) {
        $imgUrl = "../uploads/" . $row['image_url'];
    } else {
        // ถ้าไม่มีรูป ใช้ placeholder
        $imgUrl = "https://picsum.photos/60?random=" . $row['id'];
    }
?>
<tr>
    <td><img src="<?= htmlspecialchars($imgUrl) ?>" alt="product" style="max-width:60px; max-height:60px;"></td>
    <td><?= $row['id']; ?></td>
    <td><?= htmlspecialchars($row['title']); ?></td>
    <td><?= number_format($row['price'], 2); ?> ฿</td>
    <td><?= $row['stock']; ?></td>
    <td><?= htmlspecialchars($row['category_name']); ?></td>
    <td>
        <a href="edit_product.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-warning">แก้ไข</a>
        <a href="delete_product.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-danger" 
           onclick="return confirm('ยืนยันการลบสินค้า?');">ลบ</a>
    </td>
</tr>
<?php endwhile; ?>
