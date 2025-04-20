<?php
session_start();
include 'db_connect.php';
$page_title = "Products";

// Guest không có quyền mua hàng
$canPurchase = isset($_SESSION['user_id']) && ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'user');
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'head.php'; ?>

<body>
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Header -->
    <header class="collection__info section-header container">
        <div class="row">
            <div class="collection__image col-md-12 col-lg-12 mt-3">
                <img src="./images/collection_img.jpg" class="img-fluid rounded-3" alt="Album collection">
            </div>
            <div class="rte rte--header space-10 col-md-6">
                <p> </p>
            </div>
        </div>
    </header>

    <!-- Product container -->
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 product-sidebar">
                <div class="p-3">
                    <h5>Category</h5>
                    <ul class="list-group">
                        <li class="list-group-item category-filter <?php if (!isset($_GET['category_id']) || $_GET['category_id'] == 0) echo 'active'; ?>" 
                            data-category-id="0">
                            <a href="#" class="text-decoration-none">All</a>
                        </li>
                        <li class="list-group-item category-filter <?php if (isset($_GET['category_id']) && $_GET['category_id'] == 1) echo 'active'; ?>" 
                            data-category-id="1">
                            <a href="#" class="text-decoration-none">Vinyl</a>
                        </li>
                        <li class="list-group-item category-filter <?php if (isset($_GET['category_id']) && $_GET['category_id'] == 2) echo 'active'; ?>" 
                            data-category-id="2">
                            <a href="#" class="text-decoration-none">Merch</a>
                        </li>
                    </ul>
                </div>

                <!-- Spotify hiển thị trên màn lớn -->
                <div class="spotify-widget d-none d-md-block mt-4">
                    <iframe style="border-radius:12px" src="https://open.spotify.com/embed/playlist/0lLgC9zfleQhs3l4CI1k8g"
                        width="100%" height="680" frameBorder="0" allowfullscreen="" loading="lazy"
                        allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture">
                    </iframe>
                </div>
            </div>

            <!-- Product list -->
            <div class="col-md-9">
                <!-- Sort/Limit button -->
                <div class="row mb-4">
                    <div class="col-12 d-flex justify-content-end">
                        <div class="d-flex align-items-center gap-3">
                            <!-- Sort -->
                            <label for="sort" class="me-2">
                                <i class="bi bi-funnel sort-icon"></i>
                            </label>
                            <select id="sort" class="form-select rounded-pill shadow-none">
                                <option value="">None</option>
                                <option value="name_asc" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'name_asc') echo 'selected'; ?>>Name (A-Z)</option>
                                <option value="name_desc" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'name_desc') echo 'selected'; ?>>Name (Z-A)</option>
                                <option value="price_asc" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') echo 'selected'; ?>>Price (Low to High)</option>
                                <option value="price_desc" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') echo 'selected'; ?>>Price (High to Low)</option>
                            </select>

                            <!-- Limit -->
                            <label for="limit" class="me-2">
                                <i class="bi bi-layout-wtf limit-icon"></i>
                            </label>
                            <select id="limit" class="form-select rounded-pill shadow-none">
                                <option value="9" <?php if (!isset($_GET['limit']) || $_GET['limit'] == 9) echo 'selected'; ?>>9</option>
                                <option value="12" <?php if (isset($_GET['limit']) && $_GET['limit'] == 12) echo 'selected'; ?>>12</option>
                                <option value="15" <?php if (isset($_GET['limit']) && $_GET['limit'] == 15) echo 'selected'; ?>>15</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Product list -->
                <div class="product-row row" id="search-results">
                    <!-- Kết quả tìm kiếm sẽ được hiển thị động ở đây -->
                </div>

                <hr class="my-4">

                <!-- Pagination -->
                <div class="pagination d-flex justify-content-center mt-4" id="pagination">
                    <!-- Pagination sẽ được hiển thị động ở đây -->
                </div>

                <!-- Hiển thị Spotify dưới pagination trên màn hình nhỏ -->
                <div class="spotify-widget d-block d-md-none mt-4">
                    <iframe style="border-radius:12px" src="https://open.spotify.com/embed/playlist/0lLgC9zfleQhs3l4CI1k8g"
                        width="100%" height="380" frameBorder="0" allowfullscreen="" loading="lazy"
                        allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture">
                    </iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (cần cho AJAX) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <!-- Script AJAX để tìm kiếm -->
    <script>
        // Hàm thêm sản phẩm vào giỏ
        function addToCart(productId) {
            console.log('Adding product to cart:', productId); // Debug
            $.ajax({
                url: 'cart_process.php',
                method: 'POST',
                data: { action: 'add', product_id: productId, quantity: 1 },
                dataType: 'json',
                success: function (result) {
                    console.log('Add to cart response:', result); // Debug
                    if (result.success) {
                        updateCartCount(); // Chỉ cập nhật badge
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Add to cart AJAX error:', status, error); // Debug
                }
            });
        }

        // Hàm cập nhật số lượng giỏ hàng
        function updateCartCount() {
            console.log('Updating cart count'); // Debug
            $.ajax({
                url: 'cart_process.php',
                method: 'POST',
                data: { action: 'get_count' },
                dataType: 'json',
                success: function (count) {
                    console.log('Cart count:', count); // Debug
                    $('#cart-count').text(count);
                    if (count == 0) $('#cart-count').hide();
                    else $('#cart-count').show();
                },
                error: function (xhr, status, error) {
                    console.error('Cart count AJAX error:', status, error); // Debug
                }
            });
        }

        $(document).ready(function () {
            // Hàm tìm kiếm và hiển thị sản phẩm
            function searchProducts(page = 1) {
                let keyword = $('#search-input').val().trim().toLowerCase();
                let category = $('.category-filter.active').data('category-id') || '';
                let sort = $('#sort').val() || '';
                let limit = $('#limit').val() || 9;

                if (keyword === 'vinyl') {
                    window.location.href = 'product.php?category_id=1';
                    return;
                } else if (keyword === 'merch') {
                    window.location.href = 'product.php?category_id=2';
                    return;
                }

                $.ajax({
                    url: 'search.php',
                    method: 'POST',
                    data: {
                        keyword: keyword,
                        category: category,
                        sort: sort,
                        limit: limit,
                        page: page
                    },
                    success: function (response) {
                        let data = JSON.parse(response);
                        $('#search-results').html(data.products);
                        $('#pagination').html(data.pagination);
                    },
                    error: function (xhr, status, error) {
                        console.error('Search AJAX error:', status, error);
                        $('#search-results').html('<p class="text-danger text-center">Đã có lỗi xảy ra. Vui lòng thử lại.</p>');
                    }
                });
            }

            // Tải sản phẩm khi trang được tải lần đầu
            searchProducts();

            // Tìm kiếm khi người dùng chọn danh mục từ sidebar
            $('.category-filter').on('click', function (e) {
                e.preventDefault();
                $('.category-filter').removeClass('active');
                $(this).addClass('active');
                searchProducts();
            });

            // Tìm kiếm khi thay đổi sắp xếp
            $('#sort').on('change', function () {
                searchProducts();
            });

            // Tìm kiếm khi thay đổi số lượng sản phẩm mỗi trang
            $('#limit').on('change', function () {
                searchProducts();
            });

            // Xử lý phân trang
            $(document).on('click', '.page-link', function (e) {
                e.preventDefault();
                let page = $(this).data('page');
                if (page) {
                    searchProducts(page);
                }
            });
        });
    </script>
</body>
</html>