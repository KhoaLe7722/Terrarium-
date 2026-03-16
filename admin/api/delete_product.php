<?php
require_once '../admin_check.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? 0;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Missing ID']);
    exit;
}

try {
    // Check if product exists in orders
    $stmt = $conn->prepare("SELECT COUNT(*) FROM order_items WHERE product_id = ?");
    $stmt->execute([$id]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'message' => 'Không thể xóa sản phẩm đã có trong đơn hàng. Vui lòng chuyển sang "Hết hàng".']);
        exit;
    }

    // Delete product images first (though ON DELETE CASCADE should handle it, good for files)
    $stmt = $conn->prepare("SELECT duong_dan FROM product_images WHERE product_id = ?");
    $stmt->execute([$id]);
    $images = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($images as $img) {
        $path = "../../" . ltrim($img, './');
        if (file_exists($path)) {
            unlink($path);
        }
    }

    // Get main image
    $stmt = $conn->prepare("SELECT hinh_chinh FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $mainImg = $stmt->fetchColumn();
    if ($mainImg) {
        $path = "../../" . ltrim($mainImg, './');
        if (file_exists($path)) {
            unlink($path);
        }
    }

    // Delete from DB
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()]);
}
