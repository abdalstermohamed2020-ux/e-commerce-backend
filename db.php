<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// التصحيح: اكتب العنوان فقط بدون البورت هنا
$host = '127.0.0.1'; 
$user = 'root';
$pass = '';
$dbname = 'electronical_backend';
$port = 3307; 

mysqli_report(MYSQLI_REPORT_OFF);

// الربط الصحيح باستخدام المنفذ كمعامل منفصل
$conn = new mysqli($host, $user, $pass, $dbname, $port);

if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "فشل الاتصال: " . $conn->connect_error]);
    exit;
}

$conn->set_charset("utf8mb4");