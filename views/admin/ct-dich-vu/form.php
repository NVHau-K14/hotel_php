<?php
// views/admin/ct-dich-vu/form.php
?>
<div class="container admin-wide my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-1"><?= e($title ?? 'Thêm chi tiết dịch vụ') ?></h1>
            <p class="text-muted mb-0">Thêm bản ghi sử dụng dịch vụ cho đơn đặt phòng</p>
        </div>
        <a href="<?= e(admin_url('c=ct-dich-vu')) ?>" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <div class="card admin-panel border-0 shadow-sm p-4" style="max-width: 600px;">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger mb-3"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label fw-semibold">Đơn đặt phòng <span class="text-danger">*</span></label>
                <select name="maCTDatPhong" class="form-select" required>
                    <option value="">-- Chọn đơn đặt phòng --</option>
                    <?php foreach ($datPhongs as $dp): ?>
                        <option value="<?= e($dp['MaCTDatPhong']) ?>" <?= (isset($_GET['maCTDatPhong']) && $_GET['maCTDatPhong'] == $dp['MaCTDatPhong']) ? 'selected' : '' ?>>
                            Đơn #<?= e($dp['MaCTDatPhong']) ?> - Phòng <?= e($dp['MaPhong']) ?> (Khách: <?= e($dp['TenKhachHang'] ?? $dp['HoTen'] ?? 'Khách') ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Dịch vụ <span class="text-danger">*</span></label>
                <select name="maDichVu" class="form-select" required>
                    <option value="">-- Chọn dịch vụ --</option>
                    <?php foreach ($dichVus as $dv): ?>
                        <option value="<?= e($dv['MaDichVu']) ?>">
                            <?= e($dv['TenDichVu']) ?> (<?= format_currency($dv['GiaDichVu']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Số lượng <span class="text-danger">*</span></label>
                <input type="number" name="soLuong" class="form-control" value="1" min="1" required>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Lưu chi tiết
                </button>
                <a href="<?= e(admin_url('c=ct-dich-vu')) ?>" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>
