<?php
require_once 'admin_check.php';

$orderId = (int) ($_GET['id'] ?? 0);

if ($orderId <= 0) {
    header('Location: orders.php');
    exit;
}

$stmt = $conn->prepare("
    SELECT
        o.id,
        o.user_id,
        o.ho_ten_kh,
        o.email_kh,
        o.sdt_kh,
        o.dia_chi_giao,
        o.ghi_chu,
        o.tong_tien,
        o.trang_thai,
        o.ngay_dat,
        u.ho_ten AS ten_tai_khoan,
        u.email AS email_tai_khoan,
        u.so_dien_thoai AS sdt_tai_khoan,
        u.dia_chi AS dia_chi_tai_khoan
    FROM orders o
    LEFT JOIN users u ON u.id = o.user_id
    WHERE o.id = ?
    LIMIT 1
");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: orders.php');
    exit;
}

$itemsStmt = $conn->prepare("
    SELECT
        oi.product_id,
        oi.ten_sp,
        oi.gia,
        oi.so_luong,
        oi.thanh_tien,
        p.hinh_chinh,
        p.tinh_trang
    FROM order_items oi
    LEFT JOIN products p ON p.id = oi.product_id
    WHERE oi.order_id = ?
    ORDER BY oi.id ASC
");
$itemsStmt->execute([$orderId]);
$items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

$statuses = [
    'cho_xac_nhan' => ['Chờ xác nhận', 'warning'],
    'dang_xu_ly' => ['Đang xử lý', 'info'],
    'dang_giao' => ['Đang giao', 'primary'],
    'da_giao' => ['Đã giao', 'success'],
    'da_huy' => ['Đã hủy', 'danger'],
];

$statusMeta = $statuses[$order['trang_thai']] ?? ['Không xác định', 'info'];
$displayPhone = $order['sdt_kh'] ?: ($order['sdt_tai_khoan'] ?: 'Chưa cập nhật');
$displayAddress = $order['dia_chi_giao'] ?: ($order['dia_chi_tai_khoan'] ?: 'Chưa cập nhật');
$displayAccountName = $order['ten_tai_khoan'] ?: $order['ho_ten_kh'];
$displayAccountEmail = $order['email_tai_khoan'] ?: $order['email_kh'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đơn hàng #<?= (int) $order['id'] ?> | Admin Thuận Phát Garden</title>
    <link rel="icon" href="../images/avatar.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Dosis:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>

    <?php include 'components/sidebar.php'; ?>

    <main class="admin-main">
        <?php include 'components/header.php'; ?>

        <div class="admin-content">
            <div class="page-toolbar">
                <div>
                    <h1 class="page-title" style="margin-bottom: 6px;">Chi tiết đơn hàng #<?= (int) $order['id'] ?></h1>
                    <div style="color: var(--text-muted); font-size: 14px;">
                        Tạo lúc <?= date('d/m/Y H:i', strtotime($order['ngay_dat'])) ?>
                    </div>
                </div>
                <a href="orders.php" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Quay lại đơn hàng
                </a>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-user"></i></div>
                    <div class="stat-value stat-value-sm"><?= htmlspecialchars($order['ho_ten_kh']) ?></div>
                    <div class="stat-label">Khách đặt hàng</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
                    <div class="stat-value"><?= number_format((float) $order['tong_tien'], 0, ',', '.') ?>đ</div>
                    <div class="stat-label">Tổng tiền đơn hàng</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-list-check"></i></div>
                    <div class="stat-value"><?= count($items) ?></div>
                    <div class="stat-label">Sản phẩm trong đơn</div>
                </div>
            </div>

            <div class="content-grid">
                <div class="card">
                    <div class="card-header">
                        <h3>Sản phẩm đã mua</h3>
                    </div>
                    <div class="card-body" style="padding: 0;">
                        <?php if (empty($items)): ?>
                            <div class="empty-state">
                                <i class="fas fa-box-open"></i>
                                <p>Đơn hàng này chưa có sản phẩm.</p>
                            </div>
                        <?php else: ?>
                            <div style="overflow-x: auto;">
                                <table class="admin-table">
                                    <thead>
                                        <tr>
                                            <th>Sản phẩm</th>
                                            <th>Đơn giá</th>
                                            <th>Số lượng</th>
                                            <th>Thành tiền</th>
                                            <th>Trạng thái SP</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $item): ?>
                                            <tr>
                                                <td>
                                                    <div class="item-media">
                                                        <img src="../<?= htmlspecialchars($item['hinh_chinh'] ?? 'images/avatar.png') ?>"
                                                             alt="<?= htmlspecialchars($item['ten_sp']) ?>"
                                                             class="product-thumb"
                                                             onerror="this.onerror=null;this.src='../images/avatar.png';">
                                                        <div>
                                                            <div style="font-weight: 600;"><?= htmlspecialchars($item['ten_sp']) ?></div>
                                                            <div style="font-size: 12px; color: var(--text-muted);">Mã SP #<?= (int) $item['product_id'] ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?= number_format((float) $item['gia'], 0, ',', '.') ?>đ</td>
                                                <td>x<?= (int) $item['so_luong'] ?></td>
                                                <td><strong><?= number_format((float) $item['thanh_tien'], 0, ',', '.') ?>đ</strong></td>
                                                <td>
                                                    <?php if (($item['tinh_trang'] ?? '') === 'het_hang'): ?>
                                                        <span class="badge badge-danger">Hết hàng</span>
                                                    <?php elseif (!empty($item['tinh_trang'])): ?>
                                                        <span class="badge badge-success">Còn hàng</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-info">Không còn trong catalog</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div>
                    <div class="card">
                        <div class="card-header">
                            <h3>Thông tin khách hàng</h3>
                        </div>
                        <div class="card-body">
                            <div class="detail-list">
                                <div class="detail-item">
                                    <span class="detail-label">Tên khách</span>
                                    <strong><?= htmlspecialchars($order['ho_ten_kh']) ?></strong>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Email giao hàng</span>
                                    <strong><?= htmlspecialchars($order['email_kh']) ?></strong>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Số điện thoại</span>
                                    <strong><?= htmlspecialchars($displayPhone) ?></strong>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Địa chỉ giao hàng</span>
                                    <strong><?= nl2br(htmlspecialchars($displayAddress)) ?></strong>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Trạng thái đơn</span>
                                    <span class="badge badge-<?= $statusMeta[1] ?>"><?= $statusMeta[0] ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3>Liên kết tài khoản</h3>
                        </div>
                        <div class="card-body">
                            <div class="detail-list">
                                <div class="detail-item">
                                    <span class="detail-label">ID tài khoản</span>
                                    <strong><?= $order['user_id'] ? '#' . (int) $order['user_id'] : 'Khách vãng lai / không còn tài khoản' ?></strong>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Tên tài khoản</span>
                                    <strong><?= htmlspecialchars($displayAccountName) ?></strong>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Email tài khoản</span>
                                    <strong><?= htmlspecialchars($displayAccountEmail) ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3>Ghi chú và thanh toán</h3>
                        </div>
                        <div class="card-body">
                            <div class="detail-list">
                                <div class="detail-item">
                                    <span class="detail-label">Tổng tiền</span>
                                    <strong><?= number_format((float) $order['tong_tien'], 0, ',', '.') ?>đ</strong>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Tình trạng hiện có</span>
                                    <strong><?= $order['trang_thai'] === 'da_giao' ? 'Đơn đã giao' : 'Chưa có cột thanh toán riêng' ?></strong>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Ghi chú khách hàng</span>
                                    <strong><?= nl2br(htmlspecialchars($order['ghi_chu'] ?: 'Không có ghi chú')) ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="admin.js"></script>
</body>
</html>
