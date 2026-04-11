<?php
// 1. إعدادات الـ CORS عشان الـ React ميزعلش
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, cache-control");

// 2. التعامل مع طلب الـ Preflight
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

include 'db.php'; // تأكد إن ملف الاتصال موجود وشغال

// 3. استلام البيانات بصيغة JSON
$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data['user_id']) && !empty($data['total_price'])) {
    $user_id = $conn->real_escape_string($data['user_id']);
    $total_price = $conn->real_escape_string($data['total_price']);
    $status = "pending"; // الحالة الافتراضية كما في الداتا بيز عندك

    // 4. استعلام الإضافة
    $sql = "INSERT INTO orders (user_id, total_price, status) VALUES ('$user_id', '$total_price', '$status')";
    
    if ($conn->query($sql)) {
        echo json_encode(["status" => "success", "message" => "تم تسجيل الطلب بنجاح!"]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "خطأ في القاعدة: " . $conn->error]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "بيانات الطلب غير مكتملة"]);
}

$conn->close();
?>