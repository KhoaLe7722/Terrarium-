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

  <link href="https://fonts.googleapis.com/css2?family=Dosis&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Text&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../mainfont/main.css?v=20260318-2" />
  <link rel="stylesheet" href="thanhtoan.css" />
  <style>
      .checkout-form {
          max-width: 600px;
          margin: 150px auto 50px;
          padding: 30px;
          background: #fff;
          border-radius: 15px;
          box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      }
      .form-title {
          font-family: 'Dosis', sans-serif;
          color: #54794a;
          margin-bottom: 25px;
          text-align: center;
          font-size: 28px;
      }
      .form-group {
          margin-bottom: 20px;
      }
      .form-group label {
          display: block;
          margin-bottom: 8px;
          font-weight: 600;
          color: #444;
      }
      .form-group input, .form-group textarea {
          width: 100%;
          padding: 12px;
          border: 1px solid #ddd;
          border-radius: 8px;
          font-family: inherit;
      }
      .form-group input:focus, .form-group textarea:focus {
          border-color: #54794a;
          outline: none;
      }
      .order-summary {
          background: #f9f9f9;
          padding: 20px;
          border-radius: 10px;
          margin-bottom: 25px;
      }
      .order-total {
          font-size: 20px;
          font-weight: 700;
          color: #54794a;
          text-align: right;
      }
      .btn-order {
          width: 100%;
          padding: 15px;
          background: #54794a;
          color: #fff;
          border: none;
          border-radius: 8px;
          font-size: 18px;
          font-family: 'Dosis', sans-serif;
          font-weight: 700;
          cursor: pointer;
          transition: all 0.3s;
      }
      .btn-order:hover {
          background: #45653d;
          transform: translateY(-2px);
      }
      .success-box {
          display: none;
          margin: 150px auto 50px;
          text-align: center;
          max-width: 640px;
          padding: 32px;
          background: #fff;
          border-radius: 16px;
          box-shadow: 0 10px 30px rgba(0,0,0,0.08);
      }
      @media (max-width: 768px) {
          .checkout-form,
          .success-box {
              margin: 120px 16px 40px;
              padding: 22px;
          }

          .form-row {
              grid-template-columns: 1fr !important;
          }
      }
  </style>
</head>

<body data-page="checkout">
  <nav class="navigation" id="main-nav"></nav>
  <script defer src="../mainfont/layout.js?v=20260318-2"></script>
  <script defer src="../mainfont/main.js?v=20260318-2"></script>

  <div class="container">
      <div id="checkout-section" class="checkout-form">
          <h2 class="form-title">Thông tin giao hàng</h2>
          <form id="orderForm">
              <div class="form-group">
                  <label for="ho_ten_kh">Họ và tên <span style="color:red">*</span></label>
                  <input type="text" id="ho_ten_kh" name="ho_ten_kh" required
                         value="<?php echo htmlspecialchars($user['ho_ten'] ?? ''); ?>">
              </div>

              <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                  <div class="form-group">
                      <label for="email_kh">Email</label>
                      <input type="email" id="email_kh" name="email_kh"
                             value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
                  </div>
                  <div class="form-group">
                      <label for="sdt_kh">Số điện thoại</label>
                      <input type="tel" id="sdt_kh" name="sdt_kh"
                             value="<?php echo htmlspecialchars($user['so_dien_thoai'] ?? ''); ?>">
                  </div>
              </div>

              <div class="form-group">
                  <label for="dia_chi_giao">Địa chỉ giao hàng <span style="color:red">*</span></label>
                  <textarea id="dia_chi_giao" name="dia_chi_giao" rows="3" required><?php echo htmlspecialchars($user['dia_chi'] ?? ''); ?></textarea>
              </div>

              <div class="form-group">
                  <label for="ghi_chu">Ghi chú đơn hàng</label>
                  <textarea id="ghi_chu" name="ghi_chu" rows="2" placeholder="Ví dụ: Giao giờ hành chính..."></textarea>
              </div>

              <div class="order-summary">
                  <div class="order-total" id="order-total-container">
                      Tổng thanh toán: <span id="display-total">0đ</span>
                  </div>
              </div>

              <button type="submit" class="btn-order" id="submitBtn">Xác nhận đặt hàng</button>
          </form>
      </div>

      <div id="success-section" class="success-box">
        <div class="success-icon">
          <ion-icon name="checkmark-circle-outline" style="font-size: 80px; color: #54794a;"></ion-icon>
        </div>
        <div class="success-message" style="font-size: 32px; font-family: 'Dosis', sans-serif; color: #54794a; margin: 20px 0;">Đặt hàng thành công!</div>
        <p style="text-align: center; color: #666; margin-bottom: 30px;">Cảm ơn bạn đã tin tưởng Thuận Phát Garden. Đơn hàng của bạn đang được xử lý.</p>
        <a href="../dangky_dangnhap/ho_so.php" class="home-link" style="display: inline-block; padding: 12px 25px; background: #54794a; color: #fff; text-decoration: none; border-radius: 8px;">Xem đơn hàng của tôi</a>
      </div>
  </div>

  <script src="thanhtoan.js"></script>

  <footer class="site-footer" id="site-footer"></footer>
</body>

</html>
