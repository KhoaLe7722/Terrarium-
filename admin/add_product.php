<?php
require_once 'admin_check.php';

$pageTitle = 'Thêm sản phẩm';
$message = '';
$messageType = '';

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
        $hinhChinh = null;
        if (isset($_FILES['hinh_chinh']) && $_FILES['hinh_chinh']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileInfo = pathinfo($_FILES['hinh_chinh']['name']);
            $extension = $fileInfo['extension'] ?? 'jpg';
            $fileName = uniqid('sp_', true) . '.' . $extension;

            if (move_uploaded_file($_FILES['hinh_chinh']['tmp_name'], $uploadDir . $fileName)) {
                $hinhChinh = 'uploads/' . $fileName;
            }
        }

        try {
            $conn->beginTransaction();

            $stmt = $conn->prepare("
                INSERT INTO products (
                    ten_sp, gia, gia_goc, giam_gia_phan_tram, hinh_chinh, mo_ta,
                    so_luong_ton, ngay_bao_tri_gan_nhat, tinh_trang
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $tenSp,
                $gia,
                $giaGoc,
                $giamGiaPhanTram,
                $hinhChinh,
                $moTa,
                $soLuongTon,
                $ngayBaoTriGanNhat,
                $tinhTrang,
            ]);

            $productId = (int) $conn->lastInsertId();

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
                        $galleryStmt->execute([$productId, $imgPath]);
                    }
                }
            }

            $conn->commit();
            header('Location: products.php?msg=add_success');
            exit;
        } catch (PDOException $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            $message = 'Lỗi lưu dữ liệu: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}
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
                <h1 class="page-title" style="margin-bottom: 0;">Thêm sản phẩm mới</h1>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType === 'success' ? 'success' : 'error' ?>">
                    <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <div class="card" style="max-width: 860px;">
                <div class="card-body">
                    <form action="add_product.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="ten_sp">Tên sản phẩm <span style="color:red">*</span></label>
                            <input
                                type="text"
                                id="ten_sp"
                                name="ten_sp"
                                class="form-control"
                                required
                                value="<?= htmlspecialchars($_POST['ten_sp'] ?? '') ?>">
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
                                    value="<?= htmlspecialchars($_POST['gia_goc'] ?? '') ?>"
                                    placeholder="Ví dụ: 200.000">
                                <div class="form-hint">Đây là giá trước khi áp dụng giảm giá.</div>
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
                                    value="<?= htmlspecialchars($_POST['giam_gia_phan_tram'] ?? '0') ?>">
                                <div class="form-hint">Nhập 0 nếu không giảm giá.</div>
                            </div>
                            <div class="form-group">
                                <label>Giá sau giảm</label>
                                <input type="text" id="gia_hien_thi" class="form-control" readonly style="background: #f1f5f9;">
                                <input type="hidden" name="gia" id="gia_fixed">
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
                                    value="<?= htmlspecialchars($_POST['so_luong_ton'] ?? '0') ?>">
                                <div class="form-hint">Bằng 0 sẽ tự động hiện hết hàng trên web.</div>
                            </div>
                            <div class="form-group">
                                <label for="ngay_bao_tri_gan_nhat">Ngày bảo trì gần nhất</label>
                                <input
                                    type="date"
                                    id="ngay_bao_tri_gan_nhat"
                                    name="ngay_bao_tri_gan_nhat"
                                    class="form-control"
                                    value="<?= htmlspecialchars($_POST['ngay_bao_tri_gan_nhat'] ?? '') ?>">
                                <div class="form-hint">Hệ thống sẽ nhắc admin khi sản phẩm sắp đến chu kỳ 2 tháng.</div>
                            </div>
                            <div class="form-group">
                                <label for="hinh_chinh">Ảnh đại diện</label>
                                <input type="file" id="hinh_chinh" name="hinh_chinh" class="form-control" accept="image/*" style="padding: 7px 10px;">
                                <img id="imgPreview" class="img-preview" style="display: none; margin-top: 10px;" src="#" alt="Preview">
                            </div>
                        </div>

                        <div class="gallery-section">
                            <label>Thư viện ảnh sản phẩm (nhiều ảnh)</label>
                            <input type="file" id="thu_vien_anh" name="thu_vien_anh[]" class="form-control" accept="image/*" multiple style="padding: 7px 10px; margin-top: 8px;">
                            <div id="galleryPreview" class="gallery-grid"></div>
                        </div>

                        <div class="form-group">
                            <label for="mo_ta">Mô tả chi tiết</label>
                            <textarea id="mo_ta" name="mo_ta" class="form-control"><?= htmlspecialchars($_POST['mo_ta'] ?? '') ?></textarea>
                        </div>

                        <hr style="border:0; border-top:1px solid #e2e8f0; margin:24px 0;">

                        <div style="text-align: right;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu sản phẩm
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
