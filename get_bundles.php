<?php
// السماح للـ React بالوصول للسيرفر (CORS)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

include 'db.php';

try {
    // جلب البيانات مع التأكد من ترتيبها
    $query = "SELECT id, bundle_name, items_ids, price, old_price FROM bundles ORDER BY id DESC";
    $result = $conn->query($query);

    if (!$result) {
        throw new Exception("خطأ في القاعدة: " . $conn->error);
    }

    $bundles = [];
    while($row = $result->fetch_assoc()) {
        // تأمين: تحويل الـ IDs لنص نضيف عشان الـ JS ميتلخبطش
        $row['id'] = (string)$row['id'];
        $row['bundle_name'] = htmlspecialchars_decode($row['bundle_name']); // لو فيه رموز خاصة
        
        $bundles[] = $row;
    }

    // إرسال البيانات
    echo json_encode($bundles, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>