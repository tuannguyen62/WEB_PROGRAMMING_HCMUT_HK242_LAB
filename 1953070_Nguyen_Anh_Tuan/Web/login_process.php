<?php
// Bắt đầu session để lưu trạng thái đăng nhập
session_start();

// Kết nối database
include 'db_connect.php';

// Kiểm tra nếu form được gửi bằng phương thức POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Chuẩn bị truy vấn để lấy thông tin người dùng dựa trên email
    $stmt = $mysqli->prepare("SELECT * FROM accounts WHERE email = ?");
    // Gán giá trị email vào truy vấn để tránh SQL injection
    $stmt->bind_param("s", $email);
    
    // Thực thi truy vấn
    if ($stmt->execute()) {
        // Lấy kết quả truy vấn
        $result = $stmt->get_result();
        // Lấy thông tin người dùng dưới dạng mảng
        $user = $result->fetch_assoc();

        // Kiểm tra xem người dùng có tồn tại và mật khẩu có đúng không
        if ($user && password_verify($password, $user['password'])) {
            // Lưu thông tin người dùng vào session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Nếu chọn "Remember me" -> lưu email vào cookie 30 ngày
            if (isset($_POST['remember'])) {
                setcookie('email', $email, time() + (86400 * 30), "/");
            }

            // Chuyển hướng dựa trên vai trò của người dùng
            if ($user['role'] == 'admin') {
                header("Location: admin_dashboard.php");
            } elseif ($user['role'] == 'user') {
                header("Location: index.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            // Nếu email hoặc mật khẩu sai, chuyển hướng về login.php với thông báo lỗi
            header("Location: login.php?error=Invalid email or password");
            exit();
        }
    } else {
        // Nếu truy vấn thất bại, chuyển hướng về login.php với thông báo lỗi
        header("Location: login.php?error=Query failed: " . urlencode($mysqli->error));
        exit();
    }
} else {
    // Nếu không phải là POST request, chuyển hướng về login.php với thông báo lỗi
    header("Location: login.php?error=Invalid request");
    exit();
}
?>