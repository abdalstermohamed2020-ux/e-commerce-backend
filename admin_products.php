<?php

header("Access-Control-Allow-Origin: *");
// التعديل: ضفنا PUT هنا عشان المتصفح ميرفضش الطلب
header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT, OPTIONS"); 
header("Access-Control-Allow-Headers: Content-Type, Authorization, cache-control");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

$host = 'localhost:3307';
$user = 'root';
$pass = '';
$dbname = 'electronical_backend';

mysqli_report(MYSQLI_REPORT_OFF);

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

$conn->set_charset("utf8mb4");

$method = $_SERVER['REQUEST_METHOD'];

// --- 1. جلب البيانات (GET) ---
if ($method === 'GET') {
    $sql = "SELECT * FROM products ORDER BY id DESC";
    $result = $conn->query($sql);
    $products = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    echo json_encode($products, JSON_UNESCAPED_UNICODE);
}

// --- 2. إضافة منتج جديد (POST) ---
if ($method === 'POST') {
    $rawData = file_get_contents("php://input");
    $data = json_decode($rawData, true);

    $name = $conn->real_escape_string($data['name'] ?? $_POST['name'] ?? '');
    $price = $conn->real_escape_string($data['price'] ?? $_POST['price'] ?? '');
    $category = $conn->real_escape_string($data['category'] ?? $_POST['category'] ?? '');
    $desc = $conn->real_escape_string($data['description'] ?? $_POST['description'] ?? '');
    $imageData = $data['image'] ?? $_POST['image'] ?? '';

    if (!empty($name) && !empty($price)) {
        $imagePath = 'uploads/default.png';
        
        if (!empty($imageData) && strpos($imageData, 'data:image') === 0) {
            if (!is_dir('uploads')) { mkdir('uploads', 0777, true); }

            preg_match('/data:image\/(\w+);base64,/', $imageData, $matches);
            $imageType = $matches[1] ?? 'png';
            $base64Data = substr($imageData, strpos($imageData, ',') + 1);
            $imageBinary = base64_decode($base64Data);
            
            $imageName = 'product_' . time() . '.' . $imageType;
            $imagePath = 'uploads/' . $imageName;
            
            if (file_put_contents($imagePath, $imageBinary)) {
                $imagePath = 'http://localhost:8080/electronical_backend/' . $imagePath;
            }
        }

        $sql = "INSERT INTO products (name, price, category, image, description) 
                VALUES ('$name', '$price', '$category', '$imagePath', '$desc')";
        if ($conn->query($sql)) {
            echo json_encode(["status" => "success", "message" => "تمت الإضافة بنجاح"]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $conn->error]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "بيانات ناقصة"]);
    }
}

// --- 3. تعديل منتج (PUT) - اللوجيك الجديد اللي كان ناقصك ---
if ($method === 'PUT') {
    $rawData = file_get_contents("php://input");
    $data = json_decode($rawData, true);

    $id = isset($data['id']) ? intval($data['id']) : 0;
    $name = $conn->real_escape_string($data['name'] ?? '');
    $price = $conn->real_escape_string($data['price'] ?? '');
    $category = $conn->real_escape_string($data['category'] ?? '');
    $desc = $conn->real_escape_string($data['description'] ?? '');
    $imageData = $data['image'] ?? '';

    if ($id > 0) {
        $imageSql = "";
        // لو باعت صورة جديدة بنحدثها، لو مبعتش بنسيب القديمة
        if (!empty($imageData) && strpos($imageData, 'data:image') === 0) {
            preg_match('/data:image\/(\w+);base64,/', $imageData, $matches);
            $imageType = $matches[1] ?? 'png';
            $base64Data = substr($imageData, strpos($imageData, ',') + 1);
            $imageBinary = base64_decode($base64Data);
            
            $imageName = 'updated_' . time() . '.' . $imageType;
            $imagePath = 'uploads/' . $imageName;
            
            if (file_put_contents($imagePath, $imageBinary)) {
                $fullPath = 'http://localhost:8080/electronical_backend/' . $imagePath;
                $imageSql = ", image='$fullPath'";
            }
        }

        $sql = "UPDATE products SET name='$name', price='$price', category='$category', description='$desc' $imageSql WHERE id=$id";
        
        if ($conn->query($sql)) {
            echo json_encode(["status" => "success", "message" => "تم التعديل بنجاح ✨"]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $conn->error]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "ID المنتج غير صحيح"]);
    }
}

// --- 4. الحذف (DELETE) ---
if ($method === 'DELETE') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $sql = "DELETE FROM products WHERE id = $id";
    if ($conn->query($sql)) {
        echo json_encode(["message" => "تم الحذف"]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
}

$conn->close();
?>