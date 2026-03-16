const cart = JSON.parse(localStorage.getItem("cart")) || [];
const displayTotal = document.getElementById("display-total");
const orderForm = document.getElementById("orderForm");
const checkoutSection = document.getElementById("checkout-section");
const successSection = document.getElementById("success-section");

let total = 0;
cart.forEach(item => {
  total += item.price * item.quantity;
});

if (displayTotal) {
    displayTotal.textContent = total.toLocaleString() + "đ";
}

// Nếu giỏ hàng rỗng, quay lại trang sản phẩm
if (cart.length === 0) {
    alert("Giỏ hàng của bạn đang trống!");
    window.location.href = "../sanpham/sanpham.html";
}

if (orderForm) {
    orderForm.addEventListener("submit", function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById("submitBtn");
        submitBtn.disabled = true;
        submitBtn.textContent = "Đang xử lý...";

        const formData = {
            ho_ten_kh: document.getElementById("ho_ten_kh").value,
            email_kh: document.getElementById("email_kh").value,
            sdt_kh: document.getElementById("sdt_kh").value,
            dia_chi_giao: document.getElementById("dia_chi_giao").value,
            ghi_chu: document.getElementById("ghi_chu").value,
            tong_tien: total,
            cart: cart
        };

        fetch("../api/place_order.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(formData)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Xóa giỏ hàng
                localStorage.removeItem("cart");
                
                // Hiển thị thông báo thành công
                checkoutSection.style.display = "none";
                successSection.style.display = "block";
                window.scrollTo(0, 0);
            } else {
                alert(data.message || "Có lỗi xảy ra, vui lòng thử lại.");
                submitBtn.disabled = false;
                submitBtn.textContent = "Xác nhận đặt hàng";
            }
        })
        .catch(() => {
            alert("Lỗi kết nối máy chủ!");
            submitBtn.disabled = false;
            submitBtn.textContent = "Xác nhận đặt hàng";
        });
    });
}
