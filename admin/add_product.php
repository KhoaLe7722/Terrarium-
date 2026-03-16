<?php
require_once 'admin_check.php';

$pageTitle = 'Thêm sản phẩm';
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten_sp = trim($_POST['ten_sp'] ?? '');
    $gia = str_replace('.', '', $_POST['gia'] ?? '0');
    $gia_goc = str_replace('.', '', $_POST['gia_goc'] ?? '0');
    $giam_gia_phan_tram = intval($_POST['giam_gia_phan_tram'] ?? 0);
    $tinh_trang = $_POST['tinh_trang'] ?? 'con_hang';
    $mo_ta = $_POST['mo_ta'] ?? '';
    
    if ($gia_goc == '' || $gia_goc == '0') {
        $gia_goc = $gia; // Nếu không nhập giá gốc, coi giá bán là giá gốc
    }

    // Nếu có % giảm giá, tính lại giá bán
    if ($giam_gia_phan_tram > 0) {
        $gia = $gia_goc * (1 - $giam_gia_phan_tram / 100);
    } else {
        // Nếu không có % giảm giá, giá bán là giá nhập vào
        // Trường hợp này có thể gia_goc vẫn > gia nếu nhập tay, 
        // nhưng user muốn theo % là chính.
    }

    if (empty($ten_sp) || empty($gia_goc)) {
        $message = 'Vui lòng nhập tên và giá gốc/giá bán!';
        $messageType = 'error';
    } else {
        // Upload ảnh chính
        $hinh_chinh = null;
        if (isset($_FILES['hinh_chinh']) && $_FILES['hinh_chinh']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
            $fileInfo = pathinfo($_FILES['hinh_chinh']['name']);
            $filename = uniqid('sp_') . '.' . $fileInfo['extension'];
            
            if (move_uploaded_file($_FILES['hinh_chinh']['tmp_name'], $uploadDir . $filename)) {
                $hinh_chinh = 'uploads/' . $filename;
            }
        }

        try {
            $conn->beginTransaction();

            $stmt = $conn->prepare("
                INSERT INTO products (ten_sp, gia, gia_goc, giam_gia_phan_tram, hinh_chinh, mo_ta, tinh_trang)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$ten_sp, $gia, $gia_goc, $giam_gia_phan_tram, $hinh_chinh, $mo_ta, $tinh_trang]);
            $productId = $conn->lastInsertId();

            // Xử lý tải lên nhiều ảnh phụ
            if (isset($_FILES['thu_vien_anh'])) {
                $uploadDir = '../uploads/';
                foreach ($_FILES['thu_vien_anh']['tmp_name'] as $key => $tmpName) {
                    if ($_FILES['thu_vien_anh']['error'][$key] === UPLOAD_ERR_OK) {
                        $fileInfo = pathinfo($_FILES['thu_vien_anh']['name'][$key]);
                        $filename = uniqid('gallery_') . '_' . $key . '.' . $fileInfo['extension'];
                        
                        if (move_uploaded_file($tmpName, $uploadDir . $filename)) {
                            $imgPath = 'uploads/' . $filename;
                            $stmt = $conn->prepare("INSERT INTO product_images (product_id, duong_dan) VALUES (?, ?)");
                            $stmt->execute([$productId, $imgPath]);
                        }
                    }
                }
            }

            $conn->commit();
            header('Location: products.php?msg=add_success');
            exit;
        } catch (PDOException $e) {
            $conn->rollBack();
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
    <title><?= $pageTitle ?> | Admin Thuận Phát Garden</title>
    <link rel="icon" href="../images/avatar.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Dosis:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
    <!-- Summernote cho mô tả (Tùy chọn) -->
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
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <div class="card" style="max-width: 800px;">
                <div class="card-body">
                    <form action="add_product.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="ten_sp">Tên sản phẩm <span style="color:red">*</span></label>
                            <input type="text" id="ten_sp" name="ten_sp" class="form-control" required 
                                   value="<?= htmlspecialchars($_POST['ten_sp'] ?? '') ?>">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="gia_goc">Giá bán (VNĐ) <span style="color:red">*</span></label>
                                <input type="text" id="gia_goc" name="gia_goc" class="form-control currency-input" required 
                                       value="<?= htmlspecialchars($_POST['gia_goc'] ?? '') ?>" placeholder="Ví dụ: 200.000">
                                <div class="form-hint">Đây là giá gốc của sản phẩm.</div>
                            </div>
                            <div class="form-group">
                                <label for="giam_gia_phan_tram">Giảm giá (%)</label>
                                <input type="number" id="giam_gia_phan_tram" name="giam_gia_phan_tram" class="form-control" min="0" max="100"
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
                                <label for="tinh_trang">Tình trạng</label>
                                <select id="tinh_trang" name="tinh_trang" class="form-control">
                                    <option value="con_hang">Còn hàng</option>
                                    <option value="het_hang">Hết hàng</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="hinh_chinh">Ảnh đại diện</label>
                                <input type="file" id="hinh_chinh" name="hinh_chinh" class="form-control" accept="image/*" style="padding: 7px 10px;">
                                <img id="imgPreview" class="img-preview" style="display: none; margin-top: 10px;" src="#" alt="Preview">
                            </div>
                        </div>

                        <div class="gallery-section">
                            <label>Thư viện ảnh sản phẩm (Nhiều ảnh)</label>
                            <input type="file" id="thu_vien_anh" name="thu_vien_anh[]" class="form-control" accept="image/*" multiple style="padding: 7px 10px; margin-top: 8px;">
                            <div id="galleryPreview" class="gallery-grid">
                                <!-- Previews will appear here -->
                            </div>
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
                const giaGoc = parseInt(giaGocStr) || 0;
                const phanTram = parseInt($('#giam_gia_phan_tram').val()) || 0;
                
                const giaGiam = giaGoc * (1 - phanTram / 100);
                $('#gia_hien_thi').val(new Intl.NumberFormat('vi-VN').format(giaGiam) + ' đ');
                $('#gia_fixed').val(giaGiam);
            }

            $('#gia_goc, #giam_gia_phan_tram').on('input', calculatePrice);
            calculatePrice();
        });
    </script>
</body>
</html>
