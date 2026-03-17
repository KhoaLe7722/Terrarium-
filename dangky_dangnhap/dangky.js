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
            elMsg.textContent = "Vui lòng nhập email.";
            return false;
        }

        if (!emailPattern.test(emailValue)) {
            elMsg.textContent = "Email không hợp lệ.";
            return false;
        }

        elMsg.textContent = "";
        return true;
    }

    function checkPassword() {
        const elMsg = document.getElementById("feedback-password");
        const pw = password.value.trim();

        if (!pw) {
            elMsg.textContent = "Vui lòng nhập mật khẩu.";
            return false;
        }

        if (pw.length < 8) {
            elMsg.textContent = "Mật khẩu phải có ít nhất 8 ký tự.";
            return false;
        }

        elMsg.textContent = "";
        return true;
    }

    function checkConfirmPassword() {
        const elMsg = document.getElementById("feedback-confirm_password");
        const pw = password.value.trim();
        const confirmPw = confirmPassword.value.trim();

        if (!confirmPw) {
            elMsg.textContent = "Vui lòng nhập lại mật khẩu.";
            return false;
        }

        if (pw !== confirmPw) {
            elMsg.textContent = "Mật khẩu xác nhận không khớp.";
            return false;
        }

        elMsg.textContent = "";
        return true;
    }

    function checkName() {
        const elMsg = document.getElementById("feedback-name");
        const nameValue = name.value.trim();
        const namePattern = /^[A-Za-zÀ-ỹà-ỹ\s]+$/;

        if (!nameValue) {
            elMsg.textContent = "Vui lòng nhập họ và tên.";
            return false;
        }

        if (!namePattern.test(nameValue)) {
            elMsg.textContent = "Họ và tên chỉ được chứa chữ cái và khoảng trắng.";
            return false;
        }

        elMsg.textContent = "";
        return true;
    }

    document.getElementById("registerForm").addEventListener("submit", function (event) {
        const isValid =
            checkName() &&
            checkEmail() &&
            checkPassword() &&
            checkConfirmPassword();

        if (!isValid) {
            event.preventDefault();
        }
    });

    window.checkEmail = checkEmail;
    window.checkPassword = checkPassword;
    window.checkConfirmPassword = checkConfirmPassword;
    window.checkName = checkName;
};
