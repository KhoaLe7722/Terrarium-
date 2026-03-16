<!-- Header dùng chung cho các trang Admin -->
<header class="admin-header">
    <div class="header-left">
        <button class="btn-toggle-sidebar">
            <i class="fas fa-bars"></i>
        </button>
        <h2><?= isset($pageTitle) ? $pageTitle : 'Bảng điều khiển' ?></h2>
    </div>
    <div class="header-right">
        <div class="admin-user">
            <div class="admin-user-info text-right">
                <div class="name"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></div>
                <div class="role">Quản trị viên</div>
            </div>
            <div class="admin-avatar">
                <?= strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)) ?>
            </div>
        </div>
    </div>
</header>
