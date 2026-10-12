<?php
// views/admin/dat-phong/detail.php
$st = $item['TrangThai'];
?>
<div class="container admin-wide my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-0">Đơn đặt phòng #<?= $item['MaCTDatPhong'] ?></h1>
            <p class="text-muted mb-0">Ngày tạo đơn: <?= format_date($item['NgayThucHien']) ?></p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= e(admin_url('c=dat-phong')) ?>" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Danh sách
            </a>

            <?php if ($st === 'CHO_ADMIN_DUYET'): ?>
                <form method="POST" action="<?= e(admin_url('c=dat-phong&a=approve&id=' . $item['MaCTDatPhong'])) ?>" class="action-form" onsubmit="return confirm('Duyệt đơn đặt phòng này?')"><?= csrf_field() ?><button type="submit"  class="btn btn-success">
                    <i class="fa-solid fa-check me-1"></i> Duyệt đơn
                </button></form>
            <?php endif; ?>

            <?php if ($st === 'DA_XAC_NHAN'): ?>
                <form method="POST" action="<?= e(admin_url('c=dat-phong&a=checkin&id=' . $item['MaCTDatPhong'])) ?>" class="action-form" onsubmit="return confirm('Khách nhận phòng (Check-in)?')"><?= csrf_field() ?><button type="submit"  class="btn btn-primary">
                    <i class="fa-solid fa-door-open me-1"></i> Khách nhận phòng (Check-in)
                </button></form>
            <?php endif; ?>

            <?php if ($st === 'DANG_SU_DUNG'): ?>
                <form method="POST" action="<?= e(admin_url('c=dat-phong&a=checkout&id=' . $item['MaCTDatPhong'])) ?>" class="action-form" onsubmit="return confirm('Trả phòng và lập hóa đơn thanh toán?')"><?= csrf_field() ?><button type="submit"  class="btn btn-warning text-dark fw-bold">
                    <i class="fa-solid fa-receipt me-1"></i> Trả phòng & Lập hóa đơn (Check-out)
                </button></form>
            <?php endif; ?>

            <?php if (in_array($st, ['CHO_ADMIN_DUYET', 'DA_XAC_NHAN'], true)): ?>
                <form method="POST" action="<?= e(admin_url('c=dat-phong&a=cancel&id=' . $item['MaCTDatPhong'])) ?>" class="action-form" onsubmit="return confirm('Hủy đơn đặt phòng này?')"><?= csrf_field() ?><button type="submit"  class="btn btn-outline-danger">
                    <i class="fa-solid fa-ban me-1"></i> Hủy đơn
                </button></form>
            <?php endif; ?>
        </div>
    </div>

    <div class="row g-4">
        <!-- CỘT TRÁI: THÔNG TIN KHÁCH & PHÒNG -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h5 class="fw-bold text-primary mb-3"><i class="fa-solid fa-user me-2"></i>Thông tin khách hàng</h5>
                <p class="mb-1"><strong>Họ và tên:</strong> <?= e($item['TenKhachHang']) ?></p>
                <p class="mb-1"><strong>Số điện thoại:</strong> <?= e($item['SdtKhachHang']) ?></p>
                <p class="mb-1"><strong>Email:</strong> <?= e($item['EmailKhachHang'] ?? 'Chưa có') ?></p>
                <p class="mb-1"><strong>CMND/CCCD:</strong> <?= e($item['CMND'] ?? 'Chưa có') ?></p>
                <p class="mb-0"><strong>Địa chỉ:</strong> <?= e($item['DiaChi'] ?? 'Chưa có') ?></p>
            </div>

            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold text-primary mb-3"><i class="fa-solid fa-key me-2"></i>Thông tin phòng & Lưu trú</h5>
                <p class="mb-1"><strong>Phòng số:</strong> <span class="badge bg-primary fs-6"><?= e($item['MaPhong']) ?></span></p>
                <p class="mb-1"><strong>Loại phòng:</strong> <?= e($item['TenLoaiPhong']) ?></p>
                <p class="mb-1"><strong>Đơn giá:</strong> <?= format_currency($item['GiaPhong']) ?> / ngày</p>
                <hr>
                <p class="mb-1"><strong>Ngày nhận:</strong> <?= format_date($item['NgayNhan']) ?></p>
                <p class="mb-1"><strong>Ngày trả:</strong> <?= format_date($item['NgayTra']) ?></p>
                <p class="mb-1"><strong>Số ngày lưu trú:</strong> <span class="badge bg-secondary"><?= $calc['days'] ?? 1 ?> ngày</span></p>
                <p class="mb-0"><strong>Tiền phòng:</strong> <span class="text-primary fw-bold"><?= format_currency($calc['roomTotal'] ?? 0) ?></span></p>
            </div>
        </div>

        <!-- CỘT PHẢI: DỊCH VỤ SỬ DỤNG & TỔNG TIỀN -->
        <div class="col-md-7">
            <div class="card border-0 shadow-sm p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-primary mb-0"><i class="fa-solid fa-bell-concierge me-2"></i>Dịch vụ đã sử dụng</h5>
                    <?php if ($st === 'DANG_SU_DUNG' || $st === 'DA_XAC_NHAN'): ?>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#addServiceBox">
                            <i class="fa-solid fa-plus me-1"></i> Gọi thêm dịch vụ
                        </button>
                    <?php endif; ?>
                </div>

                <!-- FORM GỌI THÊM DỊCH VỤ -->
                <div class="collapse mb-3" id="addServiceBox">
                    <div class="card card-body bg-light border-0">
                        <form method="POST" action="<?= e(admin_url('c=dat-phong&a=addService')) ?>" class="row g-2 align-items-center">
            <?= csrf_field() ?>
                            <input type="hidden" name="bookingId" value="<?= $item['MaCTDatPhong'] ?>">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Chọn dịch vụ</label>
                                <select name="serviceId" class="form-select form-select-sm" required>
                                    <option value="">-- Dịch vụ --</option>
                                    <?php foreach ($allServices as $s): ?>
                                        <option value="<?= $s['MaDichVu'] ?>">
                                            <?= e($s['TenDichVu']) ?> (<?= format_currency($s['GiaDichVu']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">Số lượng</label>
                                <input type="number" name="quantity" class="form-control form-control-sm" value="1" min="1" required>
                            </div>
                            <div class="col-md-3 mt-auto">
                                <button type="submit" class="btn btn-sm btn-success w-100">Thêm</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- BẢNG DỊCH VỤ -->
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tên dịch vụ</th>
                                <th>Đơn giá</th>
                                <th>Số lượng</th>
                                <th>Thành tiền</th>
                                <?php if (in_array($st, ['DA_XAC_NHAN', 'DANG_SU_DUNG'], true)): ?>
                                    <th class="text-end">Xóa</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($calc['services'])): ?>
                                <?php foreach ($calc['services'] as $sv): ?>
                                    <tr>
                                        <td><?= e($sv['TenDichVu']) ?></td>
                                        <td><?= format_currency($sv['GiaDichVu']) ?></td>
                                        <td><span class="badge bg-secondary">x<?= $sv['SoLuong'] ?></span></td>
                                        <td class="fw-semibold"><?= format_currency($sv['TongTienDichVu']) ?></td>
                                        <?php if (in_array($st, ['DA_XAC_NHAN', 'DANG_SU_DUNG'], true)): ?>
                                            <td class="text-end">
                                                <form method="POST" action="<?= e(admin_url("c=dat-phong&a=removeService&bookingId={$item['MaCTDatPhong']}&serviceId={$sv['MaDichVu']}")) ?>" class="action-form" onsubmit="return confirm('Xóa dịch vụ này?')"><?= csrf_field() ?><button type="submit"  
                                                   class="btn btn-sm btn-outline-danger py-0 px-1">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button></form>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-muted text-center py-3">Chưa sử dụng dịch vụ nào.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TỔNG HỢP THANH TOÁN -->
            <div class="card border-0 shadow-sm p-4 bg-light">
                <h5 class="fw-bold mb-3">Tóm tắt thanh toán</h5>
                <div class="d-flex justify-content-between mb-2">
                    <span>Tiền phòng (<?= $calc['days'] ?? 1 ?> ngày):</span>
                    <span class="fw-semibold"><?= format_currency($calc['roomTotal'] ?? 0) ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Tiền dịch vụ phát sinh:</span>
                    <span class="fw-semibold"><?= format_currency($calc['serviceTotal'] ?? 0) ?></span>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-danger mb-0">TỔNG CỘNG:</h5>
                    <h3 class="fw-bold text-danger mb-0"><?= format_currency($calc['grandTotal'] ?? 0) ?></h3>
                </div>

                <?php if (!empty($invoice)): ?>
                    <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge <?= ($invoice['TrangThai'] === 'DA_THANH_TOAN') ? 'bg-success' : 'bg-warning text-dark' ?>">
                                <?= ($invoice['TrangThai'] === 'DA_THANH_TOAN') ? 'ĐÃ THANH TOÁN' : 'CHƯA THANH TOÁN' ?>
                            </span>
                        </div>
                        <a href="<?= e(admin_url('c=hoa-don&a=detail&id=' . $invoice['MaHoaDon'])) ?>" class="btn btn-sm btn-primary">
                            <i class="fa-solid fa-receipt me-1"></i> Xem hóa đơn #<?= $invoice['MaHoaDon'] ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
