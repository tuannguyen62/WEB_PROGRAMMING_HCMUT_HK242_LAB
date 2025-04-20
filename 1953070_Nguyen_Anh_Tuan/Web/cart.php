<?php
session_start();
include 'db_connect.php';
$page_title = "Shopping Cart";

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'user'])) {
    header("Location: login.php?error=Please login to view cart");
    exit();
}

// Lấy danh sách sản phẩm trong giỏ
$user_id = $_SESSION['user_id'];
$stmt = $mysqli->prepare("SELECT c.id, c.product_id, c.quantity, p.name, p.price, p.image 
                         FROM cart c 
                         JOIN products p ON c.product_id = p.id 
                         WHERE c.user_id = ?");
if (!$stmt) {
    error_log("Prepare failed: " . $mysqli->error);
    die("Error: Unable to prepare query");
}
$stmt->bind_param("i", $user_id);
if (!$stmt->execute()) {
    error_log("Execute failed: " . $stmt->error);
    die("Error: Unable to execute query");
}
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Tính tổng giá
$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += $item['price'] * $item['quantity'];
}
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

        <!-- Cart -->
        <h2 class="text--aboutUs mb-4">Shopping Cart</h2>
        <div class="glass-card p-4 rounded-3 cart">
            <?php if (empty($cart_items)): ?>
                <p class="text-center text-muted">Your cart is empty.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $item): ?>
                                <tr data-cart-id="<?php echo $item['id']; ?>">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="./images/product_img/<?php echo htmlspecialchars($item['image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                                 class="rounded shadow-sm me-3" style="max-width: 50px;">
                                            <span><?php echo htmlspecialchars($item['name']); ?></span>
                                        </div>
                                    </td>
                                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                                    <td>
                                        <input type="number" class="form-control glass-input quantity-input" 
                                               value="<?php echo $item['quantity']; ?>" min="1" 
                                               data-cart-id="<?php echo $item['id']; ?>" style="width: 80px;">
                                    </td>
                                    <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                    <td>
                                        <button class="btn btn-delete rounded-pill glass-btn" 
                                                onclick="removeFromCart(<?php echo $item['id']; ?>)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end mt-4">
                    <div>
                        <h4>Total: <span class="price fs-4 badge bg-white text-danger">$<?php echo number_format($total_price, 2); ?></span></h4>
                        <button class="btn btn-danger rounded-pill glass-btn mt-2" onclick="alert('Proceeding to checkout...')">
                            <i class="bi bi-credit-card me-2"></i>Checkout
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <!-- Bootstrap JS và jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function showAlert(type, message) {
            let alertHtml = `<div class="alert alert-${type} alert-dismissible fade show rounded-pill glass-alert" role="alert">
                                ${message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>`;
            $('#alert-container').html(alertHtml);
            setTimeout(() => $('.alert').alert('close'), 3000);
        }

        function removeFromCart(cartId) {
            console.log('Removing cart item:', cartId);
            $.ajax({
                url: 'cart_process.php',
                method: 'POST',
                data: { action: 'remove', cart_id: cartId },
                dataType: 'json',
                success: function (result) {
                    console.log('Remove response:', result);
                    if (result.success) {
                        showAlert('success', result.message);
                        $(`tr[data-cart-id="${cartId}"]`).remove();
                        updateCartCount();
                        $.ajax({
                            url: 'cart_process.php',
                            method: 'POST',
                            data: { action: 'get_total' },
                            dataType: 'json',
                            success: function (total) {
                                $('.price.badge').text('$' + total.toFixed(2));
                                if (total == 0) {
                                    $('.cart').html('<p class="text-center text-muted">Your cart is empty.</p>');
                                }
                            },
                            error: function () {
                                showAlert('danger', 'Failed to update total');
                            }
                        });
                    } else {
                        showAlert('danger', result.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Remove AJAX error:', status, error);
                    showAlert('danger', 'Failed to remove item from cart');
                }
            });
        }

        $('.quantity-input').on('change', function () {
            let cartId = $(this).data('cart-id');
            let quantity = parseInt($(this).val());
            if (quantity < 1) quantity = 1;
            console.log('Updating quantity:', cartId, quantity);
            $.ajax({
                url: 'cart_process.php',
                method: 'POST',
                data: { action: 'update', cart_id: cartId, quantity: quantity },
                dataType: 'json',
                success: function (result) {
                    console.log('Update response:', result);
                    if (result.success) {
                        showAlert('success', result.message);
                        let price = parseFloat($(`tr[data-cart-id="${cartId}"] td:nth-child(2)`).text().replace('$', ''));
                        $(`tr[data-cart-id="${cartId}"] td:nth-child(4)`).text('$' + (price * quantity).toFixed(2));
                        $.ajax({
                            url: 'cart_process.php',
                            method: 'POST',
                            data: { action: 'get_total' },
                            dataType: 'json',
                            success: function (total) {
                                $('.price.badge').text('$' + total.toFixed(2));
                            },
                            error: function () {
                                showAlert('danger', 'Failed to update total');
                            }
                        });
                    } else {
                        showAlert('danger', result.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Update AJAX error:', status, error);
                    showAlert('danger', 'Failed to update quantity');
                }
            });
        });

        function updateCartCount() {
            console.log('Updating cart count');
            $.ajax({
                url: 'cart_process.php',
                method: 'POST',
                data: { action: 'get_count' },
                dataType: 'json',
                success: function (count) {
                    console.log('Cart count:', count);
                    $('#cart-count').text(count);
                    if (count == 0) $('#cart-count').hide();
                    else $('#cart-count').show();
                },
                error: function (xhr, status, error) {
                    console.error('Cart count AJAX error:', status, error);
                }
            });
        }

        updateCartCount();
    </script>
</body>
</html>