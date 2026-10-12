<?php
// controllers/client/ReviewController.php

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../models/DanhGiaModel.php';
require_once __DIR__ . '/../../models/PhongModel.php';

class ReviewController {
    public function list(): void {
        $items = DanhGiaModel::getAll();
        $phongs = [];

        if (Auth::isClientLoggedIn()) {
            $client = Auth::currentClient();
            $phongs = DanhGiaModel::getReviewableRoomsByCustomer($client['id']);
        }

        render_client_view('reviews', [
            'title'  => 'Đánh giá khách hàng',
            'items'  => $items,
            'phongs' => $phongs
        ]);
    }

    public function submit(): void {
        Auth::requireClientLogin();
        $client = Auth::currentClient();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $maPhong = (int)($_POST['maPhong'] ?? 0);
            $soSao   = (int)($_POST['soSao'] ?? 5);
            $noiDung = trim($_POST['noiDung'] ?? '');

            if ($maPhong <= 0) {
                set_flash('danger', 'Vui lòng chọn phòng cần đánh giá.');
                redirect(client_url('c=review'));
            }

            if (empty($noiDung)) {
                set_flash('danger', 'Vui lòng nhập nội dung đánh giá.');
                redirect(client_url('c=review'));
            }

            $booking = DanhGiaModel::getReviewableBooking($client['id'], $maPhong);
            if (!$booking) {
                set_flash('danger', 'Bạn chỉ được đánh giá phòng đã đặt và đã hoàn thành ngày trả phòng.');
                redirect(client_url('c=review'));
            }

            $soSao = max(1, min(5, $soSao));
            DanhGiaModel::create($noiDung, date('Y-m-d'), $maPhong, $soSao, $client['id'], $booking['MaCTDatPhong']);

            set_flash('success', 'Cảm ơn bạn đã gửi đánh giá! Ý kiến của bạn giúp chúng tôi nâng cao chất lượng dịch vụ.');
            redirect(client_url('c=review'));
        }

        redirect(client_url('c=review'));
    }
}
