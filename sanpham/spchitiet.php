<?php
require_once '../dangky_dangnhap/config.php';
require_once '../includes/store_helpers.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
  header('Location: sanpham.php');
  exit;
}

$stmt = $conn->prepare("
  SELECT *
  FROM products
  WHERE id = ?
  LIMIT 1
");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
  header('Location: sanpham.php');
  exit;
}

$gallery = load_product_gallery($conn, $id);

// Keep a consistent detail-page feel for the taller polygon products by
// matching the same thumbnail density and suggestion block style the user
// prefers on product 1.
$displayGallery = in_array($id, [5, 6], true)
  ? array_slice($gallery, 0, 4)
  : $gallery;

$suggestionExcludeId = in_array($id, [5, 6], true) ? 1 : $id;
$suggestions = load_latest_products($conn, 3, $suggestionExcludeId);

$price = (float) $product['gia'];
$originalPrice = (float) ($product['gia_goc'] ?: $product['gia']);
$isSale = $originalPrice > $price;
$discountPercent = $isSale ? (int) round((($originalPrice - $price) / $originalPrice) * 100) : 0;
$isInStock = ($product['tinh_trang'] ?? 'con_hang') === 'con_hang';
$mainImagePath = public_asset_path($product['hinh_chinh']);
$mainImageUrl = '../' . $mainImagePath;
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" href="../images/avatar.png" type="image/png" />
  <title><?= htmlspecialchars($product['ten_sp']) ?> | Thuận Phát Garden</title>

  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Dosis&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Text&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Edu+NSW+ACT+Hand&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../mainfont/main.css?v=20260318-2" />
  <link rel="stylesheet" href="./spchitiet.css?v=20260316-2">
</head>

<body data-page="products">
  <nav class="navigation" id="main-nav"></nav>
    <script defer src="../mainfont/layout.js?v=20260318-2"></script>
    <script defer src="../mainfont/main.js?v=20260318-2"></script>

  <div class="container">
    <h2 class="product_title" id="product-title"><?= htmlspecialchars($product['ten_sp']) ?></h2>
    <p class="product-code">Mã sản phẩm: <strong>SP-<?= str_pad((string) $product['id'], 3, '0', STR_PAD_LEFT) ?></strong></p>
    <p class="product-rating">★★★★★ Sản phẩm đang được quan tâm</p>
    <hr>

    <div class="product-main">
      <div class="product-images">
        <?php if ($discountPercent > 0): ?>
          <div class="sale-badge">-<?= $discountPercent ?>%</div>
        <?php endif; ?>
        <img src="<?= htmlspecialchars($mainImageUrl) ?>" alt="Ảnh chính <?= htmlspecialchars($product['ten_sp']) ?>" class="product-images-main" id="main-image" onerror="this.onerror=null;this.src='../images/avatar.png';">
        <div class="product-thumbnails" id="thumbnails">
          <img src="<?= htmlspecialchars($mainImageUrl) ?>" class="product-thumb" alt="<?= htmlspecialchars($product['ten_sp']) ?>" onclick="changeImage(this.src)" onerror="this.onerror=null;this.src='../images/avatar.png';">
          <?php foreach ($displayGallery as $image): ?>
            <img src="<?= htmlspecialchars(normalize_public_path($image['duong_dan'])) ?>" class="product-thumb" alt="<?= htmlspecialchars($product['ten_sp']) ?>" onclick="changeImage(this.src)" onerror="this.onerror=null;this.src='../images/avatar.png';">
          <?php endforeach; ?>
        </div>
      </div>

      <div class="product-info-box">
        <p class="product-price">
          <span class="current-price" id="product-price"><?= htmlspecialchars(format_currency_vnd($price)) ?></span>
        </p>

        <?php if ($isSale): ?>
          <p class="old-price">Giá gốc: <del id="product-old-price"><?= htmlspecialchars(format_currency_vnd($originalPrice)) ?></del></p>
          <p class="discount" id="product-discount">Tiết kiệm: <?= htmlspecialchars(format_currency_vnd($originalPrice - $price)) ?></p>
        <?php endif; ?>

        <p class="product-status">
          Tình trạng:
          <span class="<?= $isInStock ? 'in-stock' : 'out-stock' ?>">
            <?= $isInStock ? 'Còn hàng' : 'Hết hàng' ?>
          </span>
        </p>

        <div class="product-quantity">
          <button class="btn-qty" type="button" onclick="changeQty(-1)">-</button>
          <input type="text" value="1" class="qty-input" id="qty-input" readonly>
          <button class="btn-qty" type="button" onclick="changeQty(1)">+</button>
        </div>

        <?php if ($isInStock): ?>
          <button id="buy-now" class="btn-buy" type="button" onclick="buyNow()">MUA NGAY</button>
        <?php else: ?>
          <button class="btn-buy" type="button" disabled style="opacity:0.6;cursor:not-allowed;">TẠM HẾT HÀNG</button>
        <?php endif; ?>

        <p class="hotline">Gọi để được tư vấn: <strong>0945720038</strong></p>
      </div>

      <div class="sidebar-widget">
        <h3>Sản phẩm gợi ý</h3>
        <ul>
          <li><a href="../huongdan/huongdan.html">Hướng dẫn chăm sóc cây</a></li>
          <li><a href="../tintuc/tintuc.html">Tin tức mới</a></li>
          <li><a href="../gioithieu/taisaochon.html">Vì sao nên chọn chúng tôi</a></li>
        </ul>

        <?php if (!empty($suggestions)): ?>
          <div class="product-suggestion-grid">
            <?php foreach ($suggestions as $suggestion): ?>
              <div class="suggestion-card">
                <a href="spchitiet.html?id=<?= (int) $suggestion['id'] ?>" class="suggestion-link">
                  <div class="suggestion-image-wrap">
                    <img
                      src="<?= htmlspecialchars(normalize_public_path($suggestion['hinh_chinh'])) ?>"
                      alt="<?= htmlspecialchars($suggestion['ten_sp']) ?>"
                      class="suggestion-image"
                      onerror="this.onerror=null;this.src='../images/avatar.png';">
                  </div>
                  <div class="suggestion-content">
                    <div class="suggestion-title"><?= htmlspecialchars($suggestion['ten_sp']) ?></div>
                    <div class="suggestion-price"><?= htmlspecialchars(format_currency_vnd($suggestion['gia'])) ?></div>
                  </div>
                </a>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <h3 style="margin-top: 20px;">MÔ TẢ</h3>
    <hr>
    <div class="product-description" id="product-description">
      <?= $product['mo_ta'] ?: '<p>Sản phẩm đang được cập nhật mô tả chi tiết.</p>' ?>
    </div>
  </div>

  <script src="../giohang/giohang.js?v=20260318-2"></script>
  <script>
    function changeImage(src) {
      document.getElementById('main-image').src = src;
    }

    function changeQty(delta) {
      var input = document.getElementById('qty-input');
      var value = parseInt(input.value, 10) + delta;

      if (value < 1) value = 1;
      if (value > 99) value = 99;

      input.value = value;
    }

    function buyNow() {
      var qty = parseInt(document.getElementById('qty-input').value, 10) || 1;
      addToCart({
        id: <?= (int) $product['id'] ?>,
        name: <?= json_encode($product['ten_sp']) ?>,
        price: <?= json_encode($price) ?>,
        quantity: qty,
        image: <?= json_encode($mainImagePath) ?>
      });
    }
  </script>

  <footer class="site-footer" id="site-footer"></footer>
</body>

</html>
