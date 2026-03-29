document.addEventListener("DOMContentLoaded", async () => {
    const inventoryUrl = "../api/product_inventory.php";
    const placeOrderUrl = "../api/place_order.php";
    const checkoutSelectionKey = "checkout_selected_ids";
    const SHIPPING_FEE = 50000;
    const FREE_SHIPPING_THRESHOLD = 500000;
    const checkoutApp = document.getElementById("checkout-app");
    const bankId = checkoutApp?.dataset.bankId || "VCB";
    const bankName = checkoutApp?.dataset.bankName || "Vietcombank";
    const bankAccount = checkoutApp?.dataset.bankAccount || "";
    const bankOwner = checkoutApp?.dataset.bankOwner || "";
    const displaySubtotal = document.getElementById("display-subtotal");
    const displayShipping = document.getElementById("display-shipping");
    const displayShippingNote = document.getElementById("display-shipping-note");
    const displayTotal = document.getElementById("display-total");
    const orderItemsContainer = document.getElementById("order-items-container");
    const orderForm = document.getElementById("orderForm");
    const successOverlay = document.getElementById("success-overlay");
    const bankTransferDetails = document.getElementById("bank-transfer-details");
    const dynamicQrCode = document.getElementById("dynamic-qr-code");
    const downloadQrBtn = document.getElementById("downloadQrBtn");
    const submitBtn = document.getElementById("submitBtn");
    const closeModalBtn = document.getElementById("closeModalBtn");
    const paymentMethodInputs = Array.from(document.querySelectorAll('input[name="phuong_thuc_tt"]'));

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

    function formatCurrency(value) {
        return Number(value || 0).toLocaleString("vi-VN") + "đ";
    }

    function calculateShipping(subtotal) {
        const normalizedSubtotal = Math.max(0, Number(subtotal) || 0);

        if (normalizedSubtotal <= 0) {
            return 0;
        }

        return normalizedSubtotal >= FREE_SHIPPING_THRESHOLD ? 0 : SHIPPING_FEE;
    }

    function buildPricingSummary(subtotal) {
        const normalizedSubtotal = Math.max(0, Number(subtotal) || 0);
        const shipping = calculateShipping(normalizedSubtotal);

        return {
            subtotal: normalizedSubtotal,
            shipping,
            total: normalizedSubtotal + shipping,
            isFreeShipping: normalizedSubtotal > 0 && shipping === 0,
        };
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
        submitBtn.textContent = "Đặt hàng";
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
        let subtotal = 0;
        const itemsHtml = cart
            .map((item) => {
                const quantity = Number(item.quantity) || 0;
                const price = Number(item.price) || 0;
                const itemTotal = price * quantity;
                subtotal += itemTotal;

                return `
                    <div class="cart-item-preview">
                        <span>${item.name || "Sản phẩm"} <strong>x${quantity}</strong></span>
                        <span>${itemTotal.toLocaleString("vi-VN")}đ</span>
                    </div>
                `;
            })
            .join("");

        const pricing = buildPricingSummary(subtotal);
        const total = pricing.total;

        if (orderItemsContainer) {
            orderItemsContainer.innerHTML = itemsHtml;
        }

        if (displaySubtotal) {
            displaySubtotal.textContent = formatCurrency(pricing.subtotal);
        }

        if (displayShipping) {
            displayShipping.textContent = pricing.isFreeShipping
                ? "Miễn phí"
                : formatCurrency(pricing.shipping);
        }

        if (displayShippingNote) {
            if (pricing.subtotal <= 0) {
                displayShippingNote.textContent = "Phí vận chuyển tiêu chuẩn là 50.000đ cho đơn dưới 500.000đ.";
            } else if (pricing.isFreeShipping) {
                displayShippingNote.textContent = "Đơn hàng này đã được miễn phí vận chuyển.";
            } else {
                const remaining = Math.max(0, FREE_SHIPPING_THRESHOLD - pricing.subtotal);
                displayShippingNote.textContent = `Mua thêm ${formatCurrency(remaining)} để được miễn phí vận chuyển.`;
            }
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
            return { notices: [], failed: false };
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

    function getSelectedPaymentMethod() {
        const selected = document.querySelector('input[name="phuong_thuc_tt"]:checked');
        return selected ? selected.value : "cod";
    }

    function syncPaymentCardState() {
        const selectedValue = getSelectedPaymentMethod();
        document.querySelectorAll(".payment-card").forEach((card) => {
            const input = card.querySelector('input[name="phuong_thuc_tt"]');
            card.classList.toggle("is-selected", Boolean(input && input.value === selectedValue));
        });
    }

    function formatOrderCode(orderId) {
        const numericOrderId = Number(orderId);
        if (!Number.isFinite(numericOrderId) || numericOrderId <= 0) {
            return "DH" + Math.floor(Math.random() * 1000000).toString().padStart(6, "0");
        }

        return "DH" + numericOrderId.toString().padStart(8, "0");
    }

    function openSuccessOverlay() {
        if (!successOverlay) {
            return;
        }

        successOverlay.hidden = false;
        document.body.style.overflow = "hidden";
    }

    function closeSuccessOverlay() {
        document.body.style.overflow = "";
        window.location.href = "../sanpham/sanpham.php";
    }

    async function downloadQrImage() {
        if (!dynamicQrCode || !dynamicQrCode.src) {
            return;
        }

        const fileName = `qr-thanh-toan-${Date.now()}.png`;

        try {
            const response = await fetch(dynamicQrCode.src);
            const blob = await response.blob();
            const url = URL.createObjectURL(blob);
            const anchor = document.createElement("a");
            anchor.href = url;
            anchor.download = fileName;
            document.body.appendChild(anchor);
            anchor.click();
            anchor.remove();
            URL.revokeObjectURL(url);
        } catch (error) {
            window.open(dynamicQrCode.src, "_blank", "noopener");
        }
    }

    if (redirectIfEmpty()) {
        return;
    }

    const initialSync = await syncCartWithServer(true);
    if (initialSync.failed) {
        alert("Không thể đồng bộ tồn kho lúc này. Vui lòng thử lại sau ít phút.");
    }

    if (redirectIfEmpty()) {
        return;
    }

    syncPaymentCardState();
    renderSummary();

    paymentMethodInputs.forEach((input) => {
        input.addEventListener("change", syncPaymentCardState);
    });

    if (downloadQrBtn) {
        downloadQrBtn.addEventListener("click", downloadQrImage);
    }

    if (closeModalBtn) {
        closeModalBtn.addEventListener("click", closeSuccessOverlay);
    }

    if (!orderForm || !submitBtn) {
        return;
    }

    orderForm.addEventListener("submit", async (event) => {
        event.preventDefault();

        submitBtn.disabled = true;
        submitBtn.textContent = "Đang xử lý...";

        const preSubmitSync = await syncCartWithServer(false);
        renderSummary();

        if (preSubmitSync.failed) {
            alert("Không thể kiểm tra tồn kho trước khi đặt hàng. Vui lòng thử lại.");
            resetSubmitButton();
            return;
        }

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

        const paymentMethod = getSelectedPaymentMethod();
        const formData = {
            ho_ten_kh: document.getElementById("ho_ten_kh")?.value.trim() || "",
            email_kh: document.getElementById("email_kh")?.value.trim() || "",
            sdt_kh: document.getElementById("sdt_kh")?.value.trim() || "",
            dia_chi_giao: document.getElementById("dia_chi_giao")?.value.trim() || "",
            ghi_chu: document.getElementById("ghi_chu")?.value.trim() || "",
            phuong_thuc_tt: paymentMethod,
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

            const orderCode = data.order_code || formatOrderCode(data.order_id);
            const subtotalAmount = cart.reduce(
                (sum, item) => sum + (Number(item.price) * Number(item.quantity)),
                0
            );
            const pricing = buildPricingSummary(subtotalAmount);
            const totalAmount = Math.max(0, Number(data.tong_tien) || pricing.total);

            document.getElementById("success-order-id").textContent = orderCode;

            if (paymentMethod === "bank") {
                const transferContent = `Thanh toan don hang ${orderCode}`;
                const qrUrl = `https://img.vietqr.io/image/${bankId}-${bankAccount}-compact2.png?amount=${Math.round(totalAmount)}&addInfo=${encodeURIComponent(transferContent)}&accountName=${encodeURIComponent(bankOwner)}`;

                document.getElementById("success-subtitle").textContent = `Vui lòng chuyển khoản theo thông tin dưới đây để hoàn tất đơn hàng tại ${bankName}.`;
                document.getElementById("success-amount").textContent = totalAmount.toLocaleString("vi-VN") + "đ";
                document.getElementById("success-order-code").textContent = orderCode;

                if (bankTransferDetails) {
                    bankTransferDetails.hidden = false;
                }

                if (dynamicQrCode) {
                    dynamicQrCode.src = qrUrl;
                }
            } else {
                document.getElementById("success-subtitle").textContent = "Chúng tôi sẽ liên hệ sớm để xác nhận đơn hàng của bạn.";
                if (bankTransferDetails) {
                    bankTransferDetails.hidden = true;
                }
                if (dynamicQrCode) {
                    dynamicQrCode.removeAttribute("src");
                }
            }

            const orderedIds = new Set(
                cart.map((item) => Number(item.id)).filter((id) => Number.isFinite(id) && id > 0)
            );
            const remainingCart = fullCart.filter((item) => !orderedIds.has(Number(item.id)));

            saveFullCart(remainingCart);
            clearCheckoutSelection();
            cart = [];
            openSuccessOverlay();
            window.scrollTo(0, 0);
        } catch (error) {
            console.error("Checkout error:", error);
            alert("Không thể kết nối đến máy chủ.");
            resetSubmitButton();
        }
    });
});
