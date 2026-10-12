<?php
// views/client/account/register.php
?>
<main class="container my-5 d-flex justify-content-center">
    <div class="card border-0 shadow-sm p-4 p-md-5" style="width: 100%; max-width: 500px; border-radius: 12px;">
        <div class="text-center mb-4">
            <h2 class="fw-bold">ĐĂNG KÝ TÀI KHOẢN</h2>
            <p class="text-muted">Tạo tài khoản để theo dõi lịch sử đặt phòng</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 mb-3"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                <input type="text" name="hoTen" class="form-control" placeholder="Nguyễn Văn A" required value="<?= e($_POST['hoTen'] ?? '') ?>">
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Số điện thoại <span class="text-danger">*</span></label>
                    <input type="tel" name="dienThoai" class="form-control" placeholder="0901234567" required value="<?= e($_POST['dienThoai'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Mật khẩu <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" placeholder="Mật khẩu" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control" placeholder="email@example.com" value="<?= e($_POST['email'] ?? '') ?>">
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">CMND / CCCD</label>
                    <input type="text" name="cmnd" class="form-control" placeholder="Số CCCD" value="<?= e($_POST['cmnd'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Địa chỉ</label>
                    <input type="text" name="diaChi" class="form-control" placeholder="Tỉnh / Thành phố" value="<?= e($_POST['diaChi'] ?? '') ?>">
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold fs-6 mt-3">
                <i class="fa-solid fa-user-plus me-2"></i> Hoàn tất đăng ký
            </button>
        </form>

        <div class="mt-4 pt-3 border-top text-center text-muted small">
            <span>Đã có tài khoản? </span>
            <a href="<?= e(client_url('c=account&a=login')) ?>" class="fw-bold text-primary text-decoration-none">Đăng nhập</a>
        </div>
    </div>
</main>
