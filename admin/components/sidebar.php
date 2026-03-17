<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar-overlay"></div>
<aside class="admin-sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <img src="../images/avatar.png" alt="Logo Thuận Phát Garden">
        </div>
        <div class="sidebar-brand">
            Thuận Phát Garden
            <small>Admin Panel</small>
        </div>
    </div>
    
    <nav class="sidebar-nav">
        <div class="nav-label">Quản lý chung</div>
        <a href="dashboard.php" class="nav-item <?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">
            <i class="fas fa-home"></i> Tổng quan
        </a>
        
        <div class="nav-label">Cửa hàng</div>
        <a href="products.php" class="nav-item <?= in_array($currentPage, ['products.php', 'add_product.php', 'edit_product.php']) ? 'active' : '' ?>">
            <i class="fas fa-box-open"></i> Sản phẩm
        </a>
        <a href="discount.php" class="nav-item <?= $currentPage == 'discount.php' ? 'active' : '' ?>">
            <i class="fas fa-tags"></i> Giảm giá
        </a>
        <a href="orders.php" class="nav-item <?= in_array($currentPage, ['orders.php', 'order_detail.php']) ? 'active' : '' ?>">
            <i class="fas fa-shopping-cart"></i> Đơn hàng
        </a>
        <a href="customers.php" class="nav-item <?= $currentPage == 'customers.php' ? 'active' : '' ?>">
            <i class="fas fa-users"></i> Khách hàng
        </a>
        
        <div class="nav-label">Website</div>
        <a href="../trangchu/index.php" class="nav-item" target="_blank">
            <i class="fas fa-globe"></i> Xem trang chính
        </a>
    </nav>
    
    <div class="sidebar-footer">
        <a href="logout.php" class="nav-item">
            <i class="fas fa-sign-out-alt"></i> Đăng xuất
        </a>
    </div>
</aside>
