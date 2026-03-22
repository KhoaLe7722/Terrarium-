document.addEventListener("DOMContentLoaded", () => {
    // 1. Lấy giỏ hàng từ LocalStorage
    const cart = (JSON.parse(localStorage.getItem("cart")) || []).filter(
        (item) => Number(item.quantity) > 0
    );

    // Xử lý giỏ hàng trống ngay từ đầu
    if (cart.length === 0) {
        alert("Giỏ hàng của bạn đang trống!");
        window.location.href = "../sanpham/sanpham.php";
        return; 
    }

    // Lấy các element cần thiết
    const displayTotal = document.getElementById("display-total");
    const orderItemsContainer = document.getElementById("order-items-container");
    const orderForm = document.getElementById("orderForm");
    const checkoutSection = document.getElementById("checkout-section");
    const successSection = document.getElementById("success-section");

    // 2. Tính tổng tiền và hiển thị chi tiết sản phẩm
    let total = 0;
    let itemsHTML = '';

    cart.forEach(item => {
        const itemTotal = Number(item.price) * Number(item.quantity);
        total += itemTotal;
        
        // Tạo HTML cho từng dòng sản phẩm (Tên sp x Số lượng)
        itemsHTML += `
            <div class="cart-item-preview">
                <span>${item.name || 'Sản phẩm'} <strong>x${item.quantity}</strong></span>
                <span>${itemTotal.toLocaleString("vi-VN")}đ</span>
            </div>
        `;
    });

    // In danh sách sản phẩm và tổng tiền ra màn hình
    if (orderItemsContainer) {
        orderItemsContainer.innerHTML = itemsHTML;
    }
    if (displayTotal) {
        displayTotal.textContent = total.toLocaleString("vi-VN") + "đ";
    }

  
    
   

    // Cập nhật QR mỗi khi khách gõ thêm/sửa số điện thoại
    if (phoneInput) {
        phoneInput.addEventListener("input", updateBankQR);
    }
    // ==========================================


    // 3. Xử lý khi bấm nút "Xác nhận đặt hàng"
    if (orderForm) {
        orderForm.addEventListener("submit", function (event) {
            event.preventDefault();

            const submitBtn = document.getElementById("submitBtn");
            submitBtn.disabled = true;
            submitBtn.textContent = "Đang xử lý...";

            // Lấy phương thức thanh toán được chọn
            const paymentMethod = document.querySelector('input[name="phuong_thuc_tt"]:checked').value;

            // Đóng gói dữ liệu gửi đi
            const formData = {
                ho_ten_kh: document.getElementById("ho_ten_kh").value.trim(),
                email_kh: document.getElementById("email_kh").value.trim(),
                sdt_kh: document.getElementById("sdt_kh").value.trim(),
                dia_chi_giao: document.getElementById("dia_chi_giao").value.trim(),
                ghi_chu: document.getElementById("ghi_chu").value.trim(),
                phuong_thuc_tt: paymentMethod, // Gửi phương thức thanh toán
                cart: cart.map((item) => ({
                    id: item.id,
                    quantity: item.quantity,
                    price: item.price
                })),
            };

            // Gửi request API
            fetch("../api/place_order.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(formData),
            })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    // Xóa giỏ hàng và hiển thị popup thành công
                    localStorage.removeItem("cart");
                    checkoutSection.style.display = "none";
                    successSection.style.display = "block";
                    window.scrollTo(0, 0); 
                } else {
                    alert(data.message || "Có lỗi xảy ra, vui lòng thử lại.");
                    submitBtn.disabled = false;
                    submitBtn.textContent = "Xác nhận đặt hàng";
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                alert("Không thể kết nối đến máy chủ.");
                submitBtn.disabled = false;
                submitBtn.textContent = "Xác nhận đặt hàng";
            });
        });
    }
}); 