<?php
// views/client/layouts/header.php
require_once __DIR__ . '/../../../config/auth.php';
$clientUser = Auth::currentClient();
$currentC = $_GET['c'] ?? 'home';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Khách sạn Nghỉ dưỡng Vip') ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>?v=<?= filemtime(__DIR__ . '/../../../css/app.css') ?>">
    <style>
        .client-nav { display: flex; align-items: center; gap: 1rem; padding: 1rem 2rem; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .client-brand { font-weight: bold; font-size: 1.25rem; color: #1e40af; text-decoration: none; }
        .spacer { flex: 1; }
        .client-nav a { text-decoration: none; color: #4b5563; font-weight: 500; }
        .client-nav a:hover, .client-nav a.active { color: #1e40af; }
        .client-nav-cta { background: #1e40af; color: #fff !important; padding: 0.4rem 1rem; border-radius: 6px; }
        .flash-msg { margin: 1rem 2rem; padding: 0.75rem 1.25rem; border-radius: 6px; font-weight: 500; }
        .flash-success { background: #def7ec; color: #03543f; border: 1px solid #bcf0da; }
        .flash-danger { background: #fde8e8; color: #9b1c1c; border: 1px solid #fbd5d5; }
        .flash-warning { background: #fef08a; color: #713f12; border: 1px solid #fde047; }
    </style>
</head>
<body class="client-page">

<nav class="client-nav">
    <a class="client-brand" href="<?= e(client_url()) ?>">
        <i class="fa-solid fa-hotel me-1"></i> Khách sạn Vip
    </a>
    <a href="<?= e(client_url()) ?>" class="<?= ($currentC === 'home' && empty($_GET['a'])) ? 'active' : '' ?>">Trang chủ</a>
    <a href="<?= e(client_url('c=room')) ?>" class="<?= ($currentC === 'room') ? 'active' : '' ?>">Phòng</a>
    <a href="<?= e(client_url('c=service')) ?>" class="<?= ($currentC === 'service') ? 'active' : '' ?>">Dịch vụ</a>
    <a href="<?= e(client_url('c=review')) ?>" class="<?= ($currentC === 'review' || $currentC === 'reviews') ? 'active' : '' ?>">Đánh giá</a>
    <a href="<?= e(client_url('c=contact')) ?>" class="<?= ($currentC === 'contact' || ($currentC === 'home' && ($_GET['a'] ?? '') === 'contact')) ? 'active' : '' ?>">Liên hệ</a>

    <span class="spacer"></span>

    <?php if ($clientUser): ?>
        <span class="nav-greeting text-muted">
            <i class="fa-solid fa-user-circle me-1 text-primary"></i> Xin chào, <strong><?= e($clientUser['fullName']) ?></strong>
        </span>
        <a href="<?= e(client_url('c=account&a=profile')) ?>" class="<?= ($currentC === 'account' && ($_GET['a'] ?? '') === 'profile') ? 'active' : '' ?>">Tài khoản</a>
        <a href="<?= e(client_url('c=account&a=history')) ?>" class="<?= ($currentC === 'account' && ($_GET['a'] ?? '') === 'history') ? 'active' : '' ?>">Lịch sử đặt</a>
        <form method="POST" action="<?= e(client_url('c=account&a=logout')) ?>" class="action-form" onsubmit="return confirm('Bạn có chắc muốn đăng xuất?')"><?= csrf_field() ?><button type="submit"  class="action-link">Đăng xuất</button></form>
    <?php else: ?>
        <a href="<?= e(client_url('c=account&a=login')) ?>">Đăng nhập</a>
        <a class="client-nav-cta" href="<?= e(client_url('c=account&a=register')) ?>">Đăng ký</a>
    <?php endif; ?>

    <a href="<?= e(admin_url()) ?>" style="font-size: 0.85rem; border-left: 1px solid #ddd; padding-left: 1rem;" title="Trang quản trị dành cho nhân viên">
        <i class="fa-solid fa-user-shield me-1"></i> Admin
    </a>
</nav>

<?php if (!empty($flash)): ?>
    <div class="flash-msg flash-<?= e($flash['type'] === 'error' ? 'danger' : $flash['type']) ?>">
        <?= e($flash['message']) ?>
    </div>
<?php endif; ?>
