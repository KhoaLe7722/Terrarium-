<?php
session_start();
require_once '../dangky_dangnhap/config.php';

$userId = $_SESSION['user_id'] ?? null;
$user = null;

if (!$userId) {
    header('Location: ../dangky_dangnhap/dangnhap.php?redirect=checkout');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: ../dangky_dangnhap/logout.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="../images/avatar.png" type="image/png" />
    <title>Thanh toán | Thuận Phát Garden</title>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Dosis:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Text&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../mainfont/main.css?v=20260324-6" />
    <link rel="stylesheet" href="thanhtoan.css?v=<?php echo time(); ?>" />
</head>

<body data-page="checkout">
    <nav class="navigation" id="main-nav"></nav>
    <script defer src="../mainfont/layout.js?v=20260324-9"></script>
    <script defer src="../mainfont/main.js?v=20260324-6"></script>

    <div class="container">
        <div id="checkout-section" class="checkout-form">
            <h2 class="form-title">Thông tin giao hàng</h2>

            <form id="orderForm">
                <div class="form-group">
                    <label for="ho_ten_kh">Họ và tên <span style="color:red">*</span></label>
                    <input type="text" id="ho_ten_kh" name="ho_ten_kh" required
                        value="<?php echo htmlspecialchars($user['ho_ten'] ?? ''); ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email_kh">Email</label>
                        <input type="email" id="email_kh" name="email_kh"
                            value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="sdt_kh">Số điện thoại <span style="color:red">*</span></label>
                        <input type="tel" id="sdt_kh" name="sdt_kh" required
                            value="<?php echo htmlspecialchars($user['so_dien_thoai'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="dia_chi_giao">Địa chỉ giao hàng <span style="color:red">*</span></label>
                    <textarea id="dia_chi_giao" name="dia_chi_giao" rows="3"
                        required><?php echo htmlspecialchars($user['dia_chi'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="ghi_chu">Ghi chú đơn hàng</label>
                    <textarea id="ghi_chu" name="ghi_chu" rows="2"
                        placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi giao..."></textarea>
                </div>

                <div class="payment-method-section">
                    <h3 class="section-subtitle">Phương thức thanh toán</h3>

                    <label class="payment-card">
                        <div class="payment-info">
                            <input type="radio" name="phuong_thuc_tt" value="cod" checked>
                            <ion-icon name="cash-outline"></ion-icon>
                            <div class="payment-text">
                                <strong>Thanh toán khi nhận hàng (COD)</strong>
                                <small>Thanh toán bằng tiền mặt khi shipper giao hàng tận nơi.</small>
                            </div>
                        </div>
                    </label>

                    <label class="payment-card">
                        <div class="payment-info">
                            <input type="radio" name="phuong_thuc_tt" value="bank">
                            <ion-icon name="card-outline"></ion-icon>
                            <div class="payment-text">
                                <strong>Chuyển khoản ngân hàng</strong>
                                <small>Thực hiện chuyển khoản nhanh qua mã VietQR.</small>
                            </div>
                        </div>
                        <div class="payment-details">
                            <div class="qr-container">
                                <img src="https://hoabancamp.com/wp-content/uploads/2022/01/HOA-BAN-CAMP-VCB-QR-CODE.jpg"
                                    alt="Mã QR Ngân Hàng"
                                    onerror="this.src='https://via.placeholder.com/150?text=QR+Vietcombank'">
                                <div class="bank-info">
                                    <p><strong>Ngân hàng:</strong> Vietcombank (VCB)</p>
                                    <p><strong>Chủ tài khoản:</strong> NGUYEN VAN A</p>
                                    <p><strong>Số tài khoản:</strong> <span
                                            style="color:#54794a; font-weight:bold; font-size:18px;">0123456789</span>
                                    </p>
                                    <p><strong>Nội dung CK:</strong> <span style="color:red;">SĐT của bạn</span></p>
                                </div>
                            </div>
                        </div>
                    </label>

                    <label class="payment-card">
                        <div class="payment-info">
                            <input type="radio" name="phuong_thuc_tt" value="momo">
                            <ion-icon name="wallet-outline"></ion-icon>
                            <div class="payment-text">
                                <strong>Thanh toán qua Ví MoMo</strong>
                                <small>Mở ứng dụng MoMo để quét mã thanh toán.</small>
                            </div>
                        </div>
                        <div class="payment-details">
                            <div class="qr-container">
                                <img src="https://homepage.momocdn.net/blogscontents/momo-upload-api-220810110042-637957260425550228.webp"
                                    alt="Mã QR MoMo"
                                    onerror="this.src='https://via.placeholder.com/150/A50064/FFFFFF?text=QR+MoMo'">
                                <div class="bank-info">
                                    <p><strong>Ví MoMo:</strong> NGUYEN VAN A</p>
                                    <p><strong>Số điện thoại:</strong> <span
                                            style="color:#A50064; font-weight:bold; font-size:18px;">0988 123 456</span>
                                    </p>
                                    <p><strong>Lời nhắn:</strong> Mua cây Terrarium + SĐT</p>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>

                <div class="order-summary">
                    <div id="order-items-container"></div>

                    <div class="order-total" id="order-total-container">
                        Tổng thanh toán: <span id="display-total">0đ</span>
                    </div>
                </div>

                <button type="submit" class="btn-order" id="submitBtn">Xác nhận đặt hàng</button>
            </form>
        </div>

        <div id="success-section" class="success-box">
            <div class="success-icon">
                <ion-icon name="checkmark-circle-outline"></ion-icon>
            </div>
            <div class="success-message">Đặt hàng thành công!</div>
            <p>Cảm ơn bạn đã tin tưởng Thuận Phát Garden. Đơn hàng của bạn đang được xử lý.</p>
            <a href="../dangky_dangnhap/ho_so.php" class="home-link">Xem đơn hàng của tôi</a>
        </div>
    </div>

    <script src="thanhtoan.js?v=20260325-1"></script>

    <footer class="site-footer" id="site-footer"></footer>
</body>

</html>








