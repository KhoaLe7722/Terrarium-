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
      var isActive = (pageKey && itemKey === pageKey) || currentPath.indexOf(href) !== -1;

      if (isActive && !item.classList.contains("right-action")) {
        matched = true;
        item.classList.add("active");
        moveIndicatorTo(item, false);
        indicator.style.opacity = 1;
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
  var loginContainer = document.querySelector('.login-container');
  var dropdown = loginContainer ? loginContainer.querySelector('.login-dropdown') : null;
  var hideTimeout;

  if (loginContainer && dropdown) {
    loginContainer.addEventListener('mouseenter', function () {
      clearTimeout(hideTimeout);
      dropdown.style.display = 'flex';
      dropdown.style.opacity = '1';
      dropdown.style.pointerEvents = 'auto';
    });

    loginContainer.addEventListener('mouseleave', function () {
      hideTimeout = setTimeout(function () {
        dropdown.style.opacity = '0';
        dropdown.style.pointerEvents = 'none';
      }, 300);
    });
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


// ==== Popup Giỏ hàng hover ====
document.addEventListener("DOMContentLoaded", function () {
  var cartToggle = document.getElementById("cart-toggle");
  var cartPopup = document.getElementById("cart-popup");

  if (cartToggle && cartPopup) {
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
