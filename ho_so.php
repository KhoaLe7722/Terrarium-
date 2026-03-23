<?php
session_start();
require_once 'config.php';
require_once __DIR__ . '/../includes/store_helpers.php';

if (empty($_SESSION['user_id'])) {
    header('Location: dangnhap.php');
    exit;
}

$userStmt = $conn->prepare(" 
    SELECT id, ho_ten, email, so_dien_thoai, dia_chi, vai_tro, ngay_tao, ngay_capnhat
    FROM users
    WHERE id = ?
");
$userStmt->execute([(int) $_SESSION['user_id']]);
$user = $userStmt->fetch();

if (!$user) {
    header('Location: logout.php');
    exit;
}

function profile_avatar_url(int $userId): string
{
    $absolutePattern = dirname(__DIR__) . '/uploads/avatars/user_' . $userId . '.*';
    $matches = glob($absolutePattern) ?: [];

    if (!empty($matches)) {
        usort($matches, static function ($a, $b) {
            return filemtime($b) <=> filemtime($a);
        });

        $absolutePath = str_replace('\\', '/', $matches[0]);
        $projectRoot = str_replace('\\', '/', dirname(__DIR__));
        $relativePath = ltrim(str_replace($projectRoot, '', $absolutePath), '/');

        return '../' . $relativePath . '?v=' . filemtime($matches[0]);
    }

    return '../images/avatar.png';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (stripos($contentType, 'application/json') !== false) {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
    } else {
        $data = $_POST;
    }

    $action = $data['action'] ?? '';

    if ($action === 'update_profile') {
        $ho_ten = trim($data['ho_ten'] ?? '');
        $email = trim($data['email'] ?? '');
        $so_dien_thoai = trim($data['so_dien_thoai'] ?? '');
        $dia_chi = trim($data['dia_chi'] ?? '');

        if ($ho_ten === '') {
            echo json_encode(['success' => false, 'error' => 'Họ tên không được để trống']);
            exit;
        }

        if ($email === '') {
            echo json_encode(['success' => false, 'error' => 'Email không được để trống']);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'error' => 'Email không hợp lệ']);
            exit;
        }

        try {
            $duplicateStmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id <> ? LIMIT 1");
            $duplicateStmt->execute([$email, $user['id']]);
            if ($duplicateStmt->fetch()) {
                echo json_encode(['success' => false, 'error' => 'Email đã được sử dụng']);
                exit;
            }

            $updateStmt = $conn->prepare("UPDATE users SET ho_ten = ?, email = ?, so_dien_thoai = ?, dia_chi = ? WHERE id = ?");
            $updateStmt->execute([$ho_ten, $email, $so_dien_thoai, $dia_chi, $user['id']]);

            echo json_encode(['success' => true]);
            exit;
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Lỗi cơ sở dữ liệu']);
            exit;
        }
    }

    if ($action === 'upload_avatar') {
        if (!isset($_FILES['avatar']) || !is_array($_FILES['avatar'])) {
            echo json_encode(['success' => false, 'error' => 'Không tìm thấy ảnh tải lên']);
            exit;
        }

        $avatarFile = $_FILES['avatar'];

        if ((int) $avatarFile['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'error' => 'Tải ảnh lên thất bại']);
            exit;
        }

        if ((int) $avatarFile['size'] > 2 * 1024 * 1024) {
            echo json_encode(['success' => false, 'error' => 'Ảnh vượt quá 2MB']);
            exit;
        }

        $extension = strtolower(pathinfo($avatarFile['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($extension, $allowedExtensions, true)) {
            echo json_encode(['success' => false, 'error' => 'Chỉ chấp nhận JPG, PNG hoặc WEBP']);
            exit;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $avatarFile['tmp_name']);
        finfo_close($finfo);

        $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($mimeType, $allowedMime, true)) {
            echo json_encode(['success' => false, 'error' => 'Định dạng ảnh không hợp lệ']);
            exit;
        }

        $uploadDir = dirname(__DIR__) . '/uploads/avatars';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
            echo json_encode(['success' => false, 'error' => 'Không thể tạo thư mục lưu ảnh']);
            exit;
        }

        $oldFiles = glob($uploadDir . '/user_' . (int) $user['id'] . '.*') ?: [];
        foreach ($oldFiles as $oldFile) {
            @unlink($oldFile);
        }

        $newFileName = 'user_' . (int) $user['id'] . '.' . $extension;
        $destination = $uploadDir . '/' . $newFileName;

        if (!move_uploaded_file($avatarFile['tmp_name'], $destination)) {
            echo json_encode(['success' => false, 'error' => 'Không thể lưu ảnh']);
            exit;
        }

        echo json_encode([
            'success' => true,
            'avatar_url' => '../uploads/avatars/' . $newFileName . '?v=' . time(),
        ]);
        exit;
    }

    echo json_encode(['success' => false, 'error' => 'Hành động không hợp lệ']);
    exit;
}

$avatarUrl = profile_avatar_url((int) $user['id']);
$roleText = $user['vai_tro'] === 'quan_tri' ? 'Quản trị' : 'Khách hàng';
$createdAt = !empty($user['ngay_tao']) ? date('d/m/Y H:i', strtotime((string) $user['ngay_tao'])) : '';
$updatedAt = !empty($user['ngay_capnhat']) ? date('d/m/Y H:i', strtotime((string) $user['ngay_capnhat'])) : 'Chưa cập nhật';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="../images/avatar.png" type="image/png" />
    <title>Hồ sơ của tôi | Thuận Phát Garden</title>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../mainfont/main.css?v=20260318-2" />
    <link rel="stylesheet" href="ho_so.css?v=20260322-2" />
