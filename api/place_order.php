<?php
session_start();
require_once '../dangky_dangnhap/config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ.']);
    exit;
}

if (empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập trước khi đặt hàng.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$cart = $data['cart'] ?? [];

if (!is_array($cart) || $cart === []) {
    echo json_encode(['success' => false, 'message' => 'Giỏ hàng đang trống.']);
    exit;
}

$userId = (int) $_SESSION['user_id'];
$hoTen = trim($data['ho_ten_kh'] ?? '');
$email = trim($data['email_kh'] ?? ($_SESSION['user_email'] ?? ''));
$sdt = trim($data['sdt_kh'] ?? '');
$diaChi = trim($data['dia_chi_giao'] ?? '');
$ghiChu = trim($data['ghi_chu'] ?? '');

if ($hoTen === '' || $diaChi === '') {
    echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ họ tên và địa chỉ giao hàng.']);
    exit;
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email giao hàng không hợp lệ.']);
    exit;
}

try {
    $conn->beginTransaction();

    $productStmt = $conn->prepare("
        SELECT id, ten_sp, gia, tinh_trang
        FROM products
        WHERE id = ?
        LIMIT 1
    ");

    $orderItems = [];
    $tongTien = 0;

    foreach ($cart as $item) {
        $productId = (int) ($item['id'] ?? 0);
        $quantity = max(1, (int) ($item['quantity'] ?? 0));

        if ($productId <= 0) {
            throw new RuntimeException('Sản phẩm trong giỏ hàng không hợp lệ.');
        }

        $productStmt->execute([$productId]);
        $product = $productStmt->fetch();

        if (!$product || $product['tinh_trang'] !== 'con_hang') {
            throw new RuntimeException('Một hoặc nhiều sản phẩm đã hết hàng hoặc không còn tồn tại.');
        }

        $subTotal = (float) $product['gia'] * $quantity;
        $tongTien += $subTotal;

        $orderItems[] = [
            'product_id' => $productId,
            'ten_sp' => $product['ten_sp'],
            'gia' => $product['gia'],
            'so_luong' => $quantity,
            'thanh_tien' => $subTotal,
        ];
    }

    $orderStmt = $conn->prepare("
        INSERT INTO orders (user_id, ho_ten_kh, email_kh, sdt_kh, dia_chi_giao, ghi_chu, tong_tien, trang_thai)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'cho_xac_nhan')
    ");
    $orderStmt->execute([$userId, $hoTen, $email, $sdt, $diaChi, $ghiChu, $tongTien]);
    $orderId = (int) $conn->lastInsertId();

    $itemStmt = $conn->prepare("
        INSERT INTO order_items (order_id, product_id, ten_sp, gia, so_luong, thanh_tien)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    foreach ($orderItems as $item) {
        $itemStmt->execute([
            $orderId,
            $item['product_id'],
            $item['ten_sp'],
            $item['gia'],
            $item['so_luong'],
            $item['thanh_tien'],
        ]);
    }

    $updateUserStmt = $conn->prepare("
        UPDATE users
        SET ho_ten = ?, so_dien_thoai = ?, dia_chi = ?
        WHERE id = ?
    ");
    $updateUserStmt->execute([$hoTen, $sdt !== '' ? $sdt : null, $diaChi, $userId]);

    $_SESSION['user_name'] = $hoTen;

    $conn->commit();

    echo json_encode([
        'success' => true,
        'order_id' => $orderId,
        'tong_tien' => $tongTien,
    ]);
} catch (Throwable $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}
