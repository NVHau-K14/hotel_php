<?php
// views/admin/phan-cong/list.php
?>
<div class="container admin-wide my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-1">Quản lý phân công công việc</h1>
            <p class="text-muted mb-0">Lịch làm việc và phân công nhiệm vụ cho nhân viên khách sạn</p>
        </div>
        <a href="<?= e(admin_url('c=phan-cong&a=add')) ?>" class="btn btn-success">
            <i class="fa-solid fa-plus me-1"></i> Thêm phân công
        </a>
    </div>

    <!-- Bộ lọc tìm kiếm -->
    <div class="card admin-panel border-0 shadow-sm p-3 mb-4">
        <form method="GET" action="<?= e(admin_url()) ?>" class="row g-2 align-items-center">
            <input type="hidden" name="c" value="phan-cong">
            <div class="col-md-4">
                <input type="text" name="keyword" class="form-control" placeholder="Tìm theo loại công việc..." value="<?= e($keyword ?? '') ?>">
            </div>
            <div class="col-md-3">
                <input type="date" name="ngay" class="form-control" value="<?= e($ngay ?? '') ?>">
            </div>
            <div class="col-md-5 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> Tìm kiếm
                </button>
                <a href="<?= e(admin_url('c=phan-cong')) ?>" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-rotate-left me-1"></i> Tất cả
                </a>
                <a href="<?= e(admin_url('c=phan-cong&a=homNay')) ?>" class="btn btn-outline-info">
                    <i class="fa-solid fa-calendar-day me-1"></i> Hôm nay
                </a>
            </div>
        </form>
    </div>

    <div class="card admin-panel border-0 shadow-sm p-4">
        <table class="table admin-table align-middle">
            <thead>
                <tr>
                    <th style="width: 100px;">Mã PC</th>
                    <th style="width: 150px;">Ngày phân công</th>
                    <th>Loại công việc</th>
                    <th>Nhân viên thực hiện</th>
                    <th>Chức vụ</th>
                    <th class="text-end" style="width: 160px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><span class="id-badge">#<?= e($item['MaPhanCong']) ?></span></td>
                            <td>
                                <span class="fw-semibold text-primary">
                                    <i class="fa-regular fa-calendar me-1"></i> <?= format_date($item['NgayPhanCong']) ?>
                                </span>
                            </td>
                            <td class="fw-bold fs-6"><?= e($item['LoaiCongViec']) ?></td>
                            <td>
                                <?php if (!empty($item['TenNhanVien'])): ?>
                                    <div class="fw-semibold text-dark"><?= e($item['TenNhanVien']) ?></div>
                                    <small class="text-muted">(@<?= e($item['Username']) ?>)</small>
                                <?php else: ?>
                                    <span class="text-muted fst-italic">Chưa chỉ định</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border"><?= e($item['TenChucVu'] ?? '-') ?></span>
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary me-1" href="<?= e(admin_url('c=phan-cong&a=edit&id=' . $item['MaPhanCong'])) ?>">
                                    <i class="fa-solid fa-pen-to-square me-1"></i>Sửa
                                </a>
                                <form method="POST" action="<?= e(admin_url('c=phan-cong&a=delete&id=' . $item['MaPhanCong'])) ?>" class="action-form" onsubmit="return confirm('Bạn có chắc muốn xóa phân công này?')"><?= csrf_field() ?><button type="submit" class="btn btn-sm btn-outline-danger" >
                                    <i class="fa-solid fa-trash me-1"></i>Xóa
                                </button></form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Chưa có phân công nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
