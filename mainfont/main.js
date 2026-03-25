function readStoredCart() {
  try {
    const storedCart = JSON.parse(localStorage.getItem("cart")) || [];
    return Array.isArray(storedCart) ? storedCart : [];
  } catch (error) {
    return [];
  }
}

function getCartQuantity(cart) {
  const cartItems = Array.isArray(cart) ? cart : readStoredCart();

  return cartItems.reduce(function (total, item) {
    const quantity = Number(item && item.quantity) || 0;
    return total + (quantity > 0 ? quantity : 0);
  }, 0);
}

function refreshCartBadge(cart) {
  const badges = document.querySelectorAll(".cart-count-badge");
  if (!badges.length) return;

  const totalQuantity = getCartQuantity(cart);
  const badgeText = totalQuantity > 99 ? "99+" : String(totalQuantity);

  for (var i = 0; i < badges.length; i++) {
    var badge = badges[i];
    var hasItems = totalQuantity > 0;

    badge.textContent = hasItems ? badgeText : "0";
    badge.classList.toggle("is-visible", hasItems);
    badge.hidden = !hasItems;
    badge.setAttribute("aria-label", hasItems
      ? "Giỏ hàng có " + totalQuantity + " sản phẩm"
      : "Giỏ hàng đang trống");
  }
}

window.refreshCartBadge = refreshCartBadge;

document.addEventListener("layout:updated", function () {
  refreshCartBadge();
});

document.addEventListener("DOMContentLoaded", function () {
  refreshCartBadge();
});

window.addEventListener("storage", function (event) {
  if (!event || event.key === "cart") {
    refreshCartBadge();
  }
});

window.addEventListener("store:cart-updated", function (event) {
  refreshCartBadge(event && event.detail ? event.detail.cart : null);
});

