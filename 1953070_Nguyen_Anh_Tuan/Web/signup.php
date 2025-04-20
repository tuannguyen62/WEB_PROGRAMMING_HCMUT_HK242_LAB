<?php
$page_title = "Sign Up";
session_start();

$signupSuccess = isset($_SESSION['signup_success']) ? $_SESSION['signup_success'] : null;
if (isset($_SESSION['signup_success'])) {
  unset($_SESSION['signup_success']);
}
?>
<!doctype html>
<html lang="en">
<?php include 'head.php'; ?>

<body>
  <!-- Navbar -->
  <?php include 'navbar.php'; ?>

  <!-- Main container -->
  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <!-- Signup container -->
    <div class="row border rounded-5 p-4 bg-white shadow-sm box-area">

      <!-- Left box -->
      <div class="col-md-6 rounded-4 d-flex justify-content-center align-items-center flex-column left-box">
        <div class="featured-image mb-3">
          <img src="./images/signup_img.png" class="img-fluid" style="width: 300px;">
        </div>
        <p class="text-white fs-3 text-wrap text-center fw-normal">Join Our Community</p>
        <p class="text-white fs-6 text-wrap text-center fw-light">Create your account to start exploring with us.</p>
      </div>

      <!-- Right box -->
      <div class="col-md-6 right-box p-4">
        <div class="row align-items-center">
          <div class="header-text mb-4 text-center">
            <h1 class="Brand">times record </h1>
            <h2 class="fw-normal text-dark">Hi!</h2>
            <p class="text-muted fw-light">Sign up to get started</p>
            <?php
            if (isset($_GET['error'])) {
              echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . htmlspecialchars($_GET['error']) . '
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }
            if ($signupSuccess) {
              echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . htmlspecialchars($signupSuccess) . '
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }
            ?>
          </div>

          <?php if (!$signupSuccess): ?>
            <!-- Chỉ hiển thị form nếu chưa đăng ký thành công -->
            <form action="signup_process.php" method="POST" onsubmit="return validatePassword()">
              <div class="input-group position-relative mb-3">
                <input type="text" class="form-control form-control-lg fs-6 rounded-pill shadow-none" name="fullname"
                  placeholder="Name" required>
              </div>

              <div class="input-group mb-3">
                <input type="email" class="form-control form-control-lg fs-6 fw-light rounded-pill shadow-none" name="email"
                  placeholder="Email address" required>
              </div>

              <div class="input-group mb-3">
                <input type="password" class="form-control form-control-lg fs-6 fw-light rounded-pill shadow-none"
                  name="password" id="password" placeholder="Password" required>
              </div>

              <div class="input-group mb-4">
                <input type="password" class="form-control form-control-lg fs-6 rounded-pill shadow-none"
                  name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
              </div>

              <!-- Thông báo lỗi mật khẩu -->
              <div id="password-error" class="text-danger mb-3" style="display: none;"></div>

              <div class="input-group mb-3">
                <button class="btn btn-lg btn-danger w-100 fs-6 rounded-pill shadow-sm" type="submit">
                  <i class="bi bi-arrow-right-short text-white"></i>
                  Sign Up
                </button>
              </div>

              <div class="row text-center">
                <small class="text-secondary fw-light">Already have an account? <a href="login.php" class="text-danger fw-bold"
                    style="text-decoration: none;">Log in</a></small>
              </div>
            </form>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </div>

  <script>
    function validatePassword() {
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirm_password').value;
      const errorDiv = document.getElementById('password-error');

      // mật khẩu có ít nhất 8 ký tự
      if (password.length < 8) {
        errorDiv.style.display = 'block';
        errorDiv.textContent = 'Password must be at least 8 characters long.';
        return false;
      }

      // có ít nhất một chữ in hoa
      if (!/[A-Z]/.test(password)) {
        errorDiv.style.display = 'block';
        errorDiv.textContent = 'Password must contain at least one uppercase letter.';
        return false;
      }

      // có ít nhất 1 ký tự đặc biệt
      if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
        errorDiv.style.display = 'block';
        errorDiv.textContent = 'Password must contain at least one special character (e.g., !, @, #, etc.).';
        return false;
      }

      // Confirm pass có khớp không
      if (password !== confirmPassword) {
        errorDiv.style.display = 'block';
        errorDiv.textContent = 'Passwords do not match.';
        return false;
      }

      errorDiv.style.display = 'none';
      return true;
    }

    const signupSuccess = <?php echo json_encode($signupSuccess !== null); ?>;
    if (signupSuccess) {
      // Redirect về login.php sau 2 giây
      setTimeout(function () {
        window.location.href = 'login.php';
      }, 2000);
    }
  </script>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>