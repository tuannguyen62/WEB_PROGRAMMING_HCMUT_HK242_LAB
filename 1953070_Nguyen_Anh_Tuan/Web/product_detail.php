<?php
session_start();
include 'db_connect.php';
$page_title = "Product Detail";

// Kiểm tra product_id
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
if ($product_id <= 0) {
    header("Location: product.php?error=Invalid product ID");
    exit();
}

// Lấy thông tin sản phẩm
$stmt = $mysqli->prepare("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->num_rows > 0 ? $result->fetch_assoc() : null;
$stmt->close();

if (!$product) {
    header("Location: product.php?error=Product not found");
    exit();
}

// Kiểm tra quyền mua hàng
$canPurchase = isset($_SESSION['user_id']) && ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'user');
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'head.php'; ?>

<body>
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Main container -->
    <div class="container my-5">
        <!-- Thông báo -->
        <div id="alert-container" class="mb-4"></div>

        <!-- Product detail -->
        <div class="row glass-card p-4 rounded-3">
            <div class="col-md-6 mb-4 mb-md-0">
                <img src="./images/product_img/<?php echo htmlspecialchars($product['image']); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                     class="img-fluid rounded-3 shadow-sm product-detail-img">
            </div>
            <div class="col-md-6 d-flex flex-column">
                <h2 class="text--aboutUs mb-3"><?php echo htmlspecialchars($product['name']); ?></h2>
                <p class="price text-black fs-4 bg-white">$<?php echo number_format($product['price'], 2); ?></p>
                <p class="text-muted mb-4"><?php echo htmlspecialchars($product['description'] ?: 'No description available.'); ?></p>
                <div class="d-flex gap-3 mt-auto">
                    <?php if ($canPurchase): ?>
                        <button class="btn btn-danger rounded-pill glass-btn flex-fill" onclick="addToCart(<?php echo $product['id']; ?>)">
                            <i class="bi bi-bag-plus me-2"></i>Add to Cart
                        </button>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-danger rounded-pill glass-btn flex-fill">
                            <i class="bi bi-bag-plus me-2"></i>Login to Add to Cart
                        </a>
                    <?php endif; ?>
                    <a href="product.php" class="btn btn-back rounded-pill flex-fill">
                        <i class="bi bi-arrow-left me-2"></i>Back to Products
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <!-- Bootstrap JS và jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function addToCart(productId) {
            $.ajax({
                url: 'cart_process.php',
                method: 'POST',
                data: { action: 'add', product_id: productId, quantity: 1 },
                dataType: 'json',
                success: function (result) {
                    if (result.success) {
                        showAlert('success', result.message);
                        updateCartCount();
                    } else {
                        showAlert('danger', result.message);
                    }
                },
                error: function () {
                    showAlert('danger', 'Failed to add product to cart');
                }
            });
        }

        function showAlert(type, message) {
            let alertHtml = `<div class="alert alert-${type} alert-dismissible fade show rounded-pill glass-alert" role="alert">
                                ${message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>`;
            $('#alert-container').html(alertHtml);
            setTimeout(() => $('.alert').alert('close'), 3000);
        }

        function updateCartCount() {
            $.ajax({
                url: 'cart_process.php',
                method: 'POST',
                data: { action: 'get_count' },
                dataType: 'json',
                success: function (count) {
                    $('#cart-count').text(count);
                    if (count == 0) $('#cart-count').hide();
                    else $('#cart-count').show();
                }
            });
        }
    </script>
</body>
</html>