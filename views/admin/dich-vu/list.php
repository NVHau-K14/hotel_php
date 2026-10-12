<?php
// views/admin/dich-vu/list.php
?>
<div class="container admin-wide my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold mb-0">Quản lý dịch vụ</h1>
        <a href="<?= e(admin_url('c=dich-vu&a=add')) ?>" class="btn btn-success">
            <i class="fa-solid fa-plus me-1"></i> Thêm dịch vụ
        </a>
    </div>

    <div class="card admin-panel border-0 shadow-sm p-4">
        <table class="table admin-table align-middle">
            <thead>
                <tr>
                    <th style="width: 80px;">Mã</th>
                    <th style="width: 100px;">Hình ảnh</th>
                    <th>Tên dịch vụ</th>
                    <th>Giá dịch vụ (VNĐ)</th>
                    <th class="text-end" style="width: 160px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><span class="id-badge"><?= e($item['MaDichVu']) ?></span></td>
                            <td>
                                <?php if (!empty($item['HinhAnh'])): ?>
                                    <img src="<?= e(asset($item['HinhAnh'])) ?>" alt="Dich vu" style="width: 60px; height: 45px; object-fit: cover; border-radius: 6px;">
                                <?php else: ?>
                                    <span class="text-muted small">Không có ảnh</span>
                                <?php endif; ?>
                            </td>
                            <td class="fw-bold fs-6"><?= e($item['TenDichVu']) ?></td>
                            <td class="text-primary fw-bold"><?= format_currency($item['GiaDichVu']) ?></td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary me-1" href="<?= e(admin_url('c=dich-vu&a=edit&id=' . $item['MaDichVu'])) ?>">
                                    <i class="fa-solid fa-pen-to-square me-1"></i>Sửa
                                </a>
                                <form method="POST" action="<?= e(admin_url('c=dich-vu&a=delete&id=' . $item['MaDichVu'])) ?>" class="action-form" onsubmit="return confirm('Bạn có chắc muốn xóa dịch vụ này?')"><?= csrf_field() ?><button type="submit" class="btn btn-sm btn-outline-danger" >
                                    <i class="fa-solid fa-trash me-1"></i>Xóa
                                </button></form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Chưa có dịch vụ nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
