<?php $page_title = "Home"; ?>
<!doctype html>
<html lang="en">
<?php include 'head.php'; ?>
<!-- Google Maps API Script -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDNI_ZWPqvdS6r6gPVO50I4TlYkfkZdXh8&callback=initMap" async defer></script>

<script>
  let map;

  // Hàm khởi tạo bản đồ
  function initMap() {
    // Tọa độ mặc định (có thể là trung tâm của các cửa hàng hoặc một vị trí cố định)
    const defaultLocation = { lat: 10.772080, lng: 106.655321 }; // Ví dụ: tọa độ TP.HCM

    // Khởi tạo bản đồ
    map = new google.maps.Map(document.getElementById("map"), {
      zoom: 12,
      center: defaultLocation,
    });

    // Gọi API để lấy danh sách cửa hàng từ backend
    fetch("get_stores.php")
      .then(response => response.json())
      .then(stores => {
        // Duyệt qua danh sách cửa hàng và đặt marker
        stores.forEach(store => {
          new google.maps.Marker({
            position: { lat: parseFloat(store.latitude), lng: parseFloat(store.longitude) },
            map: map,
            title: store.name,
          });
        });
      })
      .catch(error => console.error("Error fetching stores:", error));
  }
</script>

<body>
  <!-- Navbar -->
  <?php include 'navbar.php'; ?>

  <!-- Carousel -->
  <div class="carousel slide" id="carouselDemo" data-bs-wrap="true" data-bs-ride="carousel">
    <div class="carousel-inner">
      <div class="carousel-item active">
        <img src="images/slide_show_1.jpg" class="w-100">
      </div>
      <div class="carousel-item">
        <img src="images/slide_show_2.jpg" class="w-100">
      </div>
      <div class="carousel-item">
        <img src="images/slide_show_3.jpg" class="w-100">
      </div>
    </div>

    <button class="carousel-control-prev d-none d-md-block" type="button" data-bs-target="#carouselDemo"
      data-bs-slide="prev">
      <span class="carousel-control-prev-icon"></span>
    </button>

    <button class="carousel-control-next d-none d-md-block" type="button" data-bs-target="#carouselDemo"
      data-bs-slide="next">
      <span class="carousel-control-next-icon"></span>
    </button>
  </div>
  <!-- End Carousel -->

  <!-- Feature Section -->
  <div class="feature-section bg-white rounded-lg d-flex flex-wrap p-4">
    <div class="feature-box col-12 col-md-6 col-lg-3">
      <i class="feature-box__icon bi bi-percent"></i>
      <div>
        <h3>Discount</h3>
        <p>Every week new sales</p>
      </div>
    </div>
    <div class="feature-box col-12 col-md-6 col-lg-3">
      <i class="feature-box__icon bi bi-truck"></i>
      <div>
        <h3>Free Delivery</h3>
        <p>Free for all orders</p>
      </div>
    </div>
    <div class="feature-box col-12 col-md-6 col-lg-3">
      <i class="feature-box__icon bi bi-clock"></i>
      <div>
        <h3>Great Support 24/7</h3>
        <p>We care your experiences</p>
      </div>
    </div>
    <div class="feature-box col-12 col-md-6 col-lg-3">
      <i class="feature-box__icon bi bi-shield-lock"></i>
      <div>
        <h3>Secure Payment</h3>
        <p>Secure Payment Method</p>
      </div>
    </div>
  </div>

  <!-- About Us -->
  <div class="d-flex align-items-center justify-content-center min-vh-100" id="aboutUs">
    <div class="abt--container container form-container d-flex flex-column flex-md-row w-100 max-w-4xl">
      <!-- Left Section: About Us and Button -->
      <div class="w-100 w-md-6 mb-4 mb-md-0 me-md-4">
        <h2 class="text--aboutUs mb-4">About Us</h2>
        <p class="mb-4">
          "Welcome to Times Records – a paradise for music lovers! Whether you’re passionate about vinyl, a dedicated
          collector, or simply someone who cherishes great tunes, we’ve crafted this space just for you. If you’ve got
          the time, drop by our stores to soak in the music and shop in person. Can’t make it? No worries – just click
          the button below, and we’ll bring the music right to you!"
        </p>

        <a href="product.php" class="abt--button btn w-100">
          Shop Now <i class="bi bi-cart4"></i>
        </a>
      </div>

      <!-- Right Section: Google Map -->
      <div class="w-100 w-md-6">
        <div id="map" style="width: 100%; height: 280px; border-radius: 8px;"></div>
      </div>
    </div>
  </div>
  <!-- End About Us -->

  <!-- Footer -->
  <?php include 'footer.php'; ?>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>