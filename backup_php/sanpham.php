<?php
session_start();
require_once '../dangky_dangnhap/config.php';

$pageTitle = 'Tất cả sản phẩm';

// Lọc sản phẩm (Nếu có filter)
$query = "SELECT * FROM products WHERE tinh_trang = 'con_hang' ORDER BY id DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$productsList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= $pageTitle ?> | Thuận Phát G Garden</title>
  <link rel="icon" href="../images/avatar.png" type="image/png" />
  <link href="https://fonts.googleapis.com/css2?family=Dosis&family=Red+Hat+Text&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="sanpham.css" />
  <link rel="stylesheet" href="../mainfont/main.css" />
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body data-page="products">

  <!-- ===== HEADER ===== -->
  <nav class="navigation" id="main-nav"></nav>
  <script defer src="../mainfont/layout.js"></script>
  <script defer src="../mainfont/main.js"></script>

  <div class="page-header">
    <h1>Tất cả sản phẩm</h1>
    <div class="breadcrumb">
      <a href="../trangchu/index.php">Trang chủ</a> &gt;
      <span>Tất cả sản phẩm</span>
    </div>
  </div>


  <!-- ===== MAIN CONTENT ===== -->
  <main class="container">
    <div class="grid-layout">

      <!-- Loc san pham -->
      <aside class="sidebar filter-sidebar">
        <h3 class="aside-titles">Lọc sản phẩm</h3>
        <div class="filter-group">
          <ul>
            <li class="filter-item">
              <label><input type="checkbox"> Giá dưới 100.000đ</label>
            </li>
            <li class="filter-item">
              <label><input type="checkbox"> 100.000đ - 200.000đ</label>
            </li>
            <li class="filter-item">
              <label><input type="checkbox"> 200.000đ - 300.000đ</label>
            </li>
            <li class="filter-item">
              <label><input type="checkbox"> 300.000đ - 500.000đ</label>
            </li>
            <li class="filter-item">
              <label><input type="checkbox"> 500.000đ - 1.000.000đ</label>
            </li>
            <li class="filter-item">
              <label><input type="checkbox"> Trên 1.000.000đ</label>
            </li>
          </ul>
        </div>
      </aside>

      <!-- San pham -->
      <section class="product-section">
        <div class="product-grid">
          <?php foreach ($productsList as $product): 
              $displayPercent = 0;
              if ($product['gia_goc'] > $product['gia']) {
                  $displayPercent = round((($product['gia_goc'] - $product['gia']) / $product['gia_goc']) * 100);
              }
              $isSale = $displayPercent > 0;
          ?>
            <div class="product-card">
              <a href="spchitiet.php?id=<?= $product['id'] ?>" class="product-link">
                <?php if ($isSale): ?>
                    <div class="sale-badge">-<?= $displayPercent ?>%</div>
                <?php endif; ?>
                <img src="../<?= htmlspecialchars($product['hinh_chinh']) ?>" alt="<?= htmlspecialchars($product['ten_sp']) ?>" class="product-image">
              </a>
              <div class="product-content">
                <a href="spchitiet.php?id=<?= $product['id'] ?>" style="text-decoration: none;">
                    <h3 class="product-title"><?= htmlspecialchars($product['ten_sp']) ?></h3>
                </a>
                <div class="product-price">
                  <?php if ($isSale): ?>
                    <span class="old-price"><?= number_format($product['gia_goc'], 0, '', '.') ?>đ</span>
                  <?php endif; ?>
                  <span class="current-price"><?= number_format($product['gia'], 0, '', '.') ?>đ</span>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    </div>
  </main>

  <!-- ===== FOOTER ===== -->
  <footer class="site-footer" id="site-footer"></footer>

  <script src="../giohang/giohang.js"></script>
</body>

</html>
