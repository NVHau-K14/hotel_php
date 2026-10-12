<?php
// controllers/client/AccountController.php

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../models/KhachHangModel.php';
require_once __DIR__ . '/../../models/DatPhongModel.php';
require_once __DIR__ . '/../../models/DichVuModel.php';
require_once __DIR__ . '/../../models/HoaDonModel.php';

class AccountController {
    public function login(): void {
        if (Auth::isClientLoggedIn()) {
            redirect(client_url('c=account&a=history'));
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $account  = trim($_POST['account'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($account) || empty($password)) {
                $error = 'Vui lòng nhập số điện thoại/email và mật khẩu.';
            } else {
                if (Auth::attemptClientLogin($account, $password)) {
                    set_flash('success', 'Đăng nhập thành công! Chào mừng quý khách.');
                    redirect(client_url());
                } else {
                    $error = 'Thông tin đăng nhập hoặc mật khẩu không chính xác.';
                }
            }
        }

        render_client_view('account/login', [
            'title' => 'Đăng nhập khách hàng',
            'error' => $error
        ]);
    }

    public function register(): void {
        if (Auth::isClientLoggedIn()) {
            redirect(client_url());
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $hoTen     = trim($_POST['hoTen'] ?? '');
            $dienThoai = trim($_POST['dienThoai'] ?? '');
            $email     = trim($_POST['email'] ?? '');
            $password  = $_POST['password'] ?? '';
            $cmnd      = trim($_POST['cmnd'] ?? '');
            $diaChi    = trim($_POST['diaChi'] ?? '');

            if (empty($hoTen) || empty($dienThoai) || empty($password)) {
                $error = 'Vui lòng điền họ tên, số điện thoại và mật khẩu.';
            } else {
                $db = getDB();
                $check = $db->prepare("SELECT COUNT(*) FROM KHACHHANG WHERE DienThoai = :sdt");
                $check->execute([':sdt' => $dienThoai]);
                if ($check->fetchColumn() > 0) {
                    $error = 'Số điện thoại này đã được đăng ký tài khoản.';
                } else {
                    try {
                        KhachHangModel::create($hoTen, $email, $dienThoai, $diaChi, $cmnd, $password);
                        Auth::attemptClientLogin($dienThoai, $password);
                        set_flash('success', 'Đăng ký tài khoản thành công!');
                        redirect(client_url());
                    } catch (DomainException $exception) {
                        $error = $exception->getMessage();
                    }
                }
            }
        }

        render_client_view('account/register', [
            'title' => 'Đăng ký tài khoản khách hàng',
            'error' => $error
        ]);
    }

    public function profile(): void {
        Auth::requireClientLogin();
        $clientSession = Auth::currentClient();
        $client = KhachHangModel::getById($clientSession['id']) ?: $clientSession;

        render_client_view('account/profile', [
            'title'     => 'Tài khoản của tôi',
            'khachHang' => $client
        ]);
    }

    public function history(): void {
        Auth::requireClientLogin();
        $client = Auth::currentClient();
        $bookings = DatPhongModel::getByKhachHang($client['id']);

        render_client_view('account/history', [
            'title'    => 'Lịch sử đặt phòng của tôi',
            'client'   => $client,
            'bookings' => $bookings
        ]);
    }

    public function detail(): void {
        Auth::requireClientLogin();
        $client = Auth::currentClient();
        $id = (int)($_GET['id'] ?? 0);

        $datPhong = DatPhongModel::getById($id);
        if (!$datPhong || $datPhong['MaKhachHang'] != $client['id']) {
            set_flash('danger', 'Không tìm thấy đơn đặt phòng hoặc bạn không có quyền truy cập.');
            redirect(client_url('c=account&a=history'));
        }

        $dichVuDaDung = DichVuModel::getServicesByBooking($id);
        $calc = HoaDonModel::calculateBookingTotal($id);

        render_client_view('account/booking-detail', [
            'title'          => "Chi tiết đặt phòng #$id",
            'datPhong'       => $datPhong,
            'dichVuDaDung'   => $dichVuDaDung,
            'calc'           => $calc
        ]);
    }

    public function logout(): void {
        Auth::clientLogout();
        set_flash('success', 'Đã đăng xuất tài khoản.');
        redirect(client_url());
    }
}
