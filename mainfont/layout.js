function getLayoutScript() {
  return document.currentScript || document.querySelector('script[src*="mainfont/layout.js"]');
}

function getAppRootUrl() {
  var script = getLayoutScript();

  if (script && script.src) {
    return new URL('../', script.src);
  }

  return new URL('../', window.location.href);
}

var APP_ROOT_URL = getAppRootUrl();

function buildAppHref(path) {
  return new URL(path, APP_ROOT_URL).pathname;
}

function buildAppUrl(path) {
  return new URL(path, APP_ROOT_URL).href;
}

var STORE_PATHS = {
  home: buildAppHref('trangchu/index.php'),
  products: buildAppHref('sanpham/sanpham.php'),
  about: buildAppHref('gioithieu/gioithieu.php'),
  news: buildAppHref('tintuc/tintuc.php'),
  guide: buildAppHref('huongdan/huongdan.php'),
  cart: buildAppHref('giohang/giohang.php'),
  login: buildAppHref('dangky_dangnhap/dangnhap.php'),
  register: buildAppHref('dangky_dangnhap/dangky.php'),
  profile: buildAppHref('dangky_dangnhap/ho_so.php'),
  logout: buildAppHref('dangky_dangnhap/logout.php'),
  admin: buildAppHref('admin/dashboard.php'),
  session: buildAppUrl('dangky_dangnhap/check_session.php')
};

var ASSET_PATHS = {
  logo: buildAppHref('images/Head.jpg')
};

var NAV_ITEMS = [
  { key: "home", href: STORE_PATHS.home, icon: "home-outline", label: "Trang chủ" },
  { key: "products", href: STORE_PATHS.products, icon: "leaf-outline", label: "Sản phẩm" },
  { key: "about", href: STORE_PATHS.about, icon: "information-circle-outline", label: "Giới thiệu" },
  { key: "news", href: STORE_PATHS.news, icon: "newspaper-outline", label: "Tin tức" },
  { key: "guide", href: STORE_PATHS.guide, icon: "book-outline", label: "Hướng dẫn" }
];

var sessionState = {
  loggedIn: false,
  userName: "",
  userAvatar: "",
  userRole: "khach",
  adminUrl: null
};

function escapeHtml(value) {
  return String(value == null ? "" : value)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#39;");
}

function clearStoredCart() {
  try {
    localStorage.removeItem("cart");
  } catch (error) { }

  if (typeof window.refreshCartBadge === "function") {
    window.refreshCartBadge([]);
  }

  try {
    window.dispatchEvent(new CustomEvent("store:cart-updated", {
      detail: { cart: [] }
    }));
  } catch (error) { }
}

function getStoredCartCount() {
  try {
    var cart = JSON.parse(localStorage.getItem("cart")) || [];
    if (!Array.isArray(cart)) {
      return 0;
    }

    var total = 0;
    for (var i = 0; i < cart.length; i++) {
      var quantity = Number(cart[i] && cart[i].quantity) || 0;
      if (quantity > 0) {
        total += quantity;
      }
    }

    return total;
  } catch (error) {
    return 0;
  }
}

function buildCartBadgeMarkup() {
  var totalCount = getStoredCartCount();
  var badgeText = totalCount > 99 ? '99+' : String(totalCount);
  var hiddenAttr = totalCount > 0 ? '' : ' hidden';
  var visibleClass = totalCount > 0 ? ' is-visible' : '';
  var label = totalCount > 0
    ? 'Giỏ hàng có ' + totalCount + ' sản phẩm'
    : 'Giỏ hàng đang trống';

  return '<span class="cart-count-badge' + visibleClass + '" id="cart-count-badge" aria-live="polite" aria-atomic="true" aria-label="' + label + '"' + hiddenAttr + '>' + badgeText + '</span>';
}

var FOOTER_HTML = '' +
  '<div class="footer-container">' +
  '<div class="footer-column">' +
  '<h3>Về Chúng Tôi</h3>' +
  '<p>Thuận Phát Garden chuyên cung cấp terrarium và cây kiểng trang trí cho không gian sống xanh hơn.</p>' +
  '<p>• Sản phẩm được chăm chút kỹ lưỡng, dễ trưng bày và dễ chăm sóc.</p>' +
  '<p>• Mua sắm rõ ràng, thuận tiện và phù hợp với nhu cầu hằng ngày.</p>' +
  '</div>' +
  '<div class="footer-column">' +
  '<h3>Liên Kết Nhanh</h3>' +
  '<ul>' +
  '<li><a href="' + STORE_PATHS.home + '">Trang chủ</a></li>' +
  '<li><a href="' + STORE_PATHS.products + '">Sản phẩm</a></li>' +
  '<li><a href="' + STORE_PATHS.about + '">Giới thiệu</a></li>' +
  '<li><a href="' + STORE_PATHS.guide + '">Hướng dẫn</a></li>' +
  '<li><a href="' + STORE_PATHS.news + '">Tin tức</a></li>' +
  '</ul>' +
  '</div>' +
  '<div class="footer-column">' +
  '<h3>Liên Hệ</h3>' +
  '<p><ion-icon name="call-outline"></ion-icon> 083 977 8271</p>' +
  '<p><ion-icon name="mail-outline"></ion-icon> thuanphatggarden@gmail.com</p>' +
  '<p><ion-icon name="location-outline"></ion-icon> 131 Lý Tự Trọng, Cần Thơ</p>' +
  '</div>' +
  '<div class="footer-column">' +
  '<h3>Theo Dõi Chúng Tôi</h3>' +
  '<div class="social-icons">' +
  '<a href="https://www.facebook.com/thuanphatggarden" target="_blank" rel="noreferrer"><ion-icon name="logo-facebook"></ion-icon></a>' +
  '<a href="https://www.youtube.com/@anhshopcantho/featured" target="_blank" rel="noreferrer"><ion-icon name="logo-youtube"></ion-icon></a>' +
  '</div>' +
  '</div>' +
  '</div>' +
  '<div class="footer-bottom">' +
  '&copy; Bản quyền thuộc về Thuận Phát Garden.' +
  '</div>';

