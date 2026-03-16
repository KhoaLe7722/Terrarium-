window.onload = function () {
    const email = document.getElementById("email");
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirm_password");
    const name = document.getElementById("name");

    function checkEmail() {
        const elMsg = document.getElementById("feedback-email");
        const emailValue = email.value.trim();
        const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

        if (!emailValue) {
            elMsg.textContent = "⚠️ Vui lòng nhập email!";
            return false;
        } else if (!emailPattern.test(emailValue)) {
            elMsg.textContent = "⚠️ Email không hợp lệ!";
            return false;
        } else {
            elMsg.textContent = "";
            return true;
        }
    }

    function checkPassword() {
        const elMsg = document.getElementById("feedback-password");
        const pw = password.value.trim();

        if (!pw) {
            elMsg.textContent = "⚠️ Chưa nhập mật khẩu!";
            return false;
        } else if (pw.length < 8) {
            elMsg.textContent = "⚠️ Mật khẩu phải có ít nhất 8 ký tự!";
            return false;
        } else {
            elMsg.textContent = "";
            return true;
        }
    }

    function checkConfirmPassword() {
        const elMsg = document.getElementById("feedback-confirm_password");
        const pw = password.value.trim();
        const confirmPw = confirmPassword.value.trim();

        if (!confirmPw) {
            elMsg.textContent = "⚠️ Chưa nhập lại mật khẩu!";
            return false;
        } else if (pw !== confirmPw) {
            elMsg.textContent = "⚠️ Mật khẩu xác nhận không khớp!";
            return false;
        } else {
            elMsg.textContent = "";
            return true;
        }
    }

    function checkName() {
        const elMsg = document.getElementById("feedback-name");
        const nameValue = name.value.trim();
        const namePattern = /^[A-Za-zÀ-Ỹà-ỹ\s]+$/;

        if (!nameValue) {
            elMsg.textContent = "⚠️ Vui lòng nhập họ và tên!";
            return false;
        } else if (!namePattern.test(nameValue)) {
            elMsg.textContent = "⚠️ Họ và tên chỉ được chứa chữ cái và khoảng trắng!";
            return false;
        } else {
            elMsg.textContent = "";
            return true;
        }
    }

    // Validate trước khi submit - nếu hợp lệ thì để form POST lên PHP
    document.getElementById("registerForm").addEventListener("submit", function (event) {
        const isValid =
            checkName() &&
            checkEmail() &&
            checkPassword() &&
            checkConfirmPassword();

        if (!isValid) {
            event.preventDefault(); // Chỉ chặn khi KHÔNG hợp lệ
            return;
        }
        // Nếu hợp lệ → form sẽ tự POST lên dangky.php (không preventDefault)
    });

    // Cho phép HTML gọi các hàm validate onblur
    window.checkEmail = checkEmail;
    window.checkPassword = checkPassword;
    window.checkConfirmPassword = checkConfirmPassword;
    window.checkName = checkName;
};
