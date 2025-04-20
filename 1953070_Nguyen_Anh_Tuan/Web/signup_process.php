<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Pass có ít nhất 8 ký tự
    if (strlen($password) < 8) {
        header("Location: signup.php?error=Password must be at least 8 characters long");
        exit();
    }

    //ít nhất một chữ cái in hoa (ở bất kỳ vị trí nào)
    if (!preg_match('/[A-Z]/', $password)) {
        header("Location: signup.php?error=Password must contain at least one uppercase letter");
        exit();
    }

    // có ít nhất 1 ký tự đặc biệt
    if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
        header("Location: signup.php?error=Password must contain at least one special character");
        exit();
    }

    // Kiểm tra pass và confirmed password có khớp không
    if ($password !== $confirm_password) {
        header("Location: signup.php?error=Passwords do not match");
        exit();
    }

    // Kiểm tra email đã có trong database chưa
    $stmt = $mysqli->prepare("SELECT * FROM accounts WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        header("Location: signup.php?error=Email already exists! Please use a different email");
        exit();
    }

    // Mã hóa mật khẩu
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Thêm user mới, role mặc định là user
    $stmt = $mysqli->prepare("INSERT INTO accounts (fullname, email, password, role) VALUES (?, ?, ?, 'user')");
    $stmt->bind_param("sss", $fullname, $email, $hashedPassword);
    
    if ($stmt->execute()) {
        // Nếu thành công, lưu thông báo thành công vào session và chuyển hướng về signup.php
        $_SESSION['signup_success'] = "Sign up successful! Redirecting to login...";
        header("Location: signup.php");
        exit();
    } else {
        // Nếu thất bại, chuyển hướng về signup.php với thông báo lỗi
        header("Location: signup.php?error=Failed to sign up: " . urlencode($mysqli->error));
        exit();
    }
} else {
    // Nếu không phải là POST request, chuyển hướng về signup.php với thông báo lỗi
    header("Location: signup.php?error=Invalid request");
    exit();
}
?>