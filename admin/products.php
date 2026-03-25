<?php
require_once 'admin_check.php';
require_once '../includes/store_helpers.php';

$pageTitle = 'Sản phẩm';

$stmt = $conn->query("
    SELECT id, ten_sp, gia, gia_goc, giam_gia_phan_tram, hinh_chinh, tinh_trang, so_luong_ton, ngay_bao_tri_gan_nhat
    FROM products
    ORDER BY id DESC
");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 24px; flex-wrap: wrap;">
                <h1 class="page-title" style="margin-bottom: 0;">Danh sách sản phẩm</h1>
                <a href="add_product.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Thêm sản phẩm
                </a>
            </div>

            <div class="card">
                <div class="card-body" style="padding: 0;">
                    <?php if (empty($products)): ?>
                        <div class="empty-state">
                            <i class="fas fa-box-open"></i>
                            <p>Chưa có sản phẩm nào. Hãy thêm sản phẩm mới!</p>
                        </div>
                    <?php else: ?>
                        <div style="overflow-x: auto;">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th style="width: 56px;">ID</th>
                                        <th style="width: 84px;">Ảnh</th>
                                        <th>Tên sản phẩm</th>
                                        <th>Giá bán</th>
                                        <th>Tồn kho</th>
                                        <th>Bảo trì</th>
                                        <th>Tình trạng</th>
                                        <th style="text-align: right;">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $p): ?>
                                        <?php
                                        $pricing = get_product_pricing($p);
                                        $stock = inventory_quantity($p);
                                        $maintenance = product_maintenance_meta($p);

                                        $maintenanceBadgeClass = 'badge-success';
                                        $maintenanceLabel = 'Ổn định';
                                        if (!$maintenance['has_date']) {
                                            $maintenanceBadgeClass = 'badge-info';
                                            $maintenanceLabel = 'Chưa đặt lịch';
                                        } elseif ($maintenance['status'] === 'overdue') {
                                            $maintenanceBadgeClass = 'badge-danger';
                                            $maintenanceLabel = 'Quá hạn';
                                        } elseif ($maintenance['status'] === 'soon') {
                                            $maintenanceBadgeClass = 'badge-warning';
                                            $maintenanceLabel = 'Sắp tới hạn';
                                        }
                                        ?>
                                        <tr>
                                            <td><?= (int) $p['id'] ?></td>
                                            <td>
                                                <img
                                                    src="../<?= htmlspecialchars($p['hinh_chinh'] ?: 'images/avatar.png') ?>"
                                                    alt="Product"
                                                    class="product-thumb"
                                                    onerror="this.onerror=null;this.src='../images/avatar.png'">
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($p['ten_sp']) ?></strong>
                                                <?php if ((int) ($p['giam_gia_phan_tram'] ?? 0) > 0): ?>
                                                    <span class="badge badge-danger" style="margin-left: 8px;">-<?= (int) $p['giam_gia_phan_tram'] ?>%</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="discount-value">
                                                    <?= number_format((float) $pricing['price'], 0, ',', '.') ?>đ
                                                </div>
                                                <?php if ($pricing['original_price'] > $pricing['price']): ?>
                                                    <div class="price-original"><?= number_format((float) $pricing['original_price'], 0, ',', '.') ?>đ</div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?= $stock ?></strong>
                                                <?php if ($stock > 0 && $stock <= 3): ?>
                                                    <div style="font-size: 12px; color: var(--warning); margin-top: 4px;">Sắp hết hàng</div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge <?= $maintenanceBadgeClass ?>"><?= $maintenanceLabel ?></span>
                                                <div style="font-size: 12px; color: var(--text-muted); margin-top: 6px; line-height: 1.45;">
                                                    <?php if ($maintenance['has_date'] && $maintenance['last_date'] instanceof DateTimeImmutable && $maintenance['next_date'] instanceof DateTimeImmutable): ?>
                                                        Lần cuối: <?= htmlspecialchars($maintenance['last_date']->format('d/m/Y')) ?><br>
                                                        Tiếp theo: <?= htmlspecialchars($maintenance['next_date']->format('d/m/Y')) ?>
                                                    <?php else: ?>
                                                        Chưa nhập ngày bảo trì
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($stock > 0): ?>
                                                    <span class="badge badge-success">Còn hàng</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Hết hàng</span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="text-align: right;">
                                                <a href="edit_product.php?id=<?= (int) $p['id'] ?>" class="btn btn-sm btn-edit" title="Sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button onclick="confirmDelete(<?= (int) $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['ten_sp'])) ?>')" class="btn btn-sm btn-delete" title="Xóa">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
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

    <div id="deleteModal" class="modal-overlay">
        <div class="modal">
            <div style="font-size: 48px; color: #ef4444; margin-bottom: 16px;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3>Xóa sản phẩm?</h3>
            <p>Bạn có chắc muốn xóa <strong><span id="deleteProductName"></span></strong> không? Hành động này không thể hoàn tác.</p>
            <div class="modal-actions">
                <button onclick="closeModal('deleteModal')" class="btn btn-outline" style="flex:1;">Hủy</button>
                <button id="confirmDeleteBtn" class="btn btn-delete" style="flex:1;">Xóa sản phẩm</button>
            </div>
        </div>
    </div>

    <script src="admin.js"></script>
</body>
</html>