function buildAccountDropdown() {
  if (!sessionState.loggedIn) {
    return '' +
      '<div class="login-container">' +
      '<div class="login-trigger" role="button" tabindex="0" aria-haspopup="true" aria-expanded="false">' +
      '<span class="icon"><ion-icon name="person-outline"></ion-icon></span>' +
      '<span class="text">Đăng nhập</span>' +
      '</div>' +
      '<div class="login-dropdown">' +
      '<a href="' + STORE_PATHS.login + '">Đăng nhập</a>' +
      '<a href="' + STORE_PATHS.register + '">Đăng ký</a>' +
      '</div>' +
      '</div>';
  }

  var userLabel = sessionState.userName || 'Tài khoản';
  var adminLink = sessionState.userRole === 'quan_tri' && sessionState.adminUrl
    ? '<a href="' + sessionState.adminUrl + '">Quản trị</a>'
    : '';

  return '' +
    '<div class="login-container">' +
    '<div class="login-trigger" role="button" tabindex="0" aria-haspopup="true" aria-expanded="false">' +
    '<span class="icon"><ion-icon name="person-circle-outline"></ion-icon></span>' +
    '<span class="text">' + escapeHtml(userLabel) + '</span>' +
    '</div>' +
    '<div class="login-dropdown">' +
    '<a href="' + STORE_PATHS.profile + '">Hồ sơ</a>' +
    adminLink +
    '<a href="' + STORE_PATHS.logout + '">Đăng xuất</a>' +
    '</div>' +
    '</div>';
}

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
    '<button class="hamburger-btn" id="hamburger-btn" aria-label="Mở menu">' +
    '<span class="hamburger-line"></span>' +
    '<span class="hamburger-line"></span>' +
    '<span class="hamburger-line"></span>' +
    '</button>' +
    '<ul class="nav-left" id="nav-menu">' +
    links +
    '</ul>' +
    '<div class="nav-logo">' +
    '<a href="' + STORE_PATHS.home + '">' +
    '<img src="' + ASSET_PATHS.logo + '" alt="Logo Thuận Phát Garden" />' +
    '</a>' +
    '</div>' +
    '<ul class="nav-right">' +
    '<li class="list right-action" data-page="cart">' +
    '<a href="' + STORE_PATHS.cart + '" id="cart-toggle">' +
    '<span class="icon cart-icon-wrap">' +
    '<ion-icon name="cart-outline"></ion-icon>' +
    buildCartBadgeMarkup() +
    '</span>' +
    '<span class="text">Giỏ hàng</span>' +
    '</a>' +
    '</li>' +
    '<li class="list right-action" data-page="profile">' +
    buildAccountDropdown() +
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
  document.dispatchEvent(new CustomEvent("layout:updated"));

  if (typeof window.refreshCartBadge === "function") {
    window.refreshCartBadge();
  }
}

function renderFooter() {
  var footer = document.getElementById("site-footer") || document.querySelector("footer.site-footer");
  if (!footer) return;
  footer.innerHTML = FOOTER_HTML;
}

function loadSession() {
  return fetch(STORE_PATHS.session, { credentials: "same-origin" })
    .then(function (response) { return response.json(); })
    .then(function (data) {
      sessionState.loggedIn = !!data.loggedIn;
      sessionState.userName = data.userName || "";
      sessionState.userAvatar = data.userAvatar || "";
      sessionState.userRole = data.userRole || "khach";
      sessionState.adminUrl = data.adminUrl || null;

      if (!sessionState.loggedIn) {
        clearStoredCart();
      }
    })
    .catch(function () {
      sessionState.loggedIn = false;
      sessionState.userName = "";
      sessionState.userAvatar = "";
      sessionState.userRole = "khach";
      sessionState.adminUrl = null;
      clearStoredCart();
    });
}

document.addEventListener("DOMContentLoaded", function () {
  renderNav();
  renderFooter();

  loadSession().then(function () {
    renderNav();
  });
});

