<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, cache-control");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

include 'db.php';

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;

if ($user_id) {
    $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $orders = [];
    while($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    echo json_encode($orders, JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(["status" => "error", "message" => "user_id is missing"]);
}

$conn->close();
?>