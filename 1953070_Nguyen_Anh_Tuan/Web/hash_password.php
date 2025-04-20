<!-- Script này được viết để lấy mật khẩu đã được hash của các tài khoản demo  -->
<!-- Chạy trang, sau đó lấy mật khẩu để điền vào product_account.sql -->
<?php
// Tạo mật khẩu mã hóa cho admin
$adminPassword = "Admin123@";
$adminHashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);
echo "Admin hashed password: " . $adminHashedPassword . "\n";

// Tạo mật khẩu mã hóa cho guest
$userPassword = "User123@";
$userHashedPassword = password_hash($userPassword, PASSWORD_DEFAULT);
echo "User hashed password: " . $userHashedPassword . "\n";
?>