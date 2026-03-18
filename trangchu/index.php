<?php
require_once '../dangky_dangnhap/config.php';
require_once '../includes/store_helpers.php';

$stmt = $conn->query("
    SELECT id, ten_sp, gia, hinh_chinh
    FROM products
    WHERE tinh_trang = 'con_hang' AND id <> 8
    ORDER BY id ASC
");
$featuredProducts = $stmt->fetchAll();
$featuredPayload = array_map(
  static function (array $product): array {
    return [
      'id' => (int) $product['id'],
      'name' => $product['ten_sp'],
      'price' => format_currency_vnd($product['gia']),
      'image' => normalize_public_path($product['hinh_chinh']),
      'href' => '../sanpham/spchitiet.php?id=' . (int) $product['id'],
    ];
  },
  $featuredProducts
);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="0" />
  <link rel="icon" href="../images/avatar.png" type="image/png" />
  <title>Terrarium Cần Thơ | Thuận Phát Garden</title>

  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Dosis&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Text&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Edu+NSW+ACT+Hand&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../mainfont/main.css?v=20260318-2" />
  <link rel="stylesheet" href="index.css?v=20260316-11" />
  <style>
    .home-featured-wrap {
      max-width: 1280px;
      margin: 50px auto;
      padding: 22px;
      text-align: center;
    }

    .home-featured-wrap h2 {
      text-align: center;
      font-size: 32px;
      color: #000;
      margin-bottom: 40px;
      font-weight: bold;
    }

    .home-featured-wrap h2::after {
      content: "";
      display: block;
      width: 300px;
      height: 4px;
      background-color: #54794a;
      margin: 10px auto 0;
      border-radius: 2px;
    }

    .home-featured-empty {
      padding: 40px 0;
      color: #666;
    }

    .home-featured-carousel {
      position: relative;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .home-featured-window {
      width: 100%;
      overflow: hidden;
      touch-action: pan-y;
    }

    .home-featured-track {
      --home-featured-card-width: 280px;
      display: flex;
      gap: 18px;
      align-items: stretch;
      will-change: transform;
    }

    .home-featured-track.is-compact-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .home-featured-card {
      flex: 0 0 var(--home-featured-card-width);
      max-width: var(--home-featured-card-width);
      background: #fff;
      border-radius: 14px;
      box-shadow: 0 12px 26px rgba(27, 44, 20, 0.12);
      padding: 12px;
      overflow: hidden;
      text-align: center;
      transition: transform 0.3s ease;
    }

    .home-featured-card:hover {
      transform: translateY(-6px);
    }

    .home-featured-card a {
      display: flex;
      flex-direction: column;
      height: 100%;
      text-decoration: none;
    }

    .home-featured-image {
      width: 100%;
      height: 228px;
      margin: 0 auto;
      border-radius: 20px;
      overflow: hidden;
    }

    .home-featured-card img {
      display: block;
      width: 100%;
      height: 100%;
      object-fit: contain;
      border-radius: 20px;
    }

    .home-featured-card h3 {
      margin: 10px 0 4px;
      font-size: 1rem;
      line-height: 1.35;
      color: #333;
      min-height: 2.7em;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .home-featured-card p {
      margin-top: auto;
      color: #54794a;
      font-weight: bold;
      font-size: 0.98rem;
    }

    .home-featured-arrow {
      width: 44px;
      height: 44px;
      border: none;
      border-radius: 50%;
      background: #54794a;
      color: #fff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      flex: 0 0 44px;
      box-shadow: 0 8px 16px rgba(84, 121, 74, 0.22);
      transition: transform 0.2s ease, background 0.2s ease;
    }

    .home-featured-arrow:not(:disabled):hover {
      background: #6a965f;
      transform: translateY(-2px);
    }

    .home-featured-arrow:disabled,
    .home-featured-arrow.is-disabled {
      opacity: 0.45;
      cursor: default;
      box-shadow: none;
      background: #92ab8a;
      transform: none;
    }

    .home-featured-arrow ion-icon {
      font-size: 22px;
    }

    @media (max-width: 1200px) {
      .home-featured-wrap {
        padding: 18px;
      }

      .home-featured-track {
        gap: 16px;
      }

      .home-featured-image {
        height: 220px;
      }
    }

    @media (max-width: 900px) {
      .home-featured-wrap {
        padding: 15px;
        margin: 30px auto;
      }

      .home-featured-wrap h2 {
        font-size: 26px;
        margin-bottom: 25px;
      }

      .home-featured-wrap h2::after {
        width: 200px;
      }

      .home-featured-carousel {
        gap: 0;
        padding: 0 18px;
      }

      .home-featured-track {
        gap: 14px;
      }

      .home-featured-card {
        padding: 10px;
        border-radius: 16px;
      }

      .home-featured-image {
        height: 210px;
        border-radius: 18px;
      }

      .home-featured-arrow {
        position: absolute;
        top: 50%;
        width: 38px;
        height: 38px;
        flex-basis: auto;
        transform: translateY(-50%);
        z-index: 1;
      }

      #home-featured-prev {
        left: 0;
      }

      #home-featured-next {
        right: 0;
      }

      .home-featured-arrow:not(:disabled):hover {
        transform: translateY(-50%);
      }
    }

    @media (max-width: 575px) {
      .home-featured-wrap {
        padding: 10px;
        margin: 20px auto;
      }

      .home-featured-wrap h2 {
        font-size: 22px;
        margin-bottom: 20px;
      }

      .home-featured-wrap h2::after {
        width: 150px;
        height: 3px;
      }

      .home-featured-carousel {
        padding: 0 10px;
      }

      .home-featured-track {
        gap: 10px;
      }

      .home-featured-image {
        height: 190px;
      }

      .home-featured-arrow {
        width: 34px;
        height: 34px;
      }
    }

    @media (max-width: 480px) {
      .home-featured-wrap {
        max-width: 430px;
        padding: 8px;
      }

      .home-featured-wrap h2 {
        font-size: 20px;
        margin-bottom: 16px;
      }

      .home-featured-carousel {
        padding: 0 8px;
      }

      .home-featured-track.is-compact-grid {
        gap: 10px;
      }

      .home-featured-track.is-compact-grid .home-featured-card {
        width: 100%;
        max-width: none;
        min-width: 0;
        padding: 8px;
        border-radius: 12px;
        box-shadow: 0 8px 18px rgba(27, 44, 20, 0.11);
      }

      .home-featured-track.is-compact-grid .home-featured-card:hover {
        transform: none;
      }

      .home-featured-track.is-compact-grid .home-featured-image {
        height: clamp(80px, 21vw, 92px);
        border-radius: 14px;
      }

      .home-featured-track.is-compact-grid .home-featured-card img {
        border-radius: 14px;
      }

      .home-featured-track.is-compact-grid .home-featured-card h3 {
        margin: 8px 0 4px;
        min-height: 2.5em;
        font-size: 0.82rem;
        line-height: 1.25;
      }

      .home-featured-track.is-compact-grid .home-featured-card p {
        font-size: 0.84rem;
      }

      .home-featured-arrow {
        width: 30px;
        height: 30px;
      }

      .home-featured-arrow ion-icon {
        font-size: 18px;
      }
    }
  </style>
</head>

<body data-page="home">
  <nav class="navigation" id="main-nav"></nav>
  <script defer src="../mainfont/layout.js?v=20260318-2"></script>
  <script defer src="../mainfont/main.js?v=20260318-2"></script>

  <div class="slider">
    <div class="slides">
      <div class="slider-arrow prev"><ion-icon name="arrow-back-circle-outline"></ion-icon></div>
      <div class="slider-arrow next"><ion-icon name="arrow-forward-circle-outline"></ion-icon></div>
      <a href="../gioithieu/gioithieu.html">
        <img src="../images/trangchu/TERRAIUM (1).png" alt="Giới thiệu terrarium" />
      </a>
      <a href="../sanpham/sanpham.php">
        <img src="../images/trangchu/TERRAIUM2.png" alt="Bộ sưu tập terrarium" />
      </a>
      <a href="../tintuc/tintuc.html">
        <img src="../images/trangchu/TERRAIUM3.png" alt="Tin tức terrarium" />
      </a>
      <a href="../tintuc/tintuc.html">
        <img src="../images/trangchu/Terrarium – nghệ thuật xanh xóa nhòa khoảng cách thế hệ, kết nối mọi lứa tuổi bằng tình yêu thiên nhiên..png" alt="Terrarium nghệ thuật xanh" />
      </a>
      <a href="../huongdan/huongdan.html">
        <img src="../images/trangchu/TERRAIUM 5.png" alt="Hướng dẫn chăm sóc terrarium" />
      </a>
      <a href="../gioithieu/taisaochon.html">
        <img src="../images/trangchu/Terrarium – chill có gu, sống có chất..png" alt="Terrarium chill có gu" />
      </a>
    </div>
  </div>

  <div class="dots">
    <span class="dot active"></span>
    <span class="dot"></span>
    <span class="dot"></span>
    <span class="dot"></span>
    <span class="dot"></span>
    <span class="dot"></span>
  </div>

  <script defer src="index.js?v=20260316-11"></script>

  <section class="home-featured-wrap">
    <h2>SẢN PHẨM NỔI BẬT</h2>

    <?php if (empty($featuredProducts)): ?>
      <div class="home-featured-empty">
        Chưa có sản phẩm nào. Hãy thêm sản phẩm từ khu admin để bắt đầu bán hàng.
      </div>
    <?php else: ?>
      <div class="home-featured-carousel">
        <button class="home-featured-arrow" id="home-featured-prev" type="button" aria-label="Sản phẩm trước">
          <ion-icon name="chevron-back-outline"></ion-icon>
        </button>
        <div class="home-featured-window">
          <div class="home-featured-track" id="home-featured-track"></div>
        </div>
        <button class="home-featured-arrow" id="home-featured-next" type="button" aria-label="Sản phẩm tiếp theo">
          <ion-icon name="chevron-forward-outline"></ion-icon>
        </button>
      </div>
      <script>
        window.homeFeaturedProducts = <?= json_encode($featuredPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
      </script>
    <?php endif; ?>
  </section>

  <footer class="site-footer" id="site-footer"></footer>

  <script>
    (function() {
      var products = window.homeFeaturedProducts || [];
      var track = document.getElementById("home-featured-track");
      var prevButton = document.getElementById("home-featured-prev");
      var nextButton = document.getElementById("home-featured-next");
      var viewport = document.querySelector(".home-featured-window");
      var resizeTimer = null;
      var touchStartX = 0;
      var touchDeltaX = 0;

      if (!track || products.length === 0) {
        return;
      }

      var currentStart = 0;

      function getViewportWidth() {
        if (viewport && viewport.clientWidth) {
          return viewport.clientWidth;
        }

        return track.clientWidth || window.innerWidth || 0;
      }

      function shouldUseCompactGrid() {
        return getViewportWidth() <= 480;
      }

      function getCardsPerView() {
        var width = getViewportWidth();

        if (shouldUseCompactGrid()) {
          return 4;
        }

        if (width < 880) {
          return 2;
        }

        if (width < 1000) {
          return 3;
        }

        return 4;
      }

      function getTrackGap() {
        var width = getViewportWidth();

        if (shouldUseCompactGrid()) {
          return 10;
        }

        if (width <= 575) {
          return 12;
        }

        if (width <= 900) {
          return 14;
        }

        if (width <= 1200) {
          return 16;
        }

        return 18;
      }

      function getVisibleCount() {
        return Math.min(getCardsPerView(), products.length);
      }

      function updateLayout(cardCount) {
        var gap = getTrackGap();
        var totalGap = Math.max(cardCount - 1, 0) * gap;
        var availableWidth = getViewportWidth();
        var isCompactGrid = shouldUseCompactGrid();
        var cardWidth = availableWidth > 0
          ? Math.floor((availableWidth - totalGap) / cardCount)
          : 280;

        track.style.gap = gap + "px";
        track.classList.toggle("is-compact-grid", isCompactGrid);

        if (isCompactGrid) {
          track.style.setProperty("--home-featured-card-width", "100%");
          return;
        }

        track.style.setProperty("--home-featured-card-width", cardWidth + "px");
      }

      function getStepSize() {
        if (shouldUseCompactGrid()) {
          return getVisibleCount();
        }

        return 1;
      }

      function updateControls(cardCount) {
        var disableControls = products.length <= cardCount;

        [prevButton, nextButton].forEach(function(button) {
          if (!button) {
            return;
          }

          button.disabled = disableControls;
          button.classList.toggle("is-disabled", disableControls);
        });
      }

      function escapeHtml(value) {
        return String(value)
          .replace(/&/g, "&amp;")
          .replace(/</g, "&lt;")
          .replace(/>/g, "&gt;")
          .replace(/"/g, "&quot;")
          .replace(/'/g, "&#39;");
      }

      function buildCard(product) {
        return '' +
          '<article class="home-featured-card">' +
          '<a href="' + escapeHtml(product.href) + '">' +
          '<div class="home-featured-image">' +
          '<img src="' + escapeHtml(product.image) + '" alt="' + escapeHtml(product.name) + '" onerror="this.onerror=null;this.src=\'../images/avatar.png\';">' +
          '</div>' +
          '<h3>' + escapeHtml(product.name) + '</h3>' +
          '<p>' + escapeHtml(product.price) + '</p>' +
          '</a>' +
          '</article>';
      }

      function render() {
        var cardCount = getVisibleCount();
        var html = "";

        updateLayout(cardCount);

        for (var i = 0; i < cardCount; i += 1) {
          html += buildCard(products[(currentStart + i) % products.length]);
        }

        track.innerHTML = html;
        track.style.transition = "none";
        track.style.transform = "translateX(0)";
        updateControls(cardCount);
      }

      function showNext() {
        if (products.length <= getVisibleCount()) {
          return;
        }

        currentStart = (currentStart + getStepSize()) % products.length;
        render();
      }

      function showPrevious() {
        if (products.length <= getVisibleCount()) {
          return;
        }

        currentStart = (currentStart - getStepSize() + products.length) % products.length;
        render();
      }

      function resetTouch() {
        touchStartX = 0;
        touchDeltaX = 0;
      }

      if (prevButton) {
        prevButton.addEventListener("click", function() {
          showPrevious();
        });
      }

      if (nextButton) {
        nextButton.addEventListener("click", function() {
          showNext();
        });
      }

      if (viewport) {
        viewport.addEventListener("touchstart", function(event) {
          if (event.touches.length !== 1) {
            return;
          }

          touchStartX = event.touches[0].clientX;
          touchDeltaX = 0;
        }, {
          passive: true
        });

        viewport.addEventListener("touchmove", function(event) {
          if (!touchStartX || event.touches.length !== 1) {
            return;
          }

          touchDeltaX = event.touches[0].clientX - touchStartX;
        }, {
          passive: true
        });

        viewport.addEventListener("touchend", function() {
          if (Math.abs(touchDeltaX) >= 45) {
            if (touchDeltaX < 0) {
              showNext();
            } else {
              showPrevious();
            }
          }

          resetTouch();
        });

        viewport.addEventListener("touchcancel", resetTouch);
      }

      window.addEventListener("resize", function() {
        window.clearTimeout(resizeTimer);
        resizeTimer = window.setTimeout(function() {
          render();
        }, 120);
      });

      render();
    })();
  </script>
</body>

</html>
