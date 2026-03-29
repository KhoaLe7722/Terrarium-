<?php

if (!function_exists('format_currency_vnd')) {
    function format_currency_vnd(float|int|string $amount): string
    {
        return number_format((float) $amount, 0, ',', '.') . 'đ';
    }
}

if (!function_exists('public_asset_path')) {
    function public_asset_path(?string $path, string $fallback = 'images/avatar.png'): string
    {
        $path = ltrim(str_replace('\\', '/', trim((string) $path)), './');
        $fallback = ltrim(str_replace('\\', '/', $fallback), './');

        if ($path !== '') {
            $absolutePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
            if (is_file($absolutePath)) {
                return $path;
            }
        }

        return $fallback;
    }
}

if (!function_exists('normalize_public_path')) {
    function normalize_public_path(?string $path, string $prefix = '../'): string
    {
        return $prefix . public_asset_path($path);
    }
}

if (!function_exists('order_status_label')) {
    function order_status_label(string $status): string
    {
        $labels = [
            'cho_xac_nhan' => 'Chờ xác nhận',
            'dang_xu_ly' => 'Đang xử lý',
            'dang_giao' => 'Đang giao',
            'da_giao' => 'Đã giao',
            'da_huy' => 'Đã hủy',
        ];

        return $labels[$status] ?? $status;
    }
}

if (!function_exists('payment_method_label')) {
    function payment_method_label(?string $method): string
    {
        $labels = [
            'cod' => 'Thanh toán khi nhận hàng (COD)',
            'bank' => 'Chuyển khoản ngân hàng',
            'momo' => 'Thanh toán qua MoMo',
        ];

        $method = trim((string) $method);
        if ($method === '') {
            return 'Chưa xác định';
        }

        return $labels[$method] ?? strtoupper($method);
    }
}

if (!function_exists('order_can_customer_cancel')) {
    function order_can_customer_cancel(string $status): bool
    {
        return in_array($status, ['cho_xac_nhan', 'dang_xu_ly'], true);
    }
}

if (!function_exists('store_shipping_fee')) {
    function store_shipping_fee(): int
    {
        return 50000;
    }
}

if (!function_exists('store_free_shipping_threshold')) {
    function store_free_shipping_threshold(): int
    {
        return 500000;
    }
}

if (!function_exists('store_calculate_shipping_fee')) {
    function store_calculate_shipping_fee(float|int|string $subtotal): int
    {
        $normalizedSubtotal = max(0, (float) $subtotal);

        if ($normalizedSubtotal <= 0) {
            return 0;
        }

        return $normalizedSubtotal >= store_free_shipping_threshold()
            ? 0
            : store_shipping_fee();
    }
}

if (!function_exists('store_calculate_order_total')) {
    function store_calculate_order_total(float|int|string $subtotal): float
    {
        $normalizedSubtotal = max(0, (float) $subtotal);
        return $normalizedSubtotal + store_calculate_shipping_fee($normalizedSubtotal);
    }
}

