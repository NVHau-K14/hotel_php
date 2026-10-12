<?php
// views/admin/hoa-don/list.php
?>
<div class="container admin-wide my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold mb-0">Quản lý hóa đơn</h1>
    </div>

    <div class="card admin-panel border-0 shadow-sm p-4">
        <div class="table-responsive">
            <table class="table admin-table align-middle">
                <thead>
                    <tr>
                        <th>Mã HD</th>
                        <th>Khách hàng</th>
                        <th>Phòng</th>
                        <th>Thời gian</th>
                        <th>Tổng tiền thu</th>
                        <th>Trạng thái</th>
                        <th>Thu ngân</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($items)): ?>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><span class="id-badge">#<?= $item['MaHoaDon'] ?></span></td>
                                <td class="fw-semibold">
                                    <?= e($item['TenKhachHang']) ?>
                                    <div class="small text-muted"><?= e($item['SdtKhachHang']) ?></div>
                                </td>
                                <td>
                                    <strong>Phòng <?= e($item['MaPhong']) ?></strong>
                                    <div class="small text-muted"><?= e($item['TenLoaiPhong']) ?></div>
                                </td>
                                <td>
                                    <small>Ngày lập: <?= format_date($item['NgayThuTien']) ?></small>
                                </td>
                                <td class="text-danger fw-bold fs-6">
                                    <?= format_currency($item['SoTienThu']) ?>
                                </td>
                                <td>
                                    <?php if ($item['TrangThai'] === 'DA_THANH_TOAN'): ?>
                                        <span class="badge bg-success">Đã thanh toán</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Chưa thanh toán</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small class="text-muted"><?= e($item['TenNhanVien'] ?? 'Hệ thống') ?></small>
                                </td>
                                <td class="text-end">
                                    <a href="<?= e(admin_url('c=hoa-don&a=detail&id=' . $item['MaHoaDon'])) ?>" class="btn btn-sm btn-outline-primary">
                                        Xem hóa đơn
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Chưa có hóa đơn nào trên hệ thống.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
