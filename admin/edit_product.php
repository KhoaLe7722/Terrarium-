<?php
require_once 'admin_check.php';
require_once '../includes/store_helpers.php';

$pageTitle = 'Sửa sản phẩm';
$message = '';
$messageType = '';

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: products.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: products.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenSp = trim((string) ($_POST['ten_sp'] ?? ''));
    $gia = str_replace('.', '', (string) ($_POST['gia'] ?? '0'));
    $giaGoc = str_replace('.', '', (string) ($_POST['gia_goc'] ?? '0'));
    $giamGiaPhanTram = (int) ($_POST['giam_gia_phan_tram'] ?? 0);
    $soLuongTon = max(0, (int) ($_POST['so_luong_ton'] ?? 0));
    $ngayBaoTriGanNhat = trim((string) ($_POST['ngay_bao_tri_gan_nhat'] ?? ''));
    $ngayBaoTriGanNhat = $ngayBaoTriGanNhat !== '' ? $ngayBaoTriGanNhat : null;
    $tinhTrang = $soLuongTon > 0 ? 'con_hang' : 'het_hang';
    $moTa = (string) ($_POST['mo_ta'] ?? '');

    if ($giaGoc === '' || $giaGoc === '0') {
        $giaGoc = $gia;
    }

    if ($giamGiaPhanTram > 0) {
        $gia = $giaGoc * (1 - $giamGiaPhanTram / 100);
    }

    if ($tenSp === '' || $giaGoc === '' || $giaGoc === '0') {
        $message = 'Vui lòng nhập tên sản phẩm và giá bán.';
        $messageType = 'error';
    } else {
        $hinhChinh = $product['hinh_chinh'];

        if (isset($_FILES['hinh_chinh']) && $_FILES['hinh_chinh']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileInfo = pathinfo($_FILES['hinh_chinh']['name']);
            $extension = $fileInfo['extension'] ?? 'jpg';
            $fileName = uniqid('sp_', true) . '.' . $extension;

            if (move_uploaded_file($_FILES['hinh_chinh']['tmp_name'], $uploadDir . $fileName)) {
                if ($hinhChinh && strpos($hinhChinh, 'uploads/') === 0) {
                    $oldPath = '../' . $hinhChinh;
                    if (is_file($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $hinhChinh = 'uploads/' . $fileName;
            }
        }

        try {
            $conn->beginTransaction();

            $updateStmt = $conn->prepare("
                UPDATE products
                SET ten_sp = ?, gia = ?, gia_goc = ?, giam_gia_phan_tram = ?, hinh_chinh = ?,
                    mo_ta = ?, so_luong_ton = ?, ngay_bao_tri_gan_nhat = ?, tinh_trang = ?
                WHERE id = ?
            ");
            $updateStmt->execute([
                $tenSp,
                $gia,
                $giaGoc,
                $giamGiaPhanTram,
                $hinhChinh,
                $moTa,
                $soLuongTon,
                $ngayBaoTriGanNhat,
                $tinhTrang,
                $id,
            ]);

            if (isset($_FILES['thu_vien_anh'])) {
                $uploadDir = '../uploads/';
                foreach ($_FILES['thu_vien_anh']['tmp_name'] as $key => $tmpName) {
                    if (($_FILES['thu_vien_anh']['error'][$key] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                        continue;
                    }

                    $fileInfo = pathinfo($_FILES['thu_vien_anh']['name'][$key]);
                    $extension = $fileInfo['extension'] ?? 'jpg';
                    $fileName = uniqid('gallery_', true) . '_' . $key . '.' . $extension;

                    if (move_uploaded_file($tmpName, $uploadDir . $fileName)) {
                        $imgPath = 'uploads/' . $fileName;
                        $galleryStmt = $conn->prepare("INSERT INTO product_images (product_id, duong_dan) VALUES (?, ?)");
                        $galleryStmt->execute([$id, $imgPath]);
                    }
                }
            }

            $conn->commit();

            $message = 'Cập nhật thành công.';
            $messageType = 'success';

            $product['ten_sp'] = $tenSp;
            $product['gia'] = $gia;
            $product['gia_goc'] = $giaGoc;
            $product['giam_gia_phan_tram'] = $giamGiaPhanTram;
            $product['hinh_chinh'] = $hinhChinh;
            $product['mo_ta'] = $moTa;
            $product['so_luong_ton'] = $soLuongTon;
            $product['ngay_bao_tri_gan_nhat'] = $ngayBaoTriGanNhat;
            $product['tinh_trang'] = $tinhTrang;
        } catch (PDOException $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            $message = 'Lỗi lưu dữ liệu: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

$galleryStmt = $conn->prepare("SELECT id, duong_dan FROM product_images WHERE product_id = ? ORDER BY id ASC");
$galleryStmt->execute([$id]);
$gallery = $galleryStmt->fetchAll(PDO::FETCH_ASSOC);

$maintenanceMeta = product_maintenance_meta($product);
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
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
</head>
<body>

    <?php include 'components/sidebar.php'; ?>

    <main class="admin-main">
        <?php include 'components/header.php'; ?>

        <div class="admin-content">
            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
                <a href="products.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Trở lại</a>
                <h1 class="page-title" style="margin-bottom: 0;">Sửa sản phẩm #<?= $id ?></h1>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType === 'success' ? 'success' : 'error' ?>">
                    <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <div class="card" style="max-width: 880px;">
                <div class="card-body">
                    <form action="edit_product.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="ten_sp">Tên sản phẩm <span style="color:red">*</span></label>
                            <input type="text" id="ten_sp" name="ten_sp" class="form-control" required value="<?= htmlspecialchars($product['ten_sp']) ?>">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="gia_goc">Giá gốc (VNĐ) <span style="color:red">*</span></label>
                                <input
                                    type="text"
                                    id="gia_goc"
                                    name="gia_goc"
                                    class="form-control currency-input"
                                    required
                                    value="<?= number_format((float) ($product['gia_goc'] ?: $product['gia']), 0, '', '.') ?>">
                            </div>
                            <div class="form-group">
                                <label for="giam_gia_phan_tram">Giảm giá (%)</label>
                                <input
                                    type="number"
                                    id="giam_gia_phan_tram"
                                    name="giam_gia_phan_tram"
                                    class="form-control"
                                    min="0"
                                    max="100"
                                    value="<?= htmlspecialchars((string) ($product['giam_gia_phan_tram'] ?? '0')) ?>">
                            </div>
                            <div class="form-group">
                                <label>Giá sau giảm</label>
                                <input type="text" id="gia_hien_thi" class="form-control" readonly style="background: #f1f5f9;">
                                <input type="hidden" name="gia" id="gia_fixed" value="<?= htmlspecialchars((string) $product['gia']) ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="so_luong_ton">Số lượng còn <span style="color:red">*</span></label>
                                <input
                                    type="number"
                                    id="so_luong_ton"
                                    name="so_luong_ton"
                                    class="form-control"
                                    min="0"
                                    required
                                    value="<?= htmlspecialchars((string) ($product['so_luong_ton'] ?? 0)) ?>">
                                <div class="form-hint">
                                    Trạng thái hiện tại:
                                    <strong><?= (int) ($product['so_luong_ton'] ?? 0) > 0 ? 'Còn hàng' : 'Hết hàng' ?></strong>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="ngay_bao_tri_gan_nhat">Ngày bảo trì gần nhất</label>
                                <input
                                    type="date"
                                    id="ngay_bao_tri_gan_nhat"
                                    name="ngay_bao_tri_gan_nhat"
                                    class="form-control"
                                    value="<?= htmlspecialchars((string) ($product['ngay_bao_tri_gan_nhat'] ?? '')) ?>">
                                <div class="form-hint">
                                    <?php if ($maintenanceMeta['has_date'] && $maintenanceMeta['next_date'] instanceof DateTimeImmutable): ?>
                                        Chu kỳ tiếp theo: <?= htmlspecialchars($maintenanceMeta['next_date']->format('d/m/Y')) ?>
                                    <?php else: ?>
                                        Chưa có lịch bảo trì cho sản phẩm này.
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="hinh_chinh">Thay đổi ảnh đại diện</label>
                                <input type="file" id="hinh_chinh" name="hinh_chinh" class="form-control" accept="image/*" style="padding: 7px 10px;">

                                <div style="margin-top: 12px; display: flex; gap: 16px; flex-wrap: wrap;">
                                    <div>
                                        <div style="font-size: 12px; color: #666; margin-bottom: 4px;">Ảnh hiện tại</div>
                                        <img
                                            src="../<?= htmlspecialchars($product['hinh_chinh'] ?: 'images/avatar.png') ?>"
                                            class="img-preview"
                                            alt="Current"
                                            onerror="this.onerror=null;this.src='../images/avatar.png'">
                                    </div>
                                    <div>
                                        <div style="font-size: 12px; color: #666; margin-bottom: 4px;">Ảnh mới</div>
                                        <img id="imgPreview" class="img-preview" style="display: none;" src="#" alt="New">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="gallery-section">
                            <label>Thư viện ảnh sản phẩm</label>
                            <div class="gallery-grid" style="margin-bottom: 16px;">
                                <?php foreach ($gallery as $img): ?>
                                    <div class="gallery-item">
                                        <img src="../<?= htmlspecialchars($img['duong_dan']) ?>" alt="Gallery">
                                        <button type="button" class="btn-remove" onclick="deleteProductImage(<?= (int) $img['id'] ?>, this)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div style="background: #f8fafc; padding: 16px; border-radius: 8px; border: 1px dashed #cbd5e1;">
                                <div style="font-weight: 600; font-size: 14px; margin-bottom: 8px;">Thêm ảnh mới vào thư viện</div>
                                <input type="file" id="thu_vien_anh" name="thu_vien_anh[]" class="form-control" accept="image/*" multiple style="padding: 7px 10px;">
                                <div id="galleryPreview" class="gallery-grid"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="mo_ta">Mô tả chi tiết</label>
                            <textarea id="mo_ta" name="mo_ta" class="form-control"><?= htmlspecialchars((string) ($product['mo_ta'] ?? '')) ?></textarea>
                        </div>

                        <hr style="border:0; border-top:1px solid #e2e8f0; margin:24px 0;">

                        <div style="text-align: right;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập nhật sản phẩm
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script src="admin.js"></script>
    <script>
        $(document).ready(function() {
            $('#mo_ta').summernote({
                placeholder: 'Nhập mô tả sản phẩm...',
                tabsize: 2,
                height: 200,
                toolbar: [
                    ['style', ['style', 'bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture']],
                    ['view', ['fullscreen', 'codeview']]
                ]
            });

            function calculatePrice() {
                const giaGocStr = $('#gia_goc').val().replace(/\./g, '');
                const giaGoc = parseInt(giaGocStr, 10) || 0;
                const phanTram = parseInt($('#giam_gia_phan_tram').val(), 10) || 0;
                const giaGiam = giaGoc * (1 - phanTram / 100);

                $('#gia_hien_thi').val(new Intl.NumberFormat('vi-VN').format(giaGiam) + ' đ');
                $('#gia_fixed').val(Math.round(giaGiam));
            }

            $('#gia_goc, #giam_gia_phan_tram').on('input', calculatePrice);
            calculatePrice();
        });
    </script>
</body>
</html>
