<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

include 'db.php'; 

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

switch($method) {
    case 'GET':
        // جلب كل البكدجات مع تحويل الـ ID لنص لضمان توافق الـ Filtering
        $sql = "SELECT * FROM bundles ORDER BY id DESC";
        $result = $conn->query($sql);
        $bundles = [];
        if ($result) {
            while($row = $result->fetch_assoc()) {
                // تحويل الـ id لنص عشان الـ React Filter يلقطه فوراً
                $row['id'] = (string)$row['id'];
                $bundles[] = $row;
            }
        }
        echo json_encode($bundles, JSON_UNESCAPED_UNICODE);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!$data) {
            echo json_encode(["status" => "error", "message" => "لم يتم استقبال بيانات"]);
            break;
        }

        // --- أولاً: فحص إذا كان الطلب هو "حذف" ---
        if (isset($data['action']) && $data['action'] === 'delete') {
            $id = intval($data['id']);
            if ($id > 0) {
                $sql = "DELETE FROM bundles WHERE id = $id";
                if ($conn->query($sql)) {
                    echo json_encode(["status" => "success", "message" => "تم حذف البكدج بنجاح"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "فشل الحذف: " . $conn->error]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "المعرف غير صحيح"]);
            }
            break;
        }

        // --- ثانياً: إضافة بكدج جديدة ---
        // تأمين البيانات المدخلة
        $name = mysqli_real_escape_string($conn, $data['bundle_name']);
        $price = mysqli_real_escape_string($conn, $data['price']);
        $old_price = mysqli_real_escape_string($conn, $data['old_price']);
        $items_ids = mysqli_real_escape_string($conn, $data['items_ids']); 

        if (empty($name) || empty($price)) {
            echo json_encode(["status" => "error", "message" => "بيانات البكدج ناقصة"]);
            break;
        }

        $sql = "INSERT INTO bundles (bundle_name, price, old_price, items_ids) 
                VALUES ('$name', '$price', '$old_price', '$items_ids')";
        
        if ($conn->query($sql)) {
            echo json_encode(["status" => "success", "message" => "تم إضافة البكدج بنجاح"]);
        } else {
            echo json_encode(["status" => "error", "message" => "فشل في الإضافة: " . $conn->error]);
        }
        break;

    case 'DELETE':
        // دعم لطلب DELETE التقليدي
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id > 0) {
            $sql = "DELETE FROM bundles WHERE id = $id";
            if ($conn->query($sql)) {
                echo json_encode(["status" => "success", "message" => "تم الحذف"]);
            } else {
                echo json_encode(["status" => "error", "message" => $conn->error]);
            }
        }
        break;
}

$conn->close();
?>