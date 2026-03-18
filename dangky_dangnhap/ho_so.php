<?php
session_start();
require_once 'config.php';
require_once __DIR__ . '/../includes/store_helpers.php';

if (empty($_SESSION['user_id'])) {
    header('Location: dangnhap.php');
    exit;
}

$user = current_user($conn);
if (!$user) {
    header('Location: logout.php');
    exit;
}

$statusClasses = [
    'cho_xac_nhan' => 'status-cho_xac_nhan',
    'dang_xu_ly' => 'status-dang_xu_ly',
    'dang_giao' => 'status-dang_giao',
    'da_giao' => 'status-da_giao',
    'da_huy' => 'status-da_huy',
];

$stmt = $conn->prepare("
    SELECT id, ho_ten_kh, tong_tien, trang_thai, ngay_dat, ghi_chu
    FROM orders
    WHERE user_id = ?
    ORDER BY ngay_dat DESC
");
$stmt->execute([$user['id']]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="../images/avatar.png" type="image/png" />
    <title>Hồ sơ của tôi | Thuận Phát Garden</title>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Dosis&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Text&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../mainfont/main.css?v=20260318-2" />
    <link rel="stylesheet" href="ho_so.css" />
    <style>
        .profile-info {
            width: 100%;
            display: grid;
            gap: 12px;
            margin-top: 18px;
            text-align: left;
        }

        .profile-info__item {
            padding: 12px 14px;
            border-radius: 10px;
            background: #f8faf7;
            border: 1px solid #e3ebdf;
        }

        .profile-info__label {
            display: block;
            font-size: 12px;
            color: #667085;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .profile-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .profile-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 150px;
            padding: 10px 16px;
            border-radius: 8px;
            text-decoration: none;
            background: #54794a;
            color: #fff;
        }

        .profile-action.secondary {
            background: #eef4ec;
            color: #32532b;
        }

        .cart-preview-item {
            display: flex;
            gap: 12px;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }

        .cart-preview-item:last-child {
            border-bottom: none;
        }

        .cart-preview-item img {
            width: 64px;
            height: 64px;
            object-fit: cover;
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .profile-container {
                flex-direction: column;
            }

            .profile-sidebar {
                width: 100%;
            }
        }
    </style>
</head>

<body data-page="profile">
    <nav class="navigation" id="main-nav"></nav>
    <script defer src="../mainfont/layout.js?v=20260318-2"></script>
    <script defer src="../mainfont/main.js?v=20260318-2"></script>

    <main class="body__main" style="margin-top: 30px; min-height: 60vh;">
        <div class="profile-container">
            <aside class="profile-sidebar">
                <div class="profile-avatar-section">
                    <div class="profile-avatar-placeholder">
                        <ion-icon name="person-outline"></ion-icon>
                    </div>

                    <h3 class="profile-name"><?= htmlspecialchars($user['ho_ten']) ?></h3>
                    <p class="profile-email"><?= htmlspecialchars($user['email']) ?></p>

                    <div class="profile-info">
                        <div class="profile-info__item">
                            <span class="profile-info__label">Số điện thoại</span>
                            <?= htmlspecialchars($user['so_dien_thoai'] ?: 'Chưa cập nhật') ?>
                        </div>
                        <div class="profile-info__item">
                            <span class="profile-info__label">Địa chỉ</span>
                            <?= nl2br(htmlspecialchars($user['dia_chi'] ?: 'Chưa cập nhật')) ?>
                        </div>
                        <div class="profile-info__item">
                            <span class="profile-info__label">Loại tài khoản</span>
                            <?= htmlspecialchars($user['vai_tro'] === 'quan_tri' ? 'Quản trị' : 'Khách hàng') ?>
                        </div>
                    </div>

                    <div class="profile-actions">
                        <a class="profile-action" href="../sanpham/sanpham.php">Mua tiếp</a>
                        <a class="profile-action secondary" href="logout.php">Đăng xuất</a>
                    </div>
                </div>
            </aside>

            <section class="profile-content">
                <h2 class="profile-heading">Giỏ hàng hiện tại</h2>
                <div id="profile-cart-container" class="purchased-products" style="margin-bottom: 40px;">
                    <div class="empty-state">
                        <p>Đang tải giỏ hàng...</p>
                    </div>
                </div>

                <h2 class="profile-heading">Đơn hàng đã đặt</h2>
                <div class="purchased-products">
                    <?php if (empty($orders)): ?>
                        <div class="empty-state">
                            <ion-icon name="receipt-outline" style="font-size: 42px; color: #c6d2c1;"></ion-icon>
                            <p>Bạn chưa có đơn hàng nào.</p>
                            <a href="../sanpham/sanpham.php" class="profile-action">Bắt đầu mua sắm</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <?php
                            $itemStmt = $conn->prepare("
                                SELECT oi.ten_sp, oi.gia, oi.so_luong, oi.thanh_tien, p.hinh_chinh
                                FROM order_items oi
                                LEFT JOIN products p ON p.id = oi.product_id
                                WHERE oi.order_id = ?
                                ORDER BY oi.id ASC
                            ");
                            $itemStmt->execute([$order['id']]);
                            $items = $itemStmt->fetchAll();
                            ?>
                            <article class="order-card">
                                <div class="order-header">
                                    <span class="order-id">Đơn hàng #<?= (int) $order['id'] ?></span>
                                    <span class="order-date"><?= date('d/m/Y H:i', strtotime($order['ngay_dat'])) ?></span>
                                    <span class="order-status <?= $statusClasses[$order['trang_thai']] ?? '' ?>">
                                        <?= htmlspecialchars(order_status_label($order['trang_thai'])) ?>
                                    </span>
                                </div>

                                <div class="order-body">
                                    <?php foreach ($items as $item): ?>
                                        <div class="order-item">
                                            <img src="<?= htmlspecialchars(normalize_public_path($item['hinh_chinh'])) ?>" alt="<?= htmlspecialchars($item['ten_sp']) ?>" onerror="this.onerror=null;this.src='../images/avatar.png';">
                                            <div class="order-item-info">
                                                <div class="order-item-name"><?= htmlspecialchars($item['ten_sp']) ?></div>
                                                <div class="order-item-price">
                                                    <?= htmlspecialchars(format_currency_vnd($item['gia'])) ?> x <?= (int) $item['so_luong'] ?>
                                                </div>
                                            </div>
                                            <div class="order-item-subtotal">
                                                <?= htmlspecialchars(format_currency_vnd($item['thanh_tien'])) ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <?php if (!empty($order['ghi_chu'])): ?>
                                    <div style="margin-top:12px;color:#666;font-size:14px;">
                                        <strong>Ghi chú:</strong> <?= htmlspecialchars($order['ghi_chu']) ?>
                                    </div>
                                <?php endif; ?>

                                <div class="order-footer">
                                    Tổng thanh toán: <?= htmlspecialchars(format_currency_vnd($order['tong_tien'])) ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>

    <footer class="site-footer" id="site-footer"></footer>

    <script src="../giohang/giohang.js?v=20260318-2"></script>
    <script>
        function formatPrice(value) {
            return Number(value).toLocaleString('vi-VN') + 'đ';
        }

        function resolveCartImage(item) {
            return item && item.image ? '../' + item.image : '../images/avatar.png';
        }

        function renderProfileCart() {
            const container = document.getElementById('profile-cart-container');
            const cart = JSON.parse(localStorage.getItem('cart')) || [];

            if (!container) {
                return;
            }

            if (cart.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <ion-icon name="cart-outline" style="font-size: 42px; color: #c6d2c1;"></ion-icon>
                        <p>Giỏ hàng hiện tại đang trống.</p>
                        <a href="../sanpham/sanpham.php" class="profile-action">Xem sản phẩm</a>
                    </div>
                `;
                return;
            }

            let total = 0;
            let html = '<div class="order-card">';

            cart.forEach((item) => {
                const subTotal = Number(item.price) * Number(item.quantity);
                total += subTotal;
                html += `
                    <div class="cart-preview-item">
                        <img src="${resolveCartImage(item)}" alt="${item.name}" onerror="this.onerror=null;this.src='../images/avatar.png';">
                        <div class="order-item-info">
                            <div class="order-item-name">${item.name}</div>
                            <div class="order-item-price">${formatPrice(item.price)} x ${item.quantity}</div>
                        </div>
                        <div class="order-item-subtotal">${formatPrice(subTotal)}</div>
                    </div>
                `;
            });

            html += `
                <div class="order-footer" style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;">
                    <span>Tạm tính: ${formatPrice(total)}</span>
                    <div class="profile-actions" style="margin-top:0;">
                        <a class="profile-action secondary" href="../giohang/giohang.html">Xem giỏ hàng</a>
                        <a class="profile-action" href="../thanhtoan/thanhtoan.php">Thanh toán</a>
                    </div>
                </div>
            `;
            html += '</div>';

            container.innerHTML = html;
        }

        document.addEventListener('DOMContentLoaded', renderProfileCart);
    </script>
</body>

</html>
