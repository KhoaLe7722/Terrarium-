const cart = (JSON.parse(localStorage.getItem("cart")) || []).filter(
  (item) => Number(item.quantity) > 0
);
const displayTotal = document.getElementById("display-total");
const orderForm = document.getElementById("orderForm");
const checkoutSection = document.getElementById("checkout-section");
const successSection = document.getElementById("success-section");

const total = cart.reduce(
  (sum, item) => sum + Number(item.price) * Number(item.quantity),
  0
);

if (displayTotal) {
  displayTotal.textContent = total.toLocaleString("vi-VN") + "đ";
}

if (cart.length === 0) {
  alert("Giỏ hàng của bạn đang trống!");
  window.location.href = "../sanpham/sanpham.php";
}

if (orderForm) {
  orderForm.addEventListener("submit", function (event) {
    event.preventDefault();

    const submitBtn = document.getElementById("submitBtn");
    submitBtn.disabled = true;
    submitBtn.textContent = "Đang xử lý...";

    const formData = {
      ho_ten_kh: document.getElementById("ho_ten_kh").value.trim(),
      email_kh: document.getElementById("email_kh").value.trim(),
      sdt_kh: document.getElementById("sdt_kh").value.trim(),
      dia_chi_giao: document.getElementById("dia_chi_giao").value.trim(),
      ghi_chu: document.getElementById("ghi_chu").value.trim(),
      cart: cart.map((item) => ({
        id: item.id,
        quantity: item.quantity,
      })),
    };

    fetch("../api/place_order.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(formData),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          localStorage.removeItem("cart");
          checkoutSection.style.display = "none";
          successSection.style.display = "block";
          window.scrollTo(0, 0);
          return;
        }

        alert(data.message || "Có lỗi xảy ra, vui lòng thử lại.");
        submitBtn.disabled = false;
        submitBtn.textContent = "Xác nhận đặt hàng";
      })
      .catch(() => {
        alert("Không thể kết nối đến máy chủ.");
        submitBtn.disabled = false;
        submitBtn.textContent = "Xác nhận đặt hàng";
      });
  });
}
