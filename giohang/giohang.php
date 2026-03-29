<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" href="../images/avatar.png" type="image/png" />
  <title>Giỏ hàng | Thuận Phát Garden</title>

  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Dosis&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Text&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Edu+NSW+ACT+Hand&display=swap" rel="stylesheet">

<link rel="stylesheet" href="../mainfont/main.css?v=20260329-4" />
  <link rel="stylesheet" href="giohangchitiet.css?v=20260329-2">
</head>

<body data-page="cart">
  <nav class="navigation" id="main-nav"></nav>
<script defer src="../mainfont/layout.js?v=20260329-4"></script>
  <script defer src="../mainfont/main.js?v=20260329-2"></script>

  <main class="cart-container">
    <section class="cart-card" id="cart-popup">
      <div class="cart-title-row">
        <div>
          <h1 class="shop-title">Giỏ hàng của bạn</h1>
          <p class="cart-subtitle">Kiểm tra số lượng, chọn sản phẩm muốn mua và tiếp tục thanh toán khi đã sẵn sàng.</p>
        </div>
        <a class="cart-continue-link" href="../sanpham/sanpham.php">Tiếp tục mua sắm</a>
      </div>

      <div class="cart-head-row" aria-hidden="true">
        <div>Sản phẩm</div>
        <div>Giá</div>
        <div>Số lượng</div>
        <div>Tổng</div>
        <div>Thao tác</div>
      </div>

      <div class="cart-items" id="cart-items"></div>

      <div class="cart-bottom-wrap">
        <div class="cart-bottom-bar">
          <label class="select-all">
            <input type="checkbox" id="cart-select-all" checked>
            <span>Chọn tất cả</span>
          </label>

          <div class="cart-summary">
            <div class="cart-summary-breakdown">
              <div class="cart-summary-row">
                <span>Tạm tính</span>
                <strong id="cart-subtotal">0đ</strong>
              </div>
              <div class="cart-summary-row">
                <span>Phí vận chuyển</span>
                <strong id="cart-shipping">0đ</strong>
              </div>
              <p class="cart-shipping-note" id="cart-shipping-note">Miễn phí vận chuyển cho đơn từ 500.000đ.</p>
            </div>
            <p id="cart-summary-note">Chọn sản phẩm bạn muốn thanh toán hoặc xóa khỏi giỏ.</p>
            <div class="total-price">
              Tổng: <span id="cart-total">0đ</span>
            </div>
          </div>

          <div class="cart-bottom-actions">
            <button type="button" class="cart-secondary-btn" id="remove-selected-btn">Xóa đã chọn</button>
            <button type="button" class="checkout" id="checkout-selected-btn">Thanh toán</button>
          </div>
        </div>
      </div>
    </section>
  </main>

  <script defer src="giohang.js?v=20260329-2"></script>

  <footer class="site-footer" id="site-footer"></footer>
</body>

</html>

