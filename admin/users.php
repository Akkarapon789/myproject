<?php
include 'header.php';
$result = $conn->query("SELECT * FROM `user` ORDER BY user_id ASC");
?>

<div class="card shadow-sm mb-4">
    </div>

<script>
$(document).ready(function() {
    $('#userTable').DataTable({
        // ... config ...
    });
});
</script>

<?php include 'footer.php'; // <-- เพิ่มบรรทัดนี้เข้ามา ?>