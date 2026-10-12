<?php
// controllers/admin/DichVuController.php

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../models/DichVuModel.php';

class DichVuController {
    public function list(): void {
        Auth::requirePermission('DICH_VU');
        $items = DichVuModel::getAll();
        render_admin_view('dich-vu/list', [
            'title' => 'Quản lý dịch vụ',
            'items' => $items
        ]);
    }

    public function add(): void {
        Auth::requirePermission('DICH_VU');
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ten  = trim($_POST['tenDichVu'] ?? '');
            $gia  = (int)($_POST['giaDichVu'] ?? 0);
            $anh  = trim($_POST['hinhAnh'] ?? '');

            if (empty($ten) || $gia <= 0) {
                $error = 'Vui lòng nhập tên dịch vụ và đơn giá hợp lệ.';
            } else {
                $anh = upload_image('fileAnh', 'dichvu') ?? $anh;
                DichVuModel::create($ten, $gia, $anh ?: null);
                set_flash('success', "Đã thêm dịch vụ \"$ten\" thành công!");
                redirect(admin_url('c=dich-vu'));
            }
        }

        render_admin_view('dich-vu/form', [
            'title'  => 'Thêm dịch vụ mới',
            'isEdit' => false,
            'item'   => null,
            'error'  => $error
        ]);
    }

    public function edit(): void {
        Auth::requirePermission('DICH_VU');
        $id = (int)($_GET['id'] ?? 0);
        $item = DichVuModel::getById($id);
        if (!$item) {
            set_flash('danger', 'Không tìm thấy dịch vụ.');
            redirect(admin_url('c=dich-vu'));
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ten = trim($_POST['tenDichVu'] ?? '');
            $gia = (int)($_POST['giaDichVu'] ?? 0);
            $anh = trim($_POST['hinhAnh'] ?? $item['HinhAnh']);

            if (empty($ten) || $gia <= 0) {
                $error = 'Vui lòng nhập tên dịch vụ và đơn giá hợp lệ.';
            } else {
                $anh = upload_image('fileAnh', 'dichvu') ?? $anh;
                DichVuModel::update($id, $ten, $gia, $anh);
                set_flash('success', "Cập nhật dịch vụ \"$ten\" thành công!");
                redirect(admin_url('c=dich-vu'));
            }
        }

        render_admin_view('dich-vu/form', [
            'title'  => "Sửa dịch vụ #$id",
            'isEdit' => true,
            'item'   => $item,
            'error'  => $error
        ]);
    }

    public function delete(): void {
        Auth::requirePermission('DICH_VU');
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            DichVuModel::delete($id);
            set_flash('success', 'Đã xóa dịch vụ thành công.');
        }
        redirect(admin_url('c=dich-vu'));
    }
}
