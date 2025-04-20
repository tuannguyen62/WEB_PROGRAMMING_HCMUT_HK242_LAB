<?php
session_start();
include 'db_connect.php';
$page_title = "Admin Dashboard";

// Kiểm tra quyền truy cập
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=Access denied");
    exit();
}

// Xử lý phân trang
$limit = 10; // Số sản phẩm mỗi trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Lấy từ khóa tìm kiếm từ navbar
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

// Lấy tổng số sản phẩm (có từ khóa nếu có)
$total_sql = "SELECT COUNT(*) as total FROM products";
if (!empty($keyword)) {
    $total_sql .= " WHERE name LIKE '%" . $mysqli->real_escape_string($keyword) . "%'";
}
$total_result = $mysqli->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_products = $total_row['total'];
$total_pages = ceil($total_products / $limit);

// Lấy danh sách sản phẩm theo phân trang và từ khóa
$sql = "SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id";
if (!empty($keyword)) {
    $sql .= " WHERE p.name LIKE '%" . $mysqli->real_escape_string($keyword) . "%'";
}
$sql .= " LIMIT $limit OFFSET $offset";
$result = $mysqli->query($sql);

// Lấy danh sách danh mục cho modal
$categories = $mysqli->query("SELECT * FROM categories");
?>

<!DOCTYPE html>
<html lang="en">
<? God knows what. -->
<?php include 'head.php'; ?>

