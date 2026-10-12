<?php
// views/admin/phan-cong/form.php
?>
<div class="container admin-wide my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-1"><?= $isEdit ? "Sửa phân công #{$item['MaPhanCong']}" : "Thêm phân công mới" ?></h1>
            <p class="text-muted mb-0">Chỉ định nhiệm vụ cho nhân sự khách sạn</p>
        </div>
        <a href="<?= e(admin_url('c=phan-cong')) ?>" class="btn btn-outline-secondary">
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
                <label class="form-label fw-semibold">Ngày phân công <span class="text-danger">*</span></label>
                <input type="date" name="ngayPhanCong" class="form-control" 
                       value="<?= e($item['NgayPhanCong'] ?? date('Y-m-d')) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Loại công việc <span class="text-danger">*</span></label>
                <input type="text" name="loaiCongViec" class="form-control" 
                       placeholder="Ví dụ: Dọn phòng 101, Trực sảnh ca tối, Bảo trì máy giặt..." 
                       value="<?= e($item['LoaiCongViec'] ?? '') ?>" required autofocus>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Nhân viên thực hiện</label>
                <select name="maNhanVien" class="form-select">
                    <option value="">-- Chọn nhân viên (tùy chọn) --</option>
                    <?php foreach ($nhanViens as $nv): ?>
                        <option value="<?= e($nv['MaNhanVien']) ?>" <?= (isset($item['MaNhanVien']) && $item['MaNhanVien'] == $nv['MaNhanVien']) ? 'selected' : '' ?>>
                            <?= e($nv['TenNhanVien']) ?> (<?= e($nv['TenChucVu'] ?? 'Nhân viên') ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Lưu phân công
                </button>
                <a href="<?= e(admin_url('c=phan-cong')) ?>" class="btn btn-secondary">Hủy bỏ</a>
            </div>
        </form>
    </div>
</div>
