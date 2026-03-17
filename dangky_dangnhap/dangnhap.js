window.onload = function () {
    const email = document.getElementById("email");
    const password = document.getElementById("password");
    const form = document.getElementById("loginForm");

    form.addEventListener("submit", function (event) {
        const emailValue = email.value.trim();
        const passwordValue = password.value.trim();

        document.getElementById("feedback-email").textContent = "";
        document.getElementById("feedback-password").textContent = "";

        if (!emailValue || !passwordValue) {
            event.preventDefault();
            if (!emailValue) {
                document.getElementById("feedback-email").textContent = "Vui lòng nhập email.";
            }
            if (!passwordValue) {
                document.getElementById("feedback-password").textContent = "Vui lòng nhập mật khẩu.";
            }
        }
    });
};
