<?php
session_start();
require_once 'config.php';

function resolve_redirect_target(?string $key): string
{
    $routes = [
        'checkout' => '../thanhtoan/thanhtoan.php',
        'home' => '../trangchu/index.php',
        'profile' => 'ho_so.php',
    ];

    return $routes[$key] ?? 'ho_so.php';
}

$redirectKey = $_POST['redirect'] ?? $_GET['redirect'] ?? 'profile';
$redirectTarget = resolve_redirect_target($redirectKey);

if (!empty($_SESSION['user_id'])) {
    header('Location: ' . $redirectTarget);
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $message = 'Vui lòng nhập đầy đủ email và mật khẩu.';
    } else {
        $stmt = $conn->prepare("
            SELECT id, ho_ten, email, mat_khau, vai_tro
            FROM users
            WHERE email = ?
            LIMIT 1
        ");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['mat_khau'])) {
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['user_name'] = $user['ho_ten'];
            $_SESSION['user_email'] = $user['email'];
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

    <link rel="stylesheet" href="../mainfont/main.css" />
    <link rel="stylesheet" href="dangky_dangnhap.css" />
</head>

<body data-page="login">
    <nav class="navigation" id="main-nav"></nav>
    <script defer src="../mainfont/layout.js"></script>
    <script defer src="../mainfont/main.js"></script>

    <main class="body__main body__main__login">
        <div class="auth-form">
            <div class="auth-form__container">
                <div class="auth-form__header">
                    <h3 class="auth-form__heading">Đăng nhập</h3>
                    <a href="dangky.php" class="auth-form__switch-btn">Đăng ký</a>
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
                                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
                            <small id="feedback-email" class="form-message"></small>
                        </div>

                        <div class="auth-form__group">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input id="password" name="password" type="password" class="auth-form__input"
                                placeholder="Nhập mật khẩu" required />
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
    <script src="dangnhap.js"></script>
</body>

</html>
