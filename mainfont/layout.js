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
  searchApi: buildAppUrl('api/search_products.php'),
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

function getSearchQuery() {
  try {
    return new URL(window.location.href).searchParams.get('q') || '';
  } catch (error) {
    return '';
  }
}

function buildSearchForm() {
  var currentQuery = escapeHtml(getSearchQuery().trim());

  return '' +
    '<form class="header-search-form" id="header-search-form" action="' + STORE_PATHS.products + '" method="get" role="search">' +
    '<label class="sr-only" for="header-search-input">Tìm kiếm sản phẩm</label>' +
    '<span class="header-search-leading-icon" aria-hidden="true"><ion-icon name="search-outline"></ion-icon></span>' +
    '<input type="search" id="header-search-input" class="header-search-input" name="q" value="' + currentQuery + '" placeholder="Tìm terrarium, cây kiểng..." autocomplete="off" spellcheck="false" />' +
    '<span class="header-search-loader" id="header-search-loader" hidden aria-hidden="true"></span>' +
    '<button type="submit" class="header-search-submit" aria-label="Tìm kiếm sản phẩm">' +
    '<ion-icon name="arrow-forward-outline"></ion-icon>' +
    '</button>' +
    '<div class="header-search-dropdown" id="header-search-dropdown" hidden></div>' +
    '</form>';
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

var SUPPORT_CONTACT = {
  phoneText: '083 977 8271',
  phoneHref: 'tel:0839778271',
  emailText: 'thuanphatggarden@gmail.com',
  emailHref: 'mailto:thuanphatggarden@gmail.com',
  facebookHref: 'https://www.facebook.com/thuanphatggarden',
  facebookText: 'Thu\u1eadn Ph\u00e1t Garden'
};

function buildSupportWidget() {
  return '' +
    '<div class="support-widget" id="support-widget">' +
    '<button type="button" class="support-fab support-fab-desktop" data-support-toggle aria-expanded="false" aria-controls="support-panel" aria-label="M\u1edf h\u1ed9p t\u01b0 v\u1ea5n">' +
    '<span class="support-fab-ring support-fab-ring-one" aria-hidden="true"></span>' +
    '<span class="support-fab-ring support-fab-ring-two" aria-hidden="true"></span>' +
    '<span class="support-fab-core">' +
    '<ion-icon name="call-outline"></ion-icon>' +
    '<span>T\u01b0 v\u1ea5n</span>' +
    '</span>' +
    '</button>' +
    '<button type="button" class="support-fab support-fab-mobile" data-support-toggle aria-expanded="false" aria-controls="support-panel" aria-label="M\u1edf h\u1ed9p t\u01b0 v\u1ea5n">' +
    '<ion-icon name="call-outline"></ion-icon>' +
    '<span>T\u01b0 v\u1ea5n</span>' +
    '</button>' +
    '<aside class="support-panel" id="support-panel" aria-hidden="true">' +
    '<button type="button" class="support-close" data-support-close aria-label="\u0110\u00f3ng t\u01b0 v\u1ea5n">' +
    '<ion-icon name="close-outline"></ion-icon>' +
    '</button>' +
    '<div class="support-panel-head">' +
    '<strong>H\u1ed7 tr\u1ee3 24/7</strong>' +
    '<p>N\u1ebfu b\u1ea1n c\u1ea7n t\u01b0 v\u1ea5n ch\u1ecdn terrarium, c\u00e1ch ch\u0103m s\u00f3c ho\u1eb7c m\u1eabu qu\u00e0 t\u1eb7ng ph\u00f9 h\u1ee3p, h\u00e3y li\u00ean h\u1ec7 nhanh qua c\u00e1c k\u00eanh b\u00ean d\u01b0\u1edbi.</p>' +
    '</div>' +
    '<div class="support-panel-list">' +
    '<a class="support-item" href="' + SUPPORT_CONTACT.phoneHref + '">' +
    '<span class="support-item-icon"><ion-icon name="call-outline"></ion-icon></span>' +
    '<span class="support-item-copy">' +
    '<strong>G\u1ecdi ngay</strong>' +
    '<span>' + SUPPORT_CONTACT.phoneText + '</span>' +
    '</span>' +
    '</a>' +
    '<a class="support-item" href="' + SUPPORT_CONTACT.emailHref + '">' +
    '<span class="support-item-icon"><ion-icon name="mail-outline"></ion-icon></span>' +
    '<span class="support-item-copy">' +
    '<strong>Email</strong>' +
    '<span>' + SUPPORT_CONTACT.emailText + '</span>' +
    '</span>' +
    '</a>' +
    '<a class="support-item" href="' + SUPPORT_CONTACT.facebookHref + '" target="_blank" rel="noreferrer">' +
    '<span class="support-item-icon"><ion-icon name="logo-facebook"></ion-icon></span>' +
    '<span class="support-item-copy">' +
    '<strong>Nh\u1eafn Facebook</strong>' +
    '<span>' + SUPPORT_CONTACT.facebookText + '</span>' +
    '</span>' +
    '</a>' +
    '</div>' +
    '</aside>' +
    '</div>';
}

function initSupportWidget() {
  if (window.supportWidgetCleanup) {
    window.supportWidgetCleanup();
    window.supportWidgetCleanup = null;
  }

  var widget = document.getElementById('support-widget');
  if (!widget) return;

  var panel = widget.querySelector('#support-panel');
  var toggles = widget.querySelectorAll('[data-support-toggle]');
  var closeButton = widget.querySelector('[data-support-close]');

  function setSupportOpen(isOpen) {
    widget.classList.toggle('is-open', !!isOpen);

    if (panel) {
      panel.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
    }

    for (var i = 0; i < toggles.length; i++) {
      toggles[i].setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    }
  }

  function handleDocumentClick(event) {
    if (!widget.contains(event.target)) {
      setSupportOpen(false);
    }
  }

  function handleEscape(event) {
    if (event.key === 'Escape') {
      setSupportOpen(false);
    }
  }

  for (var i = 0; i < toggles.length; i++) {
    toggles[i].addEventListener('click', function (event) {
      event.preventDefault();
      setSupportOpen(!widget.classList.contains('is-open'));
    });
  }

  if (closeButton) {
    closeButton.addEventListener('click', function (event) {
      event.preventDefault();
      setSupportOpen(false);
    });
  }

  document.addEventListener('click', handleDocumentClick);
  document.addEventListener('keydown', handleEscape);

  window.supportWidgetCleanup = function () {
    document.removeEventListener('click', handleDocumentClick);
    document.removeEventListener('keydown', handleEscape);
  };
}

function renderSupportWidget() {
  if (!document.body) return;

  var existing = document.getElementById('support-widget');
  if (existing) {
    existing.remove();
  }

  document.body.insertAdjacentHTML('beforeend', buildSupportWidget());
  initSupportWidget();
}

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
    '<li class="header-search-item">' +
    '<button type="button" class="search-toggle" id="search-toggle" aria-label="Mở tìm kiếm sản phẩm" aria-expanded="false" aria-controls="header-search-form">' +
    '<span class="icon"><ion-icon name="search-outline"></ion-icon></span>' +
    '<span class="text">Tìm kiếm</span>' +
    '</button>' +
    buildSearchForm() +
    '</li>' +
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
  nav.classList.remove("search-open");
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
  renderSupportWidget();

  loadSession().then(function () {
    renderNav();
  });
});

