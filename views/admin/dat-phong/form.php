<?php
// views/admin/dat-phong/form.php
?>
<div class="container admin-wide my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold mb-0">Tạo đơn đặt phòng mới</h1>
        <a href="<?= e(admin_url('c=dat-phong')) ?>" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <div class="card admin-panel border-0 shadow-sm p-4" style="max-width: 650px;">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger mb-3"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label fw-semibold">Khách hàng <span class="text-danger">*</span></label>
                <select name="maKhachHang" class="form-select" required>
                    <option value="">-- Chọn khách hàng --</option>
                    <?php foreach ($customers as $c): ?>
                        <option value="<?= $c['MaKhachHang'] ?>">
                            <?= e($c['HoTen']) ?> - SĐT: <?= e($c['DienThoai']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Chọn phòng <span class="text-danger">*</span></label>
                <select name="maPhong" class="form-select" required>
                    <option value="">-- Chọn phòng --</option>
                    <?php foreach ($rooms as $r): ?>
                        <option value="<?= $r['MaPhong'] ?>">
                            Phòng <?= $r['MaPhong'] ?> (<?= e($r['TenLoaiPhong']) ?> - <?= format_currency($r['GiaPhong']) ?>/ngày) - [<?= e($r['TrangThai']) ?>]
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Ngày nhận phòng <span class="text-danger">*</span></label>
                    <input type="date" name="ngayNhan" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Ngày trả phòng <span class="text-danger">*</span></label>
                    <input type="date" name="ngayTra" class="form-control" value="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Trạng thái khởi tạo</label>
                <select name="trangThai" class="form-select">
                    <option value="DA_XAC_NHAN">Đã xác nhận (Đặt trước)</option>
                    <option value="DANG_SU_DUNG">Khách nhận phòng ngay (Check-in)</option>
                    <option value="CHO_ADMIN_DUYET">Chờ duyệt</option>
                </select>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fa-solid fa-check me-1"></i> Tạo đơn đặt phòng
                </button>
                <a href="<?= e(admin_url('c=dat-phong')) ?>" class="btn btn-secondary">Hủy bỏ</a>
            </div>
        </form>
    </div>
</div>
