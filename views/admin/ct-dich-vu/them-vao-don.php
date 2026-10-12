<?php
// views/admin/ct-dich-vu/them-vao-don.php
?>
<div class="container admin-wide my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-1">Thêm dịch vụ vào đơn #<?= e($datPhong['MaCTDatPhong']) ?></h1>
            <p class="text-muted mb-0">Phòng <?= e($datPhong['MaPhong']) ?> - Khách: <?= e($datPhong['TenKhachHang'] ?? 'Khách') ?></p>
        </div>
        <a href="<?= e(admin_url('c=dat-phong&a=detail&id=' . $datPhong['MaCTDatPhong'])) ?>" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại chi tiết đơn
        </a>
    </div>

    <div class="card admin-panel border-0 shadow-sm p-4" style="max-width: 550px;">
        <form method="POST" action="<?= e(admin_url('c=ct-dich-vu&a=saveToBooking')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="maCTDatPhong" value="<?= e($datPhong['MaCTDatPhong']) ?>">

            <div class="mb-3">
                <label class="form-label fw-semibold">Chọn dịch vụ <span class="text-danger">*</span></label>
                <select name="maDichVu" class="form-select" required autofocus>
                    <option value="">-- Chọn dịch vụ cần thêm --</option>
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
                    <i class="fa-solid fa-plus me-1"></i> Thêm vào đơn
                </button>
                <a href="<?= e(admin_url('c=dat-phong&a=detail&id=' . $datPhong['MaCTDatPhong'])) ?>" class="btn btn-secondary">
                    Hủy bỏ
                </a>
            </div>
        </form>
    </div>
</div>
