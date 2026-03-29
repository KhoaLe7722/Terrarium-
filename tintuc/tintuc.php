<?php
header('Content-Type: text/html; charset=UTF-8');
ini_set('default_charset', 'UTF-8');
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Tin tức cây kiểng, terrarium và cảm hứng sống xanh từ Thuận Phát Garden." />
  <link rel="icon" href="../images/avatar.png" type="image/png" />
  <title>Tin tức cây kiểng | Thuận Phát Garden</title>

  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Dosis&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../mainfont/main.css?v=20260329-4" />
  <link rel="stylesheet" href="tintuc.css?v=20260329-11" />
</head>

<body data-page="news">
  <nav class="navigation" id="main-nav"></nav>
  <script defer src="../mainfont/layout.js?v=20260329-4"></script>
  <script defer src="../mainfont/main.js?v=20260329-2"></script>

  <main class="news-catalog-page">
    <div class="news-catalog-shell news-catalog-main">
      <nav class="news-catalog-breadcrumbs" aria-label="Breadcrumb">
        <a href="../trangchu/index.php">Trang chủ</a>
        <span>/</span>
        <strong>Tin tức</strong>
      </nav>

      <section class="news-catalog-hero">
        <div class="news-catalog-hero__content">
          <span class="news-catalog-hero__kicker">Tin tức & cảm hứng xanh</span>
          <h1 class="news-catalog-hero__title">Góc chia sẻ về terrarium, cây kiểng và không gian sống thư thái</h1>
          <p class="news-catalog-hero__desc">
            Tổng hợp bài viết nổi bật từ Thuận Phát Garden, từ mẹo chăm cây đơn giản đến những gợi ý
            trưng bày giúp góc xanh trong nhà hài hòa và dễ chăm sóc hơn mỗi ngày.
          </p>
        </div>
      </section>

      <section class="news-catalog-toolbar">
        <form class="news-catalog-search" id="news-search-form" role="search">
          <label class="news-catalog-search__label" for="search-input">Tìm bài viết phù hợp</label>
          <div class="news-catalog-search__controls">
            <div class="news-catalog-search__field">
              <ion-icon name="search-outline" aria-hidden="true"></ion-icon>
              <input
                type="search"
                id="search-input"
                placeholder="Tìm theo tiêu đề hoặc chủ đề bạn quan tâm..."
                autocomplete="off" />
            </div>
            <button type="submit" class="news-catalog-button news-catalog-button--primary" id="search-button">Tìm kiếm</button>
            <button type="button" class="news-catalog-button news-catalog-button--ghost" id="search-reset" hidden>Xóa lọc</button>
          </div>
        </form>

        <button type="button" class="news-catalog-button news-catalog-button--favorite" id="favorites-open">
          <ion-icon name="heart-outline"></ion-icon>
          <span>Yêu thích</span>
          <strong id="favorite-badge">0</strong>
        </button>
      </section>

      <div class="news-catalog-toast" id="notification__delete" aria-live="polite"></div>

      <div class="news-catalog-layout">
        <section class="news-catalog-section">
          <div class="news-catalog-section__head">
            <div>
              <span class="news-section-kicker">Danh mục bài viết</span>
              <h2 id="section-title">Bài viết mới và nổi bật</h2>
            </div>
            <p id="search-state">Khám phá những nội dung phù hợp với góc xanh của bạn.</p>
          </div>

          <div id="tintuc__list" class="news-catalog-grid"></div>
          <div id="news-empty-state" class="news-empty-state" hidden>
            <ion-icon name="leaf-outline"></ion-icon>
            <h3>Chưa tìm thấy bài phù hợp</h3>
            <p>Hãy thử một từ khóa ngắn hơn hoặc bỏ dấu để kết quả rộng hơn.</p>
          </div>
        </section>

        <aside class="news-catalog-sidebar">
          <section class="news-sidebar-card">
            <div class="news-sidebar-card__head">
              <span class="news-section-kicker">Được quan tâm</span>
              <h2>Top đọc nhiều</h2>
            </div>
            <div id="content__docnhieu" class="news-trending-list"></div>
          </section>

          <section class="news-sidebar-card news-sidebar-card--note">
            <div class="news-sidebar-card__head">
              <span class="news-section-kicker">Gợi ý nhanh</span>
              <h2>Theo dõi góc xanh dễ hơn</h2>
            </div>
            <ul class="news-sidebar-note">
              <li>Lưu bài bạn thích để mở lại nhanh khi cần tham khảo.</li>
              <li>Xem mục đọc nhiều để bắt đầu từ những nội dung được quan tâm nhất.</li>
              <li>Khi cần chăm terrarium kỹ hơn, bạn có thể xem thêm ở trang hướng dẫn chi tiết.</li>
            </ul>
            <a class="news-sidebar-link" href="../huongdan/huongdan.php">Mở trang hướng dẫn</a>
          </section>
        </aside>
      </div>
    </div>

    <div class="news-favorites-overlay" id="favorites-overlay" hidden></div>

    <aside class="news-favorites-drawer" id="favorites-drawer" aria-hidden="true">
      <div class="news-favorites-drawer__head">
        <div>
          <span class="news-section-kicker">Danh sách lưu</span>
          <h2>Bài viết yêu thích</h2>
        </div>
        <button type="button" class="news-icon-button" id="favorites-close" aria-label="Đóng danh sách yêu thích">
          <ion-icon name="close-outline"></ion-icon>
        </button>
      </div>

      <div class="news-favorites-drawer__meta">
        <p>Tổng bài đang lưu: <strong id="sum">0</strong></p>
        <button type="button" class="news-catalog-button news-catalog-button--ghost" id="favorites-clear">Xóa tất cả</button>
      </div>

      <div id="content_of_sidebar" class="news-favorites-list"></div>
    </aside>
  </main>

  <script src="tintuc.js?v=20260329-7"></script>

  <footer class="site-footer" id="site-footer"></footer>
</body>

</html>
