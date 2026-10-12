<?php
// controllers/admin/DanhGiaController.php

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../models/DanhGiaModel.php';
require_once __DIR__ . '/../../models/PhongModel.php';

class DanhGiaController {
    public function list(): void {
        Auth::requirePermission('DANH_GIA');
        $items = DanhGiaModel::getAll();

        render_admin_view('danh-gia/list', [
            'title' => 'Quản lý đánh giá khách hàng',
            'items' => $items
        ]);
    }

    public function add(): void {
        Auth::requirePermission('DANH_GIA');
        $phongs = PhongModel::getAll();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $noiDung  = trim($_POST['noiDung'] ?? '');
            $ngay     = $_POST['ngayDanhGia'] ?? date('Y-m-d');
            $maPhong  = (int)($_POST['maPhong'] ?? 0);
            $soSao    = (int)($_POST['soSao'] ?? 5);

            if (empty($noiDung) || $maPhong <= 0) {
                $error = 'Vui lòng nhập nội dung đánh giá và chọn phòng.';
            } else {
                DanhGiaModel::create($noiDung, $ngay, $maPhong, $soSao);
                set_flash('success', 'Thêm đánh giá mới thành công!');
                redirect(admin_url('c=danh-gia'));
            }
        }

        render_admin_view('danh-gia/form', [
            'title'  => 'Thêm đánh giá',
            'isEdit' => false,
            'phongs' => $phongs,
            'item'   => ['SoSao' => 5, 'NgayDanhGia' => date('Y-m-d')],
            'error'  => $error
        ]);
    }

    public function edit(): void {
        Auth::requirePermission('DANH_GIA');
        $id = (int)($_GET['id'] ?? 0);
        $item = DanhGiaModel::getById($id);

        if (!$item) {
            set_flash('danger', 'Không tìm thấy đánh giá.');
            redirect(admin_url('c=danh-gia'));
        }

        $phongs = PhongModel::getAll();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $noiDung  = trim($_POST['noiDung'] ?? '');
            $ngay     = $_POST['ngayDanhGia'] ?? date('Y-m-d');
            $maPhong  = (int)($_POST['maPhong'] ?? 0);
            $soSao    = (int)($_POST['soSao'] ?? 5);

            if (empty($noiDung) || $maPhong <= 0) {
                $error = 'Vui lòng nhập nội dung đánh giá và chọn phòng.';
            } else {
                DanhGiaModel::update($id, $noiDung, $ngay, $maPhong, $soSao);
                set_flash('success', 'Cập nhật đánh giá thành công!');
                redirect(admin_url('c=danh-gia'));
            }
        }

        render_admin_view('danh-gia/form', [
            'title'  => "Sửa đánh giá #$id",
            'isEdit' => true,
            'phongs' => $phongs,
            'item'   => $item,
            'error'  => $error
        ]);
    }

    public function delete(): void {
        Auth::requirePermission('DANH_GIA');
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            DanhGiaModel::delete($id);
            set_flash('success', 'Đã xóa đánh giá thành công.');
        }
        redirect(admin_url('c=danh-gia'));
    }
}
