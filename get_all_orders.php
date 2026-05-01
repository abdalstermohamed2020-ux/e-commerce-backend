<?php
header("Access-Control-Allow-Methods: POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

header("Access-Control-Allow-Origin: *");


include 'db.php';

// LEFT JOIN بيضمن ظهور الأوردر حتى لو اليوزر ممسوح أو مش موجود
$sql = "SELECT orders.*, users.name AS customer_name, users.phone AS customer_phone 
        FROM orders 
        LEFT JOIN users ON orders.user_id = users.id 
        ORDER BY orders.created_at DESC";

$result = $conn->query($sql);
$orders = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $order_id = $row['id'];
        
        // جلب المنتجات لكل أوردر
        $items_sql = "SELECT oi.*, p.name as title, p.image 
                      FROM order_items oi 
                      JOIN products p ON oi.product_id = p.id 
                      WHERE oi.order_id = $order_id";
        $items_result = $conn->query($items_sql);
        $items = [];
        while($item = $items_result->fetch_assoc()) {
            $items[] = $item;
        }
        
        $row['items'] = $items;
        $orders[] = $row;
    }
}

echo json_encode($orders);
?>