<?php
// views/admin/loai-phong/list.php
?>
<div class="container admin-wide my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold mb-0">Quản lý loại phòng</h1>
        <a href="<?= e(admin_url('c=loai-phong&a=add')) ?>" class="btn btn-success">
            <i class="fa-solid fa-plus me-1"></i> Thêm loại phòng
        </a>
    </div>

    <div class="card admin-panel border-0 shadow-sm p-4">
        <table class="table admin-table align-middle">
            <thead>
                <tr>
                    <th style="width: 80px;">Mã</th>
                    <th>Tên loại phòng</th>
                    <th>Giá niêm yết (VNĐ/ngày)</th>
                    <th>Mô tả tiện nghi</th>
                    <th class="text-end" style="width: 160px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><span class="id-badge"><?= e($item['MaLoaiPhong']) ?></span></td>
                            <td class="fw-bold fs-6"><?= e($item['TenLoaiPhong']) ?></td>
                            <td class="text-primary fw-bold"><?= format_currency($item['GiaPhong']) ?></td>
                            <td class="text-muted"><?= e($item['MoTa'] ?? 'Chưa có mô tả') ?></td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary me-1" href="<?= e(admin_url('c=loai-phong&a=edit&id=' . $item['MaLoaiPhong'])) ?>">
                                    <i class="fa-solid fa-pen-to-square me-1"></i>Sửa
                                </a>
                                <form method="POST" action="<?= e(admin_url('c=loai-phong&a=delete&id=' . $item['MaLoaiPhong'])) ?>" class="action-form" onsubmit="return confirm('Bạn có chắc muốn xóa loại phòng này?')"><?= csrf_field() ?><button type="submit" class="btn btn-sm btn-outline-danger" >
                                    <i class="fa-solid fa-trash me-1"></i>Xóa
                                </button></form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Chưa có loại phòng nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
