<?php
// controllers/client/RoomController.php

require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../models/PhongModel.php';
require_once __DIR__ . '/../../models/LoaiPhongModel.php';

class RoomController {
    public function list(): void {
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
        $loaiPhongs = LoaiPhongModel::getAll();

        render_client_view('rooms', [
            'title'       => 'Danh sách phòng nghỉ',
            'items'       => $items,
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

    public function detail(): void {
        $id = (int)($_GET['id'] ?? 0);
        $room = PhongModel::getById($id);
        if (!$room) {
            set_flash('danger', 'Không tìm thấy thông tin phòng.');
            redirect(client_url('c=room'));
        }

        render_client_view('room-detail', [
            'title' => "Chi tiết phòng {$room['MaPhong']} - {$room['TenLoaiPhong']}",
            'room'  => $room
        ]);
    }
}
