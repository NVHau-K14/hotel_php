<?php
// controllers/admin/CtDichVuController.php

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../models/CtDichVuModel.php';
require_once __DIR__ . '/../../models/DichVuModel.php';
require_once __DIR__ . '/../../models/DatPhongModel.php';
require_once __DIR__ . '/../../models/HoaDonModel.php';

class CtDichVuController {
    public function list(): void {
        Auth::requirePermission('CT_DICH_VU');
        $items = CtDichVuModel::getAll();

        render_admin_view('ct-dich-vu/list', [
            'title' => 'Quản lý chi tiết sử dụng dịch vụ',
            'items' => $items
        ]);
    }

    public function add(): void {
        Auth::requirePermission('CT_DICH_VU');
        $dichVus = DichVuModel::getAll();
        $datPhongs = DatPhongModel::getAll();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $maDichVu    = (int)($_POST['maDichVu'] ?? 0);
            $maCTDatPhong= (int)($_POST['maCTDatPhong'] ?? 0);
            $soLuong     = (int)($_POST['soLuong'] ?? 1);
            $maCTDichVu  = !empty($_POST['maCTDichVu']) ? (int)$_POST['maCTDichVu'] : null;

            if ($maDichVu <= 0 || $maCTDatPhong <= 0 || $soLuong <= 0) {
                $error = 'Vui lòng chọn dịch vụ, đơn đặt phòng và số lượng hợp lệ (> 0).';
            } else {
                CtDichVuModel::save($maDichVu, $maCTDatPhong, $soLuong, null, $maCTDichVu);
                // Cập nhật hóa đơn nếu có
                $admin = Auth::currentAdmin();
                // Existing invoice totals are refreshed atomically in CtDichVuModel.

                set_flash('success', 'Thêm chi tiết dịch vụ thành công!');
                redirect(admin_url('c=ct-dich-vu'));
            }
        }

        render_admin_view('ct-dich-vu/form', [
            'title'     => 'Thêm chi tiết dịch vụ',
            'dichVus'   => $dichVus,
            'datPhongs' => $datPhongs,
            'error'     => $error,
            'item'      => []
        ]);
    }

    public function addToBooking(): void {
        Auth::requirePermission('CT_DICH_VU');
        $maCTDatPhong = (int)($_GET['maCTDatPhong'] ?? 0);
        $datPhong = DatPhongModel::getById($maCTDatPhong);

        if (!$datPhong) {
            set_flash('danger', 'Không tìm thấy đơn đặt phòng.');
            redirect(admin_url('c=dat-phong'));
        }

        $dichVus = DichVuModel::getAll();

        render_admin_view('ct-dich-vu/them-vao-don', [
            'title'    => "Thêm dịch vụ vào đơn #$maCTDatPhong",
            'datPhong' => $datPhong,
            'dichVus'  => $dichVus
        ]);
    }

    public function saveToBooking(): void {
        Auth::requirePermission('CT_DICH_VU');
        $maCTDatPhong = (int)($_POST['maCTDatPhong'] ?? 0);
        $maDichVu     = (int)($_POST['maDichVu'] ?? 0);
        $soLuong      = (int)($_POST['soLuong'] ?? 1);

        if ($maCTDatPhong > 0 && $maDichVu > 0 && $soLuong > 0) {
            CtDichVuModel::addToBooking($maCTDatPhong, $maDichVu, $soLuong);
            $admin = Auth::currentAdmin();
            // Existing invoice totals are refreshed atomically in CtDichVuModel.
            set_flash('success', 'Đã thêm dịch vụ vào đơn thành công.');
            redirect(admin_url('c=dat-phong&a=detail&id=' . $maCTDatPhong));
        }

        set_flash('danger', 'Thông tin dịch vụ hoặc số lượng không hợp lệ.');
        redirect(admin_url('c=dat-phong&a=detail&id=' . $maCTDatPhong));
    }

    public function delete(): void {
        Auth::requirePermission('CT_DICH_VU');
        $maDichVu     = (int)($_GET['maDichVu'] ?? 0);
        $maCTDatPhong = (int)($_GET['maCTDatPhong'] ?? 0);
        $back         = $_GET['back'] ?? '';

        if ($maDichVu > 0 && $maCTDatPhong > 0) {
            CtDichVuModel::delete($maDichVu, $maCTDatPhong);
            $admin = Auth::currentAdmin();
            // Existing invoice totals are refreshed atomically in CtDichVuModel.
            set_flash('success', 'Đã xóa chi tiết dịch vụ.');
        }

        if ($back === 'detail' && $maCTDatPhong > 0) {
            redirect(admin_url('c=dat-phong&a=detail&id=' . $maCTDatPhong));
        }
        redirect(admin_url('c=ct-dich-vu'));
    }
}
