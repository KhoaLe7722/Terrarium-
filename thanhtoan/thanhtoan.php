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

$bankId = 'VCB';
$bankName = 'Vietcombank';
$bankAccount = '1036579388';
$bankAccountHolder = 'TRAN NGUYEN THANH DIEN';
$bankQrPreview = sprintf('https://img.vietqr.io/image/%s-%s-compact2.png', $bankId, $bankAccount);
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

<link rel="stylesheet" href="../mainfont/main.css?v=20260329-4" />
    <link rel="stylesheet" href="thanhtoan.css?v=20260329-5" />
</head>

<body data-page="checkout">
    <nav class="navigation" id="main-nav"></nav>
<script defer src="../mainfont/layout.js?v=20260329-4"></script>
    <script defer src="../mainfont/main.js?v=20260329-2"></script>

    <div
        class="container"
        id="checkout-app"
        data-bank-id="<?= htmlspecialchars($bankId) ?>"
        data-bank-name="<?= htmlspecialchars($bankName) ?>"
        data-bank-account="<?= htmlspecialchars($bankAccount) ?>"
        data-bank-owner="<?= htmlspecialchars($bankAccountHolder) ?>"
        data-bank-qr-preview="<?= htmlspecialchars($bankQrPreview) ?>">
        <div id="checkout-section" class="checkout-form">
            <h2 class="form-title">Thông tin giao hàng</h2>

            <form id="orderForm">
                <div class="form-group">
                    <label for="ho_ten_kh">Họ và tên <span class="required-mark">*</span></label>
                    <input
                        type="text"
                        id="ho_ten_kh"
                        name="ho_ten_kh"
                        required
                        value="<?= htmlspecialchars($user['ho_ten'] ?? '') ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email_kh">Email</label>
                        <input
                            type="email"
                            id="email_kh"
                            name="email_kh"
                            value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="sdt_kh">Số điện thoại <span class="required-mark">*</span></label>
                        <input
                            type="tel"
                            id="sdt_kh"
                            name="sdt_kh"
                            required
                            value="<?= htmlspecialchars($user['so_dien_thoai'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="dia_chi_giao">Địa chỉ giao hàng <span class="required-mark">*</span></label>
                    <textarea
                        id="dia_chi_giao"
                        name="dia_chi_giao"
                        rows="3"
                        required><?= htmlspecialchars($user['dia_chi'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="ghi_chu">Ghi chú thêm (tùy chọn)</label>
                    <textarea
                        id="ghi_chu"
                        name="ghi_chu"
                        rows="2"
                        placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi giao..."></textarea>
                </div>

                <div class="payment-method-section">
                    <h3 class="section-subtitle">Phương thức thanh toán</h3>

                    <label class="payment-card is-selected">
                        <div class="payment-info">
                            <input type="radio" name="phuong_thuc_tt" value="cod" checked>
                            <ion-icon name="cash-outline"></ion-icon>
                            <div class="payment-text">
                                <strong>Trả tiền khi nhận hàng (COD)</strong>
                            </div>
                        </div>
                    </label>

                    <label class="payment-card">
                        <div class="payment-info">
                            <input type="radio" name="phuong_thuc_tt" value="bank">
                            <ion-icon name="card-outline"></ion-icon>
                            <div class="payment-text">
                                <strong>Chuyển khoản ngân hàng</strong>
                            </div>
                        </div>

                        <div class="payment-details">
                            <p class="payment-note">Thực hiện thanh toán vào tài khoản ngân hàng của chúng tôi. Đơn hàng sẽ được giao sau khi tiền đã chuyển.</p>
                            <div class="qr-preview-container">
                                <div class="qr-info-left">
                                    <p>Ngân hàng: <strong><?= htmlspecialchars($bankName) ?></strong></p>
                                    <p>Số tài khoản: <strong><?= htmlspecialchars($bankAccount) ?></strong></p>
                                    <p>Chủ tài khoản: <strong><?= htmlspecialchars($bankAccountHolder) ?></strong></p>
                                </div>
                                <div class="qr-image-right">
                                    <img src="<?= htmlspecialchars($bankQrPreview) ?>" alt="QR ngân hàng">
                                </div>
                            </div>
                        </div>
                    </label>
                </div>

                <div class="order-summary">
                    <h3 class="section-subtitle section-subtitle--summary">Đơn hàng của bạn</h3>
                    <div id="order-items-container"></div>
                    <div class="order-summary-breakdown">
                        <div class="summary-row">
                            <span>Tạm tính</span>
                            <strong id="display-subtotal">0đ</strong>
                        </div>
                        <div class="summary-row">
                            <span>Phí vận chuyển</span>
                            <strong id="display-shipping">0đ</strong>
                        </div>
                        <p class="shipping-policy-note" id="display-shipping-note">Miễn phí vận chuyển cho đơn từ 500.000đ.</p>
                    </div>
                    <div class="order-total" id="order-total-container">
                        Tổng cộng: <span id="display-total" class="order-total__value">0đ</span>
                    </div>
                </div>

                <button type="submit" class="btn-order" id="submitBtn">Đặt hàng</button>
            </form>
        </div>

        <div id="success-overlay" class="modal-overlay" hidden>
            <div id="success-section" class="success-modal" role="dialog" aria-modal="true" aria-labelledby="success-title">
                <button id="closeModalBtn" class="close-modal-btn" type="button" aria-label="Đóng">&times;</button>
                <div class="success-icon">
                    <ion-icon name="checkmark-circle-outline"></ion-icon>
                </div>
                <h2 class="success-title" id="success-title">Đặt hàng thành công!</h2>
                <p class="success-subtitle" id="success-subtitle">Chúng tôi sẽ liên hệ sớm để xác nhận đơn hàng của bạn.</p>

                <div class="order-id-box">
                    Mã đơn hàng: <strong id="success-order-id">DH00000000</strong>
                </div>

                <div id="bank-transfer-details" class="bank-transfer-details" hidden>
                    <div class="bank-info-grid">
                        <div class="grid-row"><span>Ngân hàng:</span> <strong><?= htmlspecialchars($bankName) ?></strong></div>
                        <div class="grid-row"><span>Số tài khoản:</span> <strong><?= htmlspecialchars($bankAccount) ?></strong></div>
                        <div class="grid-row"><span>Chủ tài khoản:</span> <strong><?= htmlspecialchars($bankAccountHolder) ?></strong></div>
                        <div class="grid-row"><span>Số tiền:</span> <strong id="success-amount" class="highlight-amount">0đ</strong></div>
                        <div class="grid-row"><span>Nội dung:</span> <strong class="bank-transfer-content">Thanh toán đơn hàng <span id="success-order-code"></span></strong></div>
                    </div>

                    <div class="qr-code-box">
                        <img id="dynamic-qr-code" src="" alt="Mã QR thanh toán">
                        <button class="download-qr-btn" id="downloadQrBtn" type="button">
                            <ion-icon name="download-outline"></ion-icon>
                            Tải mã QR
                        </button>
                    </div>

                    <div class="warning-box">
                        <p class="warning-box__title">Lưu ý quan trọng:</p>
                        <ul>
                            <li>Vui lòng chuyển khoản trong vòng 24 giờ để giữ hàng.</li>
                            <li>Đơn hàng sẽ được xác nhận sau khi chúng tôi nhận được thanh toán.</li>
                            <li>Bạn sẽ nhận được email xác nhận khi đơn hàng được xử lý.</li>
                            <li>Mọi thắc mắc vui lòng liên hệ hotline: 1900 8888.</li>
                        </ul>
                    </div>
                </div>

                <a href="../sanpham/sanpham.php" class="btn-continue-shopping">Tiếp tục mua sắm</a>
            </div>
        </div>
    </div>

    <script src="thanhtoan.js?v=20260329-4"></script>

    <footer class="site-footer" id="site-footer"></footer>
</body>

</html>
