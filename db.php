<?php
$host = "fdb1034.awardspace.net"; // ده الـ Host اللي كان ظاهر في صورة phpMyAdmin
$user = "4754940_electronic";     // اسم المستخدم بتاعك
$pass = "Absattar2005";          // الباسورد اللي إنت عملتها
$dbname = "4754940_electronic";   // اسم قاعدة البيانات

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>