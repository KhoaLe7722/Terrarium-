<?php
session_start();
require_once 'config.php';

$message = '';
$messageType = ''; // 'success' hoặc 'error'

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $message = 'Vui lòng nhập email và mật khẩu!';
        $messageType = 'error';
    } else {
        // Tìm user trong database
        $stmt = $conn->prepare("SELECT id, name, email, password, avatar FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Đăng nhập thành công → Lưu session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_avatar'] = $user['avatar'];

            $message = 'Đăng nhập thành công! Chào mừng ' . htmlspecialchars($user['name']);
            $messageType = 'success';
        } else {
            $message = 'Email hoặc mật khẩu không đúng!';
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="../images/avatar.png" type="image/png" />
    <title>Đăng nhập | Thuận Phát G Garden</title>

    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Dosis&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Text&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100..900&display=swap" rel="stylesheet" />

    <!-- External CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

    <!-- Main CSS -->
    <link rel="stylesheet" href="../mainfont/main.css" />
    <link rel="stylesheet" href="dangky_dangnhap.css" />
</head>

<body data-page="login">
    <!-- Header được render từ JS -->
    <nav class="navigation" id="main-nav"></nav>
    <!-- Import Layout JS -->
    <script defer src="../mainfont/layout.js"></script>
    <script defer src="../mainfont/main.js"></script>

    <!-- Login form -->
    <main class="body__main body__main__login">
        <div class="auth-form">
            <div class="auth-form__container">
                <div class="auth-form__header">
                    <h3 class="auth-form__heading">Đăng nhập</h3>
                    <a href="dangky.php" class="auth-form__switch-btn">Đăng ký</a>
                </div>

                <form id="loginForm" method="POST" action="dangnhap.php">
                    <div class="auth-form__form">
                        <div class="auth-form__group">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" name="email" type="text" class="auth-form__input"
                                placeholder="Nhập email của bạn" required
                                value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" />
                            <small id="feedback-email" class="form-message"></small>
                        </div>

                        <div class="auth-form__group">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input id="password" name="password" type="password" class="auth-form__input"
                                placeholder="Nhập mật khẩu" required />
                            <small id="feedback-password" class="form-message"></small>
                        </div>

                        <div class="checkbox-container">
                            <input type="checkbox" id="rememberMe" />
                            <label for="rememberMe">Ghi nhớ đăng nhập</label>
                        </div>
                    </div>

                    <div class="auth-form__aside">
                        <div class="auth-form__help">
                            <a href="#" class="auth-form__help-link auth-form__help-forgot">Quên mật khẩu</a>
                            <span class="auth-form__help-separate"></span>
                            <a href="#" class="auth-form__help-link">Cần trợ giúp?</a>
                        </div>
                    </div>

                    <div class="auth-form__controls">
                        <a href="../trangchu/index.html" class="btn btn--normal auth-form__controls-back">TRỞ LẠI</a>
                        <button type="submit" class="btn btn--primary">ĐĂNG NHẬP</button>
                    </div>

                    <div class="auth-form__socials">
                        <a href="https://facebook.com"
                            class="auth-form__socials--facebook btn btn--size-s btn--with-icon">
                            <i class="auth-form__socials-icon fab fa-facebook-square"></i>
                            <span class="auth-form__socials-title">Đăng nhập với Facebook</span>
                        </a>
                        <a href="https://accounts.google.com/"
                            class="auth-form__socials--google btn btn--size-s btn--with-icon">
                            <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google logo"
                                class="auth-form__socials-icon auth-form__google-img" />
                            <span class="auth-form__socials-title">Đăng nhập với Google</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Thông báo thành công -->
        <div class="box__success" style="display: <?php echo ($messageType === 'success') ? 'block' : 'none'; ?>;">
            <img class="img__success" src="../images/dangky_dangnhap/success.png" alt="thông báo">
            <p><?php echo $message; ?></p>
            <button type="button" class="button__success" onclick="window.location.href='../trangchu/index.html'">Đã hiểu</button>
        </div>

        <!-- Thông báo lỗi -->
        <div class="box__fail" style="display: <?php echo ($messageType === 'error') ? 'block' : 'none'; ?>;">
            <img class="img__fail" src="../images/dangky_dangnhap/fail.png" alt="thông báo">
            <p><?php echo $message; ?></p>
            <button type="button" class="button__fail" onclick="this.parentElement.style.display='none'">Đã hiểu</button>
        </div>
    </main>

    <!-- Footer được render từ JS -->
    <footer class="site-footer" id="site-footer"></footer>
</body>

</html>