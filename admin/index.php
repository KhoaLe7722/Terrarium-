<?php
session_start();
require_once __DIR__ . '/../dangky_dangnhap/config.php';

// Nếu đã đăng nhập admin → chuyển dashboard
if (isset($_SESSION['admin_id']) && $_SESSION['admin_role'] === 'quan_tri') {
    header('Location: dashboard.php');
    exit;
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $message = 'Vui lòng nhập email và mật khẩu!';
        $messageType = 'error';
    } else {
        $stmt = $conn->prepare("SELECT id, name, email, password, vai_tro FROM users WHERE email = ? AND vai_tro = 'quan_tri'");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['name'];
            $_SESSION['admin_email'] = $user['email'];
            $_SESSION['admin_role'] = $user['vai_tro'];
            header('Location: dashboard.php');
            exit;
        } else {
            $message = 'Email hoặc mật khẩu không đúng, hoặc bạn không có quyền quản trị!';
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Quản trị | Thuận Phát Garden</title>
    <link rel="icon" href="../images/avatar.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Dosis:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Dosis', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f4f7f4;
            overflow: hidden;
            position: relative;
        }
        /* Animated background particles */
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(84, 121, 74,0.05) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: moveParticles 25s linear infinite;
            z-index: 0;
        }
        @keyframes moveParticles {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }
        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
            padding: 20px;
        }
        .login-card {
            background: #ffffff;
            border: 1px solid rgba(84, 121, 74,0.1);
            border-radius: 24px;
            padding: 48px 40px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
        }
        .login-logo {
            text-align: center;
            margin-bottom: 32px;
        }
        .login-logo .logo-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #54794a, #6ba060);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            box-shadow: 0 8px 24px rgba(84, 121, 74, 0.2);
        }
        .login-logo .logo-icon i {
            font-size: 28px;
            color: #fff;
        }
        .login-logo h1 {
            color: #333;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .login-logo p {
            color: #666;
            font-size: 16px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            color: #444;
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .input-wrapper {
            position: relative;
        }
        .input-wrapper i.input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            font-size: 16px;
            transition: color 0.3s;
        }
        .input-wrapper input {
            width: 100%;
            padding: 14px 44px 14px 44px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 12px;
            color: #333;
            font-size: 16px;
            font-family: 'Dosis', sans-serif;
            transition: all 0.3s;
            outline: none;
        }
        .input-wrapper input::placeholder { color: #aaa; }
        .input-wrapper input:focus {
            border-color: #54794a;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(84, 121, 74, 0.1);
        }
        .input-wrapper input:focus + i.input-icon,
        .input-wrapper input:focus ~ i.input-icon { color: #54794a; }
        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #aaa;
            font-size: 16px;
            cursor: pointer;
            transition: color 0.3s;
            padding: 0;
            outline: none;
        }
        .toggle-password:hover { color: #54794a; }
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #54794a, #6ba060);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 600;
            font-family: 'Dosis', sans-serif;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 8px;
            position: relative;
            overflow: hidden;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(84, 121, 74, 0.3);
        }
        .btn-login:active { transform: translateY(0); }
        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 15px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.3s ease;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .alert-error {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.2);
            color: #dc2626;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 24px;
            color: #777;
            font-size: 15px;
            text-decoration: none;
            transition: color 0.3s;
        }
        .back-link:hover { color: #54794a; }

        /* Floating orbs */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            z-index: 0;
            animation: float 15s ease-in-out infinite;
        }
        .orb-1 { width: 300px; height: 300px; background: #54794a; top: 10%; left: 10%; }
        .orb-2 { width: 200px; height: 200px; background: #86efac; bottom: 10%; right: 15%; animation-delay: -5s; }
        .orb-3 { width: 250px; height: 250px; background: #a7f3d0; top: 60%; left: 60%; animation-delay: -10s; }
        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.05); }
            66% { transform: translate(-20px, 20px) scale(0.95); }
        }
    </style>
</head>
<body>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="login-container">
        <div class="login-card">
            <div class="login-logo">
                <div class="logo-icon">
                    <i class="fas fa-leaf"></i>
                </div>
                <h1>Thuận Phát Garden</h1>
                <p>Bảng điều khiển quản trị</p>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="index.php">
                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" placeholder="admin@example.com"
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                        <i class="fas fa-envelope input-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                        <i class="fas fa-lock input-icon"></i>
                        <button type="button" class="toggle-password" onclick="togglePassword()" title="Hiện/ẩn mật khẩu">
                            <i class="fas fa-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Đăng nhập
                </button>
            </form>

            <a href="../trangchu/index.html" class="back-link">
                <i class="fas fa-arrow-left"></i> Quay lại trang chủ
            </a>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
