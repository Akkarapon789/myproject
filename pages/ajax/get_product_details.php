<?php
// pages/ajax/get_product_details.php
require_once '../../config/connectdb.php';

header('Content-Type: application/json');

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($product_id === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid Product ID']);
    exit();
}

$stmt = $conn->prepare("
    SELECT p.*, c.title as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if ($product) {
    echo json_encode(['success' => true, 'data' => $product]);
} else {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
}
?>