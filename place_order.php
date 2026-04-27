<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

include 'db.php'; 

$data = json_decode(file_get_contents("php://input"), true);

// التأكد من وصول بيانات المستخدم وبيانات الشحن والمنتجات
if (!empty($data['user_id']) && !empty($data['total_price']) && !empty($data['cart']) && !empty($data['shipping_address'])) {
    
    $user_id = $conn->real_escape_string($data['user_id']);
    $total_price = $conn->real_escape_string($data['total_price']);
    
    // --- البيانات الجديدة المسحوبة من البروفايل ---
    $customer_name = $conn->real_escape_string($data['customer_name']);
    $customer_phone = $conn->real_escape_string($data['customer_phone']);
    $shipping_address = $conn->real_escape_string($data['shipping_address']);
    
    $status = "pending"; 

    // 1. إضافة الطلب مع بيانات الشحن "Snapshot"
    $sql_order = "INSERT INTO orders (user_id, total_price, status, customer_name, customer_phone, shipping_address) 
                  VALUES ('$user_id', '$total_price', '$status', '$customer_name', '$customer_phone', '$shipping_address')";
    
    if ($conn->query($sql_order)) {
        $order_id = $conn->insert_id; 

        // 2. إضافة المنتجات في جدول order_items
        $cart = $data['cart'];
        $success_items = true;

        foreach ($cart as $item) {
            $product_id = $conn->real_escape_string($item['id']);
            $quantity = $conn->real_escape_string($item['quantity']);
            $price = $conn->real_escape_string($item['price']);

            $sql_item = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                         VALUES ('$order_id', '$product_id', '$quantity', '$price')";
            
            if (!$conn->query($sql_item)) {
                $success_items = false;
                break;
            }
        }

        if ($success_items) {
            echo json_encode([
                "status" => "success", 
                "message" => "تم تسجيل الطلب رقم $order_id بنجاح! 🚀",
                "order_id" => $order_id
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "فشل تسجيل المنتجات: " . $conn->error]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "خطأ في تسجيل الطلب: " . $conn->error]);
    }
} else {
    // رسالة خطأ واضحة لو العميل مكملش بياناته
    echo json_encode([
        "status" => "error", 
        "message" => "بيانات الطلب أو عنوان الشحن غير مكتملة. يرجى تحديث البروفايل."
    ]);
}

$conn->close();
?>