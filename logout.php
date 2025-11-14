<?php
// 1. Luôn khởi động session trước
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Xóa tất cả các biến trong session
$_SESSION = array();

// 3. Hủy session
session_destroy();

// 4. Chuyển hướng người dùng về trang đăng nhập
// (Trang login của bạn nằm trong index.php)
header("Location: index.php?page=login");
exit();
?>