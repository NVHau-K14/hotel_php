<?php
// controllers/client/HomeController.php

require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../models/PhongModel.php';
require_once __DIR__ . '/../../models/DichVuModel.php';
require_once __DIR__ . '/../../models/LoaiPhongModel.php';

class HomeController {
    public function index(): void {
        // Lấy danh sách một số phòng nổi bật và dịch vụ
        $allRooms = PhongModel::getAll();
        $phongs = array_slice($allRooms, 0, 6);
        $dichVus = DichVuModel::getAll();

        render_client_view('home', [
            'title'   => 'Trang chủ - Khách sạn Nghỉ dưỡng Vip',
            'phongs'  => $phongs,
            'dichVus' => $dichVus
        ]);
    }

    public function contact(): void {
        render_client_view('contact', [
            'title' => 'Liên hệ - Khách sạn Nghỉ dưỡng Vip'
        ]);
    }
}
