<?php
// Bật hiển thị lỗi để debug (chỉ dùng khi phát triển, xóa khi deploy)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Kết nối database
include 'db_connect.php';

// Kiểm tra kết nối database
if ($mysqli->connect_errno) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database connection failed: ' . $mysqli->connect_error]);
    exit();
}

// Lấy từ khóa từ yêu cầu AJAX
$keyword = isset($_POST['keyword']) ? trim($_POST['keyword']) : '';

// Truy vấn lấy gợi ý sản phẩm (giới hạn 5 kết quả)
$sql = "SELECT id, name, price FROM products WHERE name LIKE ? LIMIT 10";
$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Prepare statement failed: ' . $mysqli->error]);
    exit();
}

$search_term = "%$keyword%";
$stmt->bind_param("s", $search_term);
if (!$stmt->execute()) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Execute statement failed: ' . $stmt->error]);
    exit();
}

$result = $stmt->get_result();
if (!$result) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Get result failed: ' . $stmt->error]);
    exit();
}

$suggestions = [];
while ($row = $result->fetch_assoc()) {
    $suggestions[] = [
        'id' => $row['id'],
        'name' => htmlspecialchars($row['name']),
    ];
}

// Trả về dữ liệu dưới dạng JSON
header('Content-Type: application/json');
echo json_encode($suggestions);
?>