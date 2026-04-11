<?php
// السماح للـ React بالوصول للداتا (CORS)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$host = "localhost";
$user = "root";
$pass = ""; 
$dbname = "electronical_backend";

// الاتصال بالقاعدة
$conn = new mysqli($host, $user, $pass, $dbname);

// التأكد إن الاتصال شغال
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// دعم اللغة العربية
$conn->set_charset("utf8mb4");
?>