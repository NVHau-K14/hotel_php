<?php
// views/admin/danh-gia/list.php
?>
<div class="container admin-wide my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-1">Quản lý đánh giá khách hàng</h1>
            <p class="text-muted mb-0">Xem và quản lý các nhận xét, đánh giá chất lượng phòng từ khách</p>
        </div>
        <a href="<?= e(admin_url('c=danh-gia&a=add')) ?>" class="btn btn-success">
            <i class="fa-solid fa-plus me-1"></i> Thêm đánh giá
        </a>
    </div>

    <div class="card admin-panel border-0 shadow-sm p-4">
        <table class="table admin-table align-middle">
            <thead>
                <tr>
                    <th style="width: 70px;">ID</th>
                    <th style="width: 120px;">Phòng</th>
                    <th style="width: 140px;">Đánh giá sao</th>
                    <th>Nội dung nhận xét</th>
                    <th style="width: 150px;">Khách hàng</th>
                    <th style="width: 120px;">Ngày</th>
                    <th class="text-end" style="width: 150px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><span class="id-badge">#<?= e($item['ID']) ?></span></td>
                            <td>
                                <span class="badge bg-primary fs-6">
                                    <i class="fa-solid fa-key me-1"></i> Phòng <?= e($item['MaPhong']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="text-warning">
                                    <?php for ($s = 1; $s <= 5; $s++): ?>
                                        <i class="fa-<?= ($s <= $item['SoSao']) ? 'solid' : 'regular' ?> fa-star"></i>
                                    <?php endfor; ?>
                                </div>
                                <small class="text-muted"><?= e($item['SoSao']) ?>/5 sao</small>
                            </td>
                            <td class="text-dark">
                                <div class="fw-normal"><?= nl2br(e($item['NoiDung'])) ?></div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark"><?= e($item['TenKhachHang'] ?? 'Khách vãng lai') ?></div>
                                <?php if (!empty($item['Email'])): ?>
                                    <small class="text-muted"><?= e($item['Email']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><span class="text-muted"><?= format_date($item['NgayDanhGia']) ?></span></td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary me-1" href="<?= e(admin_url('c=danh-gia&a=edit&id=' . $item['ID'])) ?>">
                                    <i class="fa-solid fa-pen-to-square me-1"></i>Sửa
                                </a>
                                <form method="POST" action="<?= e(admin_url('c=danh-gia&a=delete&id=' . $item['ID'])) ?>" class="action-form" onsubmit="return confirm('Bạn có chắc muốn xóa đánh giá này?')"><?= csrf_field() ?><button type="submit" class="btn btn-sm btn-outline-danger" >
                                    <i class="fa-solid fa-trash me-1"></i>Xóa
                                </button></form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Chưa có đánh giá nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
