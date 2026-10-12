<?php
// controllers/admin/PhanCongController.php

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../models/PhanCongModel.php';
require_once __DIR__ . '/../../models/NhanVienModel.php';

class PhanCongController {
    public function list(): void {
        Auth::requirePermission('PHAN_CONG');
        $keyword = $_GET['keyword'] ?? null;
        $ngay    = $_GET['ngay'] ?? null;

        $items = PhanCongModel::getAll($keyword, $ngay);

        render_admin_view('phan-cong/list', [
            'title'   => 'Quản lý phân công công việc',
            'items'   => $items,
            'keyword' => $keyword,
            'ngay'    => $ngay
        ]);
    }

    public function homNay(): void {
        Auth::requirePermission('PHAN_CONG');
        $today = date('Y-m-d');
        $items = PhanCongModel::getByToday();

        render_admin_view('phan-cong/list', [
            'title'   => 'Phân công công việc hôm nay',
            'items'   => $items,
            'keyword' => null,
            'ngay'    => $today
        ]);
    }

    public function add(): void {
        Auth::requirePermission('PHAN_CONG');
        $nhanViens = NhanVienModel::getAll();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ngay     = $_POST['ngayPhanCong'] ?? '';
            $congViec = trim($_POST['loaiCongViec'] ?? '');
            $maNv     = (int)($_POST['maNhanVien'] ?? 0);

            if (empty($ngay) || empty($congViec)) {
                $error = 'Vui lòng chọn ngày phân công và nhập loại công việc.';
            } elseif ($ngay < date('Y-m-d')) {
                $error = 'Không được phân công ngày trong quá khứ.';
            } else {
                PhanCongModel::create($ngay, $congViec, $maNv > 0 ? $maNv : null);
                set_flash('success', 'Thêm phân công công việc thành công!');
                redirect(admin_url('c=phan-cong'));
            }
        }

        render_admin_view('phan-cong/form', [
            'title'     => 'Thêm phân công mới',
            'isEdit'    => false,
            'nhanViens' => $nhanViens,
            'item'      => ['NgayPhanCong' => date('Y-m-d')],
            'error'     => $error
        ]);
    }

    public function edit(): void {
        Auth::requirePermission('PHAN_CONG');
        $id = (int)($_GET['id'] ?? 0);
        $item = PhanCongModel::getById($id);

        if (!$item) {
            set_flash('danger', 'Không tìm thấy phân công.');
            redirect(admin_url('c=phan-cong'));
        }

        $nhanViens = NhanVienModel::getAll();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ngay     = $_POST['ngayPhanCong'] ?? '';
            $congViec = trim($_POST['loaiCongViec'] ?? '');
            $maNv     = (int)($_POST['maNhanVien'] ?? 0);

            if (empty($ngay) || empty($congViec)) {
                $error = 'Vui lòng chọn ngày phân công và nhập loại công việc.';
            } else {
                PhanCongModel::update($id, $ngay, $congViec, $maNv > 0 ? $maNv : null);
                set_flash('success', 'Cập nhật phân công thành công!');
                redirect(admin_url('c=phan-cong'));
            }
        }

        render_admin_view('phan-cong/form', [
            'title'     => "Sửa phân công #$id",
            'isEdit'    => true,
            'nhanViens' => $nhanViens,
            'item'      => $item,
            'error'     => $error
        ]);
    }

    public function delete(): void {
        Auth::requirePermission('PHAN_CONG');
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            PhanCongModel::delete($id);
            set_flash('success', 'Đã xóa phân công công việc.');
        }
        redirect(admin_url('c=phan-cong'));
    }
}
