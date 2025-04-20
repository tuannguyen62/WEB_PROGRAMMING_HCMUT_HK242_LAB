<?php
// Kết nối database
include 'db_connect.php';

// Lấy danh sách cửa hàng
try {
    $stmt = $mysqli->prepare("SELECT name, latitude, longitude FROM stores");
    $stmt->execute();
    $result = $stmt->get_result();
    $stores = $result->fetch_all(MYSQLI_ASSOC);

    // Trả về dữ liệu dưới dạng JSON
    header("Content-Type: application/json");
    echo json_encode($stores);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>