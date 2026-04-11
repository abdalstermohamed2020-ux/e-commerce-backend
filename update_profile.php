<?php
// السماح بالوصول من الـ React
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// تعامل مع طلبات المتصفح التمهيدية
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include 'db.php';

// قراءة البيانات المرسلة من Axios
$input = file_get_contents("php://input");
$data = json_decode($input);

if ($data && isset($data->id)) {
    // تنظيف البيانات لمنع SQL Injection
    $id = mysqli_real_escape_string($conn, $data->id);
    $name = mysqli_real_escape_string($conn, $data->name);
    $email = mysqli_real_escape_string($conn, $data->email);
    $phone = mysqli_real_escape_string($conn, $data->phone);
    $gender = mysqli_real_escape_string($conn, $data->gender);
    $birthday = mysqli_real_escape_string($conn, $data->birthday);

    // استعلام التحديث
    $sql = "UPDATE users SET 
            name = '$name', 
            email = '$email', 
            phone = '$phone', 
            gender = '$gender', 
            birthday = '$birthday' 
            WHERE id = '$id'";

    if (mysqli_query($conn, $sql)) {
        echo json_encode([
            "success" => true, 
            "message" => "تم تحديث البيانات بنجاح ✨"
        ]);
    } else {
        // لو الـ SQL فيه مشكلة هيظهرلك هنا
        echo json_encode([
            "success" => false, 
            "message" => "فشل التحديث في الداتا بيز: " . mysqli_error($conn)
        ]);
    }
} else {
    echo json_encode([
        "success" => false, 
        "message" => "لم يتم استلام بيانات صحيحة من المتصفح"
    ]);
}
?>