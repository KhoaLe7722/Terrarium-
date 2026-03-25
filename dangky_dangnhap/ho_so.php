<?php
session_start();
require_once 'config.php';
require_once __DIR__ . '/../includes/store_helpers.php';

if (empty($_SESSION['user_id'])) {
    header('Location: dangnhap.php');
    exit;
}

function delete_profile_avatar_file(?string $relativePath): void
{
    $normalizedPath = ltrim(str_replace('\\', '/', trim((string) $relativePath)), './');
    if ($normalizedPath === '' || strpos($normalizedPath, 'uploads/avatars/') !== 0) {
        return;
    }

    $absolutePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $normalizedPath);
    if (is_file($absolutePath)) {
        @unlink($absolutePath);
    }
}

function upload_profile_avatar_file(array $file): array
{
    $errorCode = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($errorCode !== UPLOAD_ERR_OK) {
        return [
            'ok' => false,
            'message' => 'Tải ảnh đại diện thất bại. Vui lòng thử lại.',
        ];
    }

    $tmpName = (string) ($file['tmp_name'] ?? '');
    if ($tmpName === '' || !is_uploaded_file($tmpName)) {
        return [
            'ok' => false,
            'message' => 'Không thể đọc tệp ảnh đại diện đã chọn.',
        ];
    }

    $extension = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($extension, $allowedExtensions, true) || @getimagesize($tmpName) === false) {
        return [
            'ok' => false,
            'message' => 'Ảnh đại diện phải là tệp JPG, PNG, GIF hoặc WEBP hợp lệ.',
        ];
    }

    if ((int) ($file['size'] ?? 0) > 2 * 1024 * 1024) {
        return [
            'ok' => false,
            'message' => 'Ảnh đại diện chỉ được tối đa 2MB.',
        ];
    }

    $uploadDirectory = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'avatars';
    if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0777, true) && !is_dir($uploadDirectory)) {
        return [
            'ok' => false,
            'message' => 'Không thể tạo thư mục lưu ảnh đại diện.',
        ];
    }

    $fileName = uniqid('avatar_', true) . '.' . $extension;
    $destination = $uploadDirectory . DIRECTORY_SEPARATOR . $fileName;
    if (!move_uploaded_file($tmpName, $destination)) {
        return [
            'ok' => false,
            'message' => 'Không thể lưu ảnh đại diện. Vui lòng thử lại.',
        ];
    }

    return [
        'ok' => true,
        'path' => 'uploads/avatars/' . $fileName,
    ];
}

$user = current_user($conn);
if (!$user) {
    header('Location: logout.php');
    exit;
}

$profileMessage = '';
$profileMessageType = '';

