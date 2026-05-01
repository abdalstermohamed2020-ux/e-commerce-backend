<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, cache-control");
header("Content-Type: application/json; charset=UTF-8");

include 'db.php';

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->email) && !empty($data->password)) {
    $email = $conn->real_escape_string($data->email);
    $password = $data->password;

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // التأكد من صحة الباسورد المشفر
        if (password_verify($password, $user['password'])) {
            // بنبعت بيانات اليوزر (ماعدا الباسورد) للـ React عشان نخزنها في الـ Store
            unset($user['password']); 
            echo json_encode(["success" => true, "user" => $user]);
        } else {
            echo json_encode(["success" => false, "message" => "الباسورد غلط!"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "الإيميل غير موجود"]);
    }
}
$conn->close();
?>