<body>
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Main container -->
    <div class="container my-5">
        <div class="row align-items-center mb-4">
            <!-- Title góc trái -->
            <div class="col-6">
                <h2 class="text--aboutUs m-0">Manage Products</h2>
            </div>
            <!-- Nút Add New Product góc phải -->
            <div class="col-6 text-end">
                <button class="btn btn-danger rounded-pill glass-btn" data-bs-toggle="modal" data-bs-target="#productModal" 
                        onclick="openProductModal('create')">
                    <i class="bi bi-plus"></i> Add New Product
                </button>
            </div>
        </div>

        <!-- Thông báo -->
        <div id="alert-container" class="mb-4"></div>

        <!-- Bảng sản phẩm -->
        <div class="table-responsive glass-table p-4 rounded-3">
            <table class="table" id="product-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr data-id="<?php echo $row['id']; ?>">
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <td>$<?php echo number_format($row['price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                                <td>
                                    <img src="./images/product_img/<?php echo htmlspecialchars($row['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($row['name']); ?>" 
                                         class="rounded shadow-sm" style="max-width: 50px;">
                                </td>
                                <td class="d-flex gap-2 align-items-center">
                                    <button class="btn btn-sm btn-edit rounded-pill glass-btn" 
                                            onclick="openProductModal('update', <?php echo $row['id']; ?>)">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-delete rounded-pill glass-btn" 
                                            onclick="openDeleteModal(<?php echo $row['id']; ?>)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center">No products found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav aria-label="Product pagination" class="mt-4">
                    <ul class="pagination custom-pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>&keyword=<?php echo urlencode($keyword); ?>" aria-label="Previous">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&keyword=<?php echo urlencode($keyword); ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>&keyword=<?php echo urlencode($keyword); ?>" aria-label="Next">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal cho thêm/chỉnh sửa sản phẩm -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content glass-modal">
                <div class="modal-header border-0 py-2">
                    <h5 class="modal-title text--aboutUs m-0" id="productModalLabel">
                        <i class="bi bi-box-seam me-1"></i> Add New Product
                    </h5>
                    <button type="button" class="modal-button_close btn-close glass-btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                </div>
                <div class="modal-body py-3 px-4">
                    <form id="productForm" enctype="multipart/form-data">
                        <input type="hidden" name="id" id="productId">
                        <input type="hidden" name="action" id="formAction">
                        <div class="mb-2">
                            <label for="name" class="form-label mb-1"><i class="bi bi-tag me-1"></i> Name</label>
                            <input type="text" class="form-control rounded-pill glass-input py-1" id="name" name="name" required>
                        </div>
                        <div class="mb-2">
                            <label for="description" class="form-label mb-1"><i class="bi bi-text-paragraph me-1"></i> Description</label>
                            <textarea class="form-control glass-input" id="description" name="description" rows="2"></textarea>
                        </div>
                        <div class="mb-2">
                            <label for="price" class="form-label mb-1"><i class="bi bi-currency-dollar me-1"></i> Price</label>
                            <input type="number" step="0.01" class="form-control rounded-pill glass-input py-1" id="price" name="price" required>
                        </div>
                        <div class="mb-2">
                            <label for="category_id" class="form-label mb-1"><i class="bi bi-list-ul me-1"></i> Category</label>
                            <select class="form-select rounded-pill glass-input py-1" id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php while ($cat = $categories->fetch_assoc()): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label for="image" class="form-label mb-1"><i class="bi bi-image me-1"></i> Image</label>
                            <input type="file" class="form-control glass-input py-1" id="image" name="image" accept="image/*">
                            <small id="currentImage" class="form-text text-muted mt-1"></small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 py-2 px-4">
                    <button type="button" class="btn btn-cancel rounded-pill glass-btn py-1 px-3" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-save rounded-pill glass-btn py-1 px-3" onclick="submitProductForm()">
                        <i class="bi bi-save me-1"></i> Save
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal xác nhận xóa sản phẩm -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content glass-modal">
                <div class="modal-header border-0 py-2">

                    <h5 class="modal-title text--aboutUs m-0" id="deleteModalLabel">
                        <i class="bi bi-exclamation-circle me-1"></i> Confirm Delete
                    </h5>
                    <button type="button" class="modal-button_close btn-close glass-btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                </div>
                <div class="modal-body py-3 px-4">
                    <p class="text-muted mb-0">Are you sure you want to delete this product? This action cannot be undone.</p>
                    <input type="hidden" id="deleteProductId">
                </div>
                <div class="modal-footer border-0 py-2 px-4">
                    <button type="button" class="btn btn-cancel rounded-pill glass-btn py-1 px-3" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-danger rounded-pill glass-btn py-1 px-3" onclick="confirmDeleteProduct()">
                        <i class="bi bi-trash me-1"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <!-- Bootstrap JS và jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script AJAX -->
    <script>
        function openProductModal(action, id = null) {
            $('#formAction').val(action);
            $('#productModalLabel').text(action === 'create' ? 'Add New Product' : 'Edit Product');
            $('#productModalLabel').html(action === 'create' ? '<i class="bi bi-box-seam me-1"></i> Add New Product' : '<i class="bi bi-pencil-square me-1"></i> Edit Product');

            if (action === 'create') {
                $('#productForm')[0].reset();
                $('#productId').val('');
                $('#currentImage').text('');
                $('#image').prop('required', true);
                $('#productModal').modal('show');
            } else if (action === 'update' && id) {
                $.ajax({
                    url: 'product_process.php',
                    type: 'GET',
                    data: { action: 'get', id: id },
                    dataType: 'json',
                    success: function(product) {
                        console.log('Product data received:', product);
                        if (product && product.id) {
                            $('#productId').val(product.id);
                            $('#name').val(product.name || '');
                            $('#description').val(product.description || '');
                            $('#price').val(product.price || '');
                            $('#category_id').val(product.category_id || '');
                            $('#currentImage').text(product.image ? 'Current image: ' + product.image : 'No image');
                            $('#image').prop('required', false);
                            $('#productModal').modal('show');
                        } else {
                            showAlert('danger', 'No product data returned');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error, xhr.responseText);
                        showAlert('danger', 'Failed to load product data: ' + error);
                    }
                });
            }

            // Điều chỉnh vị trí modal khi mở
            adjustModalPosition('#productModal');
        }

        function adjustModalPosition(modalId) {
            const modalDialog = document.querySelector(`${modalId} .modal-dialog`);
            if (modalDialog) {
                const viewportHeight = window.innerHeight;
                const modalHeight = modalDialog.offsetHeight;
                const scrollTop = window.scrollY || window.pageYOffset;
                const topPosition = scrollTop + (viewportHeight - modalHeight) / 2;

                modalDialog.style.position = 'absolute';
                modalDialog.style.top = `${topPosition}px`;
                modalDialog.style.left = '50%';
                modalDialog.style.transform = 'translateX(-50%)';
            }
        }

        function openDeleteModal(id) {
            $('#deleteProductId').val(id);
            $('#deleteModal').modal('show');
            adjustModalPosition('#deleteModal');
        }

        function confirmDeleteProduct() {
            const id = $('#deleteProductId').val();
            $.ajax({
                url: 'product_process.php',
                type: 'GET',
                data: { action: 'delete', id: id },
                dataType: 'json',
                success: function(result) {
                    console.log('Delete response:', result);
                    if (result.success) {
                        showAlert('success', result.message);
                        $('#deleteModal').modal('hide');
                        refreshProductTable();
                    } else {
                        showAlert('danger', result.message);
                        $('#deleteModal').modal('hide');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Delete AJAX Error:', status, error, xhr.responseText);
                    showAlert('danger', 'Failed to delete product: ' + error);
                    $('#deleteModal').modal('hide');
                }
            });
        }

        function submitProductForm() {
            let formData = new FormData($('#productForm')[0]);
            console.log('Form data before sending:', Array.from(formData.entries()));
            $.ajax({
                url: 'product_process.php',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(result) {
                    console.log('Save response:', result);
                    if (result.success) {
                        showAlert('success', result.message);
                        $('#productModal').modal('hide');
                        refreshProductTable();
                    } else {
                        showAlert('danger', result.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Save AJAX Error:', status, error, xhr.responseText);
                    showAlert('danger', 'Failed to save product: ' + error + ' - ' + xhr.responseText);
                }
            });
        }

        function refreshProductTable() {
            let keyword = $('#search-input').val().trim();
            $.ajax({
                url: 'product_process.php',
                type: 'GET',
                data: { 
                    action: 'list', 
                    page: <?php echo $page; ?>, 
                    limit: <?php echo $limit; ?>, 
                    keyword: keyword 
                },
                success: function(response) {
                    console.log('Refresh response:', response);
                    $('#product-table tbody').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Refresh AJAX Error:', status, error, xhr.responseText);
                    showAlert('danger', 'Failed to refresh product list: ' + error + ' - Response: ' + xhr.responseText);
                }
            });
        }

        $(document).ready(function() {
            $('#search-input').on('input', function() {
                if (window.location.pathname.includes('admin_dashboard.php')) {
                    $('#search-suggestions').hide();
                    refreshProductTable();
                }
            });

            $('#search-form').on('submit', function(e) {
                e.preventDefault();
                if (window.location.pathname.includes('admin_dashboard.php')) {
                    $('#search-suggestions').hide();
                    refreshProductTable();
                }
            });

            $(document).on('keypress', '#search-input', function(e) {
                if (e.which === 13 && window.location.pathname.includes('admin_dashboard.php')) {
                    $('#search-suggestions').hide();
                    refreshProductTable();
                }
            });

            // Điều chỉnh vị trí modal khi cuộn hoặc thay đổi kích thước
            $(window).on('scroll resize', function() {
                adjustModalPosition('#productModal');
                adjustModalPosition('#deleteModal');
            });

            // Điều chỉnh vị trí modal khi modal được hiển thị
            $('#productModal').on('shown.bs.modal', function() {
                adjustModalPosition('#productModal');
            });
            $('#deleteModal').on('shown.bs.modal', function() {
                adjustModalPosition('#deleteModal');
            });
        });

        function showAlert(type, message) {
            let alertHtml = `<div class="alert alert-${type} alert-dismissible fade show rounded-pill glass-alert" role="alert">
                                ${message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>`;
            $('#alert-container').html(alertHtml);
            setTimeout(() => $('.alert').alert('close'), 3000);
        }
    </script>
</body>
</html>