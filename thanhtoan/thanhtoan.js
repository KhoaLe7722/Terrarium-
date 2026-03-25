document.addEventListener("DOMContentLoaded", async () => {
    const inventoryUrl = "../api/product_inventory.php";
    const placeOrderUrl = "../api/place_order.php";
    const checkoutSelectionKey = "checkout_selected_ids";
    const displayTotal = document.getElementById("display-total");
    const orderItemsContainer = document.getElementById("order-items-container");
    const orderForm = document.getElementById("orderForm");
    const checkoutSection = document.getElementById("checkout-section");
    const successSection = document.getElementById("success-section");
    const submitBtn = document.getElementById("submitBtn");

    let fullCart = loadStoredCart();
    let selectedIds = loadSelectedIds();
    let cart = buildCheckoutCart();

    function loadStoredCart() {
        return (JSON.parse(localStorage.getItem("cart")) || []).filter(
            (item) => Number(item.quantity) > 0
        );
    }

    function loadSelectedIds() {
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

    function persistSelectedIds(ids) {
        if (ids === null) {
            localStorage.removeItem(checkoutSelectionKey);
            return null;
        }

        const normalizedIds = Array.from(new Set(
            (Array.isArray(ids) ? ids : [])
                .map((id) => Number(id))
                .filter((id) => Number.isFinite(id) && id > 0)
        ));

        localStorage.setItem(checkoutSelectionKey, JSON.stringify(normalizedIds));
        return normalizedIds;
    }

    function syncSelectedIdsWithFullCart() {
        const availableIds = fullCart
            .map((item) => Number(item.id))
            .filter((id) => Number.isFinite(id) && id > 0);

        if (!availableIds.length) {
            selectedIds = null;
            localStorage.removeItem(checkoutSelectionKey);
            return;
        }

        if (selectedIds === null) {
            return;
        }

        selectedIds = selectedIds.filter((id) => availableIds.includes(id));
        persistSelectedIds(selectedIds);
    }

    function buildCheckoutCart() {
        syncSelectedIdsWithFullCart();

        if (selectedIds === null) {
            return fullCart.slice();
        }

        const selectedSet = new Set(selectedIds);
        return fullCart.filter((item) => selectedSet.has(Number(item.id)));
    }

    function refreshCheckoutCart() {
        cart = buildCheckoutCart();
    }

    function notifyCartChanged(nextCart) {
        const cartItems = Array.isArray(nextCart) ? nextCart : [];

        if (typeof window.refreshCartBadge === "function") {
            window.refreshCartBadge(cartItems);
        }

        window.dispatchEvent(new CustomEvent("store:cart-updated", {
            detail: { cart: cartItems }
        }));
    }

    function saveFullCart(nextCart) {
        fullCart = (Array.isArray(nextCart) ? nextCart : []).filter(
            (item) => Number(item.quantity) > 0
        );
        localStorage.setItem("cart", JSON.stringify(fullCart));
        syncSelectedIdsWithFullCart();
        refreshCheckoutCart();
        notifyCartChanged(fullCart);
    }

    function clearCheckoutSelection() {
        selectedIds = null;
        localStorage.removeItem(checkoutSelectionKey);
    }

    function replaceCheckedOutItems(nextCheckedOutCart) {
        const currentCheckoutIds = new Set(
            cart.map((item) => Number(item.id)).filter((id) => Number.isFinite(id) && id > 0)
        );
        const nextItemMap = {};

        (nextCheckedOutCart || []).forEach((item) => {
            nextItemMap[Number(item.id)] = item;
        });

        return fullCart.reduce((items, item) => {
            const itemId = Number(item.id);

            if (!currentCheckoutIds.has(itemId)) {
                items.push(item);
                return items;
            }

            if (nextItemMap[itemId]) {
                items.push(nextItemMap[itemId]);
            }

            return items;
        }, []);
    }

    function resetSubmitButton() {
        if (!submitBtn) {
            return;
        }

        submitBtn.disabled = false;
        submitBtn.textContent = "Xác nhận đặt hàng";
    }

    function redirectIfEmpty() {
        if (cart.length === 0) {
            if (fullCart.length === 0) {
                alert("Giỏ hàng của bạn đang trống!");
                window.location.href = "../sanpham/sanpham.php";
            } else {
                alert("Bạn chưa chọn sản phẩm để thanh toán. Vui lòng quay lại giỏ hàng.");
                window.location.href = "../giohang/giohang.php";
            }

            return true;
        }

        return false;
    }

    function renderSummary() {
        let total = 0;
        const itemsHtml = cart
            .map((item) => {
                const quantity = Number(item.quantity) || 0;
                const price = Number(item.price) || 0;
                const itemTotal = price * quantity;
                total += itemTotal;
                const stock = Number(item.stock) || 0;

                return `
                    <div class="cart-item-preview">
                        <span>${item.name || "Sản phẩm"} <strong>x${quantity}</strong></span>
                        <span>${itemTotal.toLocaleString("vi-VN")}đ</span>
                        <div style="width:100%;font-size:12px;color:#667085;">Tồn kho hiện tại: ${stock}</div>
                    </div>
                `;
            })
            .join("");

        if (orderItemsContainer) {
            orderItemsContainer.innerHTML = itemsHtml;
        }

        if (displayTotal) {
            displayTotal.textContent = total.toLocaleString("vi-VN") + "đ";
        }
    }

    async function fetchInventory(ids) {
        const normalizedIds = Array.from(new Set(
            (ids || [])
                .map((id) => Number(id))
                .filter((id) => Number.isFinite(id) && id > 0)
        ));

        if (!normalizedIds.length) {
            return {};
        }

        try {
            const response = await fetch(inventoryUrl, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ ids: normalizedIds }),
            });
            const data = await response.json();
            return data && data.success && data.products ? data.products : {};
        } catch (error) {
            return null;
        }
    }

    async function syncCartWithServer(showAlert) {
        if (!cart.length) {
            return { notices: [] };
        }

        const products = await fetchInventory(cart.map((item) => item.id));
        if (!products) {
            return { notices: [], failed: true };
        }

        const nextCheckedOutCart = [];
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

            nextCheckedOutCart.push({
                id: serverItem.id,
                name: serverItem.name,
                price: serverItem.price,
                quantity: nextQuantity,
                image: serverItem.image,
                stock: serverItem.stock,
            });
        });

        saveFullCart(replaceCheckedOutItems(nextCheckedOutCart));

        if (showAlert && notices.length) {
            alert(notices.join("\n"));
        }

        return { notices, failed: false };
    }

    function applyStockIssues(stockIssues) {
        const issueMap = {};
        (stockIssues || []).forEach((issue) => {
            issueMap[Number(issue.product_id)] = issue;
        });

        const checkedOutIds = new Set(
            cart.map((item) => Number(item.id)).filter((id) => Number.isFinite(id) && id > 0)
        );

        const nextFullCart = [];
        fullCart.forEach((item) => {
            const itemId = Number(item.id);
            const issue = issueMap[itemId];

            if (!checkedOutIds.has(itemId) || !issue) {
                nextFullCart.push(item);
                return;
            }

            const available = Math.max(0, Number(issue.available) || 0);
            if (available > 0) {
                nextFullCart.push(Object.assign({}, item, {
                    quantity: available,
                    stock: available,
                }));
            }
        });

        saveFullCart(nextFullCart);
    }

    function buildStockIssueMessage(stockIssues) {
        return (stockIssues || []).map((issue) => {
            const available = Math.max(0, Number(issue.available) || 0);
            if (available <= 0) {
                return `"${issue.name}" đã hết hàng.`;
            }

            return `"${issue.name}" chỉ còn ${available} sản phẩm. Giỏ hàng đã được giảm về mức này.`;
        }).join("\n");
    }

    if (redirectIfEmpty()) {
        return;
    }

    await syncCartWithServer(true);
    if (redirectIfEmpty()) {
        return;
    }

    renderSummary();

    if (!orderForm || !submitBtn) {
        return;
    }

    const getSelectedPaymentMethod = () => {
        const selected = document.querySelector('input[name="phuong_thuc_tt"]:checked');
        return selected ? selected.value : "cod";
    };

    orderForm.addEventListener("submit", async (event) => {
        event.preventDefault();

        submitBtn.disabled = true;
        submitBtn.textContent = "Đang xử lý...";

        const preSubmitSync = await syncCartWithServer(false);
        renderSummary();

        if (cart.length === 0) {
            alert("Các sản phẩm bạn chọn đã không còn hợp lệ. Vui lòng kiểm tra lại giỏ hàng.");
            window.location.href = "../giohang/giohang.php";
            return;
        }

        if (preSubmitSync.notices.length) {
            alert("Giỏ hàng vừa được cập nhật do tồn kho thay đổi. Vui lòng kiểm tra lại trước khi đặt hàng.");
            resetSubmitButton();
            return;
        }

        const formData = {
            ho_ten_kh: document.getElementById("ho_ten_kh")?.value.trim() || "",
            email_kh: document.getElementById("email_kh")?.value.trim() || "",
            sdt_kh: document.getElementById("sdt_kh")?.value.trim() || "",
            dia_chi_giao: document.getElementById("dia_chi_giao")?.value.trim() || "",
            ghi_chu: document.getElementById("ghi_chu")?.value.trim() || "",
            phuong_thuc_tt: getSelectedPaymentMethod(),
            cart: cart.map((item) => ({
                id: item.id,
                quantity: item.quantity,
            })),
        };

        try {
            const response = await fetch(placeOrderUrl, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(formData),
            });

            const data = await response.json();

            if (!data.success) {
                if (data.code === "INSUFFICIENT_STOCK" && Array.isArray(data.stock_issues)) {
                    applyStockIssues(data.stock_issues);
                    renderSummary();
                    alert(buildStockIssueMessage(data.stock_issues));

                    if (cart.length === 0) {
                        window.location.href = "../giohang/giohang.php";
                        return;
                    }
                } else {
                    alert(data.message || "Có lỗi xảy ra, vui lòng thử lại.");
                }

                resetSubmitButton();
                return;
            }

            const orderedIds = new Set(
                cart.map((item) => Number(item.id)).filter((id) => Number.isFinite(id) && id > 0)
            );
            const remainingCart = fullCart.filter((item) => !orderedIds.has(Number(item.id)));

            saveFullCart(remainingCart);
            clearCheckoutSelection();
            cart = [];
            checkoutSection.style.display = "none";
            successSection.style.display = "block";
            window.scrollTo(0, 0);
        } catch (error) {
            console.error("Checkout error:", error);
            alert("Không thể kết nối đến máy chủ.");
            resetSubmitButton();
        }
    });
});