</head>

<body data-page="profile">
    <nav class="navigation" id="main-nav"></nav>
    <script defer src="../mainfont/layout.js?v=20260318-2"></script>
    <script defer src="../mainfont/main.js?v=20260322-1"></script>

    <main class="body__main profile-page-main">
        <div class="profile-container">
            <div class="profile-card">
                <aside class="profile-left">
                    <img id="avatarPreview" class="avatar" src="<?= htmlspecialchars($avatarUrl) ?>" alt="Ảnh đại diện">
                    <h3 id="displayName"><?= htmlspecialchars($user['ho_ten']) ?></h3>
                    <p id="displayEmail"><?= htmlspecialchars($user['email']) ?></p>
                </aside>

                <section class="profile-right">
                    <form id="profileForm" novalidate>
                        <div class="form-group">
                            <label for="name">Họ tên</label>
                            <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['ho_ten']) ?>">
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>">
                        </div>

                        <div class="form-group">
                            <label for="phone">Số điện thoại</label>
                            <input type="text" id="phone" name="phone" value="<?= htmlspecialchars((string) ($user['so_dien_thoai'] ?? '')) ?>" placeholder="Chưa cập nhật">
                        </div>

                        <div class="form-group">
                            <label for="address">Địa chỉ</label>
                            <input type="text" id="address" name="address" value="<?= htmlspecialchars((string) ($user['dia_chi'] ?? '')) ?>" placeholder="Chưa cập nhật">
                        </div>

                        <div class="form-group">
                            <label for="avatarInput">Ảnh đại diện</label>
                            <input type="file" id="avatarInput" name="avatar" accept="image/*">
                        </div>

                        <div class="form-group">
                            <label for="userId">ID người dùng</label>
                            <input type="text" id="userId" value="<?= (int) $user['id'] ?>" disabled>
                        </div>

                        <div class="form-group">
                            <label for="role">Vai trò</label>
                            <input type="text" id="role" value="<?= htmlspecialchars($roleText) ?>" disabled>
                        </div>

                        <div class="form-group">
                            <label for="createdAt">Ngày tạo</label>
                            <input type="text" id="createdAt" value="<?= htmlspecialchars($createdAt) ?>" disabled>
                        </div>

                        <div class="form-group">
                            <label for="updatedAt">Cập nhật lần cuối</label>
                            <input type="text" id="updatedAt" value="<?= htmlspecialchars($updatedAt) ?>" disabled>
                        </div>

                        <button type="button" class="btn-save" id="saveProfileBtn">Cập nhật</button>
                    </form>
                </section>
            </div>
        </div>
    </main>

    <footer class="site-footer" id="site-footer"></footer>

    <script>
        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const phoneInput = document.getElementById('phone');
        const addressInput = document.getElementById('address');
        const avatarInput = document.getElementById('avatarInput');
        const avatarPreview = document.getElementById('avatarPreview');
        const saveProfileBtn = document.getElementById('saveProfileBtn');
        const updatedAtInput = document.getElementById('updatedAt');

        function refreshLeftPanel() {
            document.getElementById('displayName').textContent = nameInput.value.trim() || 'Chưa cập nhật';
            document.getElementById('displayEmail').textContent = emailInput.value.trim() || 'Chưa cập nhật';
        }

        function uploadAvatar(file) {
            const formData = new FormData();
            formData.append('action', 'upload_avatar');
            formData.append('avatar', file);

            fetch('', {
                method: 'POST',
                body: formData,
            })
                .then((response) => response.json())
                .then((result) => {
                    if (!result.success) {
                        alert('Lỗi: ' + (result.error || 'Không thể tải ảnh lên'));
                        avatarInput.value = '';
                        return;
                    }

                    avatarPreview.src = result.avatar_url;
                    alert('Đã cập nhật ảnh đại diện!');
                })
                .catch(() => {
                    alert('Lỗi kết nối khi tải ảnh lên.');
                    avatarInput.value = '';
                });
        }

        function saveProfile() {
            const payload = {
                action: 'update_profile',
                ho_ten: nameInput.value.trim(),
                email: emailInput.value.trim(),
                so_dien_thoai: phoneInput.value.trim(),
                dia_chi: addressInput.value.trim(),
            };

            if (!payload.ho_ten) {
                alert('Vui lòng nhập họ tên!');
                return;
            }

            if (!payload.email) {
                alert('Vui lòng nhập email!');
                return;
            }

            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload),
            })
                .then((response) => response.json())
                .then((result) => {
                    if (!result.success) {
                        alert('Lỗi: ' + (result.error || 'Không thể lưu thông tin'));
                        return;
                    }

                    refreshLeftPanel();
                    updatedAtInput.value = new Date().toLocaleString('vi-VN');
                    alert('Cập nhật thành công!');
                })
                .catch(() => {
                    alert('Lỗi kết nối, vui lòng thử lại.');
                });
        }

        avatarInput.addEventListener('change', (event) => {
            const file = event.target.files && event.target.files[0];
            if (!file) {
                return;
            }
            uploadAvatar(file);
        });

        saveProfileBtn.addEventListener('click', saveProfile);
        document.getElementById('profileForm').addEventListener('submit', (event) => {
            event.preventDefault();
            saveProfile();
        });

        refreshLeftPanel();
    </script>
</body>

</html>
