<?php
// views/admin/nhan-vien/form.php
?>
<div class="container admin-wide my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold mb-0"><?= $isEdit ? "Sửa nhân viên #{$item['MaNhanVien']}" : "Thêm nhân viên mới" ?></h1>
        <a href="<?= e(admin_url('c=nhan-vien')) ?>" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <div class="card admin-panel border-0 shadow-sm p-4" style="max-width: 650px;">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger mb-3"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <?= csrf_field() ?>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tên đăng nhập <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control" value="<?= e($item['Username'] ?? '') ?>" 
                           <?= $isEdit ? 'readonly' : 'required' ?> placeholder="vd: nguyenvanb">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Mật khẩu <?= $isEdit ? '(để trống nếu không đổi)' : '<span class="text-danger">*</span>' ?></label>
                    <input type="password" name="password" class="form-control" placeholder="Mật khẩu" <?= $isEdit ? '' : 'required' ?>>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Họ và tên nhân viên <span class="text-danger">*</span></label>
                <input type="text" name="tenNhanVien" class="form-control" value="<?= e($item['TenNhanVien'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Chức vụ <span class="text-danger">*</span></label>
                <select name="maChucVu" class="form-select" required>
                    <option value="">-- Chọn chức vụ --</option>
                    <?php foreach ($chucVus as $cv): ?>
                        <option value="<?= $cv['MaChucVu'] ?>" <?= (($item['MaChucVu'] ?? '') == $cv['MaChucVu']) ? 'selected' : '' ?>>
                            <?= e($cv['TenChucVu']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Ngày sinh</label>
                    <input type="date" name="ngaySinh" class="form-control" value="<?= e($item['NgaySinh'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">CMND/CCCD</label>
                    <input type="text" name="cmnd" class="form-control" value="<?= e($item['CMND'] ?? '') ?>">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Ngày vào làm</label>
                <input type="date" name="ngayVaoLam" class="form-control" value="<?= e($item['NgayVaoLam'] ?? date('Y-m-d')) ?>">
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Lưu thông tin
                </button>
                <a href="<?= e(admin_url('c=nhan-vien')) ?>" class="btn btn-secondary">Hủy bỏ</a>
            </div>
        </form>
    </div>
</div>
