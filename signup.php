<?php
include 'db.php';

// استقبال البيانات JSON من الـ React
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->name) && !empty($data->email) && !empty($data->password)) {
    $name = $conn->real_escape_string($data->name);
    $email = $conn->real_escape_string($data->email);
    // تشفير الباسورد قبل الحفظ
    $password = password_hash($data->password, PASSWORD_DEFAULT);

    // التأكد إن الإيميل مش متكرر
    $checkEmail = "SELECT email FROM users WHERE email = '$email'";
    $result = $conn->query($checkEmail);

    if ($result->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "الإيميل ده موجود فعلاً!"]);
    } else {
        $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', 'user')";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "تم إنشاء الحساب بنجاح!"]);
        } else {
            echo json_encode(["success" => false, "message" => "حصل خطأ في السيرفر"]);
        }
    }
} else {
    echo json_encode(["success" => false, "message" => "برجاء ملء جميع الحقول"]);
}

$conn->close();
?>