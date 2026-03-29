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
  var searchToggle = nav.querySelector('#search-toggle');
  var searchForm = nav.querySelector('#header-search-form');
  var searchInput = searchForm ? searchForm.querySelector('.header-search-input') : null;
  var searchLoader = nav.querySelector('#header-search-loader');
  var searchDropdown = nav.querySelector('#header-search-dropdown');
  var searchTimer = null;
  var searchRequestId = 0;
  var searchController = null;

  if (window.headerSearchCleanup) {
    window.headerSearchCleanup();
    window.headerSearchCleanup = null;
  }

  function setSearchLoading(isLoading) {
    if (searchLoader) {
      searchLoader.hidden = !isLoading;
    }

    if (searchForm) {
      searchForm.setAttribute('aria-busy', isLoading ? 'true' : 'false');
    }
  }

  function closeSearchDropdown() {
    if (!searchDropdown) return;

    searchDropdown.hidden = true;
    searchDropdown.innerHTML = '';
  }

  function buildSearchResultsUrl(query) {
    return STORE_PATHS.products + '?q=' + encodeURIComponent(query);
  }

  function renderSearchDropdown(html) {
    if (!searchDropdown) return;

    searchDropdown.innerHTML = html;
    searchDropdown.hidden = false;
  }

  function renderSearchState(message, query, actionLabel) {
    var safeMessage = typeof escapeHtml === 'function'
      ? escapeHtml(message)
      : String(message);
    var safeActionLabel = typeof escapeHtml === 'function'
      ? escapeHtml(actionLabel || 'Xem tất cả kết quả')
      : String(actionLabel || 'Xem tất cả kết quả');
    var html = '<p class="header-search-state">' + safeMessage + '</p>';

    if (query) {
      html += '<a class="header-search-view-all" href="' + buildSearchResultsUrl(query) + '">' + safeActionLabel + '</a>';
    }

    renderSearchDropdown(html);
  }

  function renderSearchResults(data, query) {
    var items = data && Array.isArray(data.items) ? data.items : [];

    if (!items.length) {
      renderSearchState('Không tìm thấy sản phẩm phù hợp với từ khóa này.', query, 'Xem trang sản phẩm');
      return;
    }

    var html = items.map(function (item) {
      var safeName = typeof escapeHtml === 'function' ? escapeHtml(item.name || '') : (item.name || '');
      var safeImage = typeof escapeHtml === 'function' ? escapeHtml(item.image || '../images/avatar.png') : (item.image || '../images/avatar.png');
      var safeLink = typeof escapeHtml === 'function' ? escapeHtml(item.link || buildSearchResultsUrl(query)) : (item.link || buildSearchResultsUrl(query));
      var safePrice = typeof escapeHtml === 'function' ? escapeHtml(item.priceText || '') : (item.priceText || '');
      var safeOldPrice = item.originalPriceText
        ? '<span class="header-search-price-old">' + (typeof escapeHtml === 'function' ? escapeHtml(item.originalPriceText) : item.originalPriceText) + '</span>'
        : '';
      var stockClass = item.inStock ? '' : ' is-out';
      var safeStock = typeof escapeHtml === 'function' ? escapeHtml(item.stockText || '') : (item.stockText || '');

      return '' +
        '<a class="header-search-result" href="' + safeLink + '">' +
        '<span class="header-search-thumb">' +
        '<img src="' + safeImage + '" alt="' + safeName + '" onerror="this.onerror=null;this.src=\'../images/avatar.png\';">' +
        '</span>' +
        '<span class="header-search-info">' +
        '<span class="header-search-name">' + safeName + '</span>' +
        '<span class="header-search-meta">' +
        '<span class="header-search-price">' + safePrice + '</span>' +
        safeOldPrice +
        '</span>' +
        '<span class="header-search-stock' + stockClass + '">' + safeStock + '</span>' +
        '</span>' +
        '</a>';
    }).join('');

    html += '<a class="header-search-view-all" href="' + buildSearchResultsUrl(query) + '">Xem tất cả kết quả</a>';
    renderSearchDropdown(html);
  }

  function performProductSearch(query) {
    if (!searchDropdown || !STORE_PATHS.searchApi) return;

    if (searchController && typeof searchController.abort === 'function') {
      searchController.abort();
    }

    searchController = typeof AbortController === 'function' ? new AbortController() : null;
    searchRequestId += 1;
    var requestId = searchRequestId;
    var requestBody = new URLSearchParams();
    requestBody.set('search', query);

    setSearchLoading(true);

    fetch(STORE_PATHS.searchApi, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
        'Accept': 'application/json'
      },
      body: requestBody.toString(),
      signal: searchController ? searchController.signal : undefined
    })
      .then(function (response) {
        if (!response.ok) {
          throw new Error('Search request failed');
        }

        return response.json();
      })
      .then(function (data) {
        if (requestId !== searchRequestId) {
          return;
        }

        renderSearchResults(data, query);
      })
      .catch(function (error) {
        if (error && error.name === 'AbortError') {
          return;
        }

        renderSearchState('Tạm thời chưa tìm được sản phẩm. Vui lòng thử lại.', query, 'Mở trang sản phẩm');
      })
      .finally(function () {
        if (requestId === searchRequestId) {
          setSearchLoading(false);
        }
      });
  }

  function scheduleProductSearch() {
    if (!searchInput) return;

    var query = searchInput.value.trim();
    clearTimeout(searchTimer);

    if (!query) {
      searchRequestId += 1;
      if (searchController && typeof searchController.abort === 'function') {
        searchController.abort();
      }
      setSearchLoading(false);
      closeSearchDropdown();
      return;
    }

    if (query.length < 2) {
      searchRequestId += 1;
      if (searchController && typeof searchController.abort === 'function') {
        searchController.abort();
      }
      setSearchLoading(false);
      renderSearchState('Nhập ít nhất 2 ký tự để tìm kiếm.', query, 'Xem trang sản phẩm');
      return;
    }

    searchTimer = setTimeout(function () {
      performProductSearch(query);
    }, 500);
  }

  function setSearchOpen(isOpen) {
    if (!searchToggle || !searchForm) return;

    var shouldOpen = !!isOpen && window.innerWidth <= 768;
    nav.classList.toggle('search-open', shouldOpen);
    searchToggle.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');

    if (!shouldOpen) {
      closeSearchDropdown();
    }

    if (shouldOpen) {
      if (hamburgerBtn && navMenu) {
        hamburgerBtn.classList.remove('active');
        navMenu.classList.remove('menu-open');
      }

      if (searchInput) {
        setTimeout(function () {
          searchInput.focus();
        }, 0);
      }
    }
  }

  if (searchToggle && searchForm) {
    var closeHeaderSearch = function (e) {
      if (window.innerWidth > 768) {
        setSearchOpen(false);
        return;
      }

      if (!searchForm.contains(e.target) && !searchToggle.contains(e.target)) {
        setSearchOpen(false);
      }
    };

    var closeHeaderSearchOnEscape = function (e) {
      if (e.key === 'Escape') {
        setSearchOpen(false);
      }
    };

    var handleSearchResize = function () {
      if (window.innerWidth > 768) {
        nav.classList.remove('search-open');
        searchToggle.setAttribute('aria-expanded', 'false');
      }
    };

    searchToggle.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      setSearchOpen(!nav.classList.contains('search-open'));
    });

    searchForm.addEventListener('click', function (e) {
      e.stopPropagation();
    });

    searchForm.addEventListener('input', function (e) {
      if (e.target === searchInput) {
        scheduleProductSearch();
      }
    });

    searchInput.addEventListener('focus', function () {
      if (searchInput.value.trim().length >= 2) {
        scheduleProductSearch();
      }
    });

    searchForm.addEventListener('submit', function () {
      clearTimeout(searchTimer);
      searchRequestId += 1;
      if (searchController && typeof searchController.abort === 'function') {
        searchController.abort();
      }
      setSearchLoading(false);
      closeSearchDropdown();
      setSearchOpen(false);
    });

    document.addEventListener('click', closeHeaderSearch);
    document.addEventListener('keydown', closeHeaderSearchOnEscape);
    window.addEventListener('resize', handleSearchResize);
    window.headerSearchCleanup = function () {
      clearTimeout(searchTimer);
      if (searchController && typeof searchController.abort === 'function') {
        searchController.abort();
      }
      document.removeEventListener('click', closeHeaderSearch);
      document.removeEventListener('keydown', closeHeaderSearchOnEscape);
      window.removeEventListener('resize', handleSearchResize);
    };
  }

  if (hamburgerBtn && navMenu) {
    hamburgerBtn.addEventListener('click', function () {
      if (!navMenu.classList.contains('menu-open')) {
        setSearchOpen(false);
      }

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
