<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'loggedIn' => true,
        'userName' => $_SESSION['user_name'],
        'userEmail' => $_SESSION['user_email'],
        'userRole' => $_SESSION['user_role'] ?? 'khach',
        'profileUrl' => '../dangky_dangnhap/ho_so.php',
        'logoutUrl' => '../dangky_dangnhap/logout.php',
        'adminUrl' => ($_SESSION['user_role'] ?? '') === 'quan_tri' ? '../admin/dashboard.php' : null,
    ]);
} else {
    echo json_encode([
        'loggedIn' => false,
    ]);
}
