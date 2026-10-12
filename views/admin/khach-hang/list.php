<?php
// views/admin/khach-hang/list.php
?>
<div class="container admin-wide my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold mb-0">Quản lý khách hàng</h1>
    </div>

    <div class="card admin-panel border-0 shadow-sm p-4">
        <form method="GET" class="d-flex gap-2 mb-3" style="max-width: 400px;">
            <input type="hidden" name="c" value="khach-hang">
            <input type="text" name="keyword" class="form-control form-control-sm" placeholder="Tìm theo tên, email, SĐT, CCCD..." value="<?= e($keyword ?? '') ?>">
            <button type="submit" class="btn btn-sm btn-primary">Tìm</button>
            <?php if (!empty($keyword)): ?>
                <a href="<?= e(admin_url('c=khach-hang')) ?>" class="btn btn-sm btn-outline-secondary">Xóa</a>
            <?php endif; ?>
        </form>

        <div class="table-responsive">
            <table class="table admin-table align-middle">
                <thead>
                    <tr>
                        <th>Mã</th>
                        <th>Họ và tên</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>CCCD/CMND</th>
                        <th>Địa chỉ</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($items)): ?>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><span class="id-badge"><?= $item['MaKhachHang'] ?></span></td>
                                <td class="fw-bold"><?= e($item['HoTen']) ?></td>
                                <td><?= e($item['Email']) ?></td>
                                <td><?= e($item['DienThoai']) ?></td>
                                <td><?= e($item['CMND'] ?? 'Chưa có') ?></td>
                                <td><?= e($item['DiaChi'] ?? 'Chưa có') ?></td>
                                <td>
                                    <?php if (($item['TrangThai'] ?? '') === 'KHOA'): ?>
                                        <span class="badge bg-danger">Đã khóa</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Hoạt động</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a href="<?= e(admin_url('c=khach-hang&a=history&id=' . $item['MaKhachHang'])) ?>" class="btn btn-sm btn-outline-info me-1">
                                        Lịch sử
                                    </a>
                                    <form method="POST" action="<?= e(admin_url('c=khach-hang&a=toggle&id=' . $item['MaKhachHang'])) ?>" class="action-form" onsubmit="return confirm('Xác nhận đổi trạng thái tài khoản này?')"><?= csrf_field() ?><button type="submit"  
                                       class="btn btn-sm <?= ($item['TrangThai'] === 'KHOA') ? 'btn-outline-success' : 'btn-outline-warning' ?>">
                                        <?= ($item['TrangThai'] === 'KHOA') ? 'Mở khóa' : 'Khóa' ?>
                                    </button></form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Không có khách hàng nào phù hợp.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
