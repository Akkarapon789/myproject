<?php
include '../config/connectdb.php';

// รับคำค้นหาจากฟอร์ม (GET หรือ POST ก็ได้)
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "SELECT * FROM products WHERE 1";

// ✅ เพิ่มเงื่อนไขค้นหา ถ้ามีคำค้น
if ($search !== '') {
    $search = mysqli_real_escape_string($conn, $search);
    $sql .= " AND (
        title LIKE '%$search%' 
        OR author LIKE '%$search%' 
        OR publisher LIKE '%$search%' 
        OR description LIKE '%$search%'
    )";
}

$result = mysqli_query($conn, $sql);
?>



<style>
.search-form {
  display: flex;
  gap: 10px;
  justify-content: center;
}

.search-box {
  width: 300px;
  padding: 10px 15px;
  border: 2px solid #2155CD;
  border-radius: 25px;
  outline: none;
  font-size: 16px;
  transition: 0.3s;
}

.search-box:focus {
  border-color: #FDDE55;
  box-shadow: 0 0 6px #FDDE55;
}

.search-btn {
  background-color: #2155CD;
  color: white;
  border: none;
  border-radius: 25px;
  padding: 10px 20px;
  font-size: 16px;
  cursor: pointer;
  transition: 0.3s;
}

.search-btn:hover {
  background-color: #FDDE55;
  color: #2155CD;
}
</style>

<?php
if (mysqli_num_rows($result) > 0) {
    echo "<div class='grid'>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<div class='item'>
                <img src='{$row['image_url']}' alt='{$row['title']}' style='width:120px;height:180px;object-fit:cover;'>
                <h4>{$row['title']}</h4>
                <p>โดย {$row['author']}</p>
                <p><b>฿{$row['price']}</b></p>
              </div>";
    }
    echo "</div>";
} else {
    echo "<p style='text-align:center;color:#999;'>ไม่พบสินค้าที่ค้นหา</p>";
}
?>