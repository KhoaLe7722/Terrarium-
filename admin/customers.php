<?php
require_once 'admin_check.php';

$pageTitle = 'Khach hang';

$stmt = $conn->query("
    SELECT
        u.id,
        u.ho_ten,
        u.email,
        u.so_dien_thoai,
        u.dia_chi,
        u.ngay_tao,
        COUNT(o.id) AS tong_don_hang,
        COALESCE(SUM(CASE WHEN o.trang_thai IN ('dang_giao', 'da_giao') THEN o.tong_tien ELSE 0 END), 0) AS tong_chi_tieu,
        MAX(o.ngay_dat) AS don_gan_nhat
    FROM users u
    LEFT JOIN orders o ON o.user_id = u.id
    WHERE u.vai_tro = 'khach'
    GROUP BY u.id, u.ho_ten, u.email, u.so_dien_thoai, u.dia_chi, u.ngay_tao
    ORDER BY u.ngay_tao DESC, u.id DESC
");
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalCustomers = count($customers);
$customersWithOrders = 0;
$totalRevenueFromCustomers = 0;

foreach ($customers as $customer) {
    if ((int) $customer['tong_don_hang'] > 0) {
        $customersWithOrders++;
    }

    $totalRevenueFromCustomers += (float) $customer['tong_chi_tieu'];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> | Admin Thuận Phát Garden</title>
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
            <h1 class="page-title">Danh sách khách hàng</h1>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-value"><?= $totalCustomers ?></div>
                    <div class="stat-label">Tài khoản khách hàng</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-receipt"></i></div>
                    <div class="stat-value"><?= $customersWithOrders ?></div>
                    <div class="stat-label">Khách đã phát sinh đơn</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-wallet"></i></div>
                    <div class="stat-value"><?= number_format($totalRevenueFromCustomers, 0, ',', '.') ?>đ</div>
                    <div class="stat-label">Doanh thu từ đơn đang giao và đã giao</div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Thông tin khách hàng</h3>
                </div>
                <div class="card-body" style="padding: 0;">
                    <?php if (empty($customers)): ?>
                        <div class="empty-state">
                            <i class="fas fa-user-slash"></i>
                            <p>Chưa có khách hàng nào đăng ký.</p>
                        </div>
                    <?php else: ?>
                        <div style="overflow-x: auto;">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Khách hàng</th>
                                        <th>Liên hệ</th>
                                        <th>Địa chỉ</th>
                                        <th>Đơn hàng</th>
                                        <th>Tổng chi tiêu</th>
                                        <th>Đơn gần nhất</th>
                                        <th>Ngày tạo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($customers as $customer): ?>
                                        <tr>
                                            <td>
                                                <div style="font-weight: 600;"><?= htmlspecialchars($customer['ho_ten']) ?></div>
                                                <div style="font-size: 12px; color: var(--text-muted);">ID #<?= (int) $customer['id'] ?></div>
                                            </td>
                                            <td style="font-size: 13px;">
                                                <div><i class="fas fa-envelope fa-fw text-muted"></i> <?= htmlspecialchars($customer['email']) ?></div>
                                                <div><i class="fas fa-phone fa-fw text-muted"></i> <?= htmlspecialchars($customer['so_dien_thoai'] ?: 'Chưa cập nhật') ?></div>
                                            </td>
                                            <td style="max-width: 260px;">
                                                <div class="text-clamp-3" title="<?= htmlspecialchars($customer['dia_chi'] ?: 'Chưa cập nhật') ?>">
                                                    <?= nl2br(htmlspecialchars($customer['dia_chi'] ?: 'Chưa cập nhật')) ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ((int) $customer['tong_don_hang'] > 0): ?>
                                                    <span class="badge badge-primary"><?= (int) $customer['tong_don_hang'] ?> đơn</span>
                                                <?php else: ?>
                                                    <span class="badge badge-info">Chưa mua</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?= number_format((float) $customer['tong_chi_tieu'], 0, ',', '.') ?>đ</strong>
                                            </td>
                                            <td>
                                                <?= $customer['don_gan_nhat'] ? date('d/m/Y H:i', strtotime($customer['don_gan_nhat'])) : 'Chưa có' ?>
                                            </td>
                                            <td>
                                                <?= date('d/m/Y H:i', strtotime($customer['ngay_tao'])) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="admin.js"></script>
</body>
</html>
