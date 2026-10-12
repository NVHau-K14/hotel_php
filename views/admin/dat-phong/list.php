<?php
// views/admin/dat-phong/list.php
?>
<div class="container admin-wide my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold mb-0">Quản lý đặt phòng</h1>
        <a href="<?= e(admin_url('c=dat-phong&a=add')) ?>" class="btn btn-success">
            <i class="fa-solid fa-plus me-1"></i> Tạo đơn đặt phòng
        </a>
    </div>

    <!-- FILTER TABS -->
    <div class="card admin-panel border-0 shadow-sm p-4 mb-4">
        <div class="d-flex flex-wrap gap-2 mb-3">
            <a href="<?= e(admin_url('c=dat-phong')) ?>" class="btn btn-sm <?= empty($trangThai) ? 'btn-primary' : 'btn-outline-secondary' ?>">
                Tất cả (<?= $counts['tongDon'] ?? 0 ?>)
            </a>
            <a href="<?= e(admin_url('c=dat-phong&trangThai=CHO_ADMIN_DUYET')) ?>" class="btn btn-sm <?= ($trangThai === 'CHO_ADMIN_DUYET') ? 'btn-warning' : 'btn-outline-warning' ?>">
                Chờ duyệt (<?= $counts['choDuyet'] ?? 0 ?>)
            </a>
            <a href="<?= e(admin_url('c=dat-phong&trangThai=DA_XAC_NHAN')) ?>" class="btn btn-sm <?= ($trangThai === 'DA_XAC_NHAN') ? 'btn-info text-white' : 'btn-outline-info' ?>">
                Đã xác nhận (<?= $counts['daXacNhan'] ?? 0 ?>)
            </a>
            <a href="<?= e(admin_url('c=dat-phong&trangThai=DANG_SU_DUNG')) ?>" class="btn btn-sm <?= ($trangThai === 'DANG_SU_DUNG') ? 'btn-primary' : 'btn-outline-primary' ?>">
                Đang ở (<?= $counts['dangSuDung'] ?? 0 ?>)
            </a>
            <a href="<?= e(admin_url('c=dat-phong&trangThai=DA_HOAN_THANH')) ?>" class="btn btn-sm <?= ($trangThai === 'DA_HOAN_THANH') ? 'btn-success' : 'btn-outline-success' ?>">
                Hoàn thành (<?= $counts['daHoanThanh'] ?? 0 ?>)
            </a>
            <a href="<?= e(admin_url('c=dat-phong&trangThai=DA_HUY')) ?>" class="btn btn-sm <?= ($trangThai === 'DA_HUY') ? 'btn-danger' : 'btn-outline-danger' ?>">
                Đã hủy (<?= $counts['daHuy'] ?? 0 ?>)
            </a>
        </div>

        <form method="GET" class="d-flex gap-2 mb-3" style="max-width: 400px;">
            <input type="hidden" name="c" value="dat-phong">
            <?php if (!empty($trangThai)): ?>
                <input type="hidden" name="trangThai" value="<?= e($trangThai) ?>">
            <?php endif; ?>
            <input type="text" name="keyword" class="form-control form-control-sm" placeholder="Tìm theo tên khách, sđt, phòng..." value="<?= e($keyword ?? '') ?>">
            <button type="submit" class="btn btn-sm btn-primary">Tìm</button>
            <?php if (!empty($keyword)): ?>
                <a href="<?= e(admin_url('c=dat-phong' . (!empty($trangThai) ? '&trangThai=' . $trangThai : ''))) ?>" class="btn btn-sm btn-outline-secondary">Xóa lọc</a>
            <?php endif; ?>
        </form>

        <!-- BOOKING TABLE -->
        <div class="table-responsive">
            <table class="table admin-table align-middle">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Số điện thoại</th>
                        <th>Phòng</th>
                        <th>Thời gian lưu trú</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($items)): ?>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><span class="id-badge">#<?= $item['MaCTDatPhong'] ?></span></td>
                                <td class="fw-semibold"><?= e($item['TenKhachHang'] ?? 'Khách vãng lai') ?></td>
                                <td><?= e($item['SdtKhachHang'] ?? '') ?></td>
                                <td>
                                    <strong>Phòng <?= e($item['MaPhong']) ?></strong>
                                    <div class="small text-muted"><?= e($item['TenLoaiPhong'] ?? '') ?></div>
                                </td>
                                <td>
                                    <div class="small">
                                        <i class="fa-regular fa-calendar-check me-1 text-success"></i><?= format_date($item['NgayNhan']) ?>
                                        <br>
                                        <i class="fa-regular fa-calendar-xmark me-1 text-danger"></i><?= format_date($item['NgayTra']) ?>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                        $st = $item['TrangThai'];
                                        $label = 'badge bg-secondary';
                                        $text = $st;
                                        switch ($st) {
                                            case 'CHO_ADMIN_DUYET': $label = 'badge bg-warning text-dark'; $text = 'Chờ duyệt'; break;
                                            case 'DA_XAC_NHAN': $label = 'badge bg-info text-white'; $text = 'Đã xác nhận'; break;
                                            case 'DANG_SU_DUNG': $label = 'badge bg-primary'; $text = 'Đang ở'; break;
                                            case 'DA_HOAN_THANH': $label = 'badge bg-success'; $text = 'Hoàn thành'; break;
                                            case 'DA_HUY': $label = 'badge bg-danger'; $text = 'Đã hủy'; break;
                                        }
                                    ?>
                                    <span class="<?= $label ?>"><?= $text ?></span>
                                </td>
                                <td class="text-end">
                                    <a href="<?= e(admin_url('c=dat-phong&a=detail&id=' . $item['MaCTDatPhong'])) ?>" class="btn btn-sm btn-outline-primary">
                                        Chi tiết & Xử lý
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Không tìm thấy đơn đặt phòng nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
