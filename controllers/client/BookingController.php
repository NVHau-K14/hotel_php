<?php
// controllers/client/BookingController.php

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../models/PhongModel.php';
require_once __DIR__ . '/../../models/DatPhongModel.php';
require_once __DIR__ . '/../../models/KhachHangModel.php';

class BookingController {
    public function create(): void {
        $input = ($_SERVER['REQUEST_METHOD'] === 'POST') ? $_POST : $_GET;
        $maPhong  = (int)($input['maPhong'] ?? 0);
        $ngayNhan = $input['ngayNhan'] ?? date('Y-m-d');
        $ngayTra  = $input['ngayTra'] ?? date('Y-m-d', strtotime('+1 day'));

        $room = PhongModel::getById($maPhong);
        if (!$room) {
            set_flash('danger', 'Vui lòng chọn phòng hợp lệ để đặt.');
            redirect(client_url('c=room'));
        }

        $clientUser = Auth::currentClient();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $hoTen     = trim($_POST['hoTen'] ?? '');
            $dienThoai = trim($_POST['dienThoai'] ?? '');
            $email     = trim($_POST['email'] ?? '');
            $cmnd      = trim($_POST['cmnd'] ?? '');
            $diaChi    = trim($_POST['diaChi'] ?? '');

            if (empty($hoTen) || empty($dienThoai) || empty($ngayNhan) || empty($ngayTra)) {
                $error = 'Vui lòng điền đầy đủ họ tên, số điện thoại, ngày nhận và ngày trả.';
            } else {
                try {
                    validate_stay($ngayNhan, $ngayTra);
                    validate_customer($hoTen, $email, $dienThoai, $diaChi, $cmnd);
                    $bookingId = db_transaction(function () use ($clientUser, $hoTen, $email, $dienThoai, $diaChi, $cmnd, $maPhong, $ngayNhan, $ngayTra): int {
                        DatPhongModel::assertAvailable($maPhong, $ngayNhan, $ngayTra);
                        // A phone/email alone does not prove ownership of an existing account.
                        $guest = !$clientUser && !empty($_SESSION['guest_customer_id']) ? KhachHangModel::getById((int)$_SESSION['guest_customer_id']) : null;
                        if ($guest && !empty($guest['MatKhau'])) $guest = null;
                        $maKhachHang = $clientUser['id'] ?? ($guest['MaKhachHang'] ?? KhachHangModel::create($hoTen, $email, $dienThoai, $diaChi, $cmnd));
                        if ($clientUser || $guest) KhachHangModel::update($maKhachHang, $hoTen, $email, $dienThoai, $diaChi, $cmnd);
                        return DatPhongModel::createBooking($maKhachHang, $maPhong, $ngayNhan, $ngayTra);
                    });
                    if (!$clientUser) {
                        $_SESSION['guest_booking_ids'][$bookingId] = true;
                        $_SESSION['guest_customer_id'] = DatPhongModel::getById($bookingId)['MaKhachHang'];
                    }
                    redirect(client_url('c=booking&a=success&id=' . $bookingId));
                } catch (DomainException $exception) {
                    $error = $exception->getMessage();
                }
            }
        }

        render_client_view('booking', [
            'title'      => "Đặt phòng {$room['MaPhong']}",
            'room'       => $room,
            'clientUser' => $clientUser,
            'ngayNhan'   => $ngayNhan,
            'ngayTra'    => $ngayTra,
            'error'      => $error
        ]);
    }

    public function success(): void {
        $id = (int)($_GET['id'] ?? 0);
        $booking = DatPhongModel::getById($id);
        $client = Auth::currentClient();
        if (!$booking || (empty($_SESSION['guest_booking_ids'][$id]) && (!$client || (int)$booking['MaKhachHang'] !== (int)$client['id']))) {
            redirect(client_url());
        }

        render_client_view('booking-success', [
            'title'   => 'Đặt phòng thành công',
            'booking' => $booking
        ]);
    }
}