if (isset($_GET['updated']) && $_GET['updated'] === '1') {
    $profileMessage = 'Thông tin khách hàng đã được cập nhật.';
    $profileMessageType = 'success';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['profile_action'] ?? '') === 'update_profile') {
    $fullName = trim((string) ($_POST['ho_ten'] ?? ''));
    $phoneNumber = trim((string) ($_POST['so_dien_thoai'] ?? ''));
    $address = trim((string) ($_POST['dia_chi'] ?? ''));
    $currentAvatarPath = $user['anh_dai_dien'] ?? null;
    $avatarPath = $currentAvatarPath;

    if ($fullName === '') {
        $profileMessage = 'Vui lòng nhập tên khách hàng.';
        $profileMessageType = 'error';
    } elseif (mb_strlen($fullName) > 100) {
        $profileMessage = 'Tên khách hàng chỉ được tối đa 100 ký tự.';
        $profileMessageType = 'error';
    } elseif (strlen($phoneNumber) > 20) {
        $profileMessage = 'Số điện thoại chỉ được tối đa 20 ký tự.';
        $profileMessageType = 'error';
    } elseif ($phoneNumber !== '' && !preg_match('/^[0-9+\-\s().]+$/', $phoneNumber)) {
        $profileMessage = 'Số điện thoại chỉ được chứa số và các ký tự + - ( ) .';
        $profileMessageType = 'error';
    } else {
        $hasNewAvatar = isset($_FILES['anh_dai_dien'])
            && (int) ($_FILES['anh_dai_dien']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;

        if ($hasNewAvatar) {
            $uploadResult = upload_profile_avatar_file($_FILES['anh_dai_dien']);
            if (!($uploadResult['ok'] ?? false)) {
                $profileMessage = (string) ($uploadResult['message'] ?? 'Cập nhật ảnh đại diện thất bại.');
                $profileMessageType = 'error';
            } else {
                $avatarPath = $uploadResult['path'];
            }
        }

        if ($profileMessage === '') {
            try {
                $updateStmt = $conn->prepare("
                    UPDATE users
                    SET ho_ten = ?, so_dien_thoai = ?, dia_chi = ?, anh_dai_dien = ?
                    WHERE id = ?
                ");
                $updateStmt->execute([
                    $fullName,
                    $phoneNumber !== '' ? $phoneNumber : null,
                    $address !== '' ? $address : null,
                    $avatarPath,
                    $user['id'],
                ]);

                if ($hasNewAvatar && $currentAvatarPath !== $avatarPath) {
                    delete_profile_avatar_file($currentAvatarPath);
                }

                $_SESSION['user_name'] = $fullName;
                $_SESSION['user_avatar'] = $avatarPath;

                header('Location: ho_so.php?updated=1');
                exit;
            } catch (Throwable $e) {
                if ($hasNewAvatar && $currentAvatarPath !== $avatarPath) {
                    delete_profile_avatar_file($avatarPath);
                    $avatarPath = $currentAvatarPath;
                }

                $profileMessage = 'Không thể cập nhật thông tin lúc này. Vui lòng thử lại.';
                $profileMessageType = 'error';
            }
        }
    }

    if ($profileMessageType === 'error') {
        $user['ho_ten'] = $fullName;
        $user['so_dien_thoai'] = $phoneNumber;
        $user['dia_chi'] = $address;
        $user['anh_dai_dien'] = $avatarPath;
    }
}

$statusClasses = [
    'cho_xac_nhan' => 'status-cho_xac_nhan',
    'dang_xu_ly' => 'status-dang_xu_ly',
    'dang_giao' => 'status-dang_giao',
    'da_giao' => 'status-da_giao',
    'da_huy' => 'status-da_huy',
];

$stmt = $conn->prepare("
    SELECT id, ho_ten_kh, email_kh, sdt_kh, dia_chi_giao, tong_tien, trang_thai, ngay_dat, ghi_chu, phuong_thuc_tt
    FROM orders
    WHERE user_id = ?
    ORDER BY ngay_dat DESC
");
$stmt->execute([$user['id']]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="../images/avatar.png" type="image/png" />
    <title>Hồ sơ của tôi | Thuận Phát Garden</title>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Dosis&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Text&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../mainfont/main.css?v=20260324-6" />
    <link rel="stylesheet" href="ho_so.css?v=20260324-3" />
    <style>
        .profile-info {
            width: 100%;
            display: grid;
            gap: 12px;
            margin-top: 18px;
            text-align: left;
        }

        .profile-info__item {
            padding: 12px 14px;
            border-radius: 10px;
            background: #f8faf7;
            border: 1px solid #e3ebdf;
        }

        .profile-info__label {
            display: block;
            font-size: 12px;
            color: #667085;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .profile-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .profile-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 150px;
            padding: 10px 16px;
            border-radius: 8px;
            text-decoration: none;
            background: #54794a;
            color: #fff;
        }

        .profile-action.secondary {
            background: #eef4ec;
            color: #32532b;
        }

        .cart-preview-item {
            display: flex;
            gap: 12px;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }

        .cart-preview-item:last-child {
            border-bottom: none;
        }

        .cart-preview-item img {
            width: 64px;
            height: 64px;
            object-fit: cover;
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .profile-container {
                flex-direction: column;
            }

            .profile-sidebar {
                width: 100%;
            }
        }
    </style>
</head>

<body data-page="profile">
    <nav class="navigation" id="main-nav"></nav>
    <script defer src="../mainfont/layout.js?v=20260324-9"></script>
    <script defer src="../mainfont/main.js?v=20260324-6"></script>

    <main class="body__main" style="margin-top: 30px; min-height: 60vh;">
        <div class="profile-container">
            <aside class="profile-sidebar">
                <div class="profile-avatar-section">
                    <form class="profile-edit-form" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="profile_action" value="update_profile">

                        <img
                            id="profile-avatar-preview"
                            class="profile-avatar"
                            src="<?= htmlspecialchars(normalize_public_path($user['anh_dai_dien'] ?? null)) ?>"
                            alt="<?= htmlspecialchars($user['ho_ten']) ?>"
                            onerror="this.onerror=null;this.src='../images/avatar.png';">

                        <h3 class="profile-name"><?= htmlspecialchars($user['ho_ten']) ?></h3>
                        <p class="profile-email"><?= htmlspecialchars($user['email']) ?></p>

                        <?php if ($profileMessage !== ''): ?>
                            <div class="profile-feedback <?= $profileMessageType === 'success' ? 'is-success' : 'is-error' ?>">
                                <?= htmlspecialchars($profileMessage) ?>
                            </div>
                        <?php endif; ?>

                        <label class="avatar-upload-form" for="anh_dai_dien">
                            <span class="profile-action secondary btn-upload">Đổi ảnh đại diện</span>
                            <input
                                class="profile-avatar-input"
                                type="file"
                                id="anh_dai_dien"
                                name="anh_dai_dien"
                                accept="image/png,image/jpeg,image/webp,image/gif">
                            <span class="profile-field-hint">PNG, JPG, GIF hoặc WEBP, tối đa 2MB.</span>
                        </label>

                        <div class="profile-info">
                            <div class="profile-info__item">
                                <span class="profile-info__label">Email đăng nhập</span>
                                <?= htmlspecialchars($user['email']) ?>
                            </div>
                        </div>

                        <div class="profile-form-group">
                            <label class="profile-form-label" for="ho_ten">Tên khách hàng</label>
                            <input
                                class="profile-input"
                                type="text"
                                id="ho_ten"
                                name="ho_ten"
                                maxlength="100"
                                required
                                value="<?= htmlspecialchars($user['ho_ten']) ?>">
                        </div>

                        <div class="profile-form-group">
                            <label class="profile-form-label" for="so_dien_thoai">Số điện thoại</label>
                            <input
                                class="profile-input"
                                type="text"
                                id="so_dien_thoai"
                                name="so_dien_thoai"
                                maxlength="20"
                                value="<?= htmlspecialchars($user['so_dien_thoai'] ?? '') ?>">
                        </div>

                        <div class="profile-form-group">
                            <label class="profile-form-label" for="dia_chi">Địa chỉ</label>
                            <textarea
                                class="profile-input profile-textarea"
                                id="dia_chi"
                                name="dia_chi"
                                rows="4"><?= htmlspecialchars($user['dia_chi'] ?? '') ?></textarea>
                        </div>

                        <div class="profile-actions profile-actions--stack">
                            <button type="submit" class="profile-action">Lưu thông tin</button>
                            <a class="profile-action secondary" href="../sanpham/sanpham.php">Mua tiếp</a>
                            <a class="profile-action secondary" href="logout.php">Đăng xuất</a>
                        </div>
                    </form>
                </div>
            </aside>

            <section class="profile-content">
                <h2 class="profile-heading">Giỏ hàng hiện tại</h2>
                <div id="profile-cart-container" class="purchased-products" style="margin-bottom: 40px;">
                    <div class="empty-state">
                        <p>Đang tải giỏ hàng...</p>
                    </div>
                </div>

                <h2 class="profile-heading">Đơn hàng đã đặt</h2>
                <div class="purchased-products">
                    <?php if (empty($orders)): ?>
                        <div class="empty-state">
                            <ion-icon name="receipt-outline" style="font-size: 42px; color: #c6d2c1;"></ion-icon>
                            <p>Bạn chưa có đơn hàng nào.</p>
                            <a href="../sanpham/sanpham.php" class="profile-action">Bắt đầu mua sắm</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <?php
                            $canCancel = order_can_customer_cancel((string) $order['trang_thai']);
                            $itemStmt = $conn->prepare("
                                SELECT oi.ten_sp, oi.gia, oi.so_luong, oi.thanh_tien, p.hinh_chinh
                                FROM order_items oi
                                LEFT JOIN products p ON p.id = oi.product_id
                                WHERE oi.order_id = ?
                                ORDER BY oi.id ASC
                            ");
                            $itemStmt->execute([$order['id']]);
                            $items = $itemStmt->fetchAll();
                            ?>
                            <article class="order-card">
                                <div class="order-header">
                                    <span class="order-id">Đơn hàng #<?= (int) $order['id'] ?></span>
                                    <span class="order-date"><?= date('d/m/Y H:i', strtotime($order['ngay_dat'])) ?></span>
                                    <span class="order-status <?= $statusClasses[$order['trang_thai']] ?? '' ?>">
                                        <?= htmlspecialchars(order_status_label($order['trang_thai'])) ?>
                                    </span>
                                </div>

                                <div class="order-body">
                                    <?php foreach ($items as $item): ?>
                                        <div class="order-item">
                                            <img src="<?= htmlspecialchars(normalize_public_path($item['hinh_chinh'])) ?>" alt="<?= htmlspecialchars($item['ten_sp']) ?>" onerror="this.onerror=null;this.src='../images/avatar.png';">
                                            <div class="order-item-info">
                                                <div class="order-item-name"><?= htmlspecialchars($item['ten_sp']) ?></div>
                                                <div class="order-item-price">
                                                    <?= htmlspecialchars(format_currency_vnd($item['gia'])) ?> x <?= (int) $item['so_luong'] ?>
                                                </div>
                                            </div>
                                            <div class="order-item-subtotal">
                                                <?= htmlspecialchars(format_currency_vnd($item['thanh_tien'])) ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <?php if (!empty($order['ghi_chu'])): ?>
                                    <div style="margin-top:12px;color:#666;font-size:14px;">
                                        <strong>Ghi chú:</strong> <?= htmlspecialchars($order['ghi_chu']) ?>
                                    </div>
                                <?php endif; ?>

                                <div class="order-inline-meta">
                                    <span><strong>Thanh toán:</strong> <?= htmlspecialchars(payment_method_label($order['phuong_thuc_tt'] ?? '')) ?></span>
                                    <span><strong>Sản phẩm:</strong> <?= count($items) ?></span>
                                </div>

                                <div class="order-actions">
                                    <a class="profile-action is-small" href="order_detail.php?id=<?= (int) $order['id'] ?>">Xem hóa đơn</a>
                                    <?php if ($canCancel): ?>
                                        <button
                                            type="button"
                                            class="profile-action danger is-small js-open-cancel-order"
                                            data-order-id="<?= (int) $order['id'] ?>"
                                            data-order-label="#<?= (int) $order['id'] ?>">
                                            Hủy đơn
                                        </button>
                                    <?php endif; ?>
                                </div>

                                <div class="order-footer">
                                    <span>Tổng thanh toán</span>
                                    <span><?= htmlspecialchars(format_currency_vnd($order['tong_tien'])) ?></span>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>

    <div class="confirm-modal" id="cancel-order-modal">
        <div class="confirm-modal__panel">
            <h3 class="confirm-modal__title">Hủy đơn hàng</h3>
            <p class="confirm-modal__message" data-cancel-order-message>Bạn có muốn hủy đơn hàng này không?</p>
            <div class="confirm-modal__actions">
                <button type="button" class="confirm-btn secondary" data-cancel-order-close>KHÔNG</button>
                <button type="button" class="confirm-btn danger" data-confirm-cancel-order>CÓ</button>
            </div>
        </div>
    </div>

    <footer class="site-footer" id="site-footer"></footer>

<script src="../giohang/giohang.js?v=20260325-1"></script>
    <script defer src="order_actions.js?v=20260324-1"></script>
    <script>
        function formatPrice(value) {
            return Number(value).toLocaleString('vi-VN') + 'đ';
        }

        function resolveCartImage(item) {
            return item && item.image ? '../' + item.image : '../images/avatar.png';
        }

        function renderProfileCart() {
            const container = document.getElementById('profile-cart-container');
            const cart = JSON.parse(localStorage.getItem('cart')) || [];

            if (!container) {
                return;
            }

            if (cart.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <ion-icon name="cart-outline" style="font-size: 42px; color: #c6d2c1;"></ion-icon>
                        <p>Giỏ hàng hiện tại đang trống.</p>
                        <a href="../sanpham/sanpham.php" class="profile-action">Xem sản phẩm</a>
                    </div>
                `;
                return;
            }

            let total = 0;
            let html = '<div class="order-card">';

            cart.forEach((item) => {
                const subTotal = Number(item.price) * Number(item.quantity);
                total += subTotal;
                html += `
                    <div class="cart-preview-item">
                        <img src="${resolveCartImage(item)}" alt="${item.name}" onerror="this.onerror=null;this.src='../images/avatar.png';">
                        <div class="order-item-info">
                            <div class="order-item-name">${item.name}</div>
                            <div class="order-item-price">${formatPrice(item.price)} x ${item.quantity}</div>
                        </div>
                        <div class="order-item-subtotal">${formatPrice(subTotal)}</div>
                    </div>
                `;
            });

            html += `
                <div class="order-footer" style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;">
                    <span>Tạm tính: ${formatPrice(total)}</span>
                    <div class="profile-actions" style="margin-top:0;">
                        <a class="profile-action secondary" href="../giohang/giohang.php">Xem giỏ hàng</a>
                        <a class="profile-action" href="../thanhtoan/thanhtoan.php">Thanh toán</a>
                    </div>
                </div>
            `;
            html += '</div>';

            container.innerHTML = html;
        }

        document.addEventListener('DOMContentLoaded', function () {
            const avatarInput = document.getElementById('anh_dai_dien');
            const avatarPreview = document.getElementById('profile-avatar-preview');
            let previewObjectUrl = null;

            if (avatarInput && avatarPreview) {
                avatarInput.addEventListener('change', function () {
                    const [file] = this.files || [];
                    if (!file) {
                        return;
                    }

                    if (previewObjectUrl) {
                        URL.revokeObjectURL(previewObjectUrl);
                    }

                    previewObjectUrl = URL.createObjectURL(file);
                    avatarPreview.src = previewObjectUrl;
                });
            }

            Promise.resolve(window.storeCartSyncPromise).finally(renderProfileCart);
        });
    </script>
</body>

</html>










