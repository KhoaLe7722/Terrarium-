window.onload = function () {
    const email = document.getElementById("email");
    const password = document.getElementById("password");
    const form = document.getElementById("loginForm");

    // Validate trước khi submit - nếu hợp lệ thì để form POST lên PHP
    form.addEventListener("submit", function (event) {
        const emailValue = email.value.trim();
        const passwordValue = password.value.trim();

        if (!emailValue || !passwordValue) {
            event.preventDefault(); // Chỉ chặn khi trống
            if (!emailValue) {
                document.getElementById("feedback-email").textContent = "⚠️ Vui lòng nhập email!";
            }
            if (!passwordValue) {
                document.getElementById("feedback-password").textContent = "⚠️ Vui lòng nhập mật khẩu!";
            }
            return;
        }
        // Nếu hợp lệ → form sẽ tự POST lên dangnhap.php (không preventDefault)
    });
};
