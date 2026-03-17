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

if (!function_exists('current_user')) {
    function current_user(PDO $conn): ?array
    {
        if (empty($_SESSION['user_id'])) {
            return null;
        }

        $stmt = $conn->prepare("
            SELECT id, ho_ten, email, so_dien_thoai, dia_chi, vai_tro, ngay_tao
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
            SELECT id, ten_sp, gia, gia_goc, giam_gia_phan_tram, hinh_chinh, tinh_trang
            FROM products
            WHERE tinh_trang = 'con_hang'
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
