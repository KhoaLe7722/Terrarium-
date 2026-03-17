document.addEventListener("DOMContentLoaded", () => {
  const cartPopup = document.getElementById("cart-popup");
  const cartItemsEl = document.getElementById("cart-items");
  const cartTotalEl = document.getElementById("cart-total");
  const cartToggle = document.getElementById("cart-toggle");
  const checkoutButton = document.querySelector(".checkout-btn");
  const checkoutLink = checkoutButton ? checkoutButton.closest("a") : null;
  const fallbackImage = "../images/avatar.png";
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

    if (cartPopup) {
      showCart();
    }
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
