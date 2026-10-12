<?php
// controllers/admin/LoaiPhongController.php

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../models/LoaiPhongModel.php';

class LoaiPhongController {
    public function list(): void {
        Auth::requirePermission('LOAI_PHONG');
        $items = LoaiPhongModel::getAll();
        render_admin_view('loai-phong/list', [
            'title' => 'Quản lý loại phòng',
            'items' => $items
        ]);
    }

    public function add(): void {
        Auth::requirePermission('LOAI_PHONG');
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ten  = trim($_POST['tenLoaiPhong'] ?? '');
            $gia  = (int)($_POST['giaPhong'] ?? 0);
            $moTa = trim($_POST['moTa'] ?? '');

            if (empty($ten) || $gia <= 0) {
                $error = 'Vui lòng nhập tên loại phòng và đơn giá hợp lệ.';
            } else {
                LoaiPhongModel::create($ten, $gia, $moTa);
                set_flash('success', "Đã thêm loại phòng \"$ten\" thành công!");
                redirect(admin_url('c=loai-phong'));
            }
        }
        render_admin_view('loai-phong/form', [
            'title'  => 'Thêm loại phòng mới',
            'isEdit' => false,
            'item'   => null,
            'error'  => $error
        ]);
    }

    public function edit(): void {
        Auth::requirePermission('LOAI_PHONG');
        $id = (int)($_GET['id'] ?? 0);
        $item = LoaiPhongModel::getById($id);
        if (!$item) {
            set_flash('danger', 'Loại phòng không tồn tại.');
            redirect(admin_url('c=loai-phong'));
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ten  = trim($_POST['tenLoaiPhong'] ?? '');
            $gia  = (int)($_POST['giaPhong'] ?? 0);
            $moTa = trim($_POST['moTa'] ?? '');

            if (empty($ten) || $gia <= 0) {
                $error = 'Vui lòng nhập tên loại phòng và đơn giá hợp lệ.';
            } else {
                LoaiPhongModel::update($id, $ten, $gia, $moTa);
                set_flash('success', "Cập nhật loại phòng \"$ten\" thành công!");
                redirect(admin_url('c=loai-phong'));
            }
        }
        render_admin_view('loai-phong/form', [
            'title'  => "Sửa loại phòng #$id",
            'isEdit' => true,
            'item'   => $item,
            'error'  => $error
        ]);
    }

    public function delete(): void {
        Auth::requirePermission('LOAI_PHONG');
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            LoaiPhongModel::delete($id);
            set_flash('success', 'Đã xóa loại phòng thành công.');
        }
        redirect(admin_url('c=loai-phong'));
    }
}