if (!function_exists('change_order_status_with_inventory')) {
    function change_order_status_with_inventory(
        PDO $conn,
        int $orderId,
        string $nextStatus,
        ?int $expectedUserId = null,
        ?array $allowedCurrentStatuses = null
    ): array {
        $validStatuses = ['cho_xac_nhan', 'dang_xu_ly', 'dang_giao', 'da_giao', 'da_huy'];
        if ($orderId <= 0 || !in_array($nextStatus, $validStatuses, true)) {
            throw new RuntimeException('Trạng thái đơn hàng không hợp lệ.');
        }

        $normalizedAllowedStatuses = $allowedCurrentStatuses === null
            ? null
            : array_values(array_unique(array_map('strval', $allowedCurrentStatuses)));

        try {
            $conn->beginTransaction();

            $orderStmt = $conn->prepare('SELECT id, user_id, trang_thai FROM orders WHERE id = ? FOR UPDATE');
            $orderStmt->execute([$orderId]);
            $order = $orderStmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                throw new RuntimeException('Không tìm thấy đơn hàng.');
            }

            if ($expectedUserId !== null && (int) ($order['user_id'] ?? 0) !== $expectedUserId) {
                throw new RuntimeException('Bạn không có quyền thao tác với đơn hàng này.');
            }

            $currentStatus = (string) ($order['trang_thai'] ?? '');
            if ($normalizedAllowedStatuses !== null && !in_array($currentStatus, $normalizedAllowedStatuses, true)) {
                throw new RuntimeException('Đơn hàng này không thể cập nhật ở trạng thái hiện tại.');
            }

            if ($currentStatus === $nextStatus) {
                $conn->commit();
                return [
                    'order_id' => $orderId,
                    'old_status' => $currentStatus,
                    'new_status' => $nextStatus,
                    'changed' => false,
                ];
            }

            $itemsStmt = $conn->prepare('SELECT product_id, ten_sp, so_luong FROM order_items WHERE order_id = ?');
            $itemsStmt->execute([$orderId]);
            $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

            if ($currentStatus !== 'da_huy' && $nextStatus === 'da_huy') {
                $restoreStmt = $conn->prepare("
                    UPDATE products
                    SET so_luong_ton = so_luong_ton + ?,
                        tinh_trang = CASE
                            WHEN so_luong_ton + ? > 0 THEN 'con_hang'
                            ELSE 'het_hang'
                        END
                    WHERE id = ?
                ");

                foreach ($items as $item) {
                    $quantity = max(0, (int) ($item['so_luong'] ?? 0));
                    if ($quantity <= 0) {
                        continue;
                    }

                    $restoreStmt->execute([
                        $quantity,
                        $quantity,
                        (int) $item['product_id'],
                    ]);
                }
            } elseif ($currentStatus === 'da_huy' && $nextStatus !== 'da_huy') {
                $productIds = array_values(array_unique(array_map(
                    static fn(array $item): int => (int) $item['product_id'],
                    $items
                )));

                if (!empty($productIds)) {
                    $placeholders = implode(', ', array_fill(0, count($productIds), '?'));
                    $productStmt = $conn->prepare("
                        SELECT id, ten_sp, so_luong_ton
                        FROM products
                        WHERE id IN ($placeholders)
                        FOR UPDATE
                    ");
                    $productStmt->execute($productIds);

                    $productMap = [];
                    foreach ($productStmt->fetchAll(PDO::FETCH_ASSOC) as $product) {
                        $productMap[(int) $product['id']] = $product;
                    }

                    foreach ($items as $item) {
                        $productId = (int) $item['product_id'];
                        $requested = max(0, (int) ($item['so_luong'] ?? 0));
                        $product = $productMap[$productId] ?? null;
                        $available = $product ? inventory_quantity($product) : 0;

                        if (!$product || $available < $requested) {
                            $productName = $product['ten_sp'] ?? $item['ten_sp'] ?? ('Sản phẩm #' . $productId);
                            throw new RuntimeException('Không đủ tồn kho để cập nhật lại đơn hàng: ' . $productName);
                        }
                    }

                    $deductStmt = $conn->prepare("
                        UPDATE products
                        SET so_luong_ton = GREATEST(so_luong_ton - ?, 0),
                            tinh_trang = CASE
                                WHEN so_luong_ton - ? <= 0 THEN 'het_hang'
                                ELSE 'con_hang'
                            END
                        WHERE id = ?
                    ");

                    foreach ($items as $item) {
                        $quantity = max(0, (int) ($item['so_luong'] ?? 0));
                        if ($quantity <= 0) {
                            continue;
                        }

                        $deductStmt->execute([
                            $quantity,
                            $quantity,
                            (int) $item['product_id'],
                        ]);
                    }
                }
            }

            $updateStmt = $conn->prepare('UPDATE orders SET trang_thai = ? WHERE id = ?');
            $updateStmt->execute([$nextStatus, $orderId]);

            $conn->commit();

            return [
                'order_id' => $orderId,
                'old_status' => $currentStatus,
                'new_status' => $nextStatus,
                'changed' => true,
            ];
        } catch (Throwable $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }

            throw $e;
        }
    }
}

if (!function_exists('current_user')) {
    function current_user(PDO $conn): ?array
    {
        if (empty($_SESSION['user_id'])) {
            return null;
        }

        $stmt = $conn->prepare("
            SELECT id, ho_ten, email, anh_dai_dien, so_dien_thoai, dia_chi, vai_tro, ngay_tao
            FROM users
            WHERE id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);

        return $stmt->fetch() ?: null;
    }
}

if (!function_exists('load_latest_products')) {
    function load_latest_products(PDO $conn, int $limit = 8, ?int $excludeId = null): array
    {
        $sql = "
            SELECT id, ten_sp, gia, gia_goc, giam_gia_phan_tram, hinh_chinh, tinh_trang, so_luong_ton
            FROM products
            WHERE so_luong_ton > 0
        ";

        $params = [];
        if ($excludeId !== null) {
            $sql .= " AND id <> ?";
            $params[] = $excludeId;
        }

        $sql .= " ORDER BY id DESC LIMIT " . max(1, $limit);

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }
}

if (!function_exists('inventory_quantity')) {
    function inventory_quantity(array $product): int
    {
        return max(0, (int) ($product['so_luong_ton'] ?? 0));
    }
}

if (!function_exists('inventory_status_from_quantity')) {
    function inventory_status_from_quantity(int $quantity): string
    {
        return $quantity > 0 ? 'con_hang' : 'het_hang';
    }
}

if (!function_exists('product_is_in_stock')) {
    function product_is_in_stock(array $product): bool
    {
        return inventory_quantity($product) > 0;
    }
}

if (!function_exists('product_maintenance_meta')) {
    function product_maintenance_meta(array $product, int $warningDays = 7): array
    {
        $rawDate = trim((string) ($product['ngay_bao_tri_gan_nhat'] ?? ''));
        if ($rawDate === '') {
            return [
                'has_date' => false,
                'last_date' => null,
                'next_date' => null,
                'days_left' => null,
                'status' => 'missing',
            ];
        }

        try {
            $lastDate = new DateTimeImmutable($rawDate);
        } catch (Throwable $e) {
            return [
                'has_date' => false,
                'last_date' => null,
                'next_date' => null,
                'days_left' => null,
                'status' => 'invalid',
            ];
        }

        $today = new DateTimeImmutable(date('Y-m-d'));
        $nextDate = $lastDate->add(new DateInterval('P2M'));
        $daysLeft = (int) $today->diff($nextDate)->format('%r%a');

        $status = 'ok';
        if ($daysLeft < 0) {
            $status = 'overdue';
        } elseif ($daysLeft <= $warningDays) {
            $status = 'soon';
        }

        return [
            'has_date' => true,
            'last_date' => $lastDate,
            'next_date' => $nextDate,
            'days_left' => $daysLeft,
            'status' => $status,
        ];
    }
}

if (!function_exists('get_product_pricing')) {
    function get_product_pricing(array $product): array
    {
        $price = (float) ($product['gia'] ?? 0);
        $originalPrice = (float) ($product['gia_goc'] ?? 0);
        $discountPercent = (int) ($product['giam_gia_phan_tram'] ?? 0);

        if ($originalPrice <= 0) {
            $originalPrice = $price;
        }

        if ($originalPrice < $price) {
            $originalPrice = $price;
        }

        if ($discountPercent <= 0 && $originalPrice > $price) {
            $discountPercent = (int) round((($originalPrice - $price) / $originalPrice) * 100);
        }

        $isSale = $originalPrice > $price && $discountPercent > 0;

        if (!$isSale) {
            $originalPrice = $price;
            $discountPercent = 0;
        }

        return [
            'price' => $price,
            'original_price' => $originalPrice,
            'discount_percent' => $discountPercent,
            'is_sale' => $isSale,
        ];
    }
}
if (!function_exists('load_product_gallery')) {
    function load_product_gallery(PDO $conn, int $productId): array
    {
        $stmt = $conn->prepare("
            SELECT id, duong_dan, thu_tu
            FROM product_images
            WHERE product_id = ?
            ORDER BY thu_tu ASC, id ASC
        ");
        $stmt->execute([$productId]);

        return $stmt->fetchAll();
    }
}


