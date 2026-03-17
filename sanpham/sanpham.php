<?php
require_once '../dangky_dangnhap/config.php';
require_once '../includes/store_helpers.php';

$stmt = $conn->query("
    SELECT id, ten_sp, gia, hinh_chinh
    FROM products
    WHERE tinh_trang = 'con_hang'
    ORDER BY id ASC
");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tất cả sản phẩm | Thuận Phát G Garden</title>
  <link rel="icon" href="../images/avatar.png" type="image/png" />
  <link href="https://fonts.googleapis.com/css2?family=Dosis&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="sanpham.css" />
  <link rel="stylesheet" href="../mainfont/main.css" />
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body data-page="products">
  <nav class="navigation" id="main-nav"></nav>
  <script defer src="../mainfont/layout.js"></script>
  <script defer src="../mainfont/main.js"></script>

  <div class="page-header">
    <h1>Tất cả sản phẩm</h1>
    <div class="breadcrumb">
      <a href="../trangchu/index.html">Trang chủ</a> &gt;
      <span>Tất cả sản phẩm</span>
    </div>
  </div>

  <main class="container">
    <div class="grid-layout">
      <aside class="sidebar filter-sidebar">
        <h3 class="aside-titles">Lọc sản phẩm</h3>
        <div class="filter-group">
          <ul>
            <li class="filter-item"><label><input type="checkbox" value="0-100000"> Giá dưới 100.000đ</label></li>
            <li class="filter-item"><label><input type="checkbox" value="100000-200000"> 100.000đ - 200.000đ</label></li>
            <li class="filter-item"><label><input type="checkbox" value="200000-300000"> 200.000đ - 300.000đ</label></li>
            <li class="filter-item"><label><input type="checkbox" value="300000-500000"> 300.000đ - 500.000đ</label></li>
            <li class="filter-item"><label><input type="checkbox" value="500000-1000000"> 500.000đ - 1.000.000đ</label></li>
            <li class="filter-item"><label><input type="checkbox" value="1000000-999999999"> Trên 1.000.000đ</label></li>
          </ul>
        </div>
      </aside>

      <section class="product-section">
        <div class="product-grid" id="product-grid">
          <?php if (empty($products)): ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px 0; color: #666;">
              Chưa có sản phẩm nào.
            </div>
          <?php else: ?>
            <?php foreach ($products as $product): ?>
              <div class="product-card" data-price="<?= htmlspecialchars((string) $product['gia']) ?>">
                <a href="spchitiet.html?id=<?= (int) $product['id'] ?>" class="product-link">
                  <div class="product-image-container">
                    <img
                      class="product-image"
                      src="<?= htmlspecialchars(normalize_public_path($product['hinh_chinh'])) ?>"
                      alt="<?= htmlspecialchars($product['ten_sp']) ?>"
                      onerror="this.onerror=null;this.src='../images/avatar.png';">
                  </div>
                </a>
                <div class="product-content">
                  <div class="product-title"><?= htmlspecialchars($product['ten_sp']) ?></div>
                  <div class="product-price"><?= htmlspecialchars(format_currency_vnd($product['gia'])) ?></div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </section>
    </div>
  </main>

  <footer class="site-footer" id="site-footer"></footer>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      var checkboxes = document.querySelectorAll(".filter-item input[type='checkbox']");
      var cards = document.querySelectorAll(".product-card");

      function filterProducts() {
        var ranges = Array.prototype.slice.call(checkboxes)
          .filter(function (checkbox) { return checkbox.checked; })
          .map(function (checkbox) { return checkbox.value.split("-").map(Number); });

        cards.forEach(function (card) {
          var price = Number(card.getAttribute("data-price"));
          var visible = ranges.length === 0 || ranges.some(function (range) {
            return price >= range[0] && price <= range[1];
          });
          card.style.display = visible ? "" : "none";
        });
      }

      checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener("change", filterProducts);
      });
    });
  </script>
</body>

</html>
