<?php
// views/admin/hoa-don/detail.php
?>
<div class="container my-4" style="max-width: 800px;">
    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-4 print-hidden">
        <a href="<?= e(admin_url('c=hoa-don')) ?>" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
        </a>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-primary">
                <i class="fa-solid fa-print me-1"></i> In hóa đơn
            </button>
            <?php if ($item['TrangThai'] !== 'DA_THANH_TOAN'): ?>
                <form method="POST" action="<?= e(admin_url('c=hoa-don&a=pay&id=' . $item['MaHoaDon'])) ?>" class="action-form" onsubmit="return confirm('Xác nhận khách đã thanh toán số tiền này?')"><?= csrf_field() ?><button type="submit"  class="btn btn-success">
                    <i class="fa-solid fa-check-double me-1"></i> Xác nhận thanh toán
                </button></form>
            <?php endif; ?>
        </div>
    </div>

    <div class="card p-5 border-0 shadow-sm bg-white" id="printableInvoice">
        <!-- HEADER HÓA ĐƠN -->
        <div class="d-flex justify-content-between border-bottom pb-4 mb-4">
            <div>
                <h3 class="fw-bold text-primary mb-1"><i class="fa-solid fa-hotel me-2"></i>KHÁCH SẠN NGHỈ DƯỠNG</h3>
                <p class="text-muted small mb-0">Địa chỉ: 123 Đường Bờ Biển, TP. Đà Nẵng</p>
                <p class="text-muted small mb-0">Hotline: 0905 123 456 - Email: contact@hotel.vn</p>
            </div>
            <div class="text-end">
                <h4 class="fw-bold text-dark mb-1">HÓA ĐƠN THANH TOÁN</h4>
                <p class="text-muted small mb-1">Số HD: <strong>#<?= $item['MaHoaDon'] ?></strong></p>
                <p class="text-muted small mb-0">Ngày lập: <?= format_date($item['NgayThuTien']) ?></p>
            </div>
        </div>

        <!-- THÔNG TIN KHÁCH HÀNG & PHÒNG -->
        <div class="row mb-4">
            <div class="col-6">
                <h6 class="fw-bold text-muted text-uppercase mb-2">Thông tin khách hàng</h6>
                <p class="mb-1 fw-bold"><?= e($item['TenKhachHang']) ?></p>
                <p class="mb-1 small">Điện thoại: <?= e($item['SdtKhachHang']) ?></p>
                <p class="mb-1 small">CMND/CCCD: <?= e($item['CMND'] ?? 'Không có') ?></p>
            </div>
            <div class="col-6 text-end">
                <h6 class="fw-bold text-muted text-uppercase mb-2">Chi tiết lưu trú</h6>
                <p class="mb-1">Phòng: <strong>Phòng <?= e($item['MaPhong']) ?></strong> (<?= e($item['TenLoaiPhong']) ?>)</p>
                <p class="mb-1 small">Ngày nhận: <?= format_date($item['NgayNhan']) ?></p>
                <p class="mb-1 small">Ngày trả: <?= format_date($item['NgayTra']) ?> (<?= $calc['days'] ?? 1 ?> ngày)</p>
            </div>
        </div>

        <!-- BẢNG CHI TIẾT THANH TOÁN -->
        <table class="table table-bordered compact-table mb-4">
            <thead class="table-light">
                <tr>
                    <th>Khoản mục</th>
                    <th class="text-center">Số lượng</th>
                    <th class="text-end">Đơn giá</th>
                    <th class="text-end">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Tiền phòng (<?= e($item['TenLoaiPhong']) ?>)</td>
                    <td class="text-center"><?= $calc['days'] ?? 1 ?> ngày</td>
                    <td class="text-end"><?= format_currency($calc['roomRate'] ?? 0) ?></td>
                    <td class="text-end fw-semibold"><?= format_currency($calc['roomTotal'] ?? 0) ?></td>
                </tr>

                <?php if (!empty($calc['services'])): ?>
                    <?php foreach ($calc['services'] as $sv): ?>
                        <tr>
                            <td>Dịch vụ: <?= e($sv['TenDichVu']) ?></td>
                            <td class="text-center"><?= $sv['SoLuong'] ?></td>
                            <td class="text-end"><?= format_currency($sv['GiaDichVu']) ?></td>
                            <td class="text-end fw-semibold"><?= format_currency($sv['TongTienDichVu']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-end fs-5">TỔNG CỘNG:</th>
                    <th class="text-end text-danger fs-4"><?= format_currency($item['SoTienThu']) ?></th>
                </tr>
            </tfoot>
        </table>

        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
            <div>
                <span>Trạng thái: </span>
                <?php if ($item['TrangThai'] === 'DA_THANH_TOAN'): ?>
                    <span class="badge bg-success fs-6"><i class="fa-solid fa-check me-1"></i>ĐÃ THANH TOÁN</span>
                <?php else: ?>
                    <span class="badge bg-warning text-dark fs-6"><i class="fa-solid fa-hourglass-half me-1"></i>CHƯA THANH TOÁN</span>
                <?php endif; ?>
            </div>
            <div class="text-end">
                <small class="text-muted d-block">Thu ngân</small>
                <strong><?= e($item['TenNhanVien'] ?? 'Quản trị viên') ?></strong>
            </div>
        </div>
    </div>
</div>
