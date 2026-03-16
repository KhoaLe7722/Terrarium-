<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'loggedIn' => true,
        'userName' => $_SESSION['user_name'],
        'userEmail' => $_SESSION['user_email'],
        'userAvatar' => $_SESSION['user_avatar'] ?? null
    ]);
} else {
    echo json_encode([
        'loggedIn' => false
    ]);
}
