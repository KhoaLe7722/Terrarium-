-- ============================================================
--  Seed carts + cart_items cho terrarium_db
--  Dựa trên schema thật: users, products, carts, cart_items
-- ============================================================

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET character_set_client = utf8mb4;
SET character_set_results = utf8mb4;
SET character_set_connection = utf8mb4;

USE `terrarium_db`;

-- ------------------------------------------------------------
-- 1) Tạo bảng carts (nếu chưa có)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `carts` (
  `id`       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`  INT UNSIGNED NOT NULL UNIQUE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 2) Tạo bảng cart_items (nếu chưa có)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cart_items` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `cart_id`    INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `so_luong`   INT UNSIGNED DEFAULT 1,
  FOREIGN KEY (`cart_id`) REFERENCES `carts`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  UNIQUE(`cart_id`, `product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 3) Seed carts cho user thật đang có trong DB
--    (chỉ tạo cart cho user chưa có cart)
-- ------------------------------------------------------------
INSERT INTO `carts` (`user_id`)
SELECT u.id
FROM `users` u
LEFT JOIN `carts` c ON c.user_id = u.id
WHERE c.id IS NULL;

-- ------------------------------------------------------------
-- 4) Seed cart_items theo product thật đang có trong DB
--    Script idempotent: bỏ qua dòng đã tồn tại (cart_id, product_id)
-- ------------------------------------------------------------

-- Item #1: mỗi cart thêm sản phẩm id=1 (nếu có)
INSERT IGNORE INTO `cart_items` (`cart_id`, `product_id`, `so_luong`)
SELECT c.id, 1, 1
FROM `carts` c
WHERE EXISTS (SELECT 1 FROM `products` p WHERE p.id = 1);

-- Item #2: mỗi cart thêm sản phẩm id=2 (nếu có)
INSERT IGNORE INTO `cart_items` (`cart_id`, `product_id`, `so_luong`)
SELECT c.id, 2, 2
FROM `carts` c
WHERE EXISTS (SELECT 1 FROM `products` p WHERE p.id = 2);

-- Item #3: mỗi cart thêm sản phẩm id=3 (nếu có)
INSERT IGNORE INTO `cart_items` (`cart_id`, `product_id`, `so_luong`)
SELECT c.id, 3, 1
FROM `carts` c
WHERE EXISTS (SELECT 1 FROM `products` p WHERE p.id = 3);

-- ------------------------------------------------------------
-- 5) Kiểm tra nhanh sau khi seed
-- ------------------------------------------------------------
SELECT COUNT(*) AS tong_cart FROM `carts`;
SELECT COUNT(*) AS tong_cart_items FROM `cart_items`;
