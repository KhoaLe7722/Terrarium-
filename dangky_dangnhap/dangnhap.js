window.onload = function () {
    const email = document.getElementById("email");
    const password = document.getElementById("password");
    const form = document.getElementById("loginForm");
    const toggleButtons = document.querySelectorAll("[data-toggle-password]");

    toggleButtons.forEach((button) => {
        const targetId = button.dataset.target;
        const input = document.getElementById(targetId);
        const icon = button.querySelector("i");

        if (!input || !icon) {
            return;
        }

        function syncPasswordToggle() {
            const isVisible = input.type === "text";
            button.setAttribute("aria-pressed", String(isVisible));
            button.setAttribute("aria-label", isVisible ? "Ẩn mật khẩu" : "Hiện mật khẩu");
            icon.classList.toggle("fa-eye", isVisible);
            icon.classList.toggle("fa-eye-slash", !isVisible);
        }

        syncPasswordToggle();

        button.addEventListener("click", function () {
            input.type = input.type === "password" ? "text" : "password";
            syncPasswordToggle();
        });
    });

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
