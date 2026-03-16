<?php
require_once 'admin_check.php';

$pageTitle = 'Tổng quan';

// Thống kê doanh thu
$stmt = $conn->query("SELECT SUM(tong_tien) as total FROM orders WHERE trang_thai IN ('da_giao', 'dang_giao')");
$doanhThu = $stmt->fetchColumn() ?: 0;

// Thống kê đơn hàng chờ xử lý
$stmt = $conn->query("SELECT COUNT(*) FROM orders WHERE trang_thai = 'cho_xac_nhan'");
$donChoXacNhan = $stmt->fetchColumn() ?: 0;

// Thống kê số lượng sản phẩm
$stmt = $conn->query("SELECT COUNT(*) FROM products");
$tongSanPham = $stmt->fetchColumn() ?: 0;

// Thống kê khách hàng
$stmt = $conn->query("SELECT COUNT(*) FROM users WHERE vai_tro = 'khach'");
$tongKhachHang = $stmt->fetchColumn() ?: 0;

// Lấy 5 đơn hàng mới nhất
$stmt = $conn->query("SELECT id, ho_ten_kh, tong_tien, trang_thai, ngay_dat FROM orders ORDER BY ngay_dat DESC LIMIT 5");
$recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mảng label trạng thái đơn hàng
$statusLabels = [
    'cho_xac_nhan' => ['Chờ xác nhận', 'warning'],
    'dang_xu_ly' => ['Đang xử lý', 'info'],
    'dang_giao' => ['Đang giao', 'primary'],
    'da_giao' => ['Đã giao', 'success'],
    'da_huy' => ['Đã hủy', 'danger']
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
                                            <?php $sBadge = $statusLabels[$order['trang_thai']]; ?>
                                            <tr>
                                                <td><strong>#<?= $order['id'] ?></strong></td>
                                                <td><?= htmlspecialchars($order['ho_ten_kh']) ?></td>
                                                <td><strong><?= number_format($order['tong_tien'], 0, ',', '.') ?>đ</strong></td>
                                                <td><span class="badge badge-<?= $sBadge[1] ?>"><?= $sBadge[0] ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="admin.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
                    datasets: [{
                        label: 'Doanh thu (VNĐ)',
                        data: [0, 0, <?= $doanhThu / 2 ?>, <?= $doanhThu ?>, 0, 0, 0], // Sample data, mostly to demonstrate
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
