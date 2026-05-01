<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, cache-control");
header("Content-Type: application/json; charset=UTF-8");

include 'db.php';

// استقبال البيانات اللي جاية من الـ React (JSON)
$data = json_decode(file_get_contents("php://input"));

if(isset($data->name) && isset($data->email) && isset($data->password)) {
    $name = $data->name;
    $email = $data->email;
    $password = password_hash($data->password, PASSWORD_DEFAULT); // تشفير كلمة السر لأمان العميل

    $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', 'user')";

    if($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "تم التسجيل بنجاح"]);
    } else {
        echo json_encode(["error" => "الإيميل ده متسجل قبل كدة"]);
    }
}
?>