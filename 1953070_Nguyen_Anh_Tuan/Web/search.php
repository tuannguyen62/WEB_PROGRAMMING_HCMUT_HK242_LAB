<?php
include 'db_connect.php';

// Guest không có quyền mua hàng
session_start();
$canPurchase = isset($_SESSION['user_id']) && ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'user');

// Lấy dữ liệu từ yêu cầu AJAX
$keyword = isset($_POST['keyword']) ? trim($_POST['keyword']) : '';
$category = isset($_POST['category']) ? trim($_POST['category']) : '';
$sort = isset($_POST['sort']) ? trim($_POST['sort']) : '';
$limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 9;
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$offset = ($page - 1) * $limit;

// Truy vấn đếm tổng số sản phẩm
$count_sql = "SELECT COUNT(*) as total FROM products WHERE 1=1";
$params = [];
$types = '';
if (!empty($keyword)) {
    $count_sql .= " AND name LIKE ?";
    $params[] = "%$keyword%";
    $types .= "s";
}
if (!empty($category)) {
    $count_sql .= " AND category_id = ?";
    $params[] = $category;
    $types .= "i";
}
$stmt = $mysqli->prepare($count_sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result_count = $stmt->get_result();
$total_row = $result_count->fetch_assoc();
$total_products = $total_row['total'];
$total_pages = ceil($total_products / $limit);

// Truy vấn lấy danh sách sản phẩm
$sql = "SELECT * FROM products WHERE 1=1";
if (!empty($keyword)) {
    $sql .= " AND name LIKE ?";
}
if (!empty($category)) {
    $sql .= " AND category_id = ?";
}
if ($sort == "name_asc") {
    $sql .= " ORDER BY name ASC";
} elseif ($sort == "name_desc") {
    $sql .= " ORDER BY name DESC";
} elseif ($sort == "price_asc") {
    $sql .= " ORDER BY price ASC";
} elseif ($sort == "price_desc") {
    $sql .= " ORDER BY price DESC";
}
$sql .= " LIMIT $limit OFFSET $offset";

$stmt = $mysqli->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Tạo HTML cho danh sách sản phẩm
$products_html = '';
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products_html .= '<div class="product-column col-md-6 col-lg-4 mb-4">';
        $products_html .= '<div class="card product-card d-flex flex-column h-100">';
        $products_html .= '<a href="product_detail.php?product_id=' . $row['id'] . '">';
        $products_html .= '<img class="product-img card-img-top" src="./images/product_img/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '" loading="lazy">';
        $products_html .= '</a>';
        $products_html .= '<div class="product-card-body card-body flex-grow-1 d-flex flex-column">';
        $products_html .= '<a href="product_detail.php?product_id=' . $row['id'] . '" class="text-decoration-none">';
        $products_html .= '<h5 class="product-card-title text-black">' . htmlspecialchars($row['name']) . '</h5>';
        $products_html .= '</a>';
        $products_html .= '<div class="mt-auto d-flex justify-content-between align-items-center">';
        $products_html .= '<span class="price badge bg-danger">$' . number_format($row['price'], 2) . '</span>';
        if ($canPurchase) {
            $products_html .= '<button class="add-to-cart-btn btn" onclick="addToCart(' . $row['id'] . ')"><i class="add-to-cart-text bi bi-bag-plus"></i></button>';
        } else {
            $products_html .= '<button class="add-to-cart-btn btn" onclick="window.location.href=\'login.php\'"><i class="add-to-cart-text bi bi-bag-plus"></i></button>';
        }
        $products_html .= '</div></div></div></div>';
    }
} else {
    $products_html = '<p class="text-center">No products found.</p>';
}

// Tạo HTML cho phân trang
$pagination_html = '';
if ($total_pages > 1) {
    $pagination_html .= '<nav><ul class="pagination custom-pagination">';
    if ($page > 1) {
        $pagination_html .= '<li class="page-item"><a class="page-link" href="#" data-page="' . ($page - 1) . '"><i class="bi bi-chevron-left"></i></a></li>';
    }
    for ($i = 1; $i <= $total_pages; $i++) {
        $pagination_html .= '<li class="page-item ' . ($i == $page ? 'active' : '') . '"><a class="page-link" href="#" data-page="' . $i . '">' . $i . '</a></li>';
    }
    if ($page < $total_pages) {
        $pagination_html .= '<li class="page-item"><a class="page-link" href="#" data-page="' . ($page + 1) . '"><i class="bi bi-chevron-right"></i></a></li>';
    }
    $pagination_html .= '</ul></nav>';
}

// Trả về dữ liệu dưới dạng JSON
echo json_encode([
    'products' => $products_html,
    'pagination' => $pagination_html
]);
?>