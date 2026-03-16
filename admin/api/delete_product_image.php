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
    // Get image path before deleting
    $stmt = $conn->prepare("SELECT duong_dan FROM product_images WHERE id = ?");
    $stmt->execute([$id]);
    $img = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($img) {
        $path = "../../" . ltrim($img['duong_dan'], './');
        if (file_exists($path)) {
            unlink($path);
        }

        // Delete from DB
        $stmt = $conn->prepare("DELETE FROM product_images WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ảnh không tồn tại']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()]);
}
