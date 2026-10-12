<?php
// views/admin/phong/form.php
?>
<div class="container admin-wide my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold mb-0"><?= $isEdit ? "Sửa phòng #{$item['MaPhong']}" : "Thêm phòng mới" ?></h1>
        <a href="<?= e(admin_url('c=phong')) ?>" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại danh sách
        </a>
    </div>

    <div class="card admin-panel border-0 shadow-sm p-4" style="max-width: 650px;">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger mb-3"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label fw-semibold">Mã phòng <span class="text-danger">*</span></label>
                <input type="number" name="maPhong" class="form-control" placeholder="Ví dụ: 101, 102..." 
                       value="<?= e($item['MaPhong'] ?? '') ?>" <?= $isEdit ? 'readonly' : 'required' ?>>
                <?php if ($isEdit): ?>
                    <small class="text-muted">Mã số phòng không thể thay đổi sau khi tạo.</small>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Loại phòng <span class="text-danger">*</span></label>
                <select name="maLoaiPhong" class="form-select" required>
                    <option value="">-- Chọn loại phòng --</option>
                    <?php foreach ($loaiPhongs as $lp): ?>
                        <option value="<?= $lp['MaLoaiPhong'] ?>" <?= (($item['MaLoaiPhong'] ?? '') == $lp['MaLoaiPhong']) ? 'selected' : '' ?>>
                            <?= e($lp['TenLoaiPhong']) ?> (<?= format_currency($lp['GiaPhong']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Trạng thái phòng</label>
                <select name="trangThai" class="form-select">
                    <?php
                        $currentSt = $item['TrangThai'] ?? 'Trống';
                        $statuses = ['Trống', 'Đang thuê', 'Đang sử dụng', 'Đã đặt', 'Bảo trì'];
                        foreach ($statuses as $st):
                    ?>
                        <option value="<?= $st ?>" <?= ($currentSt === $st) ? 'selected' : '' ?>><?= $st ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Đường dẫn ảnh đại diện (URL hoặc tải lên)</label>
                <input type="text" name="hinhAnh" class="form-control mb-2" placeholder="/Images/ChiTietPhong/Phong101/01.jpg" 
                       value="<?= e($item['HinhAnhDaiDien'] ?? '') ?>">
                <input type="file" name="fileAnh" class="form-control" accept="image/*">
                <?php if (!empty($item['HinhAnhDaiDien'])): ?>
                    <div class="mt-2">
                        <small class="text-muted d-block mb-1">Ảnh hiện tại:</small>
                        <img src="<?= e(asset($item['HinhAnhDaiDien'])) ?>" alt="Preview" style="max-height: 100px; border-radius: 6px;">
                    </div>
                <?php endif; ?>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Lưu thông tin
                </button>
                <a href="<?= e(admin_url('c=phong')) ?>" class="btn btn-secondary">Hủy bỏ</a>
            </div>
        </form>
    </div>
</div>
