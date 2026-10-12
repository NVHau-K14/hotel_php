<?php
// views/client/account/history.php
?>
<main class="container my-4" style="max-width: 950px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Lịch sử đặt phòng của bạn</h2>
            <p class="text-muted mb-0">Quản lý và theo dõi các đơn đặt phòng của bạn tại khách sạn</p>
        </div>
        <a href="<?= e(client_url('c=room')) ?>" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> Đặt thêm phòng
        </a>
    </div>

    <div class="card border-0 shadow-sm p-4" style="border-radius: 12px;">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Mã đơn</th>
                        <th>Phòng</th>
                        <th>Thời gian lưu trú</th>
                        <th>Giá niêm yết</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($bookings)): ?>
                        <?php foreach ($bookings as $b): ?>
                            <tr>
                                <td><span class="badge bg-secondary fs-6">#<?= $b['MaCTDatPhong'] ?></span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <?php if (!empty($b['HinhAnhDaiDien'])): ?>
                                            <img src="<?= e(asset($b['HinhAnhDaiDien'])) ?>" alt="Phong" style="width:50px; height:38px; object-fit:cover; border-radius:4px;">
                                        <?php endif; ?>
                                        <div>
                                            <strong>Phòng <?= e($b['MaPhong']) ?></strong>
                                            <div class="small text-muted"><?= e($b['TenLoaiPhong']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small">
                                        <i class="fa-regular fa-calendar-check text-success me-1"></i><?= format_date($b['NgayNhan']) ?>
                                        <br>
                                        <i class="fa-regular fa-calendar-xmark text-danger me-1"></i><?= format_date($b['NgayTra']) ?>
                                    </div>
                                </td>
                                <td class="fw-bold text-primary">
                                    <?= format_currency($b['GiaPhong']) ?> / đêm
                                </td>
                                <td>
                                    <?php
                                        $st = $b['TrangThai'];
                                        $badgeClass = 'bg-secondary';
                                        $badgeText = $st;
                                        if ($st === 'CHO_ADMIN_DUYET') { $badgeClass = 'bg-warning text-dark'; $badgeText = 'Chờ duyệt'; }
                                        elseif ($st === 'DA_XAC_NHAN') { $badgeClass = 'bg-info text-white'; $badgeText = 'Đã xác nhận'; }
                                        elseif ($st === 'DANG_SU_DUNG') { $badgeClass = 'bg-primary'; $badgeText = 'Đang ở'; }
                                        elseif ($st === 'DA_HOAN_THANH') { $badgeClass = 'bg-success'; $badgeText = 'Hoàn tất'; }
                                        elseif ($st === 'DA_HUY') { $badgeClass = 'bg-danger'; $badgeText = 'Đã hủy'; }
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span>
                                </td>
                                <td class="text-end">
                                    <a href="<?= e(client_url('c=account&a=detail&id=' . $b['MaCTDatPhong'])) ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fa-solid fa-eye me-1"></i>Chi tiết
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-calendar-xmark fa-3x text-muted mb-3 d-block"></i>
                                Bạn chưa có đơn đặt phòng nào.
                                <div class="mt-2">
                                    <a href="<?= e(client_url('c=room')) ?>" class="btn btn-sm btn-outline-primary">Xem danh sách phòng</a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
