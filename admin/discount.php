<?php
require_once 'admin_check.php';

$pageTitle = 'Quản lý giảm giá';

// Lấy ds sp
$stmt = $conn->query("SELECT id, ten_sp, gia, gia_goc, giam_gia_phan_tram, giam_gia_bat_dau, giam_gia_ket_thuc, hinh_chinh FROM products ORDER BY id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <style>
        .discount-form-card {
            background: rgba(84, 121, 74, 0.05);
            border: 1px solid rgba(84, 121, 74, 0.2);
            border-radius: var(--radius-sm);
            padding: 16px;
            margin-bottom: 24px;
            display: none;
        }
        .discount-form-card.active { display: block; }
    </style>
</head>
<body>

    <?php include 'components/sidebar.php'; ?>

    <main class="admin-main">
        <?php include 'components/header.php'; ?>

        <div class="admin-content">
            <h1 class="page-title">Quản lý Giảm Giá Mừng Khai Trương</h1>

            <!-- Form áp dụng giảm giá được JS mở -->
            <div id="discountFormPanel" class="discount-form-card">
                <h3 style="margin-bottom: 16px; color: #54794a;"><i class="fas fa-tags"></i> Thiết lập giảm giá: <span id="dfName"></span></h3>
                <form id="frmDiscount">
                    <input type="hidden" id="dfId" name="id">
                    <input type="hidden" id="dfGiaGoc" name="gia_goc">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Phần trăm giảm (%)</label>
                            <input type="number" id="dfPercent" name="percent" class="form-control" min="0" max="100" value="0" required>
                            <small class="form-hint">Nhập 0 để tắt giảm giá. Giá bán sau giảm: <strong id="dfNewPrice" style="color:#ef4444;">--</strong></small>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Hẹn giờ Bắt đầu (không bắt buộc)</label>
                            <input type="datetime-local" id="dfStart" name="start" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Hẹn giờ Kết thúc (không bắt buộc)</label>
                            <input type="datetime-local" id="dfEnd" name="end" class="form-control">
                        </div>
                    </div>

                    <div style="text-align: right; margin-top: 10px;">
                        <button type="button" class="btn btn-outline" onclick="closeDiscountForm()">Hủy</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Áp dụng</button>
                    </div>
                </form>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Danh sách sản phẩm</h3>
                </div>
                <div class="card-body" style="padding: 0;">
                    <div style="overflow-x: auto;">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Cập nhật</th>
                                    <th>Sản phẩm</th>
                                    <th>Trạng thái giảm giá</th>
                                    <th>Giá gốc</th>
                                    <th>Giá bán hiện tại</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $p): 
                                    $isDiscounting = $p['giam_gia_phan_tram'] > 0;
                                    $goc = $p['gia_goc'] ? $p['gia_goc'] : $p['gia']; // Nếu ko có giá gốc thì coi giá bán là giá gốc
                                ?>
                                    <tr>
                                        <td>
                                            <button onclick="openDiscountForm('<?= $p['id'] ?>', '<?= htmlspecialchars(addslashes($p['ten_sp'])) ?>', <?= $goc ?>, <?= $p['giam_gia_phan_tram'] ?>, '<?= $p['giam_gia_bat_dau'] ?>', '<?= $p['giam_gia_ket_thuc'] ?>')" 
                                                    class="btn btn-sm btn-outline">
                                                <i class="fas fa-cog"></i> Sửa
                                            </button>
                                        </td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 12px;">
                                                <img src="../<?= htmlspecialchars($p['hinh_chinh'] ?? 'images/placeholder.jpg') ?>" 
                                                     alt="thumb" class="product-thumb">
                                                <strong><?= htmlspecialchars($p['ten_sp']) ?></strong>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($isDiscounting): ?>
                                                <span class="badge badge-warning" style="font-size:13px;">
                                                    <i class="fas fa-tag"></i> -<?= $p['giam_gia_phan_tram'] ?>%
                                                </span>
                                                <div style="font-size: 11px; margin-top: 4px; color: #666;">
                                                    <?php if ($p['giam_gia_bat_dau'] || $p['giam_gia_ket_thuc']): ?>
                                                        <?= $p['giam_gia_bat_dau'] ? date('d/m/y H:i', strtotime($p['giam_gia_bat_dau'])) : 'Bây giờ' ?> 
                                                        &rarr; 
                                                        <?= $p['giam_gia_ket_thuc'] ? date('d/m/y H:i', strtotime($p['giam_gia_ket_thuc'])) : 'Vô thời hạn' ?>
                                                    <?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <span style="color:#999;font-size:12px;">Không có</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= number_format($goc, 0, ',', '.') ?>đ</td>
                                        <td>
                                            <strong><?= number_format($p['gia'], 0, ',', '.') ?>đ</strong>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="admin.js"></script>
    <script>
        function openDiscountForm(id, name, giaGoc, percent, start, end) {
            document.getElementById('discountFormPanel').classList.add('active');
            document.getElementById('dfId').value = id;
            document.getElementById('dfName').textContent = name;
            document.getElementById('dfGiaGoc').value = giaGoc;
            document.getElementById('dfPercent').value = percent || 0;
            
            // Format datetime-local requires YYYY-MM-DDThh:mm
            document.getElementById('dfStart').value = start && start.includes('0000') == false ? start.replace(' ', 'T').slice(0, 16) : '';
            document.getElementById('dfEnd').value = end && end.includes('0000') == false ? end.replace(' ', 'T').slice(0, 16) : '';
            
            calcNewPrice();
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function closeDiscountForm() {
            document.getElementById('discountFormPanel').classList.remove('active');
        }

        function calcNewPrice() {
            let goc = parseFloat(document.getElementById('dfGiaGoc').value) || 0;
            let p = parseFloat(document.getElementById('dfPercent').value) || 0;
            let newPrice = goc * (1 - p/100);
            document.getElementById('dfNewPrice').textContent = newPrice.toLocaleString('vi-VN') + 'đ';
        }

        document.getElementById('dfPercent').addEventListener('input', calcNewPrice);

        document.getElementById('frmDiscount').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btn = this.querySelector('button[type="submit"]');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';
            btn.disabled = true;

            const data = {
                id: document.getElementById('dfId').value,
                percent: document.getElementById('dfPercent').value,
                start: document.getElementById('dfStart').value || null,
                end: document.getElementById('dfEnd').value || null
            };

            fetch('api/update_discount.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    location.reload();
                } else {
                    alert(res.message || 'Lỗi lưu dữ liệu');
                    btn.innerHTML = '<i class="fas fa-save"></i> Áp dụng';
                    btn.disabled = false;
                }
            })
            .catch(err => {
                alert('Lỗi kết nối');
                btn.innerHTML = '<i class="fas fa-save"></i> Áp dụng';
                btn.disabled = false;
            });
        });
    </script>
</body>
</html>
