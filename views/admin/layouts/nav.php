<?php
// views/admin/layouts/nav.php
require_once __DIR__ . '/../../../config/auth.php';

$currentC = $_GET['c'] ?? 'dashboard';
?>
<div class="admin-shell">
    <aside class="admin-sidebar">
        <a class="admin-brand d-flex align-items-center gap-2 px-3 py-3" href="<?= e(admin_url('c=dashboard')) ?>">
            <span class="brand-icon"><i class="fa-solid fa-hotel"></i></span>
            <span>KHÁCH SẠN ADMIN</span>
        </a>

        <nav class="admin-menu px-2">
            <?php if (Auth::hasPermission('DASHBOARD')): ?>
            <a class="admin-menu-link <?= ($currentC === 'dashboard') ? 'active' : '' ?>" href="<?= e(admin_url('c=dashboard')) ?>">
                <i class="fa-solid fa-gauge-high"></i>
                <span>Dashboard</span>
            </a>
            <?php endif; ?>

            <?php if (Auth::hasPermission('PHONG')): ?>
            <a class="admin-menu-link <?= ($currentC === 'phong') ? 'active' : '' ?>" href="<?= e(admin_url('c=phong')) ?>">
                <i class="fa-solid fa-key"></i>
                <span>Phòng</span>
            </a>
            <?php endif; ?>

            <?php if (Auth::hasPermission('LOAI_PHONG')): ?>
            <a class="admin-menu-link <?= ($currentC === 'loai-phong') ? 'active' : '' ?>" href="<?= e(admin_url('c=loai-phong')) ?>">
                <i class="fa-solid fa-bed"></i>
                <span>Loại phòng</span>
            </a>
            <?php endif; ?>

            <?php if (Auth::hasPermission('DAT_PHONG')): ?>
            <a class="admin-menu-link <?= ($currentC === 'dat-phong') ? 'active' : '' ?>" href="<?= e(admin_url('c=dat-phong')) ?>">
                <i class="fa-solid fa-calendar-check"></i>
                <span>Đặt phòng</span>
            </a>
            <?php endif; ?>

            <?php if (Auth::hasPermission('DICH_VU')): ?>
            <a class="admin-menu-link <?= ($currentC === 'dich-vu') ? 'active' : '' ?>" href="<?= e(admin_url('c=dich-vu')) ?>">
                <i class="fa-solid fa-mug-saucer"></i>
                <span>Dịch vụ</span>
            </a>
            <?php endif; ?>

            <?php if (Auth::hasPermission('CT_DICH_VU')): ?>
            <a class="admin-menu-link <?= ($currentC === 'ct-dich-vu') ? 'active' : '' ?>" href="<?= e(admin_url('c=ct-dich-vu')) ?>">
                <i class="fa-solid fa-list-check"></i>
                <span>Chi tiết DV</span>
            </a>
            <?php endif; ?>

            <?php if (Auth::hasPermission('HOA_DON')): ?>
            <a class="admin-menu-link <?= ($currentC === 'hoa-don') ? 'active' : '' ?>" href="<?= e(admin_url('c=hoa-don')) ?>">
                <i class="fa-solid fa-file-invoice-dollar"></i>
                <span>Hóa đơn</span>
            </a>
            <?php endif; ?>

            <?php if (Auth::hasPermission('KHACH_HANG')): ?>
            <a class="admin-menu-link <?= ($currentC === 'khach-hang') ? 'active' : '' ?>" href="<?= e(admin_url('c=khach-hang')) ?>">
                <i class="fa-solid fa-user-check"></i>
                <span>Khách hàng</span>
            </a>
            <?php endif; ?>

            <?php if (Auth::hasPermission('NHAN_VIEN')): ?>
            <a class="admin-menu-link <?= ($currentC === 'nhan-vien') ? 'active' : '' ?>" href="<?= e(admin_url('c=nhan-vien')) ?>">
                <i class="fa-solid fa-users"></i>
                <span>Nhân viên</span>
            </a>
            <?php endif; ?>

            <?php if (Auth::hasPermission('PHAN_CONG')): ?>
            <a class="admin-menu-link <?= ($currentC === 'phan-cong') ? 'active' : '' ?>" href="<?= e(admin_url('c=phan-cong')) ?>">
                <i class="fa-solid fa-clipboard-user"></i>
                <span>Phân công</span>
            </a>
            <?php endif; ?>

            <?php if (Auth::hasPermission('CHUC_VU')): ?>
            <a class="admin-menu-link <?= ($currentC === 'chuc-vu') ? 'active' : '' ?>" href="<?= e(admin_url('c=chuc-vu')) ?>">
                <i class="fa-solid fa-id-badge"></i>
                <span>Chức vụ</span>
            </a>
            <?php endif; ?>

            <?php if (Auth::hasPermission('DANH_GIA')): ?>
            <a class="admin-menu-link <?= ($currentC === 'danh-gia') ? 'active' : '' ?>" href="<?= e(admin_url('c=danh-gia')) ?>">
                <i class="fa-solid fa-star"></i>
                <span>Đánh giá</span>
            </a>
            <?php endif; ?>

            <?php if (Auth::hasPermission('THONG_KE')): ?>
            <a class="admin-menu-link <?= ($currentC === 'thong-ke') ? 'active' : '' ?>" href="<?= e(admin_url('c=thong-ke')) ?>">
                <i class="fa-solid fa-chart-line"></i>
                <span>Thống kê</span>
            </a>
            <?php endif; ?>
        </nav>
    </aside>

    <header class="admin-topbar shadow-sm">
        <div class="d-flex align-items-center gap-2 text-muted fw-semibold">
            <i class="fa-solid fa-layer-group text-primary"></i>
            <span>Hệ thống quản lý khách sạn</span>
        </div>

        <div class="dropdown ms-auto">
            <button class="admin-account dropdown-toggle border-0 bg-transparent d-flex align-items-center gap-2"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false">
                <span class="admin-avatar">
                    <i class="fa-solid fa-user-tie"></i>
                </span>
                <span class="account-text text-start">
                    <strong><?= e($currentUser['fullName'] ?? 'Admin') ?></strong>
                    <br>
                    <small class="text-muted"><?= e($currentUser['roleName'] ?? 'Nhân viên') ?></small>
                </span>
            </button>

            <ul class="dropdown-menu dropdown-menu-end admin-account-menu shadow">
                <li>
                    <a class="dropdown-item" href="<?= e(client_url()) ?>" target="_blank">
                        <i class="fa-solid fa-globe me-2 text-info"></i>Xem trang khách
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="<?= e(admin_url('c=auth&a=changePassword')) ?>">
                        <i class="fa-solid fa-key me-2 text-warning"></i>Đổi mật khẩu
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="<?= e(admin_url('c=auth&a=logout')) ?>" class="action-form" onsubmit="return confirm('Bạn có chắc muốn đăng xuất?')"><?= csrf_field() ?><button type="submit" class="dropdown-item text-danger fw-bold" >
                        <i class="fa-solid fa-right-from-bracket me-2"></i>Đăng xuất
                    </button></form>
                </li>
            </ul>
        </div>
    </header>
</div>
