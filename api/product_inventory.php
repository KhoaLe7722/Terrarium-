<?php
require_once '../dangky_dangnhap/config.php';
require_once '../includes/store_helpers.php';

header('Content-Type: application/json; charset=utf-8');

$payload = json_decode(file_get_contents('php://input'), true);
if (!is_array($payload)) {
    $payload = [];
}

$rawIds = $payload['ids'] ?? ($_GET['ids'] ?? []);
if (is_string($rawIds)) {
    $rawIds = array_filter(array_map('trim', explode(',', $rawIds)));
}

if (!is_array($rawIds)) {
    $rawIds = [];
}

$ids = [];
foreach ($rawIds as $rawId) {
    $id = (int) $rawId;
    if ($id > 0) {
        $ids[$id] = $id;
    }
}
$ids = array_values($ids);

if (empty($ids)) {
    echo json_encode([
        'success' => true,
        'products' => new stdClass(),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$placeholders = implode(', ', array_fill(0, count($ids), '?'));
$stmt = $conn->prepare("
    SELECT id, ten_sp, gia, hinh_chinh, tinh_trang, so_luong_ton
    FROM products
    WHERE id IN ($placeholders)
");
$stmt->execute($ids);

$products = [];
foreach ($stmt->fetchAll() as $product) {
    $stock = inventory_quantity($product);
    $products[(string) $product['id']] = [
        'id' => (int) $product['id'],
        'name' => (string) $product['ten_sp'],
        'price' => (float) $product['gia'],
        'image' => public_asset_path($product['hinh_chinh']),
        'stock' => $stock,
        'status' => inventory_status_from_quantity($stock),
        'in_stock' => $stock > 0,
    ];
}

echo json_encode([
    'success' => true,
    'products' => $products,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
