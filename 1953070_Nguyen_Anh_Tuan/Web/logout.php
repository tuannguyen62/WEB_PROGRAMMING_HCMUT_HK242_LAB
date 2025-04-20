<?php
// Bắt đầu session để có thể xóa dữ liệu session
session_start();

// Xóa tất cả dữ liệu session
session_unset();
session_destroy();

// Xóa cookie 'email' nếu có (dùng cho "Remember me")
if (isset($_COOKIE['email'])) {
    setcookie('email', '', time() - 3600, "/");
}

// Chuyển hướng về trang đăng nhập
header("Location: login.php");
exit();
?>