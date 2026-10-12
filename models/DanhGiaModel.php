<?php
// models/DanhGiaModel.php

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/DatPhongModel.php';

class DanhGiaModel extends BaseModel {
    /**
     * Lấy toàn bộ đánh giá kèm thông tin phòng và khách hàng
     */
    public static function getAll(): array {
        $db = self::db();
        $sql = "
            SELECT dg.*, kh.HoTen as TenKhachHang, kh.Email, p.MaLoaiPhong, lp.TenLoaiPhong
            FROM CTDANHGIA dg
            LEFT JOIN PHONG p ON dg.MaPhong = p.MaPhong
            LEFT JOIN LOAIPHONG lp ON p.MaLoaiPhong = lp.MaLoaiPhong
            LEFT JOIN KHACHHANG kh ON dg.MaKhachHang = kh.MaKhachHang
            ORDER BY dg.NgayDanhGia DESC, dg.ID DESC
        ";
        return $db->query($sql)->fetchAll();
    }

    /**
     * Lấy đánh giá theo ID
     */
    public static function getById(int $id): ?array {
        $db = self::db();
        $stmt = $db->prepare("
            SELECT dg.*, kh.HoTen as TenKhachHang, kh.Email
            FROM CTDANHGIA dg
            LEFT JOIN KHACHHANG kh ON dg.MaKhachHang = kh.MaKhachHang
            WHERE dg.ID = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Thêm mới đánh giá
     */
    public static function create(string $noiDung, ?string $ngayDanhGia, int $maPhong, int $soSao = 5, ?int $maKhachHang = null, ?int $maCTDatPhong = null): int {
        return db_transaction(function (PDO $db) use ($noiDung, $ngayDanhGia, $maPhong, $soSao, $maKhachHang, $maCTDatPhong): int {
        $ngay = $ngayDanhGia ?: date('Y-m-d');
        $soSao = max(1, min(5, $soSao));
        if (trim($noiDung) === '' || mb_strlen($noiDung) > 255 || !valid_date($ngay)) throw new DomainException('Đánh giá cần nội dung tối đa 255 ký tự và ngày hợp lệ.');
        if ($maCTDatPhong !== null) {
            $booking = DatPhongModel::lockBooking($maCTDatPhong);
            if ($booking['TrangThai'] !== 'DA_HOAN_THANH' || (int)$booking['MaKhachHang'] !== $maKhachHang || (int)$booking['MaPhong'] !== $maPhong) throw new DomainException('Chỉ đánh giá lần lưu trú đã hoàn tất của chính bạn.');
            $check = $db->prepare('SELECT COUNT(*) FROM CTDANHGIA WHERE MaCTDatPhong = ?');
            $check->execute([$maCTDatPhong]);
            if ($check->fetchColumn()) throw new DomainException('Bạn đã đánh giá lần lưu trú này.');
        }

        $stmt = $db->prepare("
            INSERT INTO CTDANHGIA (NoiDung, NgayDanhGia, MaPhong, MaKhachHang, MaCTDatPhong, SoSao)
            VALUES (:noiDung, :ngay, :maPhong, :maKh, :maCt, :soSao)
        ");
        $stmt->execute([
            ':noiDung' => $noiDung,
            ':ngay'    => $ngay,
            ':maPhong' => $maPhong,
            ':maKh'    => $maKhachHang,
            ':maCt'    => $maCTDatPhong,
            ':soSao'   => $soSao
        ]);
        return (int)$db->lastInsertId();
        });
    }

    /**
     * Cập nhật đánh giá
     */
    public static function update(int $id, string $noiDung, ?string $ngayDanhGia, int $maPhong, int $soSao = 5): bool {
        $db = self::db();
        $ngay = $ngayDanhGia ?: date('Y-m-d');
        $soSao = max(1, min(5, $soSao));
        if (trim($noiDung) === '' || mb_strlen($noiDung) > 255 || !valid_date($ngay)) throw new DomainException('Đánh giá cần nội dung tối đa 255 ký tự và ngày hợp lệ.');
        $existing = self::getById($id);
        if ($existing && $existing['MaCTDatPhong'] && (int)$existing['MaPhong'] !== $maPhong) throw new DomainException('Không thể chuyển đánh giá của khách sang phòng khác.');

        $stmt = $db->prepare("
            UPDATE CTDANHGIA 
            SET NoiDung = :noiDung, NgayDanhGia = :ngay, MaPhong = :maPhong, SoSao = :soSao
            WHERE ID = :id
        ");
        return $stmt->execute([
            ':noiDung' => $noiDung,
            ':ngay'    => $ngay,
            ':maPhong' => $maPhong,
            ':soSao'   => $soSao,
            ':id'      => $id
        ]);
    }

    /**
     * Xóa đánh giá
     */
    public static function delete(int $id): bool {
        $db = self::db();
        $stmt = $db->prepare("DELETE FROM CTDANHGIA WHERE ID = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Lấy các phòng khách hàng có thể đánh giá (đã đặt phòng và ngày trả <= hôm nay)
     */
    public static function getReviewableRoomsByCustomer(int $maKhachHang): array {
        $db = self::db();
        $today = date('Y-m-d');
        $sql = "
            SELECT DISTINCT p.MaPhong, lp.TenLoaiPhong
            FROM CTDATPHONG c
            JOIN PHONG p ON c.MaPhong = p.MaPhong
            LEFT JOIN LOAIPHONG lp ON p.MaLoaiPhong = lp.MaLoaiPhong
            WHERE c.MaKhachHang = :khId
              AND c.NgayTra <= :today
              AND c.TrangThai = 'DA_HOAN_THANH'
              AND NOT EXISTS (SELECT 1 FROM CTDANHGIA dg WHERE dg.MaCTDatPhong = c.MaCTDatPhong)
            ORDER BY p.MaPhong ASC
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([':khId' => $maKhachHang, ':today' => $today]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy đơn đặt phòng hợp lệ gần nhất để gắn vào đánh giá
     */
    public static function getReviewableBooking(int $maKhachHang, int $maPhong): ?array {
        $db = self::db();
        $today = date('Y-m-d');
        $sql = "
            SELECT c.* FROM CTDATPHONG c
            WHERE MaKhachHang = :khId 
              AND MaPhong = :maPhong 
              AND NgayTra <= :today
              AND c.TrangThai = 'DA_HOAN_THANH'
              AND NOT EXISTS (SELECT 1 FROM CTDANHGIA dg WHERE dg.MaCTDatPhong = c.MaCTDatPhong)
            ORDER BY MaCTDatPhong DESC 
            LIMIT 1
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([':khId' => $maKhachHang, ':maPhong' => $maPhong, ':today' => $today]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
