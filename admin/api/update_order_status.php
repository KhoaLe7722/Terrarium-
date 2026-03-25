<?php
require_once '../admin_check.php';
require_once '../../includes/store_helpers.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ.']);
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true);
if (!is_array($payload)) {
    $payload = [];
}

$id = (int) ($payload['id'] ?? $_POST['id'] ?? $_POST['order_id'] ?? 0);
$status = trim((string) ($payload['status'] ?? $_POST['status'] ?? ''));

if ($id <= 0 || $status === '') {
    echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu.']);
    exit;
}

try {
    $result = change_order_status_with_inventory($conn, $id, $status);
    echo json_encode([
        'success' => true,
        'changed' => (bool) ($result['changed'] ?? true),
    ]);
} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}
?>
