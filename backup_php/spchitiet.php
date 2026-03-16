<?php
session_start();
require_once '../dangky_dangnhap/config.php';

$id = $_GET['id'] ?? 0;
if (!$id) {
    header('Location: sanpham.php');
    exit;
}

// Lấy thông tin sản phẩm
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: sanpham.php');
    exit;
}

// Lấy ảnh phụ
$stmtImg = $conn->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY thu_tu ASC");
$stmtImg->execute([$id]);
$gallery = $stmtImg->fetchAll(PDO::FETCH_ASSOC);

$displayPercent = 0;
if ($product['gia_goc'] > $product['gia']) {
    $displayPercent = round((($product['gia_goc'] - $product['gia']) / $product['gia_goc']) * 100);
}
$isSale = $displayPercent > 0;
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" href="../images/avatar.png" type="image/png" />
  <title><?= htmlspecialchars($product['ten_sp']) ?> | Thuận Phát G Garden</title>

  <!-- Ionicons -->
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
  
  <link href="https://fonts.googleapis.com/css2?family=Dosis&family=Red+Hat+Text&display=swap" rel="stylesheet">

  <!-- CSS -->
  <link rel="stylesheet" href="../mainfont/main.css" />
  <link rel="stylesheet" href="./spchitiet.css">
</head>

<body>
  <nav class="navigation" id="main-nav"></nav>
  <script defer src="../mainfont/layout.js"></script>
  <script defer src="../mainfont/main.js"></script>

  <div class="container" style="margin-top: 150px;">
    <h2 class="product_title" id="product-title" style="margin-top: 0;"><?= htmlspecialchars($product['ten_sp']) ?></h2>
    <p class="product-code">Mã sản phẩm: <strong>SP-<?= str_pad($product['id'], 3, '0', STR_PAD_LEFT) ?></strong></p>
    <p class="product-rating">★★★★★ Viết đánh giá của bạn</p>
    <hr>

    <div class="product-main">
      <div class="product-images">
        <?php if ($isSale): ?>
            <div class="sale-badge">-<?= $displayPercent ?>%</div>
        <?php endif; ?>
        <img src="../<?= htmlspecialchars($product['hinh_chinh']) ?>" alt="Ảnh chính" class="product-images-main" id="main-image">
        <div class="product-thumbnails" id="thumbnails">
          <img src="../<?= htmlspecialchars($product['hinh_chinh']) ?>" class="product-thumb" onclick="changeImage(this.src)">
          <?php foreach ($gallery as $img): ?>
            <img src="../<?= htmlspecialchars($img['duong_dan']) ?>" class="product-thumb" onclick="changeImage(this.src)">
          <?php endforeach; ?>
        </div>
      </div>

      <div class="product-info-box">
        <p class="product-price">
            <?php if ($isSale): ?>
                <span class="old-price" id="product-old-price"><?= number_format($product['gia_goc'], 0, '', '.') ?>đ</span>
            <?php endif; ?>
            <span class="current-price" id="product-price"><?= number_format($product['gia'], 0, '', '.') ?>đ</span>
        </p>
        
        <?php if ($isSale): 
            $saving = $product['gia_goc'] - $product['gia'];
        ?>
            <p class="discount" id="product-discount">Tiết kiệm: <?= number_format($saving, 0, '', '.') ?>đ</p>
        <?php endif; ?>
        
        <p class="product-status">Tình trạng: <span class="in-stock"><?= $product['tinh_trang'] == 'con_hang' ? 'Còn hàng' : 'Hết hàng' ?></span></p>

        <div class="product-quantity">
          <button class="btn-qty" onclick="changeQty(-1)">-</button>
          <input type="text" value="1" class="qty-input" id="qty-input" readonly>
          <button class="btn-qty" onclick="changeQty(1)">+</button>
        </div>

        <button id="buy-now" class="btn-buy" onclick="addToCartTrigger()">MUA NGAY</button>

        <p class="hotline">Gọi để được tư vấn: <strong>0945720038</strong></p>
      </div>

      <div class="sidebar-widget">
        <h3>Hướng dẫn chăm sóc cây</h3>
        <ul>
          <li><a href="../huongdan/huongdan.html">CÁCH CHĂM SÓC CÂY CHO NGƯỜI MỚI SETUP</a></li>
          <li><a href="../huongdan/huongdan.html">TIN TỨC MỚI TERRARIUMS</a></li>
          <li><a href="../gioithieu/taisaochon.html">NHỮNG LÝ DO BẠN TIN TƯỞNG CHÚNG TÔI</a></li>
        </ul>
      </div>
    </div>

    <h3 style="margin-top: 40px;">MÔ TẢ</h3>
    <hr>
    <div class="product-description" id="product-description">
      <?= $product['mo_ta'] ?>
    </div>
  </div>

  <script src="../giohang/giohang.js"></script>
  <script>
    function changeImage(src) {
        document.getElementById('main-image').src = src;
    }

    function changeQty(delta) {
        const input = document.getElementById('qty-input');
        let val = parseInt(input.value) + delta;
        if (val < 1) val = 1;
        if (val > 99) val = 99;
        input.value = val;
    }

    function addToCartTrigger() {
        const qty = parseInt(document.getElementById('qty-input').value);
        addToCart({
            id: <?= $product['id'] ?>,
            name: '<?= addslashes($product['ten_sp']) ?>',
            price: <?= $product['gia'] ?>,
            quantity: qty,
            image: '<?= addslashes($product['hinh_chinh']) ?>'
        });
    }
  </script>

  <footer class="site-footer" id="site-footer"></footer>
</body>

</html>
