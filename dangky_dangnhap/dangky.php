<?php
session_start();
require_once 'config.php';

$redirectKey = normalize_auth_redirect_key($_POST['redirect'] ?? $_GET['redirect'] ?? 'profile');
$redirectTarget = resolve_auth_redirect_target($redirectKey);
$loginUrl = build_auth_page_url('dangnhap.php', $redirectKey);

if (!empty($_SESSION['user_id'])) {
    header('Location: ' . $redirectTarget);
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = normalize_user_email($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($name === '' || $email === '' || $password === '' || $confirmPassword === '') {
        $message = 'Vui lòng điền đầy đủ thông tin.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Email không hợp lệ.';
    } elseif (mb_strlen($password) < 8) {
        $message = 'Mật khẩu phải có ít nhất 8 ký tự.';
    } elseif ($password !== $confirmPassword) {
        $message = 'Mật khẩu xác nhận không khớp.';
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $message = 'Email này đã được sử dụng.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            if (create_user_account($conn, $name, $email, $hashedPassword, 'khach')) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int) $conn->lastInsertId();
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = 'khach';

                header('Location: ' . $redirectTarget);
                exit;
            }

            $message = 'Không thể tạo tài khoản, vui lòng thử lại.';
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
    <title>Đăng ký | Thuận Phát Garden</title>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Dosis&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Text&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100..900&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

    <link rel="stylesheet" href="../mainfont/main.css?v=20260318-2" />
    <link rel="stylesheet" href="dangky_dangnhap.css" />
</head>

<body data-page="login">
    <nav class="navigation" id="main-nav"></nav>
    <script defer src="../mainfont/layout.js?v=20260318-2"></script>
    <script defer src="../mainfont/main.js?v=20260318-2"></script>

    <main class="body__main body__main__logout">
        <div class="auth-form">
            <div class="auth-form__container">
                <div class="auth-form__header">
                    <h3 class="auth-form__heading">Đăng ký</h3>
                    <a href="<?= htmlspecialchars($loginUrl) ?>" class="auth-form__switch-btn">Đăng nhập</a>
                </div>

                <?php if ($message !== ''): ?>
                    <div style="margin-bottom:16px;padding:12px 14px;border-radius:8px;background:#fff3f3;color:#b42318;font-size:14px;">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <form id="registerForm" method="POST" action="dangky.php">
                    <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirectKey) ?>">

                    <div class="auth-form__form">
                        <div class="auth-form__group">
                            <label for="name" class="form-label">Họ và tên</label>
                            <input id="name" name="name" type="text" class="auth-form__input"
                                placeholder="VD: Nguyễn Văn A" required
                                value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" />
                            <small id="feedback-name" class="form-message"></small>
                        </div>

                        <div class="auth-form__group">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" name="email" type="email" class="auth-form__input"
                                placeholder="VD: email@domain.com" required
                                value="<?= htmlspecialchars(normalize_user_email($_POST['email'] ?? '')) ?>" />
                            <small id="feedback-email" class="form-message"></small>
                        </div>

                        <div class="auth-form__group">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input id="password" name="password" type="password" class="auth-form__input"
                                placeholder="Ít nhất 8 ký tự" required />
                            <small id="feedback-password" class="form-message"></small>
                        </div>

                        <div class="auth-form__group">
                            <label for="confirm_password" class="form-label">Xác nhận mật khẩu</label>
                            <input id="confirm_password" name="confirm_password" type="password"
                                class="auth-form__input" placeholder="Nhập lại mật khẩu" required />
                            <small id="feedback-confirm_password" class="form-message"></small>
                        </div>
                    </div>

                    <div class="auth-form__controls">
                        <a href="../trangchu/index.php" class="btn btn--normal auth-form__controls-back">TRANG CHỦ</a>
                        <button type="submit" class="btn btn--primary">TẠO TÀI KHOẢN</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer class="site-footer" id="site-footer"></footer>
    <script src="dangky.js"></script>
</body>

</html>
