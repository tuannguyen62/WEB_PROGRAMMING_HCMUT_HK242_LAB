<?php
session_start();
include 'db_connect.php';

// Kiểm tra quyền truy cập
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit();
}

// Đường dẫn lưu trữ ảnh
$upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/Web/images/product_img/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && ($action == 'create' || $action == 'update')) {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;

    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = basename($_FILES['image']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_ext, $allowed_ext)) {
            $new_file_name = uniqid() . '.' . $file_ext;
            $dest = $upload_dir . $new_file_name;
            if (move_uploaded_file($file_tmp, $dest)) {
                $image = $new_file_name;
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
                exit();
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid image format']);
            exit();
        }
    }

    if ($action == 'create') {
        $stmt = $mysqli->prepare("INSERT INTO products (name, description, price, category_id, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdss", $name, $description, $price, $category_id, $image);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Product created successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create product: ' . $mysqli->error]);
        }
        $stmt->close();
    } elseif ($action == 'update' && $id) {
        $sql = "UPDATE products SET name = ?, description = ?, price = ?, category_id = ?";
        if ($image) {
            $sql .= ", image = ?";
            $old_image_query = $mysqli->query("SELECT image FROM products WHERE id = $id");
            if ($old_image_query && $old_image_query->num_rows > 0) {
                $old_image = $old_image_query->fetch_assoc()['image'];
                if ($old_image && file_exists($upload_dir . $old_image)) {
                    unlink($upload_dir . $old_image);
                }
            }
        }
        $sql .= " WHERE id = ?";
        
        $stmt = $mysqli->prepare($sql);
        if ($image) {
            $stmt->bind_param("ssdssi", $name, $description, $price, $category_id, $image, $id);
        } else {
            $stmt->bind_param("ssdsi", $name, $description, $price, $category_id, $id);
        }
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update product: ' . $mysqli->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action or missing ID']);
    }
} elseif ($action == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $mysqli->query("SELECT image FROM products WHERE id = $id");
    if ($result->num_rows > 0) {
        $image = $result->fetch_assoc()['image'];
        if ($image && file_exists($upload_dir . $image)) {
            unlink($upload_dir . $image);
        }
    }
    $stmt = $mysqli->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete product: ' . $mysqli->error]);
    }
    $stmt->close();
} elseif ($action == 'get' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $mysqli->prepare("SELECT id, name, description, price, category_id, image FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();
            echo json_encode($product);
        } else {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Execute failed: ' . $stmt->error]);
    }
    $stmt->close();
} elseif ($action == 'list') {
    header('Content-Type: text/html'); // Trả về HTML
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
    $offset = ($page - 1) * $limit;

    $sql = "SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id";
    if (!empty($keyword)) {
        $sql .= " WHERE p.name LIKE '%" . $mysqli->real_escape_string($keyword) . "%'";
    }
    $sql .= " LIMIT $limit OFFSET $offset";
    $result = $mysqli->query($sql);
    $html = '';
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $html .= "<tr data-id='{$row['id']}'>";
            $html .= "<td>{$row['id']}</td>";
            $html .= "<td>" . htmlspecialchars($row['name']) . "</td>";
            $html .= "<td>" . htmlspecialchars($row['description']) . "</td>";
            $html .= "<td>$" . number_format($row['price'], 2) . "</td>";
            $html .= "<td>" . htmlspecialchars($row['category_name']) . "</td>";
            $html .= "<td><img src='/Web/images/product_img/" . htmlspecialchars($row['image']) . "' alt='" . htmlspecialchars($row['name']) . "' class='rounded shadow-sm' style='max-width: 50px;'></td>";
            $html .= "<td class='d-flex gap-2 align-items-center'>";
            $html .= "<button class='btn btn-sm btn-edit rounded-pill glass-btn' onclick='openProductModal(\"update\", {$row['id']})'><i class='bi bi-pencil'></i></button>";
            $html .= "<button class='btn btn-sm btn-delete rounded-pill glass-btn' onclick='openDeleteModal({$row['id']})'><i class='bi bi-trash'></i></button>";
            $html .= "</td></tr>";
        }
    } else {
        $html = '<tr><td colspan="7" class="text-center">No products found.</td></tr>';
    }
    echo $html;
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request - Action: ' . $action]);
}
exit();
?>