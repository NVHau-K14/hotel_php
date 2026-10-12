<?php
// controllers/admin/HoaDonController.php

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../models/HoaDonModel.php';
require_once __DIR__ . '/../../models/DatPhongModel.php';

class HoaDonController {
    public function list(): void {
        Auth::requirePermission('HOA_DON');
        $items = HoaDonModel::getAll();
        render_admin_view('hoa-don/list', [
            'title' => 'Quản lý Hóa đơn',
            'items' => $items
        ]);
    }

    public function detail(): void {
        Auth::requirePermission('HOA_DON');
        $id = (int)($_GET['id'] ?? 0);
        $item = HoaDonModel::getById($id);
        if (!$item) {
            set_flash('danger', 'Hóa đơn không tồn tại.');
            redirect(admin_url('c=hoa-don'));
        }

        $calc = HoaDonModel::calculateBookingTotal($item['MaCTDatPhong']);

        render_admin_view('hoa-don/detail', [
            'title' => "Chi tiết Hóa đơn #$id",
            'item'  => $item,
            'calc'  => $calc
        ]);
    }

    public function pay(): void {
        Auth::requirePermission('HOA_DON');
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            HoaDonModel::payInvoice($id);
            set_flash('success', "Xác nhận thanh toán hóa đơn #$id thành công! Phòng đã được giải phóng về trạng thái Trống.");
        }
        redirect(admin_url('c=hoa-don&a=detail&id=' . $id));
    }
}
