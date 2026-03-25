<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" href="../images/avatar.png" type="image/png" />
  <title>Terrarium Cần Thơ | Thuận Phát G Garden</title>

  <!-- Ionicons -->
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
  <!--Fonts gg-->
  <link href="https://fonts.googleapis.com/css2?family=Dosis&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Text&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Edu+NSW+ACT+Hand&display=swap" rel="stylesheet">


  <!-- CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="../mainfont/main.css?v=20260324-6" />
  <link rel="stylesheet" href="tintuc.css">
</head>

<body data-page="news">
  <nav class="navigation" id="main-nav"></nav>
  <!-- JS -->
  <script defer src="../mainfont/layout.js?v=20260324-9"></script>
  <script defer src="../mainfont/main.js?v=20260324-6"></script>

  <!--Phần code của từng trang-->

  <div class="page" id="page--get">
    <p class="danhmuctintuc">Danh mục tin tức</p>
    <button id="open" onclick="displayYeuthich()"><i class="fa-solid fa-bars"></i> Yêu thích</button>
    <div id="notification__delete"></div>
    <div id="yeuthich" class="danhmucyeuthich">
      <button id="deletes" title="Xóa tẩt cả các thẻ yêu thích" onclick="noitifi()"><i
          class="fa-solid fa-delete-left"></i></button>
      <button id="close"><i class="fa-solid fa-backward-step"></i></button>
      <div id="content_of_sidebar"></div>
      <span id="box--sum">
        <span>Tổng số bài viết yêu thích: </span>

        <span id="sum"></span>
      </span>
    </div>
    <input type="text" id="search" placeholder="       search" title="Press Enter to search"> <button
      onclick="getValue()" class="buton">Tìm
      Kiếm</button>
    <div class="tintuc">
      <div class="flex__container">
        <div id="tintuc__list"></div>
        <div class="tintuc__docnhieu hidden__mobile">
          <p class="danhmuctintuc">Top đọc nhiều / Lượt xem</p>
          <dic id="content__docnhieu">
        </div>
      </div>
    </div>
  </div>

  <script src="tintuc.js?v=20260324-4"></script>

  <!-- Footer -->
  <footer class="site-footer" id="site-footer"></footer>
</body>

</html>








