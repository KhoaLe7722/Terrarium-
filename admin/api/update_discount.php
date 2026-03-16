<?php
require_once '../admin_check.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? 0;
$percent = (int)($data['percent'] ?? 0);
$start = !empty($data['start']) ? $data['start'] : null;
$end = !empty($data['end']) ? $data['end'] : null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Lỗi ID sản phẩm']);
    exit;
}

if ($percent < 0 || $percent > 100) {
    echo json_encode(['success' => false, 'message' => 'Phần trăm giảm giá không hợp lệ']);
    exit;
}

try {
    // Nếu percent = 0, xóa thời gian luôn
    if ($percent == 0) {
        $start = null;
        $end = null;
    }

    $stmt = $conn->prepare("
        UPDATE products 
        SET giam_gia_phan_tram = ?, giam_gia_bat_dau = ?, giam_gia_ket_thuc = ?
        WHERE id = ?
    ");
    
    $stmt->execute([$percent, $start, $end, $id]);
    
    // Tính lại giá bán (gia_goc * (1 - percent/100))
    // Nếu có gia_goc thì dùng gia_goc, không thì dùng gia cũ làm gia_goc
    $stmt = $conn->prepare("SELECT gia, gia_goc FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $p = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$p['gia_goc'] && $percent > 0) {
        // gán giá hiện tại làm giá gốc
        $stmt = $conn->prepare("UPDATE products SET gia_goc = ? WHERE id = ?");
        $stmt->execute([$p['gia'], $id]);
        $goc = $p['gia'];
    } else {
        $goc = $p['gia_goc'] ?: $p['gia'];
    }
    
    // Cập nhật giá bán mới
    $newPrice = $goc * (1 - $percent / 100);
    $stmt = $conn->prepare("UPDATE products SET gia = ? WHERE id = ?");
    $stmt->execute([$newPrice, $id]);

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi DB: ' . $e->getMessage()]);
}
