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
    // 1. جلب الطلبات الأساسية للمستخدم
    $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $orders = [];
    while($row = $result->fetch_assoc()) {
        $order_id = $row['id'];
        
        // 2. لكل طلب، نجلب العناصر (المنتجات) المرتبطة به
        // نستخدم JOIN لجلب اسم المنتج من جدول products بناءً على product_id
        $items_sql = "SELECT oi.quantity, oi.price, p.name as product_name 
                      FROM order_items oi 
                      JOIN products p ON oi.product_id = p.id 
                      WHERE oi.order_id = ?";
        
        $item_stmt = $conn->prepare($items_sql);
        $item_stmt->bind_param("i", $order_id);
        $item_stmt->execute();
        $items_result = $item_stmt->get_result();
        
        $items = [];
        while($item_row = $items_result->fetch_assoc()) {
            $items[] = $item_row;
        }
        
        // إلحاق مصفوفة المنتجات داخل كائن الطلب
        $row['items'] = $items;
        $orders[] = $row;
    }
    
    echo json_encode($orders, JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "user_id is missing"]);
}

$conn->close();
?>