USE `terrarium_db`;

SET @demo_email := 'demo.orders@thuanphatgarden.vn';
SET @demo_name := 'Khach Demo Dat Hang';
SET @demo_phone := '0909123456';
SET @demo_address := '131 Ly Tu Trong, Ninh Kieu, Can Tho';
SET @demo_password_hash := '$2y$10$hEh3VhivtOgMICnniZxkK.yCDcj5UZ1NcY7pmb80qmBOisshrEvmS';

INSERT INTO `users` (
  `ho_ten`,
  `email`,
  `mat_khau`,
  `so_dien_thoai`,
  `dia_chi`,
  `vai_tro`
) VALUES (
  @demo_name,
  @demo_email,
  @demo_password_hash,
  @demo_phone,
  @demo_address,
  'khach'
)
ON DUPLICATE KEY UPDATE
  `ho_ten` = VALUES(`ho_ten`),
  `mat_khau` = VALUES(`mat_khau`),
  `so_dien_thoai` = VALUES(`so_dien_thoai`),
  `dia_chi` = VALUES(`dia_chi`),
  `vai_tro` = 'khach';

SET @demo_user_id := (
  SELECT `id`
  FROM `users`
  WHERE `email` = @demo_email
  LIMIT 1
);

DELETE oi
FROM `order_items` oi
INNER JOIN `orders` o ON o.`id` = oi.`order_id`
WHERE o.`user_id` = @demo_user_id;

DELETE FROM `orders`
WHERE `user_id` = @demo_user_id;

INSERT INTO `orders` (
  `user_id`,
  `ho_ten_kh`,
  `email_kh`,
  `sdt_kh`,
  `dia_chi_giao`,
  `ghi_chu`,
  `tong_tien`,
  `trang_thai`,
  `ngay_dat`
) VALUES (
  @demo_user_id,
  @demo_name,
  @demo_email,
  @demo_phone,
  @demo_address,
  'Giao gio hanh chinh, goi truoc 10 phut.',
  0,
  'cho_xac_nhan',
  '2026-03-14 09:15:00'
);
SET @order_1_id := LAST_INSERT_ID();

INSERT INTO `order_items` (`order_id`, `product_id`, `ten_sp`, `gia`, `so_luong`, `thanh_tien`)
SELECT @order_1_id, p.`id`, p.`ten_sp`, p.`gia`, 1, p.`gia`
FROM `products` p
WHERE p.`id` = 1;

INSERT INTO `order_items` (`order_id`, `product_id`, `ten_sp`, `gia`, `so_luong`, `thanh_tien`)
SELECT @order_1_id, p.`id`, p.`ten_sp`, p.`gia`, 2, p.`gia` * 2
FROM `products` p
WHERE p.`id` = 2;

UPDATE `orders`
SET `tong_tien` = (
  SELECT COALESCE(SUM(`thanh_tien`), 0)
  FROM `order_items`
  WHERE `order_id` = @order_1_id
)
WHERE `id` = @order_1_id;

INSERT INTO `orders` (
  `user_id`,
  `ho_ten_kh`,
  `email_kh`,
  `sdt_kh`,
  `dia_chi_giao`,
  `ghi_chu`,
  `tong_tien`,
  `trang_thai`,
  `ngay_dat`
) VALUES (
  @demo_user_id,
  @demo_name,
  @demo_email,
  @demo_phone,
  '45 Nguyen Viet Hong, Ninh Kieu, Can Tho',
  'Nhan hang vao buoi chieu.',
  0,
  'dang_xu_ly',
  '2026-03-10 14:20:00'
);
SET @order_2_id := LAST_INSERT_ID();

INSERT INTO `order_items` (`order_id`, `product_id`, `ten_sp`, `gia`, `so_luong`, `thanh_tien`)
SELECT @order_2_id, p.`id`, p.`ten_sp`, p.`gia`, 1, p.`gia`
FROM `products` p
WHERE p.`id` = 3;

INSERT INTO `order_items` (`order_id`, `product_id`, `ten_sp`, `gia`, `so_luong`, `thanh_tien`)
SELECT @order_2_id, p.`id`, p.`ten_sp`, p.`gia`, 1, p.`gia`
FROM `products` p
WHERE p.`id` = 1;

