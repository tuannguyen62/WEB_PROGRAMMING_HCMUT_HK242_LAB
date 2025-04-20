<?php $page_title = "Login"; ?>
<!doctype html>
<html lang="en">
<?php include 'head.php'; ?>

<body>
  <!-- Navbar -->
  <?php include 'navbar.php'; ?>

  <!-- Main container -->
  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <!-- Login container -->
    <div class="row border rounded-5 p-4 bg-white shadow-none box-area">
      <!-- Left box -->
      <div class="col-md-6 rounded-4 d-flex justify-content-center align-items-center flex-column left-box">
        <div class="featured-image mb-3">
          <img src="./images/login_img.png" class="img-fluid" style="width: 250px;">
        </div>
        <p class="text-white fs-3 text-wrap text-center fw-normal">Be Verified!</p>
        <p class="text-white fs-6 text-wrap text-center fw-light">Login to shop with us now.</p>
      </div>

      <!-- Right box -->
      <div class="col-md-6 right-box p-4">
        <div class="row align-items-center">
          <div class="header-text mb-4 text-center">
            <h1 class="Brand">times record </h1>
            <h2 class="fw-normal text-dark">Hello, Again</h2>
            <p class="text-muted fw-light">We're happy to have you back</p>
            <?php
            if (isset($_GET['error'])) {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . htmlspecialchars($_GET['error']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }
            ?>
          </div>

          <!-- Form login -->
          <form action="login_process.php" method="POST">
            <div class="input-group mb-3">
              <input type="email" class="form-control form-control-lg fs-6 rounded-pill shadow-none" name="email"
                placeholder="Email address" required>
            </div>

            <div class="input-group mb-3">
              <input type="password" class="form-control form-control-lg fs-6 rounded-pill shadow-none" name="password"
                placeholder="Password" required>
            </div>

            <!-- Remember me + Forgot password -->
            <div class="input-group mb-4 d-flex justify-content-between">
              <div class="form-check">
                <input type="checkbox" class="form-check-input rounded-pill" id="formCheck" name="remember">
                <label for="formCheck" class="form-check-label text-secondary fw-light"><small>Remember me</small></label>
              </div>
              <div class="forgot">
                <small><a href="#" class="text-danger" style="text-decoration:none;">Forgot Password?</a></small>
              </div>
            </div>

            <!-- Login button -->
            <div class="input-group mb-3">
              <button type="submit" class="btn btn-lg btn-danger w-100 fs-6 rounded-pill shadow-sm">
                <i class="bi bi-arrow-right-short text-white"></i>
                Login
              </button>
            </div>
          </form>
          <!-- End form -->

          <!-- Sign up link -->
          <div class="row text-center">
            <small class="text-secondary fw-light">Don't have an account? <a href="signup.php" class="text-danger fw-bold" style="text-decoration: none;">Sign Up</a></small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>