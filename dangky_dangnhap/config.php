<?php
if (!function_exists('env_value')) {
    function env_value(string $name, ?string $default = null, bool $allowEmpty = false): ?string
    {
        $value = getenv($name);

        if ($value === false) {
            return $default;
        }

        if (!$allowEmpty && $value === '') {
            return $default;
        }

        return $value;
    }
}

// Ket noi MySQL
$host = env_value('DB_HOST', 'localhost');
$port = env_value('DB_PORT', '3306');
$dbname = env_value('DB_NAME', 'terrarium_db');
$username = env_value('DB_USERNAME', 'root');
$password = env_value('DB_PASSWORD', '', true) ?? ''; // Mac dinh XAMPP khong co mat khau
$rootUsername = env_value('DB_ROOT_USERNAME', $username);
$rootPassword = env_value('DB_ROOT_PASSWORD', $password, true) ?? '';

$serverDsn = "mysql:host=$host;port=$port;charset=utf8mb4";
$databaseDsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

try {
    try {
        $conn = new PDO($databaseDsn, $username, $password);
    } catch (PDOException $databaseException) {
        $errorInfo = $databaseException->errorInfo;
        $mysqlErrorCode = is_array($errorInfo) ? (int) ($errorInfo[1] ?? 0) : 0;
        $message = strtolower($databaseException->getMessage());
        $isMissingDatabase = $mysqlErrorCode === 1049 || str_contains($message, 'unknown database');

        if (!$isMissingDatabase) {
            throw $databaseException;
        }

        $serverConn = new PDO($serverDsn, $rootUsername, $rootPassword);
        $serverConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $serverConn->exec("
            CREATE DATABASE IF NOT EXISTS `$dbname`
            CHARACTER SET utf8mb4
            COLLATE utf8mb4_unicode_ci
        ");

        $conn = new PDO($databaseDsn, $username, $password);
    }

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Ket noi that bai: " . $e->getMessage());
}

if (!function_exists('db_table_exists')) {
    function db_table_exists(PDO $conn, string $table): bool
    {
        $stmt = $conn->prepare("
            SELECT COUNT(*)
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?
        ");
        $stmt->execute([$table]);

        return (int) $stmt->fetchColumn() > 0;
    }
}

if (!function_exists('db_column_exists')) {
    function db_column_exists(PDO $conn, string $table, string $column): bool
    {
        if (!db_table_exists($conn, $table)) {
            return false;
        }

        $stmt = $conn->prepare("
            SELECT COUNT(*)
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = ?
              AND COLUMN_NAME = ?
        ");
        $stmt->execute([$table, $column]);

        return (int) $stmt->fetchColumn() > 0;
    }
}

if (!function_exists('safe_exec')) {
    function safe_exec(PDO $conn, string $sql): void
    {
        try {
            $conn->exec($sql);
        } catch (PDOException $e) {
            // Bootstrap schema should not break the request for already-existing columns/indexes.
        }
    }
}

if (!function_exists('normalize_auth_redirect_key')) {
    function normalize_auth_redirect_key(?string $key): string
    {
        $allowedKeys = ['checkout', 'home', 'profile'];
        return in_array($key, $allowedKeys, true) ? $key : 'profile';
    }
}

if (!function_exists('resolve_auth_redirect_target')) {
    function resolve_auth_redirect_target(?string $key): string
    {
        $routes = [
            'checkout' => '../thanhtoan/thanhtoan.php',
            'home' => '../trangchu/index.php',
            'profile' => 'ho_so.php',
        ];

        $normalizedKey = normalize_auth_redirect_key($key);
        return $routes[$normalizedKey] ?? 'ho_so.php';
    }
}

if (!function_exists('build_auth_page_url')) {
    function build_auth_page_url(string $path, ?string $redirectKey): string
    {
        $normalizedKey = normalize_auth_redirect_key($redirectKey);
        return $path . '?redirect=' . rawurlencode($normalizedKey);
    }
}

if (!function_exists('normalize_user_email')) {
    function normalize_user_email(string $email): string
    {
        return strtolower(trim($email));
    }
}

if (!function_exists('is_password_hash_value')) {
    function is_password_hash_value(string $value): bool
    {
        $info = password_get_info($value);
        return !empty($info['algo']);
    }
}

if (!function_exists('persist_user_password_hash')) {
    function persist_user_password_hash(PDO $conn, int $userId, string $hash): void
    {
        $columns = ['mat_khau'];

        if (db_column_exists($conn, 'users', 'password')) {
            $columns[] = 'password';
        }

        $setClauses = [];
        $params = [];
        foreach ($columns as $column) {
            $setClauses[] = $column . ' = ?';
            $params[] = $hash;
        }

        $params[] = $userId;
        $sql = "UPDATE users SET " . implode(', ', $setClauses) . " WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
    }
}

if (!function_exists('verify_and_upgrade_user_password')) {
    function verify_and_upgrade_user_password(PDO $conn, array $user, string $plainPassword): bool
    {
        $storedPassword = (string) ($user['mat_khau'] ?? '');
        if ($storedPassword === '') {
            return false;
        }

        $isHashedPassword = is_password_hash_value($storedPassword);
        $isValid = $isHashedPassword
            ? password_verify($plainPassword, $storedPassword)
            : hash_equals($storedPassword, $plainPassword);

        if (!$isValid) {
            return false;
        }

        if (!$isHashedPassword || password_needs_rehash($storedPassword, PASSWORD_DEFAULT)) {
            $newHash = password_hash($plainPassword, PASSWORD_DEFAULT);
            persist_user_password_hash($conn, (int) $user['id'], $newHash);
        }

        return true;
    }
}

if (!function_exists('create_user_account')) {
    function create_user_account(PDO $conn, string $name, string $email, string $passwordHash, string $role = 'khach'): bool
    {
        $columns = ['ho_ten', 'email', 'mat_khau', 'vai_tro'];
        $params = [$name, $email, $passwordHash, $role];

        if (db_column_exists($conn, 'users', 'name')) {
            $columns[] = 'name';
            $params[] = $name;
        }

        if (db_column_exists($conn, 'users', 'password')) {
            $columns[] = 'password';
            $params[] = $passwordHash;
        }

        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        $sql = 'INSERT INTO users (' . implode(', ', $columns) . ') VALUES (' . $placeholders . ')';
        $stmt = $conn->prepare($sql);

        return $stmt->execute($params);
    }
}

if (!function_exists('ensure_store_schema')) {
    function ensure_store_schema(PDO $conn): void
    {
        safe_exec($conn, "
            CREATE TABLE IF NOT EXISTS users (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                ho_ten VARCHAR(100) NOT NULL,
                email VARCHAR(150) NOT NULL UNIQUE,
                mat_khau VARCHAR(255) NOT NULL,
                anh_dai_dien VARCHAR(300) DEFAULT NULL,
                so_dien_thoai VARCHAR(20) DEFAULT NULL,
                dia_chi TEXT DEFAULT NULL,
                vai_tro ENUM('khach', 'quan_tri') NOT NULL DEFAULT 'khach',
                ngay_tao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                ngay_capnhat DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        $userColumns = [
            'ho_ten' => "ALTER TABLE users ADD COLUMN ho_ten VARCHAR(100) NOT NULL DEFAULT ''",
            'mat_khau' => "ALTER TABLE users ADD COLUMN mat_khau VARCHAR(255) NOT NULL DEFAULT ''",
            'anh_dai_dien' => "ALTER TABLE users ADD COLUMN anh_dai_dien VARCHAR(300) DEFAULT NULL",
            'so_dien_thoai' => "ALTER TABLE users ADD COLUMN so_dien_thoai VARCHAR(20) DEFAULT NULL",
            'dia_chi' => "ALTER TABLE users ADD COLUMN dia_chi TEXT DEFAULT NULL",
            'vai_tro' => "ALTER TABLE users ADD COLUMN vai_tro ENUM('khach', 'quan_tri') NOT NULL DEFAULT 'khach'",
            'ngay_tao' => "ALTER TABLE users ADD COLUMN ngay_tao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP",
            'ngay_capnhat' => "ALTER TABLE users ADD COLUMN ngay_capnhat DATETIME DEFAULT NULL"
        ];

        foreach ($userColumns as $column => $sql) {
            if (!db_column_exists($conn, 'users', $column)) {
                safe_exec($conn, $sql);
            }
        }

        if (db_column_exists($conn, 'users', 'name')) {
            safe_exec($conn, "
                UPDATE users
                SET ho_ten = COALESCE(NULLIF(ho_ten, ''), name)
                WHERE name IS NOT NULL AND name <> ''
            ");
        }

        if (db_column_exists($conn, 'users', 'password')) {
            safe_exec($conn, "
                UPDATE users
                SET mat_khau = COALESCE(NULLIF(mat_khau, ''), password)
                WHERE password IS NOT NULL AND password <> ''
            ");
        }

        if (db_column_exists($conn, 'users', 'created_at')) {
            safe_exec($conn, "
                UPDATE users
                SET ngay_tao = created_at
                WHERE created_at IS NOT NULL
            ");
        }

        safe_exec($conn, "
            CREATE TABLE IF NOT EXISTS products (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                ten_sp VARCHAR(200) NOT NULL,
                gia DECIMAL(12,0) NOT NULL,
                gia_goc DECIMAL(12,0) DEFAULT NULL,
                giam_gia_phan_tram TINYINT UNSIGNED DEFAULT 0,
                giam_gia_bat_dau DATETIME DEFAULT NULL,
                giam_gia_ket_thuc DATETIME DEFAULT NULL,
                hinh_chinh VARCHAR(300) DEFAULT NULL,
                mo_ta LONGTEXT DEFAULT NULL,
                so_luong_ton INT UNSIGNED NOT NULL DEFAULT 0,
                ngay_bao_tri_gan_nhat DATE DEFAULT NULL,
                tinh_trang ENUM('con_hang', 'het_hang') NOT NULL DEFAULT 'con_hang',
                ngay_tao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                ngay_capnhat DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        $stockColumnAdded = false;
        $productColumns = [
            'gia_goc' => "ALTER TABLE products ADD COLUMN gia_goc DECIMAL(12,0) DEFAULT NULL",
            'giam_gia_phan_tram' => "ALTER TABLE products ADD COLUMN giam_gia_phan_tram TINYINT UNSIGNED DEFAULT 0",
            'giam_gia_bat_dau' => "ALTER TABLE products ADD COLUMN giam_gia_bat_dau DATETIME DEFAULT NULL",
            'giam_gia_ket_thuc' => "ALTER TABLE products ADD COLUMN giam_gia_ket_thuc DATETIME DEFAULT NULL",
            'hinh_chinh' => "ALTER TABLE products ADD COLUMN hinh_chinh VARCHAR(300) DEFAULT NULL",
            'mo_ta' => "ALTER TABLE products ADD COLUMN mo_ta LONGTEXT DEFAULT NULL",
            'so_luong_ton' => "ALTER TABLE products ADD COLUMN so_luong_ton INT UNSIGNED NOT NULL DEFAULT 0",
            'ngay_bao_tri_gan_nhat' => "ALTER TABLE products ADD COLUMN ngay_bao_tri_gan_nhat DATE DEFAULT NULL",
            'tinh_trang' => "ALTER TABLE products ADD COLUMN tinh_trang ENUM('con_hang', 'het_hang') NOT NULL DEFAULT 'con_hang'",
            'ngay_tao' => "ALTER TABLE products ADD COLUMN ngay_tao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP",
            'ngay_capnhat' => "ALTER TABLE products ADD COLUMN ngay_capnhat DATETIME DEFAULT NULL"
        ];

        foreach ($productColumns as $column => $sql) {
            if (!db_column_exists($conn, 'products', $column)) {
                safe_exec($conn, $sql);
                if ($column === 'so_luong_ton') {
                    $stockColumnAdded = true;
                }
            }
        }

        if ($stockColumnAdded) {
            safe_exec($conn, "
                UPDATE products
                SET so_luong_ton = CASE
                    WHEN tinh_trang = 'con_hang' THEN 1
                    ELSE 0
                END
                WHERE so_luong_ton = 0
            ");
        }

        safe_exec($conn, "
            UPDATE products
            SET tinh_trang = CASE
                WHEN COALESCE(so_luong_ton, 0) > 0 THEN 'con_hang'
                ELSE 'het_hang'
            END
            WHERE (COALESCE(so_luong_ton, 0) > 0 AND tinh_trang <> 'con_hang')
               OR (COALESCE(so_luong_ton, 0) = 0 AND tinh_trang <> 'het_hang')
        ");

        safe_exec($conn, "
            CREATE TABLE IF NOT EXISTS product_images (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                product_id INT UNSIGNED NOT NULL,
                duong_dan VARCHAR(300) NOT NULL,
                thu_tu TINYINT UNSIGNED NOT NULL DEFAULT 0,
                CONSTRAINT fk_product_images_product
                    FOREIGN KEY (product_id) REFERENCES products(id)
                    ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        if (!db_column_exists($conn, 'product_images', 'thu_tu')) {
            safe_exec($conn, "ALTER TABLE product_images ADD COLUMN thu_tu TINYINT UNSIGNED NOT NULL DEFAULT 0");
        }

        safe_exec($conn, "
            CREATE TABLE IF NOT EXISTS orders (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id INT UNSIGNED DEFAULT NULL,
                ho_ten_kh VARCHAR(100) NOT NULL,
                email_kh VARCHAR(150) NOT NULL,
                sdt_kh VARCHAR(20) DEFAULT NULL,
                dia_chi_giao TEXT NOT NULL,
                ghi_chu TEXT DEFAULT NULL,
                tong_tien DECIMAL(14,0) NOT NULL DEFAULT 0,
                phuong_thuc_tt VARCHAR(20) NOT NULL DEFAULT 'cod',
                trang_thai ENUM('cho_xac_nhan', 'dang_xu_ly', 'dang_giao', 'da_giao', 'da_huy')
                    NOT NULL DEFAULT 'cho_xac_nhan',
                ngay_dat DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                ngay_capnhat DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_orders_user
                    FOREIGN KEY (user_id) REFERENCES users(id)
                    ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        if (!db_column_exists($conn, 'orders', 'phuong_thuc_tt')) {
            safe_exec($conn, "ALTER TABLE orders ADD COLUMN phuong_thuc_tt VARCHAR(20) NOT NULL DEFAULT 'cod' AFTER tong_tien");
        }

        safe_exec($conn, "
            CREATE TABLE IF NOT EXISTS order_items (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                order_id INT UNSIGNED NOT NULL,
                product_id INT UNSIGNED NOT NULL,
                ten_sp VARCHAR(200) NOT NULL,
                gia DECIMAL(12,0) NOT NULL,
                so_luong SMALLINT UNSIGNED NOT NULL DEFAULT 1,
                thanh_tien DECIMAL(14,0) NOT NULL,
                CONSTRAINT fk_order_items_order
                    FOREIGN KEY (order_id) REFERENCES orders(id)
                    ON DELETE CASCADE,
                CONSTRAINT fk_order_items_product
                    FOREIGN KEY (product_id) REFERENCES products(id)
                    ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        $adminEmail = 'admin@thuanphatgarden.vn';
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$adminEmail]);

        if ((int) $stmt->fetchColumn() === 0) {
            create_user_account(
                $conn,
                'Quan Tri Vien',
                $adminEmail,
                password_hash('Admin@123', PASSWORD_BCRYPT),
                'quan_tri'
            );
        } else {
            $stmt = $conn->prepare("
                UPDATE users
                SET vai_tro = 'quan_tri'
                WHERE email = ?
            ");
            $stmt->execute([$adminEmail]);
        }
    }
}

ensure_store_schema($conn);
