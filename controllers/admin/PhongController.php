<?php
// controllers/admin/PhongController.php

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../models/PhongModel.php';
require_once __DIR__ . '/../../models/LoaiPhongModel.php';

class PhongController {
    public function list(): void {
        Auth::requirePermission('PHONG');

        $keyword     = $_GET['keyword'] ?? null;
        $maLoaiPhong = isset($_GET['maLoaiPhong']) && $_GET['maLoaiPhong'] !== '' ? (int)$_GET['maLoaiPhong'] : null;
        $giaTu       = isset($_GET['giaTu']) && $_GET['giaTu'] !== '' ? (int)$_GET['giaTu'] : null;
        $giaDen      = isset($_GET['giaDen']) && $_GET['giaDen'] !== '' ? (int)$_GET['giaDen'] : null;
        $ngayNhan    = $_GET['ngayNhan'] ?? null;
        $ngayTra     = $_GET['ngayTra'] ?? null;

        $dateError = '';
        if (($ngayNhan || $ngayTra) && (!$ngayNhan || !$ngayTra || !valid_date($ngayNhan) || !valid_date($ngayTra) || $ngayNhan >= $ngayTra)) {
            $dateError = 'Vui lòng chọn đủ ngày nhận/trả hợp lệ; ngày trả phải sau ngày nhận.';
            $ngayNhan = null;
            $ngayTra = null;
        }

        $items = PhongModel::getAll($keyword, $maLoaiPhong, $giaTu, $giaDen, $ngayNhan, $ngayTra);
        $counts = PhongModel::countByStatus();
        $loaiPhongs = LoaiPhongModel::getAll();

        render_admin_view('phong/list', [
            'title'       => 'Quản lý phòng',
            'items'       => $items,
            'counts'      => $counts,
            'loaiPhongs'  => $loaiPhongs,
            'keyword'     => $keyword,
            'maLoaiPhong' => $maLoaiPhong,
            'giaTu'       => $giaTu,
            'giaDen'      => $giaDen,
            'ngayNhan'    => $ngayNhan,
            'ngayTra'     => $ngayTra,
            'dateError'   => $dateError
        ]);
    }

    public function add(): void {
        Auth::requirePermission('PHONG');
        $loaiPhongs = LoaiPhongModel::getAll();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $maPhong     = (int)($_POST['maPhong'] ?? 0);
            $maLoaiPhong = (int)($_POST['maLoaiPhong'] ?? 0);
            $trangThai   = trim($_POST['trangThai'] ?? 'Trống');
            $hinhAnh     = trim($_POST['hinhAnh'] ?? '');

            if ($maPhong <= 0 || $maLoaiPhong <= 0) {
                $error = 'Vui lòng nhập mã phòng và chọn loại phòng hợp lệ.';
            } elseif (PhongModel::getById($maPhong)) {
                $error = "Mã phòng $maPhong đã tồn tại trên hệ thống.";
            } else {
                // Xử lý upload ảnh nếu có file được gửi lên
                $hinhAnh = upload_image('fileAnh', 'phong') ?? $hinhAnh;

                PhongModel::create($maPhong, $maLoaiPhong, $trangThai, $hinhAnh ?: null);
                set_flash('success', "Thêm phòng $maPhong thành công!");
                redirect(admin_url('c=phong'));
            }
        }

        render_admin_view('phong/form', [
            'title'      => 'Thêm phòng mới',
            'loaiPhongs' => $loaiPhongs,
            'isEdit'     => false,
            'item'       => null,
            'error'      => $error
        ]);
    }

    public function edit(): void {
        Auth::requirePermission('PHONG');
        $maPhong = (int)($_GET['id'] ?? 0);
        $item = PhongModel::getById($maPhong);
        if (!$item) {
            set_flash('danger', 'Không tìm thấy thông tin phòng.');
            redirect(admin_url('c=phong'));
        }

        $loaiPhongs = LoaiPhongModel::getAll();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $maLoaiPhong = (int)($_POST['maLoaiPhong'] ?? 0);
            $trangThai   = trim($_POST['trangThai'] ?? 'Trống');
            $hinhAnh     = trim($_POST['hinhAnh'] ?? $item['HinhAnhDaiDien']);

            if ($maLoaiPhong <= 0) {
                $error = 'Vui lòng chọn loại phòng hợp lệ.';
            } else {
                $hinhAnh = upload_image('fileAnh', 'phong') ?? $hinhAnh;

                PhongModel::update($maPhong, $maLoaiPhong, $trangThai, $hinhAnh);
                set_flash('success', "Cập nhật thông tin phòng $maPhong thành công!");
                redirect(admin_url('c=phong'));
            }
        }

        render_admin_view('phong/form', [
            'title'      => "Sửa phòng #$maPhong",
            'loaiPhongs' => $loaiPhongs,
            'isEdit'     => true,
            'item'       => $item,
            'error'      => $error
        ]);
    }

    public function delete(): void {
        Auth::requirePermission('PHONG');
        $maPhong = (int)($_GET['id'] ?? 0);
        if ($maPhong > 0) {
            PhongModel::delete($maPhong);
            set_flash('success', "Đã xóa phòng $maPhong thành công.");
        }
        redirect(admin_url('c=phong'));
    }
}
