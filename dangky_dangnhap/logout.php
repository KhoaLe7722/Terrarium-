<?php
session_start();
unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_email'], $_SESSION['user_role'], $_SESSION['user_avatar']);
session_destroy();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="refresh" content="0;url=../trangchu/index.php" />
    <title>Đang đăng xuất...</title>
</head>

<body>
    <script>
        try {
            localStorage.removeItem('cart');
            window.dispatchEvent(new CustomEvent('store:cart-updated', {
                detail: { cart: [] }
            }));
        } catch (error) { }

        window.location.replace('../trangchu/index.php');
    </script>
</body>

</html>
