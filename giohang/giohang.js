document.addEventListener("DOMContentLoaded", () => {
  const cartPopup = document.getElementById("cart-popup");
  const cartItemsEl = document.getElementById("cart-items");
  const cartTotalEl = document.getElementById("cart-total");
  const cartToggle = document.getElementById("cart-toggle");
  const checkoutButton = document.querySelector(".checkout-btn");
  const checkoutLink = checkoutButton ? checkoutButton.closest("a") : null;
  const fallbackImage = "../images/avatar.png";
  const storeUrls = {
    cart: new URL("../giohang/giohang.html", window.location.href).href,
    login: new URL("../dangky_dangnhap/dangnhap.php", window.location.href).href,
    register: new URL("../dangky_dangnhap/dangky.php", window.location.href).href,
    session: new URL("../dangky_dangnhap/check_session.php", window.location.href).href,
  };
  let sessionCache = null;
  let dialogElements = null;
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

  function showAddedToCartDialog() {
    showStoreDialog({
      title: "Đã thêm vào giỏ hàng",
      message: "Sản phẩm đã được thêm vào giỏ hàng của bạn.",
      actions: [
        { label: "Xem giỏ hàng", href: storeUrls.cart },
        { label: "Tiếp tục mua", secondary: true },
      ],
    });
  }

  function normalizeCartItem(item) {
    const normalizedId = Number(item.id);
    const override = productOverrides[normalizedId] || {};

    return {
      id: normalizedId,
      name: override.name || item.name,
      price: Number(item.price) || 0,
      quantity: Math.max(1, Number(item.quantity) || 1),
      image: override.image || item.image,
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

  function saveCart(cart) {
    localStorage.setItem("cart", JSON.stringify(cart.map(normalizeCartItem)));
  }

  function updateCart(cart) {
    saveCart(cart);
    renderCart();
  }

  function showCart() {
    if (cartPopup) {
      cartPopup.style.display = "block";
      renderCart();
    }
  }

  function hideCart() {
    if (cartPopup) {
      cartPopup.style.display = "none";
    }
  }

  function removeFromCart(id) {
    const nextCart = getCart().filter((item) => Number(item.id) !== Number(id));
    updateCart(nextCart);
  }

  function changeQuantity(id, delta) {
    const cart = getCart().map((item) => {
      if (Number(item.id) !== Number(id)) {
        return item;
      }

      const nextQuantity = Math.max(1, Number(item.quantity) + delta);
      return { ...item, quantity: nextQuantity };
    });

    updateCart(cart);
  }

  function resolveImage(item) {
    return item && item.image ? `../${item.image}` : fallbackImage;
  }

  function formatPrice(value) {
    return Number(value).toLocaleString("vi-VN") + "đ";
  }

  function renderCart() {
    const cart = getCart();

    if (cartItemsEl) {
      cartItemsEl.innerHTML = "";
    }

    let total = 0;

    if (cartItemsEl) {
      if (cart.length === 0) {
        cartItemsEl.innerHTML = `
          <tr>
            <td colspan="4" style="padding: 20px; text-align: center; color: #666;">
              Giỏ hàng của bạn đang trống.
            </td>
          </tr>
        `;
      } else {
        cart.forEach((item) => {
          const row = document.createElement("tr");
          const itemTotal = Number(item.price) * Number(item.quantity);
          total += itemTotal;

          row.innerHTML = `
            <td>
              <div style="display:flex;gap:10px;align-items:center;">
                <img src="${resolveImage(item)}" alt="${item.name}" style="width:90px;height:60px;object-fit:cover;border-radius:4px;" onerror="this.onerror=null;this.src='${fallbackImage}';">
                <div>
                  <div style="font-weight:600;">${item.name}</div>
                  <button class="remove-btn" data-id="${item.id}" style="margin-top:8px;background:none;border:none;color:#b42318;cursor:pointer;padding:0;">Xóa</button>
                </div>
              </div>
            </td>
            <td>${formatPrice(item.price)}</td>
            <td>
              <div style="display:inline-flex;align-items:center;gap:8px;">
                <button class="qty-btn" data-id="${item.id}" data-delta="-1" style="width:28px;height:28px;border:1px solid #d0d5dd;background:#fff;cursor:pointer;">-</button>
                <span>${Number(item.quantity)}</span>
                <button class="qty-btn" data-id="${item.id}" data-delta="1" style="width:28px;height:28px;border:1px solid #d0d5dd;background:#fff;cursor:pointer;">+</button>
              </div>
            </td>
            <td>${formatPrice(itemTotal)}</td>
          `;

          cartItemsEl.appendChild(row);
        });
      }

      cartItemsEl.querySelectorAll(".remove-btn").forEach((button) => {
        button.addEventListener("click", () => removeFromCart(button.dataset.id));
      });

      cartItemsEl.querySelectorAll(".qty-btn").forEach((button) => {
        button.addEventListener("click", () => {
          changeQuantity(button.dataset.id, Number(button.dataset.delta));
        });
      });
    } else {
      total = cart.reduce((sum, item) => sum + Number(item.price) * Number(item.quantity), 0);
    }

    if (cartTotalEl) {
      cartTotalEl.textContent = formatPrice(total);
    }

    if (checkoutButton) {
      checkoutButton.disabled = cart.length === 0;
      checkoutButton.style.opacity = cart.length === 0 ? "0.6" : "1";
      checkoutButton.style.cursor = cart.length === 0 ? "not-allowed" : "pointer";
    }

    if (checkoutLink) {
      checkoutLink.style.pointerEvents = cart.length === 0 ? "none" : "auto";
    }
  }

  window.addToCart = function (product) {
    return fetchSessionState(true).then((sessionState) => {
      if (!sessionState.loggedIn) {
        showLoginRequiredDialog();
        return false;
      }

      const nextProduct = normalizeCartItem(product);
      const cart = getCart();
      const existingIndex = cart.findIndex((item) => Number(item.id) === Number(nextProduct.id));

      if (existingIndex !== -1) {
        cart[existingIndex].quantity += Number(nextProduct.quantity) || 1;
      } else {
        cart.push(nextProduct);
      }

      saveCart(cart);
      renderCart();
      hideCart();
      showAddedToCartDialog();

      return true;
    });
  };

  let hideTimer;

  if (cartToggle && cartPopup) {
    cartToggle.addEventListener("mouseenter", () => {
      clearTimeout(hideTimer);
      showCart();
    });

    cartToggle.addEventListener("mouseleave", () => {
      hideTimer = setTimeout(hideCart, 300);
    });

    cartPopup.addEventListener("mouseenter", () => {
      clearTimeout(hideTimer);
    });

    cartPopup.addEventListener("mouseleave", () => {
      hideTimer = setTimeout(hideCart, 300);
    });
  }

  renderCart();
});
