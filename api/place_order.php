<?php
session_start();
require_once '../dangky_dangnhap/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['cart']) || empty($data['cart'])) {
    echo json_encode(['success' => false, 'message' => 'Giỏ hàng trống']);
    exit;
}

$userId = $_SESSION['user_id'] ?? null;
$hoTen = $data['ho_ten_kh'] ?? '';
$email = $data['email_kh'] ?? '';
$sdt = $data['sdt_kh'] ?? '';
$diaChi = $data['dia_chi_giao'] ?? '';
$ghiChu = $data['ghi_chu'] ?? '';
$tongTien = $data['tong_tien'] ?? 0;

if (empty($hoTen) || empty($diaChi)) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ họ tên và địa chỉ giao hàng']);
    exit;
}

try {
    $conn->beginTransaction();

    // 1. Lưu vào bảng orders
    $stmt = $conn->prepare("
        INSERT INTO orders (user_id, ho_ten_kh, email_kh, sdt_kh, dia_chi_giao, ghi_chu, tong_tien, trang_thai)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'cho_xac_nhan')
    ");
    $stmt->execute([$userId, $hoTen, $email, $sdt, $diaChi, $ghiChu, $tongTien]);
    $orderId = $conn->lastInsertId();

    // 2. Lưu vào bảng order_items
    $itemStmt = $conn->prepare("
        INSERT INTO order_items (order_id, product_id, ten_sp, gia, so_luong, thanh_tien)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    foreach ($data['cart'] as $item) {
        $productId = $item['id'];
        $name = $item['name'];
        $price = $item['price'];
        $qty = $item['quantity'];
        $subtotal = $price * $qty;

        $itemStmt->execute([$orderId, $productId, $name, $price, $qty, $subtotal]);
    }

    $conn->commit();
    echo json_encode(['success' => true, 'order_id' => $orderId]);

} catch (PDOException $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
