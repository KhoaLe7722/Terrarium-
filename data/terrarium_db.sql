-- ============================================================
--  TERRARIUM DB - Thuan Phat Garden
--  Database: terrarium_db
--  Tao ngay: 2026-03-04
-- ============================================================

-- Bat buoc khai bao nay truoc de MySQL doc dung tieng Viet
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET character_set_client = utf8mb4;
SET character_set_results = utf8mb4;
SET character_set_connection = utf8mb4;

CREATE DATABASE IF NOT EXISTS `terrarium_db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `terrarium_db`;

-- ------------------------------------------------------------
-- 1. BẢNG NGƯỜI DÙNG (users)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id`         INT          UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `ho_ten`     VARCHAR(100) NOT NULL,
  `email`      VARCHAR(150) NOT NULL UNIQUE,
  `mat_khau`   VARCHAR(255) NOT NULL,          -- Lưu dạng hash (password_hash)
  `so_dien_thoai` VARCHAR(20)  DEFAULT NULL,
  `dia_chi`    TEXT         DEFAULT NULL,
  `vai_tro`    ENUM('khach','quan_tri') NOT NULL DEFAULT 'khach',
  `ngay_tao`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ngay_capnhat` DATETIME   DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 2. BẢNG SẢN PHẨM (products)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `products` (
  `id`           INT          UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `ten_sp`       VARCHAR(200) NOT NULL,
  `gia`          DECIMAL(12,0) NOT NULL,          -- Giá bán (VND)
  `gia_goc`      DECIMAL(12,0) DEFAULT NULL,       -- Giá gốc (trước giảm)
  `giam_gia_phan_tram` TINYINT UNSIGNED DEFAULT 0, -- % Giảm giá
  `giam_gia_bat_dau` DATETIME DEFAULT NULL,        -- Ngày bắt đầu giảm giá
  `giam_gia_ket_thuc` DATETIME DEFAULT NULL,       -- Ngày kết thúc giảm giá
  `hinh_chinh`   VARCHAR(300) DEFAULT NULL,        -- Đường dẫn ảnh chính
  `mo_ta`        LONGTEXT     DEFAULT NULL,        -- Mô tả HTML
  `tinh_trang`   ENUM('con_hang','het_hang') NOT NULL DEFAULT 'con_hang',
  `ngay_tao`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ngay_capnhat` DATETIME     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 3. BẢNG ẢNH PHỤ SẢN PHẨM (product_images)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `product_images` (
  `id`         INT    UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT    UNSIGNED NOT NULL,
  `duong_dan`  VARCHAR(300) NOT NULL,
  `thu_tu`     TINYINT UNSIGNED NOT NULL DEFAULT 0,   -- Thứ tự hiển thị
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 4. BẢNG ĐƠN HÀNG (orders)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `orders` (
  `id`             INT      UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`        INT      UNSIGNED DEFAULT NULL,      -- NULL nếu khách vãng lai
  `ho_ten_kh`      VARCHAR(100) NOT NULL,
  `email_kh`       VARCHAR(150) NOT NULL,
  `sdt_kh`         VARCHAR(20)  DEFAULT NULL,
  `dia_chi_giao`   TEXT         NOT NULL,
  `ghi_chu`        TEXT         DEFAULT NULL,
  `tong_tien`      DECIMAL(14,0) NOT NULL DEFAULT 0,
  `trang_thai`     ENUM('cho_xac_nhan','dang_xu_ly','dang_giao','da_giao','da_huy')
                   NOT NULL DEFAULT 'cho_xac_nhan',
  `ngay_dat`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ngay_capnhat`   DATETIME     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 5. BẢNG CHI TIẾT ĐƠN HÀNG (order_items)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `order_items` (
  `id`         INT    UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id`   INT    UNSIGNED NOT NULL,
  `product_id` INT    UNSIGNED NOT NULL,
  `ten_sp`     VARCHAR(200) NOT NULL,           -- Snapshot tên lúc mua
  `gia`        DECIMAL(12,0) NOT NULL,          -- Snapshot giá lúc mua
  `so_luong`   SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  `thanh_tien` DECIMAL(14,0) NOT NULL,
  FOREIGN KEY (`order_id`)   REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  DỮ LIỆU MẪU
-- ============================================================

-- ------------------------------------------------------------
-- Tài khoản quản trị mặc định
-- (Mật khẩu gốc: Admin@123  –  đã hash bằng PASSWORD_BCRYPT)
-- ------------------------------------------------------------
INSERT INTO `users` (`ho_ten`, `email`, `mat_khau`, `vai_tro`) VALUES
('Quản Trị Viên', 'admin@thuanphatgarden.vn',
 '$2y$12$z7G5vXkH5oNp1nVSa5gFbu3jOaJuqoHcl3ZbWepUJkNkd8hDclnAq',
 'quan_tri');

-- ------------------------------------------------------------
-- Sản phẩm (8 sản phẩm theo code JS)
-- ------------------------------------------------------------
INSERT INTO `products` (`id`, `ten_sp`, `gia`, `gia_goc`, `hinh_chinh`, `mo_ta`, `tinh_trang`) VALUES

(1,
 'Terrarium Bình Trứng Mini',
 157000,
 188400,
 './sanpham/image_sanpham/Bình Trứng Mini/1.jpg',
 '<p><strong>Concept:</strong> Thuận Phát Garden</p>
<p><strong>Loại bình:</strong> Bình Terrarium hình trứng – thiết kế bo tròn đáng yêu, phù hợp làm quà tặng</p>
<p><strong>Chất liệu:</strong> Thuỷ tinh dày 3mm, bo cong tinh tế, độ trong suốt cao</p>
<p><strong>Kích thước bình:</strong><br><span style="font-size:30px">10 x 10 x 12 cm</span></p>
<p><strong>Thực vật bên trong:</strong></p>
<ul>
  <li>Rêu bản địa tươi tốt</li>
  <li>Cỏ và đá nhỏ trang trí</li>
  <li>*Có thể kèm mô hình thú mini dễ thương*</li>
</ul>
<p><strong>Phụ kiện kèm theo:</strong> Bình xịt, hướng dẫn chăm sóc</p>
<p><strong>Ứng dụng:</strong> Quà tặng sinh nhật, bàn học, kệ sách nhỏ</p>
<p><strong>Hướng dẫn chăm sóc:</strong></p>
<ul>
  <li>Tránh nắng trực tiếp</li>
  <li>Tưới 2–3 lần/tuần</li>
  <li>Đậy nắp khi không quan sát để giữ ẩm</li>
</ul>
<p class="thank-you">Thuận Phát Garden cảm ơn bạn đã lựa chọn!</p>',
 'con_hang'),

(2,
 'Terrarium Bình Trụ 14x9',
 357000,
 428400,
 './sanpham/image_sanpham/Terrarium bình trụ 14x9 (2)/1.jpg',
 '<p><strong>Concept:</strong> Tinh hoa vườn cảnh thu nhỏ – Thuận Phát Garden</p>
<p><strong>Loại bình:</strong> Bình hình trụ trong suốt, thiết kế cân đối và hiện đại.</p>
<p><strong>Chất liệu:</strong> Thủy tinh dày cao cấp, đáy bình được gia cố chắc chắn chống trượt.</p>
<p><strong>Kích thước bình:</strong><br><span style="font-size:30px">14 x 9 cm</span></p>
<p><strong>Thực vật bên trong:</strong></p>
<ul>
  <li>Rêu xanh bản địa tươi tốt, giữ ẩm tự nhiên</li>
  <li>Rêu đá phủ nền, mang lại sự cổ kính như vườn Nhật trăm tuổi</li>
  <li>Bộ tiểu cảnh <strong>cầu thang đá và cổng Torii Nhật Bản</strong></li>
</ul>
<p><strong>Phụ kiện đi kèm:</strong></p>
<ul>
  <li>Bình xịt chuyên dụng chống mốc</li>
  <li>Tài liệu hướng dẫn chăm sóc chi tiết</li>
</ul>
<p><strong>Phong cách thiết kế:</strong> Thiền định (Zen) – tối giản, sâu lắng và hài hoà.</p>
<p><strong>Hướng dẫn chăm sóc:</strong></p>
<ul>
  <li><strong>Nhiệt độ lý tưởng:</strong> 18–32°C</li>
  <li><strong>Chế độ tưới:</strong> 2–3 lần mỗi tuần, dùng bình xịt sương</li>
  <li><strong>Chiếu sáng:</strong> Đèn LED từ 4–6 tiếng mỗi ngày</li>
</ul>
<p class="thank-you"><strong>Thuận Phát Garden</strong> – chúng tôi không chỉ bán terrarium, chúng tôi trao tặng những lát cắt của thiên nhiên.</p>',
 'con_hang'),

(3,
 'Bình Mini Cube 12x12',
 599000,
 718800,
 './sanpham/image_sanpham/Bình Mini Cube 12x12/1.jpg',
 '<p><strong>Concept:</strong> Thuận Phát Garden</p>
<p><strong>Loại bình:</strong> Bình Terrarium hình hộp đứng – viền kính trong suốt, thiết kế hiện đại</p>
<p><strong>Chất liệu:</strong> Thuỷ tinh dày 3mm, viền silicon độ đàn hồi cao, được làm thủ công tỉ mỉ.</p>
<p><strong>Kích thước bình (Dài × Rộng × Cao):</strong><br><span style="font-size:30px">12 x 12 x 12 cm</span></p>
<p><strong>Thực vật bên trong:</strong></p>
<ul>
  <li>Rêu xanh tươi bản địa (4–10 loại tùy mùa)</li>
  <li>Dương xỉ lá me, cỏ trang trí</li>
  <li>Gỗ mục tạo hiệu ứng cổ điển</li>
  <li>*Giá chưa bao gồm mô hình động vật*</li>
</ul>
<p><strong>Phụ kiện kèm theo:</strong> Bình xịt chống nấm mốc, hướng dẫn chăm sóc</p>
<p><strong>Hướng dẫn chăm sóc:</strong></p>
<ul>
  <li>Nhiệt độ: 18 – 32°C</li>
  <li>Tưới nước 3–4 ngày/lần trong 3 tuần đầu. Sau đó, chỉ tưới khi thấy bình khô.</li>
  <li>Không để nắng trực tiếp. Có thể dùng đèn LED 3–8 tiếng/ngày.</li>
  <li>Luôn đóng nắp bình để giữ ẩm.</li>
</ul>
<p class="thank-you">Thuận Phát Garden xin chân thành cảm ơn Quý Khách!</p>',
 'con_hang'),

(4,
 'Terrarium Đa Giác 16x16x32',
 957000,
 1148400,
 './sanpham/image_sanpham/Terrarium Đa Giác 16x16x32/1.jpg',
 '<p><strong>Concept:</strong> Thuận Phát Garden – Trở về cõi tĩnh lặng</p>
<p><strong>Loại bình:</strong> Terrarium đa giác đứng – khung cảnh 360°, tái hiện một thế giới tĩnh tại bên trong lớp kính</p>
<p><strong>Chất liệu:</strong> Kính cường lực trong suốt kết hợp khung thép đen mờ – vừa hiện đại, vừa hoài cổ.</p>
<p><strong>Kích thước bình:</strong><br><span style="font-size:30px">16 x 16 x 32 cm</span></p>
<p><strong>Thực vật và cảnh quan:</strong></p>
<ul>
  <li>Rêu tươi bản địa, cây lá màu nhỏ</li>
  <li>Gỗ mục nghệ thuật, đá tự nhiên thô mộc</li>
  <li>Tiểu cảnh tượng thiền, đền thờ gỗ</li>
</ul>
<p><strong>Phụ kiện đi kèm:</strong> Bình xịt dưỡng ẩm, hướng dẫn chăm sóc chi tiết</p>
<p><strong>Phong cách tổng thể:</strong> Thiền – Cổ – Mộc</p>
<p><strong>Hướng dẫn chăm sóc:</strong></p>
<ul>
  <li>Tưới nhẹ 2–3 lần mỗi tuần để giữ độ ẩm</li>
  <li>Dùng đèn LED ban ngày (4–6 tiếng) để hỗ trợ quang hợp</li>
</ul>
<p class="thank-you">🌱 Thuận Phát Garden – cảm ơn bạn đã để rừng trú ngụ nơi tim mình.</p>',
 'con_hang'),

(5,
 'Terrarium Đa Giác 20x20x32',
 957000,
 1148400,
 './sanpham/image_sanpham/Terrarium Đa Giác 20x20x32/1.jpg',
 '<p><strong>Concept:</strong> Thuận Phát Garden</p>
<p><strong>Loại bình:</strong> Terrarium đa giác đứng – thiết kế mở 360°, dễ dàng quan sát từ mọi phía</p>
<p><strong>Chất liệu:</strong> Kính cường lực kết hợp khung thép sơn tĩnh điện màu đen – bền bỉ, sang trọng</p>
<p><strong>Kích thước bình:</strong><br><span style="font-size:30px">20 x 20 x 32 cm</span></p>
<p><strong>Thực vật và tiểu cảnh:</strong></p>
<ul>
  <li>Rêu xanh mướt, cây nhỏ ưa ẩm</li>
  <li>Gỗ lũa, đá tự nhiên, tượng thiền và đền thờ mini</li>
</ul>
<p><strong>Phụ kiện kèm theo:</strong> Bình xịt giữ ẩm + Hướng dẫn chăm sóc chi tiết</p>
<p><strong>Phong cách tổng thể:</strong> Rừng sâu huyền bí – nơi ngôi đền cổ thiêng lặng giữa thiên nhiên</p>
<p><strong>Hướng dẫn chăm sóc:</strong></p>
<ul>
  <li>Tưới 2–3 lần mỗi tuần bằng bình xịt</li>
  <li>Chiếu sáng gián tiếp bằng đèn LED 4–6h/ngày (tránh ánh nắng trực tiếp)</li>
</ul>
<p class="thank-you">Thuận Phát Garden chân thành cảm ơn quý khách đã chọn lựa sản phẩm!</p>',
 'con_hang'),

(6,
 'Terrarium Đa Giác 16x16x34',
 1357000,
 1628400,
 './sanpham/image_sanpham/Terrarium Đa Giác 16x16x34/1.jpg',
 '<p><strong>Concept:</strong> Thuận Phát Garden</p>
<p><strong>Loại bình:</strong> Terrarium đa giác đứng – không gian mở 360°, giúp ngắm trọn vẻ đẹp tiểu cảnh từ mọi góc nhìn</p>
<p><strong>Chất liệu:</strong> Kính cường lực trong suốt kết hợp khung thép sơn tĩnh điện màu đen</p>
<p><strong>Kích thước bình:</strong><br><span style="font-size:30px">16 x 16 x 34 cm</span></p>
<p><strong>Thực vật và tiểu cảnh:</strong></p>
<ul>
  <li>Rêu xanh, cây lá màu nhỏ (ưa ẩm, sống tốt trong điều kiện trong nhà)</li>
  <li>Gỗ khô, đá tự nhiên</li>
  <li>Tượng thiền định và mô hình đền thờ mini</li>
</ul>
<p><strong>Phụ kiện kèm theo:</strong> Bình xịt giữ ẩm chuyên dụng + hướng dẫn chăm sóc chi tiết</p>
<p><strong>Phong cách tổng thể:</strong> Thiền viện cổ kính – tĩnh lặng và đầy chiều sâu tâm hồn</p>
<p><strong>Hướng dẫn chăm sóc:</strong></p>
<ul>
  <li>Tưới nước 2–3 lần mỗi tuần bằng bình xịt nhẹ</li>
  <li>Chiếu sáng gián tiếp bằng đèn LED từ 4–6 giờ mỗi ngày (không ánh nắng trực tiếp)</li>
</ul>
<p class="thank-you">Thuận Phát Garden chân thành cảm ơn quý khách đã trao niềm tin và tình yêu cho thiên nhiên!</p>',
 'con_hang'),

(7,
 'Terrarium Đa Giác 23x23x40',
 1500000,
 1800000,
 './sanpham/image_sanpham/Terrarium Đa Giác 23x23x40/1.jpg',
 '<p><strong>Concept:</strong> Thuận Phát Garden</p>
<p><strong>Loại bình:</strong> Terrarium đa giác đứng – khối hình lớn, cho phép xây dựng cảnh quan tầng lớp nhiều lớp</p>
<p><strong>Chất liệu:</strong> Kính cường lực cao cấp, khung thép đen chống gỉ – độ bền và thẩm mỹ song hành</p>
<p><strong>Kích thước bình:</strong><br><span style="font-size:30px">23 x 23 x 40 cm</span></p>
<p><strong>Thực vật &amp; tiểu cảnh:</strong></p>
<ul>
  <li>Rêu tươi phủ nền, cây cảnh lá nhỏ ưa ẩm</li>
  <li>Đá cuội lớn, gỗ lũa, tượng thiền định và đền thờ gỗ mini</li>
  <li>Tiểu cảnh cầu gỗ, bậc đá, lối mòn dẫn lên chùa</li>
</ul>
<p><strong>Phụ kiện kèm theo:</strong> Bình xịt tạo ẩm chuyên dụng + Hướng dẫn chăm sóc đi kèm</p>
<p><strong>Phong cách tổng thể:</strong> Núi thiêng ẩn tu – nơi không gian mở ra cho sự an yên, thiền định và kết nối với thiên nhiên</p>
<p><strong>Hướng dẫn chăm sóc:</strong></p>
<ul>
  <li>Xịt nước nhẹ 2–3 lần/tuần (tăng giảm tùy độ ẩm không khí)</li>
  <li>Chiếu sáng bằng đèn LED 4–6 giờ mỗi ngày, đặt nơi mát và tránh ánh nắng trực tiếp</li>
</ul>
<p class="thank-you">Thuận Phát Garden kính chúc quý khách tìm thấy sự tĩnh lặng và cảm hứng từ những khu vườn thiền thu nhỏ!</p>',
 'con_hang'),

(8,
 'Đèn LED Đế Gỗ Tầng Cao Terrarium',
 160000,
 192000,
 './sanpham/image_sanpham/Đèn.jpg',
 '<p><strong>Concept:</strong> Nâng tầm trưng bày Terrarium</p>
<p><strong>Loại sản phẩm:</strong> Giá đỡ nhiều tầng kèm đèn LED rọi từ trên</p>
<p><strong>Chất liệu:</strong> Đế gỗ tự nhiên – Đèn LED cổ mềm điều chỉnh linh hoạt</p>
<p><strong>Kích thước tổng thể:</strong><br><span style="font-size:30px">Đế gỗ hình vuông, cao khoảng 25–35 cm</span></p>
<p><strong>Tiện ích:</strong></p>
<ul>
  <li>Đèn rọi từ trên chiếu sáng nổi bật tiểu cảnh</li>
  <li>Thiết kế đế nhiều tầng giúp tăng chiều sâu trưng bày</li>
  <li>Phù hợp các bình terrarium cao hoặc độc đáo</li>
</ul>
<p><strong>Đi kèm:</strong> Bộ nguồn và dây cắm USB</p>
<p><strong>Phong cách:</strong> Tối giản – Kiến trúc – Trưng bày chuyên nghiệp</p>
<p><strong>Hướng dẫn sử dụng:</strong></p>
<ul>
  <li>Đặt bình terrarium trên tầng cao nhất</li>
  <li>Chỉnh đèn LED chiếu đúng vị trí cần làm nổi bật</li>
  <li>Cắm điện bằng cổng USB để sử dụng</li>
</ul>
<p class="thank-you">Thuận Phát Garden xin chân thành cảm ơn!</p>',
 'con_hang');

-- ------------------------------------------------------------
-- Ảnh phụ sản phẩm (product_images)
-- ------------------------------------------------------------
INSERT INTO `product_images` (`product_id`, `duong_dan`, `thu_tu`) VALUES
-- SP 1: Bình trứng Mini
(1, './sanpham/image_sanpham/Bình Trứng Mini/2.jpg', 1),
(1, './sanpham/image_sanpham/Bình Trứng Mini/3.jpg', 2),

-- SP 2: Bình trụ 14x9
(2, './sanpham/image_sanpham/Terrarium bình trụ 14x9 (2)/2.jpg', 1),
(2, './sanpham/image_sanpham/Terrarium bình trụ 14x9 (2)/3.jpg', 2),
(2, './sanpham/image_sanpham/Terrarium bình trụ 14x9 (2)/4.jpg', 3),
(2, './sanpham/image_sanpham/Terrarium bình trụ 14x9 (2)/5.jpg', 4),
(2, './sanpham/image_sanpham/Terrarium bình trụ 14x9 (2)/Terrarium bình trụ 14x9 (2).jpg', 5),
(2, './sanpham/image_sanpham/Terrarium bình trụ 14x9 (2)/Terrarium bình trụ 14x9.jpg', 6),

-- SP 3: Mini Cube 12x12
(3, './sanpham/image_sanpham/Bình Mini Cube 12x12/2.jpg', 1),
(3, './sanpham/image_sanpham/Bình Mini Cube 12x12/3.jpg', 2),

-- SP 4: Đa giác 16x16x32
(4, './sanpham/image_sanpham/Terrarium Đa Giác 16x16x32/2.jpg', 1),
(4, './sanpham/image_sanpham/Terrarium Đa Giác 16x16x32/3.jpg', 2),
(4, './sanpham/image_sanpham/Terrarium Đa Giác 16x16x32/4.jpg', 3),
(4, './sanpham/image_sanpham/Terrarium Đa Giác 16x16x32/5.jpg', 4),
(4, './sanpham/image_sanpham/Terrarium Đa Giác 16x16x32/Terrarium Đa Giác 16x16x32.jpg', 5),

-- SP 5: Đa giác 20x20x32
(5, './sanpham/image_sanpham/Terrarium Đa Giác 20x20x32/2.jpg', 1),
(5, './sanpham/image_sanpham/Terrarium Đa Giác 20x20x32/3.jpg', 2),
(5, './sanpham/image_sanpham/Terrarium Đa Giác 20x20x32/4.jpg', 3),
(5, './sanpham/image_sanpham/Terrarium Đa Giác 20x20x32/5.jpg', 4),
(5, './sanpham/image_sanpham/Terrarium Đa Giác 20x20x32/Terrarium Đa Giác 20x20x32 (2).jpg', 5),
(5, './sanpham/image_sanpham/Terrarium Đa Giác 20x20x32/Terrarium Đa Giác 20x20x32.jpg', 6),

-- SP 6: Đa giác 16x16x34
(6, './sanpham/image_sanpham/Terrarium Đa Giác 16x16x34/2.jpg', 1),
(6, './sanpham/image_sanpham/Terrarium Đa Giác 16x16x34/3.jpg', 2),

-- SP 7: Đa giác 23x23x40
(7, './sanpham/image_sanpham/Terrarium Đa Giác 23x23x40/2.jpg', 1),
(7, './sanpham/image_sanpham/Terrarium Đa Giác 23x23x40/3.jpg', 2),
(7, './sanpham/image_sanpham/Terrarium Đa Giác 23x23x40/4.jpg', 3),
(7, './sanpham/image_sanpham/Terrarium Đa Giác 23x23x40/Terrarium Đa Giác 23x23x40.jpg', 4);

-- SP 8: Đèn LED – không có ảnh phụ

-- ============================================================
--  VIEWS TIỆN ÍCH
-- ============================================================

-- View: danh sách sản phẩm kèm phần trăm giảm giá
CREATE OR REPLACE VIEW `v_products_overview` AS
SELECT
  p.id,
  p.ten_sp,
  p.gia,
  p.gia_goc,
  ROUND((1 - p.gia / p.gia_goc) * 100) AS phan_tram_giam,
  p.hinh_chinh,
  p.tinh_trang,
  COUNT(pi.id) AS so_anh_phu
FROM `products` p
LEFT JOIN `product_images` pi ON pi.product_id = p.id
GROUP BY p.id;
