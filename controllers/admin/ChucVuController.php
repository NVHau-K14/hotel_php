<?php
// controllers/admin/ChucVuController.php

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../models/NhanVienModel.php';

class ChucVuController {
    public const ALL_PERMISSIONS = [
        'DASHBOARD'   => 'Xem Bảng điều khiển (Dashboard)',
        'PHONG'       => 'Quản lý Phòng',
        'LOAI_PHONG'  => 'Quản lý Loại phòng',
        'DAT_PHONG'   => 'Quản lý Đặt phòng',
        'DICH_VU'     => 'Quản lý Dịch vụ',
        'CT_DICH_VU'  => 'Thêm/bớt Dịch vụ vào Đơn phòng',
        'HOA_DON'     => 'Quản lý & Thanh toán Hóa đơn',
        'KHACH_HANG'  => 'Quản lý Khách hàng',
        'NHAN_VIEN'   => 'Quản lý Nhân viên',
        'PHAN_CONG'   => 'Quản lý Phân công công việc',
        'CHUC_VU'     => 'Quản lý Chức vụ & Phân quyền',
        'DANH_GIA'    => 'Quản lý Đánh giá khách hàng',
        'THONG_KE'    => 'Xem Báo cáo Thống kê'
    ];

    public function list(): void {
        Auth::requirePermission('CHUC_VU');
        $chucVus = NhanVienModel::getAllChucVu();

        // Gắn quyền cho từng chức vụ
        foreach ($chucVus as &$cv) {
            $cv['permissions'] = NhanVienModel::getPermissionsByChucVu($cv['MaChucVu']);
        }
        unset($cv);

        render_admin_view('chuc-vu/list', [
            'title'          => 'Quản lý Chức vụ & Phân quyền',
            'chucVus'        => $chucVus,
            'allPermissions' => self::ALL_PERMISSIONS
        ]);
    }

    public function updatePermissions(): void {
        Auth::requirePermission('CHUC_VU');
        $maChucVu = (int)($_POST['maChucVu'] ?? 0);
        $permissions = $_POST['permissions'] ?? [];
        if (!is_array($permissions) || array_diff($permissions, array_keys(self::ALL_PERMISSIONS))) throw new DomainException('Danh sách quyền không hợp lệ.');
        $permissions = array_values(array_unique($permissions));

        if ($maChucVu > 0) {
            NhanVienModel::updateChucVuPermissions($maChucVu, $permissions);
            set_flash('success', "Cập nhật quyền thành công cho chức vụ #$maChucVu.");
        }
        redirect(admin_url('c=chuc-vu'));
    }
}
