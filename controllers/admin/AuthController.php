<?php
// controllers/admin/AuthController.php

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../config/database.php';

class AuthController {
    public function login(): void {
        if (Auth::isAdminLoggedIn()) {
            redirect(admin_url('c=dashboard'));
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $error = 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu.';
            } else {
                if (Auth::attemptAdminLogin($username, $password)) {
                    set_flash('success', 'Đăng nhập quản trị thành công! Chào mừng bạn trở lại.');
                    redirect(admin_url('c=dashboard'));
                } else {
                    $error = 'Tài khoản hoặc mật khẩu không chính xác.';
                }
            }
        }

        render_admin_view('auth/login', [
            'title'   => 'Đăng nhập Quản trị',
            'hideNav' => true,
            'error'   => $error
        ]);
    }

    public function changePassword(): void {
        Auth::requireAdminLogin();
        $user = Auth::currentAdmin();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $matKhauCu       = $_POST['matKhauCu'] ?? '';
            $matKhauMoi      = $_POST['matKhauMoi'] ?? '';
            $xacNhanMatKhau  = $_POST['xacNhanMatKhau'] ?? '';

            $db = getDB();
            $stmt = $db->prepare("SELECT Password FROM NHANVIEN WHERE MaNhanVien = :id LIMIT 1");
            $stmt->execute([':id' => $user['id']]);
            $currentPass = $stmt->fetchColumn();

            if (!verify_stored_password($matKhauCu, (string)$currentPass)) {
                $error = 'Mật khẩu cũ không chính xác.';
            } elseif (empty($matKhauMoi) || strlen($matKhauMoi) < 1) {
                $error = 'Vui lòng nhập mật khẩu mới.';
            } elseif ($matKhauMoi !== $xacNhanMatKhau) {
                $error = 'Xác nhận mật khẩu mới không khớp.';
            } elseif ($matKhauMoi === $matKhauCu) {
                $error = 'Mật khẩu mới phải khác mật khẩu hiện tại.';
            } else {
                $stmtUpdate = $db->prepare("UPDATE NHANVIEN SET Password = :pass WHERE MaNhanVien = :id");
                $stmtUpdate->execute([':pass' => password_hash($matKhauMoi, PASSWORD_DEFAULT), ':id' => $user['id']]);
                session_regenerate_id(true);

                set_flash('success', 'Đổi mật khẩu thành công!');
                redirect(admin_url('c=dashboard'));
            }
        }

        render_admin_view('auth/change-password', [
            'title' => 'Đổi mật khẩu quản trị',
            'error' => $error,
            'user'  => $user
        ]);
    }

    public function logout(): void {
        Auth::adminLogout();
        set_flash('success', 'Đã đăng xuất thành công.');
        redirect(admin_url('c=auth&a=login'));
    }
}
