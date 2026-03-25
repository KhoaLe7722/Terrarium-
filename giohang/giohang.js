document.addEventListener("DOMContentLoaded", () => {
  const cartSurface = document.getElementById("cart-popup")
    || document.querySelector(".cart-card")
    || document.querySelector(".cart-page-shell");
  const cartItemsEl = document.getElementById("cart-items");
  const cartTotalEl = document.getElementById("cart-total");
  const cartSummaryNoteEl = document.getElementById("cart-summary-note");
  const cartToggle = document.getElementById("cart-toggle");
  const isCartPage = document.body.getAttribute("data-page") === "cart";
  const checkoutButton = document.getElementById("checkout-selected-btn")
    || document.querySelector(".checkout-btn");
  const removeSelectedButton = document.getElementById("remove-selected-btn");
  const selectAllCheckbox = document.getElementById("cart-select-all");
  const fallbackImage = "../images/avatar.png";
  const checkoutSelectionKey = "checkout_selected_ids";
  const storeUrls = {
    cart: new URL("../giohang/giohang.php", window.location.href).href,
    checkout: new URL("../thanhtoan/thanhtoan.php", window.location.href).href,
    login: new URL("../dangky_dangnhap/dangnhap.php", window.location.href).href,
    register: new URL("../dangky_dangnhap/dangky.php", window.location.href).href,
    session: new URL("../dangky_dangnhap/check_session.php", window.location.href).href,
    inventory: new URL("../api/product_inventory.php", window.location.href).href,
  };

  let sessionCache = null;
  let dialogElements = null;
  let hideTimer = null;
  let stockCache = {};

  const productOverrides = {
    1: { name: "Bình Trứng Mini", image: "sanpham/image_sanpham/Bình trứng Mini/1.jpg" },
    2: { name: "Terrarium Bình Trụ 14x9", image: "sanpham/image_sanpham/Terrarium bình trụ 14x9 (2)/1.jpg" },
    3: { name: "Bình Mini Cube 12x12", image: "sanpham/image_sanpham/Bình Mini Cube 12x12/1.jpg" },
    4: { name: "Terrarium Đa Giác 16x16x32", image: "sanpham/image_sanpham/Terrarium Đa Giác 16x16x32/1.jpg" },
    5: { name: "Terrarium Đa Giác 20x20x32", image: "sanpham/image_sanpham/Terrarium Đa Giác 20x20x32/1.jpg" },
    6: { name: "Terrarium Đa Giác 16x16x34", image: "sanpham/image_sanpham/Terrarium Đa Giác 16x16x34/1.jpg" },
    7: { name: "Terrarium Đa Giác 23x23x40", image: "sanpham/image_sanpham/Terrarium Đa Giác 23x23x40/1.jpg" },
    8: { name: "Đèn LED Đế Gỗ Terrarium", image: "sanpham/image_sanpham/Đèn.jpg" }
  };

  function ensureStoreDialog() {
    if (dialogElements) {
      return dialogElements;
    }

    const styleId = "store-action-dialog-style";
    if (!document.getElementById(styleId)) {
      const style = document.createElement("style");
      style.id = styleId;
      style.textContent = `
        .store-dialog-overlay {
          position: fixed;
          inset: 0;
          display: none;
          align-items: center;
          justify-content: center;
          padding: 20px;
          background: rgba(26, 36, 21, 0.45);
          z-index: 3000;
        }
        .store-dialog-overlay.is-open {
          display: flex;
        }
        .store-dialog {
          width: min(100%, 420px);
          background: #ffffff;
          border-radius: 18px;
          border: 1px solid #dbe7d5;
          box-shadow: 0 20px 40px rgba(0, 0, 0, 0.18);
          padding: 24px 22px 20px;
          color: #23311f;
          position: relative;
        }
        .store-dialog__close {
          position: absolute;
          top: 10px;
          right: 10px;
          width: 34px;
          height: 34px;
          border: 0;
          background: transparent;
          color: #6b7280;
          font-size: 22px;
          cursor: pointer;
        }
        .store-dialog__title {
          margin: 0 34px 10px 0;
          font-size: 24px;
          line-height: 1.2;
          color: #2f4f29;
        }
        .store-dialog__message {
          margin: 0;
          font-size: 15px;
          line-height: 1.65;
          color: #4b5563;
          white-space: pre-line;
        }
        .store-dialog__actions {
          display: flex;
          flex-wrap: wrap;
          gap: 10px;
          margin-top: 20px;
        }
        .store-dialog__btn {
          display: inline-flex;
          align-items: center;
          justify-content: center;
          min-width: 130px;
          min-height: 42px;
          padding: 10px 16px;
          border-radius: 999px;
          border: 1px solid #54794a;
          background: #54794a;
          color: #fff;
          text-decoration: none;
          font-weight: 700;
          cursor: pointer;
          transition: transform 0.18s ease, opacity 0.18s ease, background 0.18s ease;
        }
        .store-dialog__btn:hover {
          transform: translateY(-1px);
          opacity: 0.95;
        }
        .store-dialog__btn.is-secondary {
          background: #fff;
          color: #54794a;
          border-color: #c7d7c1;
        }
        @media (max-width: 480px) {
          .store-dialog {
            padding: 22px 18px 18px;
            border-radius: 16px;
          }
          .store-dialog__actions {
            flex-direction: column;
          }
          .store-dialog__btn {
            width: 100%;
          }
        }
      `;
      document.head.appendChild(style);
    }

    const overlay = document.createElement("div");
    overlay.className = "store-dialog-overlay";
    overlay.innerHTML = `
      <div class="store-dialog" role="dialog" aria-modal="true" aria-labelledby="store-dialog-title">
        <button type="button" class="store-dialog__close" aria-label="Đóng">&times;</button>
        <h3 class="store-dialog__title" id="store-dialog-title"></h3>
        <p class="store-dialog__message"></p>
        <div class="store-dialog__actions"></div>
      </div>
    `;

    document.body.appendChild(overlay);

    const actionsEl = overlay.querySelector(".store-dialog__actions");

    function closeDialog() {
      overlay.classList.remove("is-open");
      actionsEl.innerHTML = "";
    }

    overlay.addEventListener("click", (event) => {
      if (event.target === overlay) {
        closeDialog();
      }
    });

    overlay.querySelector(".store-dialog__close").addEventListener("click", closeDialog);

    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape" && overlay.classList.contains("is-open")) {
        closeDialog();
      }
    });

    dialogElements = {
      overlay,
      titleEl: overlay.querySelector(".store-dialog__title"),
      messageEl: overlay.querySelector(".store-dialog__message"),
      actionsEl,
      closeDialog,
    };

    return dialogElements;
  }

  function showStoreDialog(options) {
    const dialog = ensureStoreDialog();
    dialog.titleEl.textContent = options.title;
    dialog.messageEl.textContent = options.message;
    dialog.actionsEl.innerHTML = "";

    (options.actions || []).forEach((action) => {
      const element = action.href ? document.createElement("a") : document.createElement("button");

      if (action.href) {
        element.href = action.href;
      } else {
        element.type = "button";
      }

      element.className = "store-dialog__btn" + (action.secondary ? " is-secondary" : "");
      element.textContent = action.label;
      element.addEventListener("click", () => {
        if (typeof action.onClick === "function") {
          action.onClick();
        }

        if (!action.keepOpen) {
          dialog.closeDialog();
        }
      });

      dialog.actionsEl.appendChild(element);
    });

    dialog.overlay.classList.add("is-open");
  }

  function showLoginRequiredDialog() {
    showStoreDialog({
      title: "Mời bạn đăng nhập",
      message: "Mời bạn đăng nhập hoặc tạo tài khoản để mua hàng.",
      actions: [
        { label: "Đăng nhập", href: storeUrls.login },
        { label: "Tạo tài khoản", href: storeUrls.register, secondary: true },
      ],
    });
  }

  function showAddedToCartDialog(message) {
    showStoreDialog({
      title: "Đã cập nhật giỏ hàng",
      message: message || "Sản phẩm đã được thêm vào giỏ hàng của bạn.",
      actions: [
        { label: "Xem giỏ hàng", href: storeUrls.cart },
        { label: "Tiếp tục mua", secondary: true },
      ],
    });
  }

  function showStockLimitDialog(message) {
    showStoreDialog({
      title: "Vượt quá tồn kho",
      message,
      actions: [
        { label: "Đã hiểu" },
      ],
    });
  }

  function normalizeCartItem(item) {
    const normalizedId = Number(item.id);
    const override = productOverrides[normalizedId] || {};
    const stock = Number(item.stock);

    return {
      id: normalizedId,
      name: item.name || override.name || "Sản phẩm",
      price: Number(item.price) || 0,
      quantity: Math.max(1, Number(item.quantity) || 1),
      image: item.image || override.image || "",
      stock: Number.isFinite(stock) && stock > 0 ? stock : 0,
    };
  }

  function getCart() {
    const rawCart = JSON.parse(localStorage.getItem("cart")) || [];
    const normalizedCart = rawCart
      .filter((item) => item && item.id !== undefined)
      .map(normalizeCartItem);

    if (JSON.stringify(rawCart) !== JSON.stringify(normalizedCart)) {
      localStorage.setItem("cart", JSON.stringify(normalizedCart));
    }

    return normalizedCart;
  }

  function readSelectedIds() {
    try {
      const rawValue = localStorage.getItem(checkoutSelectionKey);
      if (rawValue === null) {
        return null;
      }

      const parsed = JSON.parse(rawValue);
      if (!Array.isArray(parsed)) {
        return null;
      }

      return Array.from(new Set(
        parsed
          .map((id) => Number(id))
          .filter((id) => Number.isFinite(id) && id > 0)
      ));
    } catch (error) {
      return null;
    }
  }

  function saveSelectedIds(ids) {
    const normalizedIds = Array.from(new Set(
      (Array.isArray(ids) ? ids : [])
        .map((id) => Number(id))
        .filter((id) => Number.isFinite(id) && id > 0)
    ));

    localStorage.setItem(checkoutSelectionKey, JSON.stringify(normalizedIds));
    return normalizedIds;
  }

  function syncSelectedIdsWithCart(cart, options) {
    const config = Object.assign({ fallbackToAll: false }, options || {});
    const cartIds = cart
      .map((item) => Number(item.id))
      .filter((id) => Number.isFinite(id) && id > 0);
    const storedIds = readSelectedIds();
    let nextSelectedIds = [];

    if (!cartIds.length) {
      localStorage.removeItem(checkoutSelectionKey);
      return [];
    }

    if (storedIds === null) {
      nextSelectedIds = config.fallbackToAll ? cartIds.slice() : [];
    } else {
      nextSelectedIds = storedIds.filter((id) => cartIds.includes(id));

      if (config.fallbackToAll && storedIds.length > 0 && nextSelectedIds.length === 0) {
        nextSelectedIds = cartIds.slice();
      }
    }

    return saveSelectedIds(nextSelectedIds);
  }

  function getSelectedIdsForCart(cart) {
    return syncSelectedIdsWithCart(cart, { fallbackToAll: true });
  }

  function notifyCartChanged(cart) {
    const normalizedCart = Array.isArray(cart) ? cart.map(normalizeCartItem) : getCart();

    if (typeof window.refreshCartBadge === "function") {
      window.refreshCartBadge(normalizedCart);
    }

    window.dispatchEvent(new CustomEvent("store:cart-updated", {
      detail: { cart: normalizedCart }
    }));
  }

  function saveCart(cart) {
    const previousCart = getCart();
    const normalizedCart = cart.map(normalizeCartItem);
    localStorage.setItem("cart", JSON.stringify(normalizedCart));
    syncSelectedIdsWithCart(normalizedCart, {
      fallbackToAll: previousCart.length === 0 && normalizedCart.length > 0
    });
    notifyCartChanged(normalizedCart);
  }

  function updateCart(cart) {
    saveCart(cart);
    renderCart();
  }

  function formatPrice(value) {
    return Number(value).toLocaleString("vi-VN") + "đ";
  }

  function resolveImage(item) {
    return item && item.image ? `../${item.image}` : fallbackImage;
  }

  function fetchSessionState(forceRefresh) {
    if (!forceRefresh && sessionCache) {
      return Promise.resolve(sessionCache);
    }

    return fetch(storeUrls.session, { credentials: "same-origin" })
      .then((response) => response.json())
      .then((data) => {
        sessionCache = data;
        return data;
      })
      .catch(() => {
        sessionCache = { loggedIn: false };
        return sessionCache;
      });
  }

  function fetchInventory(ids) {
    const normalizedIds = Array.from(new Set(
      (ids || [])
        .map((id) => Number(id))
        .filter((id) => Number.isFinite(id) && id > 0)
    ));

    if (!normalizedIds.length) {
      return Promise.resolve({});
    }

    return fetch(storeUrls.inventory, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ ids: normalizedIds }),
    })
      .then((response) => response.json())
      .then((data) => (data && data.success && data.products) ? data.products : {})
      .catch(() => null);
  }

  function updateCartSummary(cart, selectedIds) {
    const selectedIdSet = new Set(selectedIds);
    const selectedItems = cart.filter((item) => selectedIdSet.has(Number(item.id)));
    const total = selectedItems.reduce((sum, item) => {
      return sum + Number(item.price) * Number(item.quantity);
    }, 0);

    if (cartTotalEl) {
      cartTotalEl.textContent = formatPrice(total);
    }

    if (cartSummaryNoteEl) {
      if (!cart.length) {
        cartSummaryNoteEl.textContent = "Thêm sản phẩm vào giỏ để bắt đầu đơn hàng của bạn.";
      } else if (!selectedItems.length) {
        cartSummaryNoteEl.textContent = "Chọn ít nhất 1 sản phẩm để thanh toán hoặc xóa.";
      } else {
        const totalQuantity = selectedItems.reduce((sum, item) => {
          return sum + (Number(item.quantity) || 0);
        }, 0);
        cartSummaryNoteEl.textContent = `Đã chọn ${selectedItems.length} sản phẩm, tổng ${totalQuantity} món trong đơn này.`;
      }
    }

    if (selectAllCheckbox) {
      selectAllCheckbox.disabled = cart.length === 0;
      selectAllCheckbox.checked = cart.length > 0 && selectedItems.length === cart.length;
      selectAllCheckbox.indeterminate = selectedItems.length > 0 && selectedItems.length < cart.length;
    }

    if (checkoutButton) {
      checkoutButton.disabled = selectedItems.length === 0;
    }

    if (removeSelectedButton) {
      removeSelectedButton.disabled = selectedItems.length === 0;
    }
  }

  function getPageSelectedIds() {
    if (!cartItemsEl) {
      return [];
    }

    return Array.from(cartItemsEl.querySelectorAll("[data-cart-select]:checked"))
      .map((input) => Number(input.value))
      .filter((id) => Number.isFinite(id) && id > 0);
  }

  function renderCart() {
    const cart = getCart();

    if (!cartItemsEl) {
      const total = cart.reduce((sum, item) => sum + Number(item.price) * Number(item.quantity), 0);
      if (cartTotalEl) {
        cartTotalEl.textContent = formatPrice(total);
      }
      notifyCartChanged(cart);
      return;
    }

    const selectedIds = getSelectedIdsForCart(cart);
    const selectedIdSet = new Set(selectedIds);

    if (!cart.length) {
      cartItemsEl.innerHTML = `
        <div class="empty-cart">
          Giỏ hàng của bạn đang trống. <a href="../sanpham/sanpham.php">Mua sắm ngay</a>
        </div>
      `;
      updateCartSummary(cart, []);
      notifyCartChanged(cart);
      return;
    }

    cartItemsEl.innerHTML = cart.map((item) => {
      const itemId = Number(item.id);
      const itemTotal = Number(item.price) * Number(item.quantity);
      const stock = Math.max(0, Number(item.stock) || 0);
      const stockNote = stock > 0
        ? `Còn ${stock} sản phẩm trong kho`
        : "Đang cập nhật tồn kho";
      const maxQuantity = stock > 0 ? stock : 99;

      return `
        <article class="cart-item-card" data-cart-item-id="${itemId}">
          <div class="item-main">
            <label class="row-checkbox-wrap" title="Chọn sản phẩm">
              <input
                class="row-checkbox"
                type="checkbox"
                data-cart-select
                value="${itemId}"
                ${selectedIdSet.has(itemId) ? "checked" : ""}>
            </label>
            <div class="product">
              <img
                src="${resolveImage(item)}"
                alt="${item.name}"
                onerror="this.onerror=null;this.src='${fallbackImage}';">
              <div class="product-copy">
                <div class="product-name">${item.name}</div>
                <div class="product-sub">Mã SP: #${itemId}</div>
                <div class="product-stock-note">${stockNote}</div>
              </div>
            </div>
          </div>

          <div class="col-price">
            <span class="price-text">${formatPrice(item.price)}</span>
          </div>

          <div class="col-qty">
            <div class="qty">
              <button type="button" class="qty-btn" data-id="${itemId}" data-delta="-1" aria-label="Giảm số lượng">-</button>
              <input
                class="qty-input-control"
                type="number"
                inputmode="numeric"
                min="1"
                max="${maxQuantity}"
                data-id="${itemId}"
                value="${Number(item.quantity)}">
              <button type="button" class="qty-btn" data-id="${itemId}" data-delta="1" aria-label="Tăng số lượng">+</button>
            </div>
          </div>

          <div class="col-total">
            <span class="line-total">${formatPrice(itemTotal)}</span>
          </div>

          <div class="col-action">
            <button class="remove-btn" type="button" data-id="${itemId}" title="Xóa">×</button>
          </div>
        </article>
      `;
    }).join("");

    const rowCheckboxes = Array.from(cartItemsEl.querySelectorAll("[data-cart-select]"));

    rowCheckboxes.forEach((checkbox) => {
      checkbox.onchange = () => {
        const nextSelectedIds = rowCheckboxes
          .filter((input) => input.checked)
          .map((input) => Number(input.value))
          .filter((id) => Number.isFinite(id) && id > 0);

        saveSelectedIds(nextSelectedIds);
        updateCartSummary(cart, nextSelectedIds);
      };
    });

    cartItemsEl.querySelectorAll(".qty-btn").forEach((button) => {
      button.onclick = () => {
        changeQuantity(button.dataset.id, Number(button.dataset.delta));
      };
    });

    cartItemsEl.querySelectorAll(".qty-input-control").forEach((input) => {
      input.onchange = () => {
        setQuantity(input.dataset.id, input.value);
      };
    });

    cartItemsEl.querySelectorAll(".remove-btn").forEach((button) => {
      button.onclick = () => {
        const itemId = Number(button.dataset.id);
        const nextCart = getCart().filter((item) => Number(item.id) !== itemId);
        const storedIds = readSelectedIds() || [];
        saveSelectedIds(storedIds.filter((id) => id !== itemId));
        updateCart(nextCart);
      };
    });

    if (selectAllCheckbox) {
      selectAllCheckbox.onchange = () => {
        const shouldSelectAll = !!selectAllCheckbox.checked;
        const nextSelectedIds = shouldSelectAll
          ? cart.map((item) => Number(item.id))
          : [];

        rowCheckboxes.forEach((input) => {
          input.checked = shouldSelectAll;
        });

        saveSelectedIds(nextSelectedIds);
        updateCartSummary(cart, nextSelectedIds);
      };
    }

    if (removeSelectedButton) {
      removeSelectedButton.onclick = () => {
        const nextSelectedIds = getPageSelectedIds();
        if (!nextSelectedIds.length) {
          showStoreDialog({
            title: "Chưa chọn sản phẩm",
            message: "Vui lòng chọn ít nhất 1 sản phẩm để xóa khỏi giỏ hàng.",
            actions: [{ label: "Đã hiểu" }],
          });
          return;
        }

        const selectedSet = new Set(nextSelectedIds);
        const nextCart = getCart().filter((item) => !selectedSet.has(Number(item.id)));
        saveSelectedIds([]);
        updateCart(nextCart);
      };
    }

    if (checkoutButton) {
      checkoutButton.onclick = (event) => {
        if (event) {
          event.preventDefault();
        }

        const nextSelectedIds = getPageSelectedIds();
        if (!nextSelectedIds.length) {
          showStoreDialog({
            title: "Chưa chọn sản phẩm",
            message: "Vui lòng chọn ít nhất 1 sản phẩm trước khi thanh toán.",
            actions: [{ label: "Đã hiểu" }],
          });
          return;
        }

        saveSelectedIds(nextSelectedIds);
        window.location.href = storeUrls.checkout;
      };
    }

    updateCartSummary(cart, selectedIds);
    notifyCartChanged(cart);
  }

  function syncCartWithServer(options) {
    const config = Object.assign({ showDialog: false }, options || {});
    const cart = getCart();

    if (!cart.length) {
      renderCart();
      return Promise.resolve({ cart: [], notices: [] });
    }

    return fetchInventory(cart.map((item) => item.id)).then((products) => {
      if (!products) {
        renderCart();
        return { cart: cart.slice(), notices: [] };
      }

      stockCache = products || {};

      const nextCart = [];
      const notices = [];

      cart.forEach((item) => {
        const serverItem = products[String(item.id)];

        if (!serverItem) {
          notices.push(`"${item.name}" không còn tồn tại trên hệ thống nên đã được gỡ khỏi giỏ hàng.`);
          return;
        }

        if (!serverItem.in_stock || Number(serverItem.stock) <= 0) {
          notices.push(`"${serverItem.name}" hiện đã hết hàng nên đã được gỡ khỏi giỏ hàng.`);
          return;
        }

        const nextQuantity = Math.min(Math.max(1, Number(item.quantity) || 1), Number(serverItem.stock));
        if (nextQuantity !== Number(item.quantity)) {
          notices.push(`"${serverItem.name}" chỉ còn ${serverItem.stock} sản phẩm, giỏ hàng đã được điều chỉnh.`);
        }

        nextCart.push(normalizeCartItem({
          id: serverItem.id,
          name: serverItem.name,
          price: serverItem.price,
          image: serverItem.image,
          quantity: nextQuantity,
          stock: serverItem.stock,
        }));
      });

      saveCart(nextCart);
      renderCart();

      if (config.showDialog && notices.length) {
        showStoreDialog({
          title: "Giỏ hàng đã cập nhật",
          message: notices.join("\n"),
          actions: [{ label: "Đã hiểu" }],
        });
      }

      return { cart: nextCart, notices };
    }).catch(() => {
      renderCart();
      return { cart: getCart(), notices: [] };
    });
  }

  function setQuantity(id, quantity) {
    const targetId = Number(id);
    const cart = getCart();
    const currentItem = cart.find((item) => Number(item.id) === targetId);

    if (!currentItem) {
      return;
    }

    const maxStock = Math.max(
      0,
      Number(currentItem.stock) || Number((stockCache[String(targetId)] || {}).stock) || 0
    );

    let nextQuantity = parseInt(quantity, 10);
    if (Number.isNaN(nextQuantity) || nextQuantity < 1) {
      nextQuantity = 1;
    }

    if (maxStock > 0 && nextQuantity > maxStock) {
      nextQuantity = maxStock;
      showStockLimitDialog(`"${currentItem.name}" chỉ còn ${maxStock} sản phẩm trong kho.`);
    }

    const nextCart = cart.map((item) => {
      if (Number(item.id) !== targetId) {
        return item;
      }

      return Object.assign({}, item, { quantity: nextQuantity });
    });

    updateCart(nextCart);
  }

  function changeQuantity(id, delta) {
    const targetId = Number(id);
    const currentItem = getCart().find((item) => Number(item.id) === targetId);

    if (!currentItem) {
      return;
    }

    setQuantity(targetId, Number(currentItem.quantity) + Number(delta || 0));
  }

  function showCart() {
    if (cartSurface) {
      cartSurface.style.display = "block";
      renderCart();
    }
  }

  function hideCart() {
    if (cartSurface && !isCartPage) {
      cartSurface.style.display = "none";
    }
  }

  window.syncStoreCart = function syncStoreCart(options) {
    return syncCartWithServer(options);
  };

  window.addToCart = function addToCart(product) {
    return fetchSessionState(true).then((sessionState) => {
      if (!sessionState.loggedIn) {
        showLoginRequiredDialog();
        return false;
      }

      return fetchInventory([product.id]).then((products) => {
        if (!products) {
          showStoreDialog({
            title: "Không thể kiểm tra tồn kho",
            message: "Hệ thống tạm thời không kết nối được đến kho hàng. Vui lòng thử lại sau.",
            actions: [{ label: "Đã hiểu" }],
          });
          return false;
        }

        const serverItem = products[String(product.id)];
        if (!serverItem || !serverItem.in_stock || Number(serverItem.stock) <= 0) {
          showStoreDialog({
            title: "Sản phẩm tạm hết hàng",
            message: "Sản phẩm này hiện không còn tồn kho để đặt mua.",
            actions: [{ label: "Đã hiểu" }],
          });
          return false;
        }

        stockCache[String(product.id)] = serverItem;

        const cart = getCart();
        const existingIndex = cart.findIndex((item) => Number(item.id) === Number(serverItem.id));
        const existingQuantity = existingIndex !== -1 ? Number(cart[existingIndex].quantity) || 0 : 0;
        const requestedQuantity = Math.max(1, Number(product.quantity) || 1);
        const finalQuantity = Math.min(existingQuantity + requestedQuantity, Number(serverItem.stock));

        if (finalQuantity <= existingQuantity) {
          showStockLimitDialog(`"${serverItem.name}" chỉ còn ${serverItem.stock} sản phẩm trong kho.`);
          return false;
        }

        const nextItem = normalizeCartItem({
          id: serverItem.id,
          name: serverItem.name,
          price: serverItem.price,
          image: serverItem.image,
          quantity: finalQuantity,
          stock: serverItem.stock,
        });

        if (existingIndex !== -1) {
          cart[existingIndex] = nextItem;
        } else {
          cart.push(nextItem);
        }

        saveCart(cart);
        renderCart();
        hideCart();

        const adjusted = finalQuantity !== existingQuantity + requestedQuantity;
        showAddedToCartDialog(
          adjusted
            ? `"${serverItem.name}" đã được thêm vào giỏ hàng và giới hạn ở mức ${serverItem.stock} sản phẩm theo tồn kho hiện tại.`
            : `"${serverItem.name}" đã được thêm vào giỏ hàng của bạn.`
        );

        return true;
      });
    });
  };

  if (cartToggle && cartSurface) {
    if (isCartPage) {
      cartSurface.style.display = "block";
    } else {
      cartToggle.addEventListener("mouseenter", () => {
        clearTimeout(hideTimer);
        showCart();
      });

      cartToggle.addEventListener("mouseleave", () => {
        hideTimer = setTimeout(hideCart, 300);
      });

      cartSurface.addEventListener("mouseenter", () => {
        clearTimeout(hideTimer);
      });

      cartSurface.addEventListener("mouseleave", () => {
        hideTimer = setTimeout(hideCart, 300);
      });
    }
  }

  renderCart();
  window.storeCartSyncPromise = syncCartWithServer({ showDialog: false });
});
