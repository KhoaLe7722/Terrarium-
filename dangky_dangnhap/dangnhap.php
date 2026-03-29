<?php
session_start();
require_once 'config.php';

$redirectKey = normalize_auth_redirect_key($_POST['redirect'] ?? $_GET['redirect'] ?? 'profile');
$redirectTarget = resolve_auth_redirect_target($redirectKey);
$registerUrl = build_auth_page_url('dangky.php', $redirectKey);

if (!empty($_SESSION['user_id'])) {
    header('Location: ' . $redirectTarget);
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = normalize_user_email($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $message = 'Vui lòng nhập đầy đủ email và mật khẩu.';
    } else {
        $stmt = $conn->prepare("
            SELECT id, ho_ten, email, mat_khau, anh_dai_dien, vai_tro
            FROM users
            WHERE email = ?
            LIMIT 1
        ");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && verify_and_upgrade_user_password($conn, $user, $password)) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['user_name'] = $user['ho_ten'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_avatar'] = $user['anh_dai_dien'] ?? null;
            $_SESSION['user_role'] = $user['vai_tro'];

            header('Location: ' . $redirectTarget);
            exit;
        }

        $message = 'Email hoặc mật khẩu không đúng.';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="../images/avatar.png" type="image/png" />
    <title>Đăng nhập | Thuận Phát Garden</title>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Dosis&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Text&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100..900&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

<link rel="stylesheet" href="../mainfont/main.css?v=20260329-4" />
    <link rel="stylesheet" href="dangky_dangnhap.css?v=20260324-1" />
</head>

<body data-page="login">
    <nav class="navigation" id="main-nav"></nav>
<script defer src="../mainfont/layout.js?v=20260329-4"></script>
    <script defer src="../mainfont/main.js?v=20260329-2"></script>

    <main class="body__main body__main__login">
        <div class="auth-form">
            <div class="auth-form__container">
                <div class="auth-form__header">
                    <h3 class="auth-form__heading">Đăng nhập</h3>
                    <a href="<?= htmlspecialchars($registerUrl) ?>" class="auth-form__switch-btn">Đăng ký</a>
                </div>

                <?php if ($message !== ''): ?>
                    <div style="margin-bottom:16px;padding:12px 14px;border-radius:8px;background:#fff3f3;color:#b42318;font-size:14px;">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <form id="loginForm" method="POST" action="dangnhap.php">
                    <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirectKey) ?>">

                    <div class="auth-form__form">
                        <div class="auth-form__group">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" name="email" type="email" class="auth-form__input"
                                placeholder="Nhập email của bạn" required
                                value="<?= htmlspecialchars(normalize_user_email($_POST['email'] ?? '')) ?>" />
                            <small id="feedback-email" class="form-message"></small>
                        </div>

                        <div class="auth-form__group">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <div class="auth-form__password-wrap">
                                <input id="password" name="password" type="password" class="auth-form__input auth-form__input--password"
                                    placeholder="Nhập mật khẩu" required />
                                <button
                                    type="button"
                                    class="auth-form__toggle-password"
                                    data-toggle-password
                                    data-target="password"
                                    aria-label="Hiện mật khẩu"
                                    aria-pressed="false">
                                    <i class="far fa-eye-slash" aria-hidden="true"></i>
                                </button>
                            </div>
                            <small id="feedback-password" class="form-message"></small>
                        </div>
                    </div>

                    <div class="auth-form__controls">
                        <a href="../trangchu/index.php" class="btn btn--normal auth-form__controls-back">TRANG CHỦ</a>
                        <button type="submit" class="btn btn--primary">ĐĂNG NHẬP</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer class="site-footer" id="site-footer"></footer>
    <script src="dangnhap.js?v=20260324-2"></script>
</body>

</html>
