<?php
// controllers/admin/NhanVienController.php

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../models/NhanVienModel.php';

class NhanVienController {
    public function list(): void {
        Auth::requirePermission('NHAN_VIEN');
        $items = NhanVienModel::getAll();
        render_admin_view('nhan-vien/list', [
            'title' => 'Quản lý Nhân viên',
            'items' => $items
        ]);
    }

    public function add(): void {
        Auth::requirePermission('NHAN_VIEN');
        $chucVus = NhanVienModel::getAllChucVu();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username   = trim($_POST['username'] ?? '');
            $password   = $_POST['password'] ?? '';
            $ten        = trim($_POST['tenNhanVien'] ?? '');
            $ngaySinh   = $_POST['ngaySinh'] ?? null;
            $cmnd       = $_POST['cmnd'] ?? null;
            $ngayVaoLam = $_POST['ngayVaoLam'] ?? null;
            $maChucVu   = (int)($_POST['maChucVu'] ?? 0);

            if (empty($username) || empty($password) || empty($ten) || $maChucVu <= 0) {
                $error = 'Vui lòng điền đầy đủ tài khoản, mật khẩu, họ tên và chức vụ.';
            } else {
                NhanVienModel::create($username, $password, $ten, $ngaySinh, $cmnd, $ngayVaoLam, $maChucVu);
                set_flash('success', "Thêm nhân viên \"$ten\" thành công!");
                redirect(admin_url('c=nhan-vien'));
            }
        }

        render_admin_view('nhan-vien/form', [
            'title'   => 'Thêm nhân viên mới',
            'isEdit'  => false,
            'item'    => null,
            'chucVus' => $chucVus,
            'error'   => $error
        ]);
    }

    public function edit(): void {
        Auth::requirePermission('NHAN_VIEN');
        $id = (int)($_GET['id'] ?? 0);
        $item = NhanVienModel::getById($id);
        if (!$item) {
            set_flash('danger', 'Nhân viên không tồn tại.');
            redirect(admin_url('c=nhan-vien'));
        }

        $chucVus = NhanVienModel::getAllChucVu();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ten        = trim($_POST['tenNhanVien'] ?? '');
            $password   = $_POST['password'] ?? '';
            $ngaySinh   = $_POST['ngaySinh'] ?? null;
            $cmnd       = $_POST['cmnd'] ?? null;
            $ngayVaoLam = $_POST['ngayVaoLam'] ?? null;
            $maChucVu   = (int)($_POST['maChucVu'] ?? 0);

            if (empty($ten) || $maChucVu <= 0) {
                $error = 'Vui lòng nhập họ tên và chọn chức vụ.';
            } else {
                NhanVienModel::update($id, $ten, $ngaySinh, $cmnd, $ngayVaoLam, $maChucVu, $password ?: null);
                set_flash('success', "Cập nhật nhân viên \"$ten\" thành công!");
                redirect(admin_url('c=nhan-vien'));
            }
        }

        render_admin_view('nhan-vien/form', [
            'title'   => "Sửa nhân viên #$id",
            'isEdit'  => true,
            'item'    => $item,
            'chucVus' => $chucVus,
            'error'   => $error
        ]);
    }

    public function delete(): void {
        Auth::requirePermission('NHAN_VIEN');
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            NhanVienModel::delete($id);
            set_flash('success', 'Đã xóa nhân viên.');
        }
        redirect(admin_url('c=nhan-vien'));
    }
}
