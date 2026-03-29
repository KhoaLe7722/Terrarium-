<?php
header('Content-Type: text/html; charset=UTF-8');
ini_set('default_charset', 'UTF-8');
if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Chi tiết bài viết tin tức từ Thuận Phát Garden." />
  <link rel="icon" href="../images/avatar.png" type="image/png" />
  <title>Chi tiết tin tức | Thuận Phát Garden</title>

  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Dosis&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../mainfont/main.css?v=20260329-4">
  <link rel="stylesheet" href="detail.css?v=20260329-2">
</head>

<body data-page="news-detail">
  <nav class="navigation" id="main-nav"></nav>
  <script defer src="../mainfont/layout.js?v=20260329-4"></script>
  <script defer src="../mainfont/main.js?v=20260329-2"></script>

  <main class="news-page">
    <section class="news-article-page">
      <div class="container">
        <nav class="news-breadcrumbs" aria-label="breadcrumb">
          <a href="../trangchu/index.php">Trang chủ</a>
          <span>/</span>
          <a href="tintuc.php">Tin tức</a>
          <span>/</span>
          <span id="breadcrumb-title">Đang tải bài viết...</span>
        </nav>
      </div>

      <div class="container news-main">
        <div class="news-layout">
          <article class="news-post">
            <header class="news-post__header">
              <span class="news-post__category">Tin tức cây cảnh</span>
              <h1 class="news-post__title" id="title">Đang tải bài viết...</h1>
              <p class="news-post__meta">Đăng bởi: <strong>Thuận Phát Garden</strong></p>

              <div class="news-actions">
                <a class="news-action" href="tintuc.php" title="Quay lại trang tin tức">
                  <i class="fa-solid fa-arrow-left"></i>
                  <span>Quay lại</span>
                </a>
                <a class="news-action" id="share-mail" href="mailto:">
                  <i class="fa-solid fa-envelope"></i>
                  <span>Chia sẻ</span>
                </a>
                <button id="like" class="news-action news-action--button" type="button" aria-label="Yêu thích bài viết">
                  <i class="fa-solid fa-heart"></i>
                  <span>Yêu thích</span>
                </button>
              </div>
            </header>

            <div class="news-post__hero">
              <img src="../images/avatar.png" alt="" id="img" loading="eager">
            </div>

            <div class="news-rte">
              <div id="content" class="news-rte__lead"></div>
              <div id="article-sections" class="news-rte__sections"></div>
            </div>
          </article>

          <aside class="news-sidebar">
            <section class="news-sidebar-card">
              <div class="news-sidebar-card__header">
                <h2>Tin tức liên quan</h2>
                <p>Xem thêm một vài bài viết khác từ Thuận Phát Garden.</p>
              </div>
              <div id="tintuclienquan__container" class="news-related-list"></div>
            </section>
          </aside>
        </div>

        <section class="news-comments">
          <div class="news-comments__header">
            <div>
              <h2>Bình luận</h2>
              <p>Chia sẻ cảm nhận hoặc đặt câu hỏi ngay dưới bài viết này.</p>
            </div>
            <button id="btn__themmoi" class="news-comment-toggle open" type="button">
              <i class="fa-solid fa-comment"></i>
              <span>Viết bình luận</span>
            </button>
          </div>

          <div id="blcuaban" class="news-comments__title">
            <p>Bình luận của bạn</p>
          </div>

          <form action="" id="comment__form" class="news-comment-form">
            <div class="control-input">
              <input id="name" type="text" class="form-input" placeholder="Nhập tên của bạn.">
              <span class="control-noti"></span>
            </div>

            <div class="control-input">
              <textarea id="comment__content" class="form-input" placeholder="Bình luận ở đây."></textarea>
              <span class="control-noti"></span>
            </div>

            <div class="news-comment-form__actions">
              <button id="form-submit" type="submit">Gửi bình luận</button>
            </div>
          </form>

          <p id="notification" class="news-notification"></p>
          <div class="comment--box" id="comment__box__id"></div>
        </section>
      </div>
    </section>
  </main>

  <script src="detail.js?v=20260329-2"></script>
  <footer class="site-footer" id="site-footer"></footer>
</body>

</html>
