<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include 'db.php';

$input = file_get_contents("php://input");
$data = json_decode($input);

if ($data && isset($data->id)) {
    // تنظيف البيانات
    $id = mysqli_real_escape_string($conn, $data->id);
    $name = mysqli_real_escape_string($conn, $data->name);
    $father_name = mysqli_real_escape_string($conn, $data->father_name); // تم إضافة اسم الأب
    $email = mysqli_real_escape_string($conn, $data->email);
    $phone = mysqli_real_escape_string($conn, $data->phone);
    $address = mysqli_real_escape_string($conn, $data->address);
    $birthday = mysqli_real_escape_string($conn, $data->birthday);

    // تحديث الاستعلام ليشمل اسم الأب
    $sql = "UPDATE users SET 
            name = '$name', 
            father_name = '$father_name', 
            email = '$email', 
            phone = '$phone', 
            address = '$address',
            birthday = '$birthday' 
            WHERE id = '$id'";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true, "message" => "تم تحديث بيانات بروفايلك بنجاح ✨"]);
    } else {
        echo json_encode(["success" => false, "message" => "خطأ في قاعدة البيانات: " . mysqli_error($conn)]);
    }
} else {
    echo json_encode(["success" => false, "message" => "بيانات غير مكتملة"]);
}
?>