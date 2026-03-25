<?php
session_start();
require_once '../dangky_dangnhap/config.php';
require_once '../includes/store_helpers.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Yêu cầu không hợp lệ.',
    ]);
    exit;
}

if (empty($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Bạn cần đăng nhập để thao tác với đơn hàng.',
    ]);
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true);
if (!is_array($payload)) {
    $payload = [];
}

$orderId = (int) ($payload['id'] ?? $_POST['id'] ?? $_POST['order_id'] ?? 0);
if ($orderId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Không tìm thấy đơn hàng cần hủy.',
    ]);
    exit;
}

try {
    change_order_status_with_inventory(
        $conn,
        $orderId,
        'da_huy',
        (int) $_SESSION['user_id'],
        ['cho_xac_nhan', 'dang_xu_ly']
    );

    echo json_encode([
        'success' => true,
        'message' => 'Đơn hàng đã được hủy và số lượng sản phẩm đã được hoàn lại kho.',
    ]);
} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}
?>
