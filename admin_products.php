<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, cache-control");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

include 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

// 1. جلب المنتجات
if ($method === 'GET') {
    $sql = "SELECT * FROM products ORDER BY id DESC";
    $result = $conn->query($sql);
    $products = [];
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    echo json_encode($products, JSON_UNESCAPED_UNICODE);
}

// 2. حذف منتج
if ($method === 'DELETE') {
    $id = $_GET['id'];
    $sql = "DELETE FROM products WHERE id = '$id'";
    if ($conn->query($sql)) {
        echo json_encode(["message" => "تم الحذف بنجاح"]);
    }
}

// 3. إضافة منتج جديد
if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $name = $conn->real_escape_string($data['name']);
    $price = $data['price'];
    $category = $conn->real_escape_string($data['category']);
    $image = $data['image']; // Base64
    $desc = $conn->real_escape_string($data['description']);

    $sql = "INSERT INTO products (name, price, category, image, description) 
            VALUES ('$name', '$price', '$category', '$image', '$desc')";
            
    if ($conn->query($sql)) {
        echo json_encode(["message" => "تمت الإضافة بنجاح"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => $conn->error]);
    }
}
?>