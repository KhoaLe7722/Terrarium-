<?php
require_once 'admin_check.php';

$pageTitle = 'Đơn hàng';

// Lấy danh sách đơn hàng lấy mới nhất
$stmt = $conn->query("
    SELECT o.id, o.ho_ten_kh, o.sdt_kh, o.dia_chi_giao, o.tong_tien, o.trang_thai, o.ngay_dat
    FROM orders o
    ORDER BY o.ngay_dat DESC
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Các trạng thái
$statuses = [
    'cho_xac_nhan' => 'Chờ xác nhận',
    'dang_xu_ly' => 'Đang xử lý',
    'dang_giao' => 'Đang giao',
    'da_giao' => 'Đã giao',
    'da_huy' => 'Đã hủy'
];
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
            <h1 class="page-title">Quản lý Đơn hàng</h1>

            <div class="card">
                <div class="card-body" style="padding: 0;">
                    <?php if (empty($orders)): ?>
                        <div class="empty-state">
                            <i class="fas fa-box-open"></i>
                            <p>Không có đơn hàng nào.</p>
                        </div>
                    <?php else: ?>
                        <div style="overflow-x: auto;">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Mã ĐH</th>
                                        <th>Khách hàng</th>
                                        <th>Thông tin liên hệ</th>
                                        <th>Tổng tiền</th>
                                        <th>Ngày tạo</th>
                                        <th style="min-width: 150px;">Trạng thái</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $o): ?>
                                        <tr>
                                            <td><strong>#<?= $o['id'] ?></strong></td>
                                            <td>
                                                <div style="font-weight: 500;"><?= htmlspecialchars($o['ho_ten_kh']) ?></div>
                                            </td>
                                            <td style="font-size: 13px;">
                                                <div><i class="fas fa-phone fa-fw text-muted"></i> <?= htmlspecialchars($o['sdt_kh'] ?? 'Không có') ?></div>
                                                <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;" title="<?= htmlspecialchars($o['dia_chi_giao']) ?>">
                                                    <i class="fas fa-map-marker-alt fa-fw text-muted"></i> <?= htmlspecialchars($o['dia_chi_giao']) ?>
                                                </div>
                                            </td>
                                            <td>
                                                <strong style="color:var(--danger);"><?= number_format($o['tong_tien'], 0, ',', '.') ?>đ</strong>
                                            </td>
                                            <td style="font-size: 13px; color: var(--text-muted);">
                                                <?= date('d/m/Y H:i', strtotime($o['ngay_dat'])) ?>
                                            </td>
                                            <td>
                                                <select class="status-select" 
                                                        data-order-id="<?= $o['id'] ?>" 
                                                        onchange="updateOrderStatus(<?= $o['id'] ?>, this.value)">
                                                    <?php foreach ($statuses as $val => $label): ?>
                                                        <option value="<?= $val ?>" <?= $o['trang_thai'] == $val ? 'selected' : '' ?>>
                                                            <?= $label ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td>
                                                <a href="order_detail.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-outline">
                                                    Chi tiết
                                                </a>
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
