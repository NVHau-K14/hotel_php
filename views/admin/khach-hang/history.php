<?php
// views/admin/khach-hang/history.php
?>
<div class="container admin-wide my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-0">Lịch sử đặt phòng: <?= e($customer['HoTen']) ?></h1>
            <p class="text-muted mb-0">Email: <?= e($customer['Email']) ?> | SĐT: <?= e($customer['DienThoai']) ?></p>
        </div>
        <a href="<?= e(admin_url('c=khach-hang')) ?>" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <div class="card admin-panel border-0 shadow-sm p-4">
        <table class="table admin-table align-middle">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Phòng</th>
                    <th>Ngày nhận</th>
                    <th>Ngày trả</th>
                    <th>Trạng thái</th>
                    <th class="text-end">Chi tiết</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($bookings)): ?>
                    <?php foreach ($bookings as $b): ?>
                        <tr>
                            <td><span class="id-badge">#<?= $b['MaCTDatPhong'] ?></span></td>
                            <td>Phòng <?= e($b['MaPhong']) ?> (<?= e($b['TenLoaiPhong']) ?>)</td>
                            <td><?= format_date($b['NgayNhan']) ?></td>
                            <td><?= format_date($b['NgayTra']) ?></td>
                            <td><span class="badge bg-secondary"><?= e($b['TrangThai']) ?></span></td>
                            <td class="text-end">
                                <a href="<?= e(admin_url('c=dat-phong&a=detail&id=' . $b['MaCTDatPhong'])) ?>" class="btn btn-sm btn-outline-primary">
                                    Xem đơn
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Khách hàng chưa có đơn đặt phòng nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
