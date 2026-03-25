<?php
require_once 'admin_check.php';
require_once '../includes/store_helpers.php';

$pageTitle = 'Tổng quan';

$doanhThu = (float) ($conn->query("
    SELECT SUM(tong_tien) AS total
    FROM orders
    WHERE trang_thai IN ('da_giao', 'dang_giao')
")->fetchColumn() ?: 0);

$donChoXacNhan = (int) ($conn->query("
    SELECT COUNT(*)
    FROM orders
    WHERE trang_thai = 'cho_xac_nhan'
")->fetchColumn() ?: 0);

$tongSanPham = (int) ($conn->query("SELECT COUNT(*) FROM products")->fetchColumn() ?: 0);
$tongKhachHang = (int) ($conn->query("SELECT COUNT(*) FROM users WHERE vai_tro = 'khach'")->fetchColumn() ?: 0);

$sanPhamSapHet = (int) ($conn->query("
    SELECT COUNT(*)
    FROM products
    WHERE so_luong_ton > 0 AND so_luong_ton <= 3
")->fetchColumn() ?: 0);

$sanPhamSapBaoTri = (int) ($conn->query("
    SELECT COUNT(*)
    FROM products
    WHERE ngay_bao_tri_gan_nhat IS NOT NULL
      AND DATE_ADD(ngay_bao_tri_gan_nhat, INTERVAL 2 MONTH) <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
")->fetchColumn() ?: 0);

$sanPhamChuaDatLich = (int) ($conn->query("
    SELECT COUNT(*)
    FROM products
    WHERE ngay_bao_tri_gan_nhat IS NULL
")->fetchColumn() ?: 0);

$recentOrders = $conn->query("
    SELECT id, ho_ten_kh, tong_tien, trang_thai, ngay_dat
    FROM orders
    ORDER BY ngay_dat DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

$maintenanceProducts = $conn->query("
    SELECT
        id,
        ten_sp,
        so_luong_ton,
        ngay_bao_tri_gan_nhat,
        DATE_ADD(ngay_bao_tri_gan_nhat, INTERVAL 2 MONTH) AS ngay_bao_tri_tiep_theo,
        DATEDIFF(DATE_ADD(ngay_bao_tri_gan_nhat, INTERVAL 2 MONTH), CURDATE()) AS so_ngay_con_lai
    FROM products
    WHERE ngay_bao_tri_gan_nhat IS NOT NULL
      AND DATE_ADD(ngay_bao_tri_gan_nhat, INTERVAL 2 MONTH) <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
    ORDER BY so_ngay_con_lai ASC, id ASC
    LIMIT 6
")->fetchAll(PDO::FETCH_ASSOC);

$monthlyRows = $conn->query("
    SELECT DATE_FORMAT(ngay_dat, '%Y-%m') AS month_key, SUM(tong_tien) AS total
    FROM orders
    WHERE trang_thai IN ('dang_giao', 'da_giao')
      AND ngay_dat >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(ngay_dat, '%Y-%m')
    ORDER BY month_key ASC
")->fetchAll(PDO::FETCH_ASSOC);

$monthlyMap = [];
foreach ($monthlyRows as $row) {
    $monthlyMap[$row['month_key']] = (float) $row['total'];
}

$chartLabels = [];
$chartData = [];
for ($offset = 6; $offset >= 0; $offset--) {
    $monthKey = date('Y-m', strtotime("-{$offset} month"));
    $chartLabels[] = 'T' . date('m', strtotime($monthKey . '-01'));
    $chartData[] = $monthlyMap[$monthKey] ?? 0;
}

$statusLabels = [
    'cho_xac_nhan' => ['Chờ xác nhận', 'warning'],
    'dang_xu_ly' => ['Đang xử lý', 'info'],
    'dang_giao' => ['Đang giao', 'primary'],
    'da_giao' => ['Đã giao', 'success'],
    'da_huy' => ['Đã hủy', 'danger'],
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | Admin Thuận Phát Garden</title>
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
            <h1 class="page-title">Bảng điều khiển</h1>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-wallet"></i></div>
                    <div class="stat-value"><?= number_format($doanhThu, 0, ',', '.') ?>đ</div>
                    <div class="stat-label">Tổng doanh thu</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-box"></i></div>
                    <div class="stat-value"><?= $donChoXacNhan ?></div>
                    <div class="stat-label">Đơn chờ xác nhận</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-leaf"></i></div>
                    <div class="stat-value"><?= $tongSanPham ?></div>
                    <div class="stat-label">Sản phẩm</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-value"><?= $tongKhachHang ?></div>
                    <div class="stat-label">Khách hàng</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-triangle-exclamation"></i></div>
                    <div class="stat-value"><?= $sanPhamSapHet ?></div>
                    <div class="stat-label">Sắp hết hàng</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="stat-value"><?= $sanPhamSapBaoTri ?></div>
                    <div class="stat-label">Sắp đến hạn bảo trì</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <div class="stat-value"><?= $sanPhamChuaDatLich ?></div>
                    <div class="stat-label">Chưa đặt ngày bảo trì</div>
                </div>
            </div>

            <div class="content-grid">
                <div class="card">
                    <div class="card-header">
                        <h3>Biểu đồ doanh thu</h3>
                    </div>
                    <div class="card-body chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>Đơn hàng gần đây</h3>
                        <a href="orders.php" class="btn btn-sm btn-outline">Xem tất cả</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recentOrders)): ?>
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <p>Chưa có đơn hàng nào.</p>
                            </div>
                        <?php else: ?>
                            <div style="overflow-x: auto;">
                                <table class="admin-table">
                                    <thead>
                                        <tr>
                                            <th>Mã ĐH</th>
                                            <th>Khách hàng</th>
                                            <th>Tổng tiền</th>
                                            <th>Trạng thái</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentOrders as $order): ?>
                                            <?php $badge = $statusLabels[$order['trang_thai']] ?? ['Không xác định', 'info']; ?>
                                            <tr>
                                                <td><strong>#<?= (int) $order['id'] ?></strong></td>
                                                <td><?= htmlspecialchars($order['ho_ten_kh']) ?></td>
                                                <td><strong><?= number_format((float) $order['tong_tien'], 0, ',', '.') ?>đ</strong></td>
                                                <td><span class="badge badge-<?= $badge[1] ?>"><?= htmlspecialchars($badge[0]) ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Sản phẩm cần chăm sóc</h3>
                    <a href="products.php" class="btn btn-sm btn-outline">Mở danh sách sản phẩm</a>
                </div>
                <div class="card-body">
                    <?php if (empty($maintenanceProducts)): ?>
                        <div class="empty-state">
                            <i class="fas fa-seedling"></i>
                            <p>Chưa có sản phẩm nào sắp đến hạn bảo trì trong 7 ngày tới.</p>
                        </div>
                    <?php else: ?>
                        <div style="overflow-x: auto;">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Tồn kho</th>
                                        <th>Lần bảo trì gần nhất</th>
                                        <th>Hạn tiếp theo</th>
                                        <th>Cảnh báo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($maintenanceProducts as $item): ?>
                                        <tr>
                                            <td>
                                                <a href="edit_product.php?id=<?= (int) $item['id'] ?>" style="color: inherit; text-decoration: none; font-weight: 600;">
                                                    <?= htmlspecialchars($item['ten_sp']) ?>
                                                </a>
                                            </td>
                                            <td><?= (int) $item['so_luong_ton'] ?></td>
                                            <td><?= htmlspecialchars(date('d/m/Y', strtotime((string) $item['ngay_bao_tri_gan_nhat']))) ?></td>
                                            <td><?= htmlspecialchars(date('d/m/Y', strtotime((string) $item['ngay_bao_tri_tiep_theo']))) ?></td>
                                            <td>
                                                <?php if ((int) $item['so_ngay_con_lai'] < 0): ?>
                                                    <span class="badge badge-danger">Quá hạn <?= abs((int) $item['so_ngay_con_lai']) ?> ngày</span>
                                                <?php elseif ((int) $item['so_ngay_con_lai'] === 0): ?>
                                                    <span class="badge badge-danger">Đến hạn hôm nay</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning">Còn <?= (int) $item['so_ngay_con_lai'] ?> ngày</span>
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
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="admin.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($chartLabels) ?>,
                    datasets: [{
                        label: 'Doanh thu (VNĐ)',
                        data: <?= json_encode($chartData) ?>,
                        backgroundColor: 'rgba(84, 121, 74, 0.7)',
                        borderColor: '#54794a',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(200, 200, 200, 0.2)' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }
    });
    </script>
</body>
</html>
