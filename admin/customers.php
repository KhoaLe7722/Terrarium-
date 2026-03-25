<?php
require_once 'admin_check.php';

function u(string $value): string
{
    return json_decode('"' . $value . '"', true);
}

$pageTitle = u('Kh\u00e1ch h\u00e0ng');

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
    <title><?= $pageTitle ?> | Admin <?= u('Thu\u1eadn Ph\u00e1t') ?> Garden</title>
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
            <h1 class="page-title"><?= u('Danh s\u00e1ch kh\u00e1ch h\u00e0ng') ?></h1>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-value"><?= $totalCustomers ?></div>
                    <div class="stat-label"><?= u('T\u00e0i kho\u1ea3n kh\u00e1ch h\u00e0ng') ?></div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-receipt"></i></div>
                    <div class="stat-value"><?= $customersWithOrders ?></div>
                    <div class="stat-label"><?= u('Kh\u00e1ch \u0111\u00e3 ph\u00e1t sinh \u0111\u01a1n') ?></div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-wallet"></i></div>
                    <div class="stat-value"><?= number_format($totalRevenueFromCustomers, 0, ',', '.') ?><?= u('\u0111') ?></div>
                    <div class="stat-label"><?= u('Doanh thu t\u1eeb \u0111\u01a1n \u0111ang giao v\u00e0 \u0111\u00e3 giao') ?></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3><?= u('Th\u00f4ng tin kh\u00e1ch h\u00e0ng') ?></h3>
                </div>
                <div class="card-body" style="padding: 0;">
                    <?php if (empty($customers)): ?>
                        <div class="empty-state">
                            <i class="fas fa-user-slash"></i>
                            <p><?= u('Ch\u01b0a c\u00f3 kh\u00e1ch h\u00e0ng n\u00e0o \u0111\u0103ng k\u00fd.') ?></p>
                        </div>
                    <?php else: ?>
                        <div style="overflow-x: auto;">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th><?= u('Kh\u00e1ch h\u00e0ng') ?></th>
                                        <th><?= u('Li\u00ean h\u1ec7') ?></th>
                                        <th><?= u('\u0110\u1ecba ch\u1ec9') ?></th>
                                        <th><?= u('\u0110\u01a1n h\u00e0ng') ?></th>
                                        <th><?= u('T\u1ed5ng chi ti\u00eau') ?></th>
                                        <th><?= u('\u0110\u01a1n g\u1ea7n nh\u1ea5t') ?></th>
                                        <th><?= u('Ng\u00e0y t\u1ea1o') ?></th>
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
                                                <div><i class="fas fa-phone fa-fw text-muted"></i> <?= htmlspecialchars($customer['so_dien_thoai'] ?: u('Ch\u01b0a c\u1eadp nh\u1eadt')) ?></div>
                                            </td>
                                            <td style="max-width: 260px;">
                                                <div class="text-clamp-3" title="<?= htmlspecialchars($customer['dia_chi'] ?: u('Ch\u01b0a c\u1eadp nh\u1eadt')) ?>">
                                                    <?= nl2br(htmlspecialchars($customer['dia_chi'] ?: u('Ch\u01b0a c\u1eadp nh\u1eadt'))) ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ((int) $customer['tong_don_hang'] > 0): ?>
                                                    <span class="badge badge-primary"><?= (int) $customer['tong_don_hang'] ?> <?= u('\u0111\u01a1n') ?></span>
                                                <?php else: ?>
                                                    <span class="badge badge-info"><?= u('Ch\u01b0a mua') ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?= number_format((float) $customer['tong_chi_tieu'], 0, ',', '.') ?><?= u('\u0111') ?></strong>
                                            </td>
                                            <td>
                                                <?= $customer['don_gan_nhat'] ? date('d/m/Y H:i', strtotime($customer['don_gan_nhat'])) : u('Ch\u01b0a c\u00f3') ?>
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