// ==== Indicator Nav (Thanh chỉ mục xanh) ====
function initNavigation() {
  var nav = document.getElementById('main-nav');
  if (!nav) return;

  var lists = nav.querySelectorAll('.list');
  var indicator = nav.querySelector('.indicator');

  // Di chuyển indicator theo icon
  function moveIndicatorTo(item, withTransition) {
    if (withTransition === undefined) withTransition = true;
    if (!indicator) return;
    var icon = item.querySelector('.icon');
    var iconRect = icon.getBoundingClientRect();
    var navRect = nav.getBoundingClientRect();
    var left = iconRect.left - navRect.left + icon.offsetWidth / 2 - 30;

    indicator.style.left = left + 'px';
    indicator.style.transition = withTransition ? 'left 0.3s ease' : 'none';

    // Nếu không có hiệu ứng thì bật lại sau 1 frame
    if (!withTransition) {
      requestAnimationFrame(function () {
        indicator.style.transition = 'left 0.3s ease';
      });
    }
  }

  // Đặt menu active dựa vào URL
  function setActiveByURL() {
    if (!indicator || !lists.length) return;

    var currentPath = window.location.pathname;
    var pageKey = document.body.getAttribute('data-page') || "";
    var matched = false;

    for (var i = 0; i < lists.length; i++) {
      var item = lists[i];
      var link = item.querySelector("a");
      var href = link.getAttribute("href");
      var itemKey = item.getAttribute('data-page');
      var resolvedPath = "";

      if (href) {
        try {
          resolvedPath = new URL(href, window.location.href).pathname;
        } catch (e) {
          resolvedPath = "";
        }
      }

      var isActive = (pageKey && itemKey === pageKey) || (resolvedPath && currentPath === resolvedPath);

      if (isActive) {
        item.classList.add("active");
        if (!item.classList.contains("right-action")) {
          matched = true;
          moveIndicatorTo(item, false);
          indicator.style.opacity = 1;
        }
      } else {
        item.classList.remove("active");
      }
    }

    if (!matched) {
      indicator.style.opacity = 0;
    } else {
      try { localStorage.removeItem("navActiveIndex"); } catch (e) { }
    }
  }

  // Xử lý click vào menu
  for (var j = 0; j < lists.length; j++) {
    (function (index) {
      var item = lists[index];
      var link = item.querySelector('a');

      link.addEventListener('click', function (e) {
        e.preventDefault();

        if (item.classList.contains("right-action")) {
          try { localStorage.removeItem("navActiveIndex"); } catch (e) { }
          for (var k = 0; k < lists.length; k++) {
            lists[k].classList.remove("active");
          }
          if (indicator) indicator.style.opacity = 0;
        } else {
          try { localStorage.setItem("navActiveIndex", index); } catch (e) { }
          for (var k = 0; k < lists.length; k++) {
            lists[k].classList.remove("active");
          }
          item.classList.add("active");
          if (indicator) indicator.style.opacity = 1;
          moveIndicatorTo(item, true);
        }

        // Điều hướng sau 300ms
        setTimeout(function () {
          window.location.href = link.getAttribute("href");
        }, 300);
      });
    })(j);
  }

  // Tự set active khi load trang
  window.addEventListener('load', setActiveByURL);

  // ==== Ẩn hiện thanh menu khi cuộn ====
  var lastScrollY = window.pageYOffset || document.documentElement.scrollTop;
  window.addEventListener('scroll', function () {
    var currentScrollY = window.pageYOffset || document.documentElement.scrollTop;

    if (currentScrollY > lastScrollY && currentScrollY > 10) {
      nav.classList.add('nav-hidden');
    } else {
      nav.classList.remove('nav-hidden');
    }

    lastScrollY = currentScrollY;
  });

  // ==== Dropdown đăng nhập ====
  if (window.accountMenuCleanup) {
    window.accountMenuCleanup();
    window.accountMenuCleanup = null;
  }

  var loginContainer = nav.querySelector('.login-container');
  var loginTrigger = loginContainer ? loginContainer.querySelector('.login-trigger') : null;

  if (loginContainer && loginTrigger) {
    var accountCloseTimer = null;
    var dropdownLinks = loginContainer.querySelectorAll('.login-dropdown a');

    function clearAccountCloseTimer() {
      if (accountCloseTimer) {
        clearTimeout(accountCloseTimer);
        accountCloseTimer = null;
      }
    }

    function setAccountMenuOpen(isOpen) {
      clearAccountCloseTimer();
      loginContainer.classList.toggle('active', isOpen);
      loginTrigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    }

    function scheduleAccountMenuClose() {
      clearAccountCloseTimer();
      accountCloseTimer = setTimeout(function () {
        setAccountMenuOpen(false);
      }, 220);
    }

    loginContainer.addEventListener('mouseenter', function () {
      setAccountMenuOpen(true);
    });

    loginContainer.addEventListener('mouseleave', function () {
      scheduleAccountMenuClose();
    });

    loginContainer.addEventListener('click', function (e) {
      e.stopPropagation();
    });

    loginTrigger.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      setAccountMenuOpen(!loginContainer.classList.contains('active'));
    });

    loginTrigger.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        setAccountMenuOpen(!loginContainer.classList.contains('active'));
      }

      if (e.key === 'Escape') {
        setAccountMenuOpen(false);
      }
    });

    var closeAccountMenu = function (e) {
      if (!loginContainer.contains(e.target)) {
        setAccountMenuOpen(false);
      }
    };

    var closeAccountMenuOnEscape = function (e) {
      if (e.key === 'Escape') {
        setAccountMenuOpen(false);
      }
    };

    for (var n = 0; n < dropdownLinks.length; n++) {
      dropdownLinks[n].addEventListener('click', function () {
        clearAccountCloseTimer();
        setAccountMenuOpen(false);
      });
    }

    document.addEventListener('click', closeAccountMenu);
    document.addEventListener('keydown', closeAccountMenuOnEscape);
    window.accountMenuCleanup = function () {
      clearAccountCloseTimer();
      document.removeEventListener('click', closeAccountMenu);
      document.removeEventListener('keydown', closeAccountMenuOnEscape);
    };
  }

  // ==== Hamburger Menu Toggle ====
  var hamburgerBtn = document.getElementById('hamburger-btn');
  var navMenu = document.getElementById('nav-menu');

  if (hamburgerBtn && navMenu) {
    hamburgerBtn.addEventListener('click', function () {
      hamburgerBtn.classList.toggle('active');
      navMenu.classList.toggle('menu-open');
    });

    // Đóng menu khi click vào link
    var menuLinks = navMenu.querySelectorAll('a');
    for (var m = 0; m < menuLinks.length; m++) {
      menuLinks[m].addEventListener('click', function () {
        hamburgerBtn.classList.remove('active');
        navMenu.classList.remove('menu-open');
      });
    }

    // Đóng menu khi click ra ngoài
    document.addEventListener('click', function (e) {
      var isClickInsideMenu = navMenu.contains(e.target);
      var isClickOnHamburger = hamburgerBtn.contains(e.target);

      if (!isClickInsideMenu && !isClickOnHamburger) {
        hamburgerBtn.classList.remove('active');
        navMenu.classList.remove('menu-open');
      }
    });
  }
}

document.addEventListener('DOMContentLoaded', initNavigation);
document.addEventListener('layout:updated', initNavigation);
window.initNavigation = initNavigation;


// ==== Popup Giỏ hàng hover ====
document.addEventListener("DOMContentLoaded", function () {
  var cartToggle = document.getElementById("cart-toggle");
  var cartPopup = document.getElementById("cart-popup");
  var isCartPage = document.body.getAttribute("data-page") === "cart";

  if (cartToggle && cartPopup) {
    if (isCartPage) {
      cartPopup.style.display = "block";
      return;
    }

    var hideTimer;

    function showCart() {
      cartPopup.style.display = "block";
    }

    function hideCart() {
      cartPopup.style.display = "none";
    }

    function clearHideTimer() {
      clearTimeout(hideTimer);
    }

    function startHideTimer() {
      hideTimer = setTimeout(hideCart, 300);
    }

    cartToggle.addEventListener("mouseenter", function () {
      showCart();
      clearHideTimer();
    });

    cartToggle.addEventListener("mouseleave", startHideTimer);
    cartPopup.addEventListener("mouseenter", clearHideTimer);
    cartPopup.addEventListener("mouseleave", startHideTimer);

    // Nút đóng trong popup
    var closeBtn = cartPopup.querySelector(".close-cart");
    if (closeBtn) {
      closeBtn.addEventListener("click", hideCart);
    }
  }
});
