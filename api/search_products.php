<?php
require_once '../dangky_dangnhap/config.php';
require_once '../includes/store_helpers.php';

header('Content-Type: application/json; charset=utf-8');

$search = trim((string) ($_POST['search'] ?? $_GET['search'] ?? ''));
$searchLength = function_exists('mb_strlen') ? mb_strlen($search) : strlen($search);

if ($searchLength < 2) {
    echo json_encode([
        'success' => true,
        'query' => $search,
        'totalItems' => 0,
        'items' => [],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$basePath = str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '')));
if ($basePath === '/' || $basePath === '\\') {
    $basePath = '';
}
$basePath = trim($basePath, '/');

$makePath = static function (string $path) use ($basePath): string {
    $normalized = ltrim(str_replace('\\', '/', $path), '/');
    return '/' . ltrim(($basePath !== '' ? $basePath . '/' : '') . $normalized, '/');
};

$stmt = $conn->prepare("
    SELECT id, ten_sp, gia, gia_goc, giam_gia_phan_tram, hinh_chinh, mo_ta, so_luong_ton
    FROM products
    WHERE ten_sp LIKE :search
       OR mo_ta LIKE :search
    ORDER BY
        (so_luong_ton > 0) DESC,
        CASE WHEN ten_sp LIKE :prefix THEN 0 ELSE 1 END,
        id DESC
    LIMIT 6
");
$stmt->execute([
    'search' => '%' . $search . '%',
    'prefix' => $search . '%',
]);

$items = [];
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $product) {
    $pricing = get_product_pricing($product);
    $stock = inventory_quantity($product);

    $items[] = [
        'id' => (int) $product['id'],
        'name' => (string) $product['ten_sp'],
        'price' => (float) $pricing['price'],
        'priceText' => format_currency_vnd($pricing['price']),
        'originalPriceText' => $pricing['is_sale'] ? format_currency_vnd($pricing['original_price']) : null,
        'image' => $makePath(public_asset_path($product['hinh_chinh'])),
        'link' => $makePath('sanpham/spchitiet.php?id=' . (int) $product['id']),
        'stock' => $stock,
        'stockText' => $stock > 0 ? 'Còn ' . $stock . ' sản phẩm' : 'Tạm hết hàng',
        'inStock' => $stock > 0,
        'isSale' => (bool) $pricing['is_sale'],
        'discountPercent' => (int) $pricing['discount_percent'],
    ];
}

echo json_encode([
    'success' => true,
    'query' => $search,
    'totalItems' => count($items),
    'items' => $items,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
