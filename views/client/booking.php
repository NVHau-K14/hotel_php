<?php
// views/client/booking.php
?>
<main class="container my-4" style="max-width: 800px;">
    <div class="mb-3">
        <a href="<?= e(client_url('c=room&a=detail&id=' . $room['MaPhong'])) ?>" class="text-decoration-none text-muted">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại chi tiết phòng
        </a>
    </div>

    <div class="card border-0 shadow-sm p-4 p-md-5" style="border-radius: 12px;">
        <h2 class="fw-bold mb-1 text-center">Yêu cầu đặt phòng</h2>
        <p class="text-muted text-center mb-4">Điền thông tin của bạn để khách sạn giữ phòng và liên hệ xác nhận</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger mb-4"><?= e($error) ?></div>
        <?php endif; ?>

        <!-- TÓM TẮT PHÒNG -->
        <div class="card bg-light border-0 p-3 mb-4">
            <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-1">Phòng số <?= e($room['MaPhong']) ?> - <?= e($room['TenLoaiPhong']) ?></h5>
                    <span class="text-muted">Đơn giá: <strong><?= format_currency($room['GiaPhong']) ?></strong> / đêm</span>
                </div>
                <span class="badge bg-primary fs-6">Phòng <?= e($room['MaPhong']) ?></span>
            </div>
        </div>

        <form method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="maPhong" value="<?= $room['MaPhong'] ?>">

            <h5 class="fw-bold text-primary mb-3">1. Thời gian lưu trú</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Ngày nhận phòng <span class="text-danger">*</span></label>
                    <input type="date" name="ngayNhan" class="form-control" value="<?= e($ngayNhan) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Ngày trả phòng <span class="text-danger">*</span></label>
                    <input type="date" name="ngayTra" class="form-control" value="<?= e($ngayTra) ?>" required>
                </div>
            </div>

            <h5 class="fw-bold text-primary mb-3">2. Thông tin khách đặt</h5>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                    <input type="text" name="hoTen" class="form-control" placeholder="Nguyễn Văn A" 
                           value="<?= e($_POST['hoTen'] ?? ($clientUser['fullName'] ?? '')) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Số điện thoại <span class="text-danger">*</span></label>
                    <input type="tel" name="dienThoai" class="form-control" placeholder="0901234567" 
                           value="<?= e($_POST['dienThoai'] ?? ($clientUser['phone'] ?? '')) ?>" required>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="email@example.com" 
                           value="<?= e($_POST['email'] ?? ($clientUser['email'] ?? '')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">CMND / Căn cước công dân</label>
                    <input type="text" name="cmnd" class="form-control" placeholder="Số CCCD" 
                           value="<?= e($_POST['cmnd'] ?? ($clientUser['cmnd'] ?? '')) ?>">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Địa chỉ</label>
                <input type="text" name="diaChi" class="form-control" placeholder="Thành phố / Tỉnh" 
                       value="<?= e($_POST['diaChi'] ?? ($clientUser['address'] ?? '')) ?>">
            </div>

            <button type="submit" class="btn btn-primary booking-submit w-100 py-3 fs-5 fw-bold shadow-sm">
                <i class="fa-solid fa-check-circle" aria-hidden="true"></i>
                <span>Xác nhận gửi yêu cầu đặt phòng</span>
            </button>
        </form>
    </div>
</main>
