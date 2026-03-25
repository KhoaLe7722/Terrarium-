<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';
require_once __DIR__ . '/../includes/store_helpers.php';

if (isset($_SESSION['user_id'])) {
    $currentUser = current_user($conn);

    if (!$currentUser) {
        unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_email'], $_SESSION['user_role'], $_SESSION['user_avatar']);

        echo json_encode([
            'loggedIn' => false,
        ]);
        exit;
    }

    $_SESSION['user_name'] = $currentUser['ho_ten'];
    $_SESSION['user_email'] = $currentUser['email'];
    $_SESSION['user_role'] = $currentUser['vai_tro'] ?? 'khach';
    $_SESSION['user_avatar'] = $currentUser['anh_dai_dien'] ?? null;

    echo json_encode([
        'loggedIn' => true,
        'userName' => $currentUser['ho_ten'],
        'userEmail' => $currentUser['email'],
        'userRole' => $currentUser['vai_tro'] ?? 'khach',
        'userAvatar' => !empty($currentUser['anh_dai_dien']) ? normalize_public_path($currentUser['anh_dai_dien']) : null,
        'profileUrl' => '../dangky_dangnhap/ho_so.php',
        'logoutUrl' => '../dangky_dangnhap/logout.php',
        'adminUrl' => ($currentUser['vai_tro'] ?? '') === 'quan_tri' ? '../admin/dashboard.php' : null,
    ]);
} else {
    echo json_encode([
        'loggedIn' => false,
    ]);
}
