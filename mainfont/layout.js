var NAV_ITEMS = [
  { key: "home", href: "../trangchu/index.html", icon: "home-outline", label: "Trang Chủ" },
  { key: "products", href: "../sanpham/sanpham.html", icon: "leaf-outline", label: "Sản Phẩm" },
  { key: "about", href: "../gioithieu/gioithieu.html", icon: "information-circle-outline", label: "Giới Thiệu" },
  { key: "news", href: "../tintuc/tintuc.html", icon: "newspaper-outline", label: "Tin Tức" },
  { key: "guide", href: "../huongdan/huongdan.html", icon: "book-outline", label: "Hướng Dẫn" }
];

var FOOTER_HTML = '' +
  '<div class="footer-container">' +
  '<div class="footer-column">' +
  '<h3>Về Chúng Tôi</h3>' +
  '<p>Thuận Phát G Garden chuyên cung cấp các loại terrarium độc đáo, mang thiên nhiên đến gần hơn với bạn.</p>' +
  '<p>• Vườn nhiệt đới cây và rêu trong bể kính </p>' +
  '<p>• Trung thực với khách hàng - Sáng tạo với nghệ thuật</p>' +
  '</div>' +
  '<div class="footer-column">' +
  '<h3>Liên Kết Nhanh</h3>' +
  '<ul>' +
  '<li><a href="../trangchu/index.html">Trang Chủ</a></li>' +
  '<li><a href="../sanpham/sanpham.html">Sản Phẩm</a></li>' +
  '<li><a href="../gioithieu/gioithieu.html">Giới Thiệu</a></li>' +
  '<li><a href="../huongdan/huongdan.html">Hướng Dẫn</a></li>' +
  '<li><a href="../lienhe/lienhe.html">Liên Hệ</a></li>' +
  '</ul>' +
  '</div>' +
  '<div class="footer-column">' +
  '<h3>Liên Hệ</h3>' +
  '<p><ion-icon name="call-outline"></ion-icon> 083 977 8271</p>' +
  '<p><ion-icon name="mail-outline"></ion-icon> thuanphatggarden@gmail.com</p>' +
  '<p><ion-icon name="location-outline"></ion-icon> Quán nước HOA YÊN, 131 đường Lý Tự Trọng, Cần Thơ.</p>' +
  '<p><ion-icon name="location-outline"></ion-icon> Ngọc Trương Coffee, 372B Đ. Nguyễn Văn Cừ, Phường An Khánh, Ninh Kiều, Cần Thơ.</p>' +
  '<p><ion-icon name="location-outline"></ion-icon> Tổ Của Yến Coffee, số 13, đường Trần Ngọc Quế, P Xuân Khánh, Q Ninh Kiều, TP Cần Thơ.</p>' +
  '</div>' +
  '<div class="footer-column">' +
  '<h3>Theo Dõi Chúng Tôi</h3>' +
  '<div class="social-icons">' +
  '<a href="https://www.facebook.com/thuanphatggarden" target="_blank"><ion-icon name="logo-facebook"></ion-icon></a>' +
  '<a href="https://www.youtube.com/@anhshopcantho/featured" target="_blank"><ion-icon name="logo-youtube"></ion-icon></a>' +
  '</div>' +
  '</div>' +
  '</div>' +
  '<div class="footer-bottom">' +
  '&copy; Bản quyền thuộc về Thuận Phát G Garden.' +
  '</div>';

function buildNav(activeKey) {
  var links = '';
  for (var i = 0; i < NAV_ITEMS.length; i++) {
    var item = NAV_ITEMS[i];
    var isActive = item.key === activeKey;
    links += '<li class="list' + (isActive ? ' active' : '') + '" data-page="' + item.key + '">' +
      '<a href="' + item.href + '">' +
      '<span class="icon"><ion-icon name="' + item.icon + '"></ion-icon></span>' +
      '<span class="text">' + item.label + '</span>' +
      '</a>' +
      '</li>';
  }

  return '<div class="nav-wrapper">' +
    // Hamburger button for mobile
    '<button class="hamburger-btn" id="hamburger-btn" aria-label="Menu">' +
    '<span class="hamburger-line"></span>' +
    '<span class="hamburger-line"></span>' +
    '<span class="hamburger-line"></span>' +
    '</button>' +
    '<ul class="nav-left" id="nav-menu">' +
    links +
    '</ul>' +
    '<div class="nav-logo">' +
    '<a href="../trangchu/index.html">' +
    '<img src="../images/Head.jpg" alt="Logo" />' +
    '</a>' +
    '</div>' +
    '<ul class="nav-right">' +
    '<li class="list right-action">' +
    '<a href="../giohang/giohang.html" id="cart-toggle">' +
    '<span class="icon"><ion-icon name="cart-outline"></ion-icon></span>' +
    '<span class="text">Giỏ Hàng</span>' +
    '</a>' +
    '</li>' +
    '<li class="list right-action">' +
    '<div class="login-container">' +
    '<div class="login-trigger">' +
    '<span class="icon"><ion-icon name="person-outline"></ion-icon></span>' +
    '<span class="text">Đăng Nhập</span>' +
    '</div>' +
    '<div class="login-dropdown">' +
    '<a href="../dangky_dangnhap/dangnhap.html">Đăng Nhập</a>' +
    '<a href="../dangky_dangnhap/dangky.html">Đăng Ký</a>' +
    '</div>' +
    '</div>' +
    '</li>' +
    '</ul>' +
    '<div class="indicator"></div>' +
    '</div>' +
    '<div class="green-bar"></div>';
}

function renderNav() {
  var nav = document.getElementById("main-nav") || document.querySelector("nav.navigation");
  if (!nav) return;

  var activeKey = document.body.getAttribute("data-page") || "";
  nav.innerHTML = buildNav(activeKey);
}

function renderFooter() {
  var footer = document.getElementById("site-footer") || document.querySelector("footer.site-footer");
  if (!footer) return;
  footer.innerHTML = FOOTER_HTML;
}

document.addEventListener("DOMContentLoaded", function () {
  renderNav();
  renderFooter();
});
