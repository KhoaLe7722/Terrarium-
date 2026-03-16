<?php
session_start();
require_once __DIR__ . '/../dangky_dangnhap/config.php';

// Kiểm tra đã đăng nhập và có vai trò quản trị
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'quan_tri') {
    // Tự động xác định đường dẫn đến thư mục admin
    $script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    // Nếu đang ở trong thư mục api, cần ra ngoài một cấp
    if (strpos($script_dir, '/api') !== false) {
        header('Location: ../index.php');
    } else {
        header('Location: index.php');
    }
    exit;
}
