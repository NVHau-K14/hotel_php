<?php
// views/admin/danh-gia/form.php
?>
<div class="container admin-wide my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-1"><?= $isEdit ? "Sửa đánh giá #{$item['ID']}" : "Thêm đánh giá mới" ?></h1>
            <p class="text-muted mb-0">Cập nhật thông tin đánh giá chất lượng phòng</p>
        </div>
        <a href="<?= e(admin_url('c=danh-gia')) ?>" class="btn btn-outline-secondary">
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
                <label class="form-label fw-semibold">Phòng được đánh giá <span class="text-danger">*</span></label>
                <select name="maPhong" class="form-select" required>
                    <option value="">-- Chọn phòng --</option>
                    <?php foreach ($phongs as $p): ?>
                        <option value="<?= e($p['MaPhong']) ?>" <?= (isset($item['MaPhong']) && $item['MaPhong'] == $p['MaPhong']) ? 'selected' : '' ?>>
                            Phòng <?= e($p['MaPhong']) ?> (<?= e($p['TenLoaiPhong'] ?? 'Phòng') ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Số sao đánh giá <span class="text-danger">*</span></label>
                <select name="soSao" class="form-select" required>
                    <option value="5" <?= (($item['SoSao'] ?? 5) == 5) ? 'selected' : '' ?>>⭐⭐⭐⭐⭐ 5 sao - Rất tốt</option>
                    <option value="4" <?= (($item['SoSao'] ?? 5) == 4) ? 'selected' : '' ?>>⭐⭐⭐⭐ 4 sao - Tốt</option>
                    <option value="3" <?= (($item['SoSao'] ?? 5) == 3) ? 'selected' : '' ?>>⭐⭐⭐ 3 sao - Bình thường</option>
                    <option value="2" <?= (($item['SoSao'] ?? 5) == 2) ? 'selected' : '' ?>>⭐⭐ 2 sao - Chưa tốt</option>
                    <option value="1" <?= (($item['SoSao'] ?? 5) == 1) ? 'selected' : '' ?>>⭐ 1 sao - Kém</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Ngày đánh giá</label>
                <input type="date" name="ngayDanhGia" class="form-control" value="<?= e($item['NgayDanhGia'] ?? date('Y-m-d')) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Nội dung nhận xét <span class="text-danger">*</span></label>
                <textarea name="noiDung" rows="4" class="form-control" placeholder="Nhập cảm nhận, nhận xét về phòng..." required><?= e($item['NoiDung'] ?? '') ?></textarea>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Lưu đánh giá
                </button>
                <a href="<?= e(admin_url('c=danh-gia')) ?>" class="btn btn-secondary">Hủy bỏ</a>
            </div>
        </form>
    </div>
</div>
