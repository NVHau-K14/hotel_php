<?php
// controllers/admin/KhachHangController.php

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../models/KhachHangModel.php';
require_once __DIR__ . '/../../models/DatPhongModel.php';

class KhachHangController {
    public function list(): void {
        Auth::requirePermission('KHACH_HANG');
        $keyword = $_GET['keyword'] ?? null;
        $items = KhachHangModel::getAll($keyword);
        render_admin_view('khach-hang/list', [
            'title'   => 'Quản lý Khách hàng',
            'items'   => $items,
            'keyword' => $keyword
        ]);
    }

    public function toggle(): void {
        Auth::requirePermission('KHACH_HANG');
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            KhachHangModel::toggleStatus($id);
            set_flash('success', "Đã thay đổi trạng thái tài khoản khách hàng #$id.");
        }
        redirect(admin_url('c=khach-hang'));
    }

    public function history(): void {
        Auth::requirePermission('KHACH_HANG');
        $id = (int)($_GET['id'] ?? 0);
        $customer = KhachHangModel::getById($id);
        if (!$customer) {
            set_flash('danger', 'Khách hàng không tồn tại.');
            redirect(admin_url('c=khach-hang'));
        }
        $bookings = DatPhongModel::getByKhachHang($id);

        render_admin_view('khach-hang/history', [
            'title'    => "Lịch sử đặt phòng - {$customer['HoTen']}",
            'customer' => $customer,
            'bookings' => $bookings
        ]);
    }
}
