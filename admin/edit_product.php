<?php
require_once 'admin_check.php';

$pageTitle = 'Sửa sản phẩm';
$message = '';
$messageType = '';

$id = $_GET['id'] ?? 0;
if (!$id) {
    header('Location: products.php');
    exit;
}

// Lấy thông tin sản phẩm
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: products.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten_sp = trim($_POST['ten_sp'] ?? '');
    $gia = str_replace('.', '', $_POST['gia'] ?? '0');
    $gia_goc = str_replace('.', '', $_POST['gia_goc'] ?? '0');
    $giam_gia_phan_tram = intval($_POST['giam_gia_phan_tram'] ?? 0);
    $tinh_trang = $_POST['tinh_trang'] ?? 'con_hang';
    $mo_ta = $_POST['mo_ta'] ?? '';
    
    if ($gia_goc == '' || $gia_goc == '0') {
        $gia_goc = $gia;
    }

    if ($giam_gia_phan_tram > 0) {
        $gia = $gia_goc * (1 - $giam_gia_phan_tram / 100);
    }

    if (empty($ten_sp) || empty($gia_goc)) {
        $message = 'Vui lòng nhập tên và giá!';
        $messageType = 'error';
    } else {
        $hinh_chinh = $product['hinh_chinh']; // Giữ ảnh cũ by default
        
        // Upload ảnh mới nếu có
        if (isset($_FILES['hinh_chinh']) && $_FILES['hinh_chinh']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
            $fileInfo = pathinfo($_FILES['hinh_chinh']['name']);
            $filename = uniqid('sp_') . '.' . $fileInfo['extension'];
            
            if (move_uploaded_file($_FILES['hinh_chinh']['tmp_name'], $uploadDir . $filename)) {
                // Xóa ảnh cũ nếu là ảnh trong uploads (không xóa ảnh mặc định của template)
                if ($hinh_chinh && strpos($hinh_chinh, 'uploads/') === 0) {
                    $oldPath = '../' . $hinh_chinh;
                    if (file_exists($oldPath)) unlink($oldPath);
                }
                $hinh_chinh = 'uploads/' . $filename;
            }
        }

        try {
            $conn->beginTransaction();

            $stmt = $conn->prepare("
                UPDATE products 
                SET ten_sp=?, gia=?, gia_goc=?, giam_gia_phan_tram=?, hinh_chinh=?, mo_ta=?, tinh_trang=?
                WHERE id=?
            ");
            $stmt->execute([$ten_sp, $gia, $gia_goc, $giam_gia_phan_tram, $hinh_chinh, $mo_ta, $tinh_trang, $id]);

            // Xử lý tải lên thêm ảnh phụ mới
            if (isset($_FILES['thu_vien_anh'])) {
                $uploadDir = '../uploads/';
                foreach ($_FILES['thu_vien_anh']['tmp_name'] as $key => $tmpName) {
                    if ($_FILES['thu_vien_anh']['error'][$key] === UPLOAD_ERR_OK) {
                        $fileInfo = pathinfo($_FILES['thu_vien_anh']['name'][$key]);
                        $filename = uniqid('gallery_') . '_' . $key . '.' . $fileInfo['extension'];
                        
                        if (move_uploaded_file($tmpName, $uploadDir . $filename)) {
                            $imgPath = 'uploads/' . $filename;
                            $stmt = $conn->prepare("INSERT INTO product_images (product_id, duong_dan) VALUES (?, ?)");
                            $stmt->execute([$id, $imgPath]);
                        }
                    }
                }
            }

            $conn->commit();
            
            $message = 'Cập nhật thành công!';
            $messageType = 'success';
            
            // Cập nhật lại data hiện thị
            $product['ten_sp'] = $ten_sp;
            $product['gia'] = $gia;
            $product['gia_goc'] = $gia_goc;
            $product['hinh_chinh'] = $hinh_chinh;
            $product['tinh_trang'] = $tinh_trang;
            $product['mo_ta'] = $mo_ta;
            
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
                <div class="alert alert-<?= $messageType ?>">
                    <i class="fas fa-<?= $messageType == 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i> 
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <div class="card" style="max-width: 800px;">
                <div class="card-body">
                    <form action="edit_product.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="ten_sp">Tên sản phẩm <span style="color:red">*</span></label>
                            <input type="text" id="ten_sp" name="ten_sp" class="form-control" required 
                                   value="<?= htmlspecialchars($product['ten_sp']) ?>">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="gia_goc">Giá bán (VNĐ) <span style="color:red">*</span></label>
                                <input type="text" id="gia_goc" name="gia_goc" class="form-control currency-input" required 
                                       value="<?= number_format($product['gia_goc'] ?: $product['gia'], 0, '', '.') ?>">
                            </div>
                            <div class="form-group">
                                <label for="giam_gia_phan_tram">Giảm giá (%)</label>
                                <input type="number" id="giam_gia_phan_tram" name="giam_gia_phan_tram" class="form-control" min="0" max="100"
                                       value="<?= htmlspecialchars($product['giam_gia_phan_tram'] ?? '0') ?>">
                            </div>
                            <div class="form-group">
                                <label>Giá sau giảm</label>
                                <input type="text" id="gia_hien_thi" class="form-control" readonly style="background: #f1f5f9;">
                                <input type="hidden" name="gia" id="gia_fixed" value="<?= $product['gia'] ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="tinh_trang">Tình trạng</label>
                                <select id="tinh_trang" name="tinh_trang" class="form-control">
                                    <option value="con_hang" <?= $product['tinh_trang'] == 'con_hang' ? 'selected' : '' ?>>Còn hàng</option>
                                    <option value="het_hang" <?= $product['tinh_trang'] == 'het_hang' ? 'selected' : '' ?>>Hết hàng</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="hinh_chinh">Thay đổi ảnh đại diện</label>
                                <input type="file" id="hinh_chinh" name="hinh_chinh" class="form-control" accept="image/*" style="padding: 7px 10px;">
                                
                                <div style="margin-top: 12px; display: flex; gap: 16px;">
                                    <div>
                                        <div style="font-size: 12px; color: #666; margin-bottom: 4px;">Ảnh hiện tại</div>
                                        <img src="../<?= htmlspecialchars($product['hinh_chinh'] ?? 'images/placeholder.jpg') ?>" 
                                             class="img-preview" alt="Current" onerror="this.src='../images/avatar.png'">
                                    </div>
                                    <div>
                                        <div style="font-size: 12px; color: #666; margin-bottom: 4px;">Ảnh mới (preview)</div>
                                        <img id="imgPreview" class="img-preview" style="display: none;" src="#" alt="New">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                        // Lấy ảnh phụ hiện có
                        $stmt = $conn->prepare("SELECT id, duong_dan FROM product_images WHERE product_id = ? ORDER BY id ASC");
                        $stmt->execute([$id]);
                        $gallery = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <div class="gallery-section">
                            <label>Thư viện ảnh sản phẩm</label>
                            <div class="gallery-grid" style="margin-bottom: 16px;">
                                <?php foreach ($gallery as $img): ?>
                                    <div class="gallery-item">
                                        <img src="../<?= htmlspecialchars($img['duong_dan']) ?>" alt="Gallery">
                                        <button type="button" class="btn-remove" onclick="deleteProductImage(<?= $img['id'] ?>, this)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div style="background: #f8fafc; padding: 16px; border-radius: 8px; border: 1px dashed #cbd5e1;">
                                <div style="font-weight: 600; font-size: 14px; margin-bottom: 8px;">Thêm ảnh mới vào thư viện</div>
                                <input type="file" id="thu_vien_anh" name="thu_vien_anh[]" class="form-control" accept="image/*" multiple style="padding: 7px 10px;">
                                <div id="galleryPreview" class="gallery-grid">
                                    <!-- New previews here -->
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="mo_ta">Mô tả chi tiết</label>
                            <textarea id="mo_ta" name="mo_ta" class="form-control"><?= htmlspecialchars($product['mo_ta'] ?? '') ?></textarea>
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
