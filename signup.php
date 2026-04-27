<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

include 'db.php';

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->name) && !empty($data->email) && !empty($data->password)) {
    
    // تنظيف البيانات واستلام الحقول بناءً على جدولك
    $name        = $conn->real_escape_string($data->name);
    $email       = $conn->real_escape_string($data->email);
    $password    = password_hash($data->password, PASSWORD_DEFAULT);
    
    // الحقول الإضافية (تأكد أن الفورم بتبعت الحقول دي بنفس الأسامي)
    $phone       = isset($data->phone) ? $conn->real_escape_string($data->phone) : '';
    $address     = isset($data->address) ? $conn->real_escape_string($data->address) : '';
    $father_name = isset($data->father_name) ? $conn->real_escape_string($data->father_name) : '';
    $birthday    = isset($data->birthday) ? $conn->real_escape_string($data->birthday) : '';
    $gender      = isset($data->gender) ? $conn->real_escape_string($data->gender) : 'male'; // قيمة افتراضية

    // التأكد من عدم تكرار الإيميل
    $checkEmail = "SELECT email FROM users WHERE email = '$email'";
    $result = $conn->query($checkEmail);

    if ($result->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "الإيميل ده موجود فعلاً!"]);
    } else {
        // الإدخال بناءً على أسامي أعمدة جدولك بالظبط
        $sql = "INSERT INTO users (name, email, password, phone, address, father_name, birthday, gender, role) 
                VALUES ('$name', '$email', '$password', '$phone', '$address', '$father_name', '$birthday', '$gender', 'user')";
        
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "تم إنشاء الحساب بنجاح!"]);
        } else {
            echo json_encode(["success" => false, "message" => "خطأ في السيرفر: " . $conn->error]);
        }
    }
} else {
    echo json_encode(["success" => false, "message" => "برجاء ملء البيانات الأساسية"]);
}
$conn->close();
?>