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

$orderId = (int) ($_GET['id'] ?? 0);
if ($orderId <= 0) {
    header('Location: ho_so.php');
    exit;
}

$statusClasses = [
    'cho_xac_nhan' => 'status-cho_xac_nhan',
    'dang_xu_ly' => 'status-dang_xu_ly',
    'dang_giao' => 'status-dang_giao',
    'da_giao' => 'status-da_giao',
    'da_huy' => 'status-da_huy',
];

$orderStmt = $conn->prepare("
    SELECT id, user_id, ho_ten_kh, email_kh, sdt_kh, dia_chi_giao, ghi_chu, tong_tien, trang_thai, ngay_dat, phuong_thuc_tt
    FROM orders
    WHERE id = ? AND user_id = ?
    LIMIT 1
");
$orderStmt->execute([$orderId, $user['id']]);
$order = $orderStmt->fetch();

if (!$order) {
    header('Location: ho_so.php');
    exit;
}

$itemsStmt = $conn->prepare("
    SELECT oi.product_id, oi.ten_sp, oi.gia, oi.so_luong, oi.thanh_tien, p.hinh_chinh
    FROM order_items oi
    LEFT JOIN products p ON p.id = oi.product_id
    WHERE oi.order_id = ?
    ORDER BY oi.id ASC
");
$itemsStmt->execute([$orderId]);
$items = $itemsStmt->fetchAll();

$displayName = trim((string) ($order['ho_ten_kh'] ?? '')) !== ''
    ? $order['ho_ten_kh']
    : $user['ho_ten'];
$displayEmail = trim((string) ($order['email_kh'] ?? '')) !== ''
    ? $order['email_kh']
    : $user['email'];
$displayPhone = trim((string) ($order['sdt_kh'] ?? '')) !== ''
    ? $order['sdt_kh']
    : ($user['so_dien_thoai'] ?: 'Chưa cập nhật');
$displayAddress = trim((string) ($order['dia_chi_giao'] ?? '')) !== ''
    ? $order['dia_chi_giao']
    : ($user['dia_chi'] ?: 'Chưa cập nhật');
$totalQuantity = 0;
foreach ($items as $item) {
    $totalQuantity += max(0, (int) ($item['so_luong'] ?? 0));
}

$canCancel = order_can_customer_cancel((string) $order['trang_thai']);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="../images/avatar.png" type="image/png" />
    <title>Hóa đơn đơn hàng #<?= (int) $order['id'] ?> | Thuận Phát Garden</title>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Dosis&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Text&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../mainfont/main.css?v=20260324-6" />
    <link rel="stylesheet" href="ho_so.css?v=20260324-2" />
    <style>
        .order-detail-page {
            max-width: 1120px;
            margin: 0 auto;
            padding: 30px 20px 60px;
        }

        .detail-page-header {
            background: linear-gradient(135deg, #f7fbf5 0%, #ffffff 100%);
            border: 1px solid #e3ebdf;
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 12px 32px rgba(31, 41, 55, 0.08);
            margin-bottom: 24px;
        }

        .detail-page-topline {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .detail-page-eyebrow {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6b7280;
            margin-bottom: 10px;
        }

        .detail-page-title {
            font-size: 36px;
            line-height: 1.1;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .detail-page-subtitle {
            font-size: 15px;
            color: #5b6472;
        }

        .detail-page-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 18px;
        }

        .detail-highlight-row {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-top: 22px;
        }

        .detail-highlight {
            border-radius: 16px;
            background: #ffffff;
            border: 1px solid #edf2ea;
            padding: 16px;
        }

        .detail-highlight__label {
            display: block;
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 8px;
        }

        .detail-highlight__value {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.5fr) minmax(320px, 0.9fr);
            gap: 24px;
        }

        .detail-card {
            background: #fff;
            border-radius: 18px;
            border: 1px solid #e7eee4;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.08);
            padding: 24px;
        }

        .detail-card + .detail-card {
            margin-top: 18px;
        }

        .detail-card__title {
            font-size: 24px;
            color: #1f2937;
            margin-bottom: 18px;
        }

        .detail-list {
            display: grid;
            gap: 14px;
        }

        .detail-item {
            padding-bottom: 14px;
            border-bottom: 1px dashed #dbe5d6;
        }

        .detail-item:last-child {
            padding-bottom: 0;
            border-bottom: 0;
        }

        .detail-item__label {
            display: block;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #6b7280;
            margin-bottom: 6px;
        }

        .detail-item__value {
            font-size: 16px;
            line-height: 1.65;
            color: #1f2937;
        }

        .invoice-items {
            display: grid;
            gap: 16px;
        }

        .invoice-item {
            display: grid;
            grid-template-columns: 88px minmax(0, 1fr) auto;
            gap: 16px;
            align-items: center;
            padding: 14px 0;
            border-bottom: 1px dashed #dbe5d6;
        }

        .invoice-item:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .invoice-item__image {
            width: 88px;
            height: 88px;
            object-fit: cover;
            border-radius: 14px;
            background: #f7f7f7;
        }

        .invoice-item__name {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 6px;
        }

        .invoice-item__meta {
            color: #5b6472;
            line-height: 1.6;
        }

        .invoice-item__subtotal {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
            white-space: nowrap;
        }

        .invoice-summary {
            margin-top: 22px;
            padding-top: 18px;
            border-top: 1px solid #dbe5d6;
            display: grid;
            gap: 10px;
        }

        .invoice-summary__row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            color: #374151;
        }

        .invoice-summary__row.total {
            font-size: 22px;
            font-weight: 700;
            color: #1f2937;
        }

        .note-box {
            margin-top: 18px;
            padding: 16px 18px;
            border-radius: 14px;
            background: #f8faf7;
            color: #475467;
            line-height: 1.7;
            border: 1px solid #e3ebdf;
        }

        @media (max-width: 980px) {
            .detail-highlight-row,
            .detail-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .order-detail-page {
                padding: 22px 14px 42px;
            }

            .detail-page-header,
            .detail-card {
                padding: 20px;
            }

            .detail-page-title {
                font-size: 28px;
            }

            .invoice-item {
                grid-template-columns: 72px minmax(0, 1fr);
            }

            .invoice-item__subtotal {
                grid-column: 1 / -1;
                padding-left: 88px;
            }
        }
    </style>
</head>

<body data-page="profile">
    <nav class="navigation" id="main-nav"></nav>
    <script defer src="../mainfont/layout.js?v=20260324-9"></script>
    <script defer src="../mainfont/main.js?v=20260324-6"></script>

    <main class="body__main">
        <div class="order-detail-page">
            <section class="detail-page-header">
                <div class="detail-page-topline">
                    <div>
                        <div class="detail-page-eyebrow">Hóa đơn đơn hàng</div>
                        <h1 class="detail-page-title">Đơn hàng #<?= (int) $order['id'] ?></h1>
                        <p class="detail-page-subtitle">
                            Đặt lúc <?= date('d/m/Y H:i', strtotime($order['ngay_dat'])) ?>,
                            phương thức thanh toán <?= htmlspecialchars(payment_method_label($order['phuong_thuc_tt'] ?? '')) ?>.
                        </p>
                    </div>
                    <span class="order-status <?= htmlspecialchars($statusClasses[$order['trang_thai']] ?? '') ?>">
                        <?= htmlspecialchars(order_status_label((string) $order['trang_thai'])) ?>
                    </span>
                </div>

                <div class="detail-page-actions">
                    <a class="profile-action secondary" href="ho_so.php">Quay lại hồ sơ</a>
                    <?php if ($canCancel): ?>
                        <button
                            type="button"
                            class="profile-action danger js-open-cancel-order"
                            data-order-id="<?= (int) $order['id'] ?>"
                            data-order-label="#<?= (int) $order['id'] ?>">
                            Hủy đơn hàng
                        </button>
                    <?php endif; ?>
                </div>

                <div class="detail-highlight-row">
                    <div class="detail-highlight">
                        <span class="detail-highlight__label">Mã hóa đơn</span>
                        <div class="detail-highlight__value">HD-<?= str_pad((string) $order['id'], 6, '0', STR_PAD_LEFT) ?></div>
                    </div>
                    <div class="detail-highlight">
                        <span class="detail-highlight__label">Tổng sản phẩm</span>
                        <div class="detail-highlight__value"><?= (int) $totalQuantity ?></div>
                    </div>
                    <div class="detail-highlight">
                        <span class="detail-highlight__label">Số dòng hàng</span>
                        <div class="detail-highlight__value"><?= count($items) ?></div>
                    </div>
                    <div class="detail-highlight">
                        <span class="detail-highlight__label">Tổng thanh toán</span>
                        <div class="detail-highlight__value"><?= htmlspecialchars(format_currency_vnd($order['tong_tien'])) ?></div>
                    </div>
                </div>
            </section>

            <section class="detail-grid">
                <div>
                    <article class="detail-card">
                        <h2 class="detail-card__title">Sản phẩm trong đơn</h2>

                        <?php if (empty($items)): ?>
                            <div class="empty-state" style="padding: 24px 0 8px;">
                                <p>Đơn hàng này chưa có sản phẩm.</p>
                            </div>
                        <?php else: ?>
                            <div class="invoice-items">
                                <?php foreach ($items as $item): ?>
                                    <div class="invoice-item">
                                        <img
                                            class="invoice-item__image"
                                            src="<?= htmlspecialchars(normalize_public_path($item['hinh_chinh'])) ?>"
                                            alt="<?= htmlspecialchars($item['ten_sp']) ?>"
                                            onerror="this.onerror=null;this.src='../images/avatar.png';">
                                        <div>
                                            <div class="invoice-item__name"><?= htmlspecialchars($item['ten_sp']) ?></div>
                                            <div class="invoice-item__meta">
                                                Mã sản phẩm: #<?= (int) ($item['product_id'] ?? 0) ?><br>
                                                Đơn giá: <?= htmlspecialchars(format_currency_vnd($item['gia'])) ?><br>
                                                Số lượng: <?= (int) ($item['so_luong'] ?? 0) ?>
                                            </div>
                                        </div>
                                        <div class="invoice-item__subtotal">
                                            <?= htmlspecialchars(format_currency_vnd($item['thanh_tien'])) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="invoice-summary">
                                <div class="invoice-summary__row">
                                    <span>Tổng số lượng</span>
                                    <strong><?= (int) $totalQuantity ?> sản phẩm</strong>
                                </div>
                                <div class="invoice-summary__row">
                                    <span>Tổng dòng hàng</span>
                                    <strong><?= count($items) ?> mục</strong>
                                </div>
                                <div class="invoice-summary__row total">
                                    <span>Tổng thanh toán</span>
                                    <span><?= htmlspecialchars(format_currency_vnd($order['tong_tien'])) ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </article>
                </div>

                <div>
                    <article class="detail-card">
                        <h2 class="detail-card__title">Thông tin nhận hàng</h2>
                        <div class="detail-list">
                            <div class="detail-item">
                                <span class="detail-item__label">Người nhận</span>
                                <div class="detail-item__value"><?= htmlspecialchars($displayName) ?></div>
                            </div>
                            <div class="detail-item">
                                <span class="detail-item__label">Email</span>
                                <div class="detail-item__value"><?= htmlspecialchars($displayEmail) ?></div>
                            </div>
                            <div class="detail-item">
                                <span class="detail-item__label">Số điện thoại</span>
                                <div class="detail-item__value"><?= htmlspecialchars($displayPhone) ?></div>
                            </div>
                            <div class="detail-item">
                                <span class="detail-item__label">Địa chỉ giao hàng</span>
                                <div class="detail-item__value"><?= nl2br(htmlspecialchars($displayAddress)) ?></div>
                            </div>
                        </div>
                    </article>

                    <article class="detail-card">
                        <h2 class="detail-card__title">Thông tin đơn hàng</h2>
                        <div class="detail-list">
                            <div class="detail-item">
                                <span class="detail-item__label">Trạng thái đơn hàng</span>
                                <div class="detail-item__value"><?= htmlspecialchars(order_status_label((string) $order['trang_thai'])) ?></div>
                            </div>
                            <div class="detail-item">
                                <span class="detail-item__label">Phương thức thanh toán</span>
                                <div class="detail-item__value"><?= htmlspecialchars(payment_method_label($order['phuong_thuc_tt'] ?? '')) ?></div>
                            </div>
                            <div class="detail-item">
                                <span class="detail-item__label">Thời gian đặt</span>
                                <div class="detail-item__value"><?= date('d/m/Y H:i', strtotime($order['ngay_dat'])) ?></div>
                            </div>
                            <div class="detail-item">
                                <span class="detail-item__label">Ghi chú của khách</span>
                                <div class="detail-item__value">
                                    <?= trim((string) ($order['ghi_chu'] ?? '')) !== '' ? nl2br(htmlspecialchars($order['ghi_chu'])) : 'Không có ghi chú.' ?>
                                </div>
                            </div>
                        </div>

                        <?php if ($canCancel): ?>
                            <div class="note-box">
                                Đơn hàng này đang ở trạng thái có thể hủy. Khi bạn xác nhận hủy đơn, số lượng sản phẩm sẽ được hoàn trả lại kho.
                            </div>
                        <?php endif; ?>
                    </article>
                </div>
            </section>
        </div>
    </main>

    <div class="confirm-modal" id="cancel-order-modal">
        <div class="confirm-modal__panel">
            <h3 class="confirm-modal__title">Hủy đơn hàng</h3>
            <p class="confirm-modal__message" data-cancel-order-message>Bạn có muốn hủy đơn hàng này không?</p>
            <div class="confirm-modal__actions">
                <button type="button" class="confirm-btn secondary" data-cancel-order-close>KHÔNG</button>
                <button type="button" class="confirm-btn danger" data-confirm-cancel-order>CÓ</button>
            </div>
        </div>
    </div>

    <footer class="site-footer" id="site-footer"></footer>

    <script defer src="order_actions.js?v=20260324-1"></script>
</body>

</html>