UPDATE `orders`
SET `tong_tien` = (
  SELECT COALESCE(SUM(`thanh_tien`), 0)
  FROM `order_items`
  WHERE `order_id` = @order_2_id
)
WHERE `id` = @order_2_id;

INSERT INTO `orders` (
  `user_id`,
  `ho_ten_kh`,
  `email_kh`,
  `sdt_kh`,
  `dia_chi_giao`,
  `ghi_chu`,
  `tong_tien`,
  `trang_thai`,
  `ngay_dat`
) VALUES (
  @demo_user_id,
  @demo_name,
  @demo_email,
  @demo_phone,
  '12 Tran Van Kheo, Ninh Kieu, Can Tho',
  'Ship nhanh giup minh trong ngay.',
  0,
  'dang_giao',
  '2026-03-06 08:45:00'
);
SET @order_3_id := LAST_INSERT_ID();

INSERT INTO `order_items` (`order_id`, `product_id`, `ten_sp`, `gia`, `so_luong`, `thanh_tien`)
SELECT @order_3_id, p.`id`, p.`ten_sp`, p.`gia`, 1, p.`gia`
FROM `products` p
WHERE p.`id` = 4;

UPDATE `orders`
SET `tong_tien` = (
  SELECT COALESCE(SUM(`thanh_tien`), 0)
  FROM `order_items`
  WHERE `order_id` = @order_3_id
)
WHERE `id` = @order_3_id;

INSERT INTO `orders` (
  `user_id`,
  `ho_ten_kh`,
  `email_kh`,
  `sdt_kh`,
  `dia_chi_giao`,
  `ghi_chu`,
  `tong_tien`,
  `trang_thai`,
  `ngay_dat`
) VALUES (
  @demo_user_id,
  @demo_name,
  @demo_email,
  @demo_phone,
  '88 Mau Than, Xuan Khanh, Ninh Kieu, Can Tho',
  'Da nhan du, dong goi rat dep.',
  0,
  'da_giao',
  '2026-02-27 16:10:00'
);
SET @order_4_id := LAST_INSERT_ID();

INSERT INTO `order_items` (`order_id`, `product_id`, `ten_sp`, `gia`, `so_luong`, `thanh_tien`)
SELECT @order_4_id, p.`id`, p.`ten_sp`, p.`gia`, 1, p.`gia`
FROM `products` p
WHERE p.`id` = 5;

INSERT INTO `order_items` (`order_id`, `product_id`, `ten_sp`, `gia`, `so_luong`, `thanh_tien`)
SELECT @order_4_id, p.`id`, p.`ten_sp`, p.`gia`, 1, p.`gia`
FROM `products` p
WHERE p.`id` = 2;

UPDATE `orders`
SET `tong_tien` = (
  SELECT COALESCE(SUM(`thanh_tien`), 0)
  FROM `order_items`
  WHERE `order_id` = @order_4_id
)
WHERE `id` = @order_4_id;

INSERT INTO `orders` (
  `user_id`,
  `ho_ten_kh`,
  `email_kh`,
  `sdt_kh`,
  `dia_chi_giao`,
  `ghi_chu`,
  `tong_tien`,
  `trang_thai`,
  `ngay_dat`
) VALUES (
  @demo_user_id,
  @demo_name,
  @demo_email,
  @demo_phone,
  '201 Nguyen Van Cu, An Hoa, Ninh Kieu, Can Tho',
  'Khach doi mau chau, don nay da huy.',
  0,
  'da_huy',
  '2026-02-20 11:05:00'
);
SET @order_5_id := LAST_INSERT_ID();

INSERT INTO `order_items` (`order_id`, `product_id`, `ten_sp`, `gia`, `so_luong`, `thanh_tien`)
SELECT @order_5_id, p.`id`, p.`ten_sp`, p.`gia`, 1, p.`gia`
FROM `products` p
WHERE p.`id` = 6;

UPDATE `orders`
SET `tong_tien` = (
  SELECT COALESCE(SUM(`thanh_tien`), 0)
  FROM `order_items`
  WHERE `order_id` = @order_5_id
)
WHERE `id` = @order_5_id;
