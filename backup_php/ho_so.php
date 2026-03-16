<?php
session_start();
require_once 'config.php';

// Kiểm tra xem đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header('Location: dangnhap.php');
    exit();
}

$userId = $_SESSION['user_id'];
$message = '';
$messageType = '';

// Lấy thông tin user hiện tại
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Xử lý upload ảnh
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $file = $_FILES['avatar'];
    if ($file['error'] === 0) {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $fileInfo = pathinfo($file['name']);
        $fileExtension = strtolower($fileInfo['extension']);

        if (in_array($fileExtension, $allowedExtensions)) {
            // Tạo tên file mới để tránh trùng lặp
            $newFileName = 'avatar_' . $userId . '_' . time() . '.' . $fileExtension;
            $uploadDir = '../uploads/avatars/';
            $uploadPath = $uploadDir . $newFileName;

            // Đường dẫn lưu vào DB
            $dbPath = 'uploads/avatars/' . $newFileName;

            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                // Cập nhật database
                $updateStmt = $conn->prepare("UPDATE users SET avatar = ? WHERE id = ?");
                if ($updateStmt->execute([$dbPath, $userId])) {
                    $_SESSION['user_avatar'] = $dbPath; // Cập nhật session
                    $user['avatar'] = $dbPath; // Cập nhật biến hiển thị
                    $message = 'Cập nhật ảnh đại diện thành công!';
                    $messageType = 'success';
                } else {
                    $message = 'Lỗi cập nhật CSDL.';
                    $messageType = 'error';
                }
            } else {
                $message = 'Không thể sao chép file. Vui lòng thử lại!';
                $messageType = 'error';
            }
        } else {
            $message = 'Chỉ cho phép tải lên file ảnh (JPG, JPEG, PNG, GIF).';
            $messageType = 'error';
        }
    } else {
        $message = 'Có lỗi xảy ra trong quá trình tải file.';
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="../images/avatar.png" type="image/png" />
    <title>Hồ sơ của tôi | Thuận Phát G Garden</title>

    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Dosis&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Text&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100..900&display=swap" rel="stylesheet" />

    <!-- External CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

    <!-- Main CSS -->
    <link rel="stylesheet" href="../mainfont/main.css" />
    <link rel="stylesheet" href="dangky_dangnhap.css" />
    <link rel="stylesheet" href="ho_so.css" />
</head>

<body data-page="profile">
    <nav class="navigation" id="main-nav"></nav>
    <script defer src="../mainfont/layout.js"></script>
    <script defer src="../mainfont/main.js"></script>

    <main class="body__main" style="margin-top: 150px; min-height: 60vh;">
        <div class="profile-container">
            <div class="profile-sidebar">
                <div class="profile-avatar-section">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="../<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar" class="profile-avatar" id="avatarPreview">
                    <?php else: ?>
                        <div class="profile-avatar-placeholder" id="avatarPlaceholder">
                            <ion-icon name="person-outline"></ion-icon>
                        </div>
                    <?php endif; ?>

                    <h3 class="profile-name"><?php echo htmlspecialchars($user['name']); ?></h3>
                    <p class="profile-email"><?php echo htmlspecialchars($user['email']); ?></p>

                    <form action="ho_so.php" method="POST" enctype="multipart/form-data" class="avatar-upload-form">
                        <label for="avatarInput" class="btn btn--primary btn--size-s btn-upload">Chọn ảnh mới</label>
                        <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display: none;" onchange="document.getElementById('submitAvatarBtn').style.display='inline-block'; previewImage(this);">
                        <button type="submit" id="submitAvatarBtn" class="btn btn--normal btn--size-s" style="display: none; margin-top: 10px;">Lưu ảnh đại diện</button>
                    </form>
                </div>
            </div>

            <div class="profile-content">
                <!-- Giỏ hàng hiện tại (Client-side) -->
                <h2 class="profile-heading">Giỏ hàng hiện tại</h2>
                <div id="profile-cart-container" class="purchased-products" style="margin-bottom: 40px;">
                    <div class="empty-state">
                        <p>Đang tải giỏ hàng...</p>
                    </div>
                </div>

                <!-- Sản phẩm đã mua (Server-side) -->
                <h2 class="profile-heading">Sản phẩm đã mua</h2>
                <div class="purchased-products">
                    <?php
                    // Lấy danh sách đơn hàng của user
                    $stmt = $conn->prepare("
                        SELECT o.*, 
                               (SELECT GROUP_CONCAT(CONCAT(ten_sp, ' x', so_luong) SEPARATOR ', ') FROM order_items WHERE order_id = o.id) as items_summary
                        FROM orders o 
                        WHERE user_id = ? OR email_kh = ?
                        ORDER BY ngay_dat DESC
                    ");
                    $stmt->execute([$userId, $user['email']]);
                    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (empty($orders)): ?>
                        <div class="empty-state">
                            <img src="../images/dangky_dangnhap/fail.png" alt="Empty" style="width: 100px; opacity: 0.5;">
                            <p>Bạn chưa mua sản phẩm nào.</p>
                            <a href="../sanpham/sanpham.php" class="btn btn--primary">Mua sắm ngay</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($orders as $order): 
                            // Lấy chi tiết từng sản phẩm trong đơn để lấy ảnh
                            $itemStmt = $conn->prepare("
                                SELECT oi.*, p.hinh_chinh 
                                FROM order_items oi
                                LEFT JOIN products p ON oi.product_id = p.id
                                WHERE oi.order_id = ?
                            ");
                            $itemStmt->execute([$order['id']]);
                            $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                            <div class="order-card">
                                <div class="order-header">
                                    <span class="order-id">Đơn hàng #<?php echo $order['id']; ?></span>
                                    <span class="order-date"><?php echo date('d/m/Y H:i', strtotime($order['ngay_dat'])); ?></span>
                                    <span class="order-status status-<?php echo $order['trang_thai']; ?>">
                                        <?php 
                                            $statusNames = [
                                                'cho_xac_nhan' => 'Chờ xác nhận',
                                                'dang_xu_ly' => 'Đang xử lý',
                                                'dang_giao' => 'Đang giao',
                                                'da_giao' => 'Đã giao',
                                                'da_huy' => 'Đã hủy'
                                            ];
                                            echo $statusNames[$order['trang_thai']] ?? $order['trang_thai'];
                                        ?>
                                    </span>
                                </div>
                                <div class="order-body">
                                    <?php foreach ($items as $item): ?>
                                        <div class="order-item">
                                            <img src="../<?php echo htmlspecialchars($item['hinh_chinh'] ?? 'images/avatar.png'); ?>" alt="Product">
                                            <div class="order-item-info">
                                                <div class="order-item-name"><?php echo htmlspecialchars($item['ten_sp']); ?></div>
                                                <div class="order-item-price">
                                                    <?php echo number_format($item['gia'], 0, '', '.'); ?>đ x <?php echo $item['so_luong']; ?>
                                                </div>
                                            </div>
                                            <div class="order-item-subtotal">
                                                <?php echo number_format($item['thanh_tien'], 0, '', '.'); ?>đ
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="order-footer">
                                    Tổng thanh toán: <?php echo number_format($order['tong_tien'], 0, '', '.'); ?>đ
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if ($message): ?>
            <!-- Thông báo -->
            <div class="box__<?php echo ($messageType === 'success') ? 'success' : 'fail'; ?>" style="display: block;">
                <img class="img__<?php echo ($messageType === 'success') ? 'success' : 'fail'; ?>" src="../images/dangky_dangnhap/<?php echo ($messageType === 'success') ? 'success.png' : 'fail.png'; ?>" alt="thông báo">
                <p><?php echo $message; ?></p>
                <button type="button" class="button__<?php echo ($messageType === 'success') ? 'success' : 'fail'; ?>" onclick="this.parentElement.style.display='none'">Đã hiểu</button>
            </div>
        <?php endif; ?>
    </main>

    <footer class="site-footer" id="site-footer"></footer>

    <script>
        function renderProfileCart() {
            const container = document.getElementById('profile-cart-container');
            const cart = JSON.parse(localStorage.getItem('cart')) || [];

            if (cart.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <ion-icon name="cart-outline" style="font-size: 40px; color: #ccc;"></ion-icon>
                        <p>Giỏ hàng của bạn đang trống.</p>
                        <a href="../sanpham/sanpham.php" class="btn btn--primary btn--size-s">Mua sắm ngay</a>
                    </div>
                `;
                return;
            }

            let html = '<div class="order-card" style="border-style: dashed; border-color: #8B9D77;">';
            let total = 0;
            
            cart.forEach(item => {
                const subtotal = item.price * item.quantity;
                total += subtotal;
                html += `
                    <div class="order-item">
                        <img src="../${item.image}" alt="${item.name}">
                        <div class="order-item-info">
                            <div class="order-item-name">${item.name}</div>
                            <div class="order-item-price">${item.price.toLocaleString()}đ x ${item.quantity}</div>
                        </div>
                        <div class="order-item-subtotal">${subtotal.toLocaleString()}đ</div>
                    </div>
                `;
            });

            html += `
                <div class="order-footer" style="padding-top: 15px; border-top: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                    <div style="font-size: 14px; font-weight: 400; color: #666;">Tạm tính: <strong>${total.toLocaleString()}đ</strong></div>
                    <a href="../thanhtoan/thanhtoan.php" class="btn btn--primary" style="padding: 8px 20px;">Thanh toán ngay</a>
                </div>
            `;
            html += '</div>';
            
            container.innerHTML = html;
        }

        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var preview = document.getElementById('avatarPreview');
                    var placeholder = document.getElementById('avatarPlaceholder');

                    if (preview) {
                        preview.src = e.target.result;
                    } else if (placeholder) {
                        var img = document.createElement('img');
                        img.src = e.target.result;
                        img.id = 'avatarPreview';
                        img.className = 'profile-avatar';
                        placeholder.parentNode.replaceChild(img, placeholder);
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        document.addEventListener('DOMContentLoaded', renderProfileCart);
    </script>
</body>

</html>