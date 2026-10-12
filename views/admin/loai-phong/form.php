<?php
// views/admin/loai-phong/form.php
?>
<div class="container admin-wide my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold mb-0"><?= $isEdit ? "Sửa loại phòng #{$item['MaLoaiPhong']}" : "Thêm loại phòng mới" ?></h1>
        <a href="<?= e(admin_url('c=loai-phong')) ?>" class="btn btn-outline-secondary">
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
                <label class="form-label fw-semibold">Tên loại phòng <span class="text-danger">*</span></label>
                <input type="text" name="tenLoaiPhong" class="form-control" placeholder="Ví dụ: Bình Dân, Vip, Thương Gia..." 
                       value="<?= e($item['TenLoaiPhong'] ?? '') ?>" required autofocus>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Giá phòng theo ngày (VNĐ) <span class="text-danger">*</span></label>
                <input type="number" name="giaPhong" class="form-control" placeholder="Ví dụ: 300000" 
                       value="<?= e($item['GiaPhong'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Mô tả tiện ích, trang thiết bị</label>
                <textarea name="moTa" class="form-control" rows="4" placeholder="Một giường đôi. Máy lạnh. View ngắm biển..."><?= e($item['MoTa'] ?? '') ?></textarea>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Lưu thông tin
                </button>
                <a href="<?= e(admin_url('c=loai-phong')) ?>" class="btn btn-secondary">Hủy bỏ</a>
            </div>
        </form>
    </div>
</div>
