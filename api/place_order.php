<?php
session_start();
require_once '../dangky_dangnhap/config.php';
require_once '../includes/store_helpers.php';

function u(string $value): string
{
    return json_decode('"' . $value . '"', true);
}

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => u('Y\u00eau c\u1ea7u kh\u00f4ng h\u1ee3p l\u1ec7.')]);
    exit;
}

if (empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => u('B\u1ea1n c\u1ea7n \u0111\u0103ng nh\u1eadp tr\u01b0\u1edbc khi \u0111\u1eb7t h\u00e0ng.')]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$cart = $data['cart'] ?? [];

if (!is_array($cart) || $cart === []) {
    echo json_encode(['success' => false, 'message' => u('Gi\u1ecf h\u00e0ng \u0111ang tr\u1ed1ng.')]);
    exit;
}

$userId = (int) $_SESSION['user_id'];
$hoTen = trim((string) ($data['ho_ten_kh'] ?? ''));
$email = trim((string) ($data['email_kh'] ?? ($_SESSION['user_email'] ?? '')));
$sdt = trim((string) ($data['sdt_kh'] ?? ''));
$diaChi = trim((string) ($data['dia_chi_giao'] ?? ''));
$ghiChu = trim((string) ($data['ghi_chu'] ?? ''));
$phuongThucTT = trim((string) ($data['phuong_thuc_tt'] ?? 'cod'));

if ($hoTen === '' || $diaChi === '' || $sdt === '') {
    echo json_encode([
        'success' => false,
        'message' => u('Vui l\u00f2ng nh\u1eadp \u0111\u1ea7y \u0111\u1ee7 h\u1ecd t\u00ean, s\u1ed1 \u0111i\u1ec7n tho\u1ea1i v\u00e0 \u0111\u1ecba ch\u1ec9 giao h\u00e0ng.'),
    ]);
    exit;
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => u('Email giao h\u00e0ng kh\u00f4ng h\u1ee3p l\u1ec7.')]);
    exit;
}

$normalizedCart = [];
foreach ($cart as $item) {
    $productId = (int) ($item['id'] ?? 0);
    $quantity = max(1, (int) ($item['quantity'] ?? 0));

    if ($productId <= 0) {
        echo json_encode(['success' => false, 'message' => u('S\u1ea3n ph\u1ea9m trong gi\u1ecf h\u00e0ng kh\u00f4ng h\u1ee3p l\u1ec7.')]);
        exit;
    }

    if (!isset($normalizedCart[$productId])) {
        $normalizedCart[$productId] = 0;
    }
    $normalizedCart[$productId] += $quantity;
}

try {
    $conn->beginTransaction();

    $productIds = array_keys($normalizedCart);
    $placeholders = implode(', ', array_fill(0, count($productIds), '?'));
    $productStmt = $conn->prepare("
        SELECT id, ten_sp, gia, hinh_chinh, so_luong_ton
        FROM products
        WHERE id IN ($placeholders)
        FOR UPDATE
    ");
    $productStmt->execute($productIds);

    $productMap = [];
    foreach ($productStmt->fetchAll() as $product) {
        $productMap[(int) $product['id']] = $product;
    }

    $orderItems = [];
    $stockIssues = [];
    $tongTien = 0;

    foreach ($normalizedCart as $productId => $quantity) {
        $product = $productMap[$productId] ?? null;
        $available = $product ? inventory_quantity($product) : 0;

        if (!$product || $available <= 0 || $quantity > $available) {
            $stockIssues[] = [
                'product_id' => $productId,
                'name' => (string) ($product['ten_sp'] ?? (u('S\u1ea3n ph\u1ea9m #') . $productId)),
                'requested' => $quantity,
                'available' => $available,
            ];
            continue;
        }

        $subTotal = (float) $product['gia'] * $quantity;
        $tongTien += $subTotal;

        $orderItems[] = [
            'product_id' => $productId,
            'ten_sp' => (string) $product['ten_sp'],
            'gia' => (float) $product['gia'],
            'so_luong' => $quantity,
            'thanh_tien' => $subTotal,
        ];
    }

    if (!empty($stockIssues)) {
        $conn->rollBack();
        echo json_encode([
            'success' => false,
            'code' => 'INSUFFICIENT_STOCK',
            'message' => u('T\u1ed3n kho v\u1eeba thay \u0111\u1ed5i. Gi\u1ecf h\u00e0ng \u0111\u00e3 v\u01b0\u1ee3t qu\u00e1 s\u1ed1 l\u01b0\u1ee3ng c\u00f2n l\u1ea1i.'),
            'stock_issues' => $stockIssues,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    $orderStmt = $conn->prepare("
        INSERT INTO orders (user_id, ho_ten_kh, email_kh, sdt_kh, dia_chi_giao, ghi_chu, tong_tien, phuong_thuc_tt, trang_thai)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'cho_xac_nhan')
    ");
    $orderStmt->execute([$userId, $hoTen, $email, $sdt, $diaChi, $ghiChu, $tongTien, $phuongThucTT]);

    $orderId = (int) $conn->lastInsertId();

    $itemStmt = $conn->prepare("
        INSERT INTO order_items (order_id, product_id, ten_sp, gia, so_luong, thanh_tien)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stockStmt = $conn->prepare("
        UPDATE products
        SET so_luong_ton = GREATEST(so_luong_ton - ?, 0),
            tinh_trang = CASE
                WHEN so_luong_ton - ? <= 0 THEN 'het_hang'
                ELSE 'con_hang'
            END
        WHERE id = ?
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

        $stockStmt->execute([
            $item['so_luong'],
            $item['so_luong'],
            $item['product_id'],
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
?>