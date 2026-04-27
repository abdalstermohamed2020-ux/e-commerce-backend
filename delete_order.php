<?php
header("Access-Control-Allow-Origin: *");
// التعديل: سمحنا بالـ DELETE والـ POST والـ OPTIONS
header("Access-Control-Allow-Methods: POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

include 'db.php';

$method = $_SERVER['REQUEST_METHOD'];
// قراءة البيانات سواء جاية كـ JSON أو كـ Query Parameter
$data = json_decode(file_get_contents("php://input"), true);
$order_id = $data['order_id'] ?? $_GET['id'] ?? null;

if ($order_id) {
    // --- الحالة الأولى: حذف طلب واحد محدد ---
    $id = intval($order_id);
    
    // حذف العناصر المرتبطة بالطلب أولاً عشان الـ Foreign Key
    $conn->query("DELETE FROM order_items WHERE order_id = $id");
    
    // حذف الطلب نفسه
    if ($conn->query("DELETE FROM orders WHERE id = $id")) {
        echo json_encode(["status" => "success", "message" => "تم حذف الطلب بنجاح"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
} else {
    // --- الحالة الثانية: تصفية كل المرتجعات (اللوجيك الجديد) ---
    // بنمسح الأول الـ items بتاعة الطلبات اللي حالتها مرتجع عشان م يحصلش Error في الـ Database
    $deleteItemsSql = "DELETE FROM order_items WHERE order_id IN (SELECT id FROM orders WHERE status = 'مرتجع')";
    $conn->query($deleteItemsSql);

    // بنمسح الطلبات نفسها اللي حالتها مرتجع
    $deleteOrdersSql = "DELETE FROM orders WHERE status = 'مرتجع'";
    
    if ($conn->query($deleteOrdersSql)) {
        echo json_encode(["status" => "success", "message" => "تم تصفية سجل المرتجعات بالكامل"]);
    } else {
        echo json_encode(["status" => "error", "message" => "فشل في تصفية السجل: " . $conn->error]);
    }
}

$conn->close();
?>