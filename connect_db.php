<?php
$servername = "localhost";
$username = "root";     // Tên mặc định của XAMPP
$password = "vertrigo";         // Thường để trống
$dbname = "qldapm";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
