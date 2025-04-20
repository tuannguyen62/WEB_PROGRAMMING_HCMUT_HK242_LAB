<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
$isLoggedIn = isset($_SESSION['user_id']);

// Ẩn nút "Login" ở trang Login và Sign Up
$currentPage = basename($_SERVER['PHP_SELF']);
$hideLoginButton = ($currentPage == 'login.php' || $currentPage == 'signup.php');

// Kiểm tra nếu đang ở phần About Us trong index.php
$isAboutSection = ($currentPage == 'index.php' && isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '#aboutUs') !== false);
?>

<nav class="navbar navbar-expand-lg sticky-top bg-white">
    <div class="container-fluid">
        <a class="navbar-brand me-auto" href="#">times records</a>

        <!-- Offcanvas menu cho màn nhỏ -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasNavbarLabel">times records</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav justify-content-center flex-grow-1 pe-3">
                    <li class="nav-item">
                        <a class="nav-link mx-lg-2 <?php echo ($currentPage == 'index.php' && !$isAboutSection) ? 'active' : ''; ?>" 
                           aria-current="page" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mx-lg-2 <?php echo ($currentPage == 'product.php') ? 'active' : ''; ?>" 
                           href="product.php">Product</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mx-lg-2 <?php echo $isAboutSection ? 'active' : ''; ?>" 
                           href="index.php#aboutUs">About</a>
                    </li>
                </ul>
                <!-- Thanh tìm kiếm -->
                <form class="searchBar d-flex mx-lg-2 shadow-none position-relative" role="search" id="search-form">
                    <input id="search-input" class="form-control me-2 rounded-pill shadow-none" type="search" placeholder="Search"
                           aria-label="Search" autocomplete="off">
                    <!-- Dropdown gợi ý sản phẩm -->
                    <ul id="search-suggestions" class="dropdown-menu shadow-sm" 
                        style="display: none; width: 100%; max-height: 300px; overflow-y: auto;"></ul>
                </form>
            </div>
        </div>

        <!-- Biểu tượng giỏ hàng -->
        <?php if ($isLoggedIn && in_array($_SESSION['role'], ['admin', 'user'])): ?>
            <a href="cart.php" class="cart-icon me-3 position-relative">
                <i class="bi bi-cart3 fs-4"></i>
                <span id="cart-count" class="badge bg-danger rounded-pill position-absolute" style="top: -10px; right: -10px; display: none;">0</span>
            </a>
        <?php endif; ?>

        <!-- Hiển thị nút Login hoặc thông tin người dùng -->
        <?php if ($isLoggedIn): ?>
            <div class="dropdown">
                <a class="user-info btn btn-outline-danger d-flex align-items-center rounded-pill" href="#" role="button"
                   id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle me-2"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <li><a class="dropdown-item" href="admin_dashboard.php"><i class="bi bi-gear me-2"></i>Dashboard</a></li>
                    <?php elseif ($_SESSION['role'] == 'user'): ?>
                        <li><a class="dropdown-item" href="user_dashboard.php"><i class="bi bi-gear me-2"></i>Dashboard</a></li>
                    <?php else: ?>
                        <li><a class="dropdown-item" href="index.php"><i class="bi bi-gear me-2"></i>Dashboard</a></li>
                    <?php endif; ?>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                </ul>
            </div>
        <?php else: ?>
            <?php if (!$hideLoginButton): ?>
                <a href="login.php" class="login-button">Login
                    <i class="login-icon bi bi-arrow-right"></i>
                </a>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Nút toggler trên màn nhỏ -->
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
                aria-controls="offcanvasNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>

    <!-- jQuery (cần cho AJAX) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
            integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <!-- Script AJAX để tìm kiếm -->
    <script>
        $(document).ready(function () {
            $('#search-input').on('input', function () {
                let keyword = $(this).val().trim().toLowerCase();
                let isAdminPage = window.location.pathname.includes('admin_dashboard.php');

                if (keyword === 'vinyl') {
                    window.location.href = 'product.php?category_id=1';
                    return;
                } else if (keyword === 'merch') {
                    window.location.href = 'product.php?category_id=2';
                    return;
                }

                if (isAdminPage) {
                    $('#search-suggestions').hide();
                    $.ajax({
                        url: 'product_process.php',
                        method: 'GET',
                        data: {
                            action: 'list',
                            page: 1,
                            limit: 10,
                            keyword: keyword
                        },
                        success: function (response) {
                            $('#product-table tbody').html(response);
                        },
                        error: function (xhr, status, error) {
                            console.error('Lỗi AJAX (admin search):', error);
                            $('#product-table tbody').html('<tr><td colspan="7" class="text-center">Error loading products: ' + error + '</td></tr>');
                        }
                    });
                } else if (window.location.pathname.includes('product.php')) {
                    $('#search-suggestions').hide();
                    $.ajax({
                        url: 'search.php',
                        method: 'POST',
                        data: {
                            keyword: keyword,
                            category: $('.category-filter.active').data('category-id') || '',
                            sort: $('#sort').val() || '',
                            limit: $('#limit').val() || 9,
                            page: 1
                        },
                        success: function (response) {
                            let data = JSON.parse(response);
                            $('#search-results').html(data.products);
                            $('#pagination').html(data.pagination);
                        },
                        error: function (xhr, status, error) {
                            console.error('Lỗi AJAX (real-time search):', error);
                            $('#search-results').html('<p class="text-danger text-center">Đã có lỗi xảy ra. Vui lòng thử lại.</p>');
                        }
                    });
                } else if (keyword.length > 0) {
                    $.ajax({
                        url: 'search_suggestions.php',
                        method: 'POST',
                        data: { keyword: keyword },
                        success: function (suggestions) {
                            let suggestionsHtml = '';
                            if (suggestions.error) {
                                suggestionsHtml = '<li class="dropdown-item text-danger">' + suggestions.error + '</li>';
                            } else if (suggestions.length > 0) {
                                suggestions.forEach(function (product) {
                                    suggestionsHtml += '<li class="dropdown-item" data-product-id="' + product.id + '">' + product.name + '</li>';
                                });
                            } else {
                                suggestionsHtml = '<li class="dropdown-item text-muted">No products found</li>';
                            }
                            $('#search-suggestions').html(suggestionsHtml).show();
                        },
                        error: function (xhr, status, error) {
                            console.error('Lỗi AJAX (gợi ý):', error);
                            $('#search-suggestions').html('<li class="dropdown-item text-danger">Error loading suggestions: ' + error + '</li>').show();
                        }
                    });
                } else {
                    $('#search-suggestions').hide();
                }
            });

            $('#search-form').on('submit', function (e) {
                e.preventDefault();
                let keyword = $('#search-input').val().trim().toLowerCase();
                let isAdminPage = window.location.pathname.includes('admin_dashboard.php');

                if (keyword === 'vinyl') {
                    window.location.href = 'product.php?category_id=1';
                } else if (keyword === 'merch') {
                    window.location.href = 'product.php?category_id=2';
                } else if (isAdminPage) {
                    $('#search-suggestions').hide();
                    $.ajax({
                        url: 'product_process.php',
                        method: 'GET',
                        data: {
                            action: 'list',
                            page: 1,
                            limit: 10,
                            keyword: keyword
                        },
                        success: function (response) {
                            $('#product-table tbody').html(response);
                        },
                        error: function (xhr, status, error) {
                            console.error('Lỗi AJAX (admin search):', error);
                        }
                    });
                } else if (keyword.length > 0) {
                    window.location.href = 'product.php?keyword=' + encodeURIComponent(keyword);
                }
            });

            $(document).on('click', '#search-suggestions .dropdown-item', function () {
                let productId = $(this).data('product-id');
                if (productId) {
                    window.location.href = 'product_detail.php?product_id=' + productId;
                }
            });

            $(document).on('click', function (e) {
                if (!$(e.target).closest('#search-input').length && !$(e.target).closest('#search-suggestions').length) {
                    $('#search-suggestions').hide();
                }
            });

            // Cập nhật số lượng giỏ hàng khi tải trang
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
            updateCartCount();
        });
    </script>
</nav>