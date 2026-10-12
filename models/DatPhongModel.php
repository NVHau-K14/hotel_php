<?php
// models/DatPhongModel.php

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/PhongModel.php';

class DatPhongModel extends BaseModel {
    public static function getAll(?string $trangThai = null, ?string $keyword = null): array {
        $db = self::db();
        $sql = "
            SELECT 
                cdp.*,
                kh.HoTen AS TenKhachHang, kh.DienThoai AS SdtKhachHang, kh.Email AS EmailKhachHang,
                p.MaPhong, lp.TenLoaiPhong, COALESCE(cdp.GiaPhongDat, lp.GiaPhong) AS GiaPhong,
                nv.TenNhanVien
            FROM CTDATPHONG cdp
            LEFT JOIN KHACHHANG kh ON cdp.MaKhachHang = kh.MaKhachHang
            LEFT JOIN PHONG p ON cdp.MaPhong = p.MaPhong
            LEFT JOIN LOAIPHONG lp ON p.MaLoaiPhong = lp.MaLoaiPhong
            LEFT JOIN NHANVIEN nv ON cdp.MaNhanVien = nv.MaNhanVien
            WHERE 1=1
        ";
        $params = [];

        if ($trangThai !== null && $trangThai !== '' && $trangThai !== 'ALL') {
            $sql .= " AND cdp.TrangThai = :trangThai";
            $params[':trangThai'] = $trangThai;
        }

        if ($keyword !== null && trim($keyword) !== '') {
            $sql .= " AND (cdp.MaCTDatPhong LIKE :kw0 OR kh.HoTen LIKE :kw1 OR kh.DienThoai LIKE :kw2 OR p.MaPhong LIKE :kw3)";
            $params[':kw0'] = '%' . trim($keyword) . '%';
            $params[':kw1'] = '%' . trim($keyword) . '%';
            $params[':kw2'] = '%' . trim($keyword) . '%';
            $params[':kw3'] = '%' . trim($keyword) . '%';
        }

        $sql .= " ORDER BY cdp.MaCTDatPhong DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function getById(int $id): ?array {
        $db = self::db();
        $stmt = $db->prepare("
            SELECT 
                cdp.*,
                kh.HoTen AS TenKhachHang, kh.DienThoai AS SdtKhachHang, kh.Email AS EmailKhachHang, kh.CMND, kh.DiaChi,
                p.MaPhong, lp.TenLoaiPhong, COALESCE(cdp.GiaPhongDat, lp.GiaPhong) AS GiaPhong,
                nv.TenNhanVien
            FROM CTDATPHONG cdp
            LEFT JOIN KHACHHANG kh ON cdp.MaKhachHang = kh.MaKhachHang
            LEFT JOIN PHONG p ON cdp.MaPhong = p.MaPhong
            LEFT JOIN LOAIPHONG lp ON p.MaLoaiPhong = lp.MaLoaiPhong
            LEFT JOIN NHANVIEN nv ON cdp.MaNhanVien = nv.MaNhanVien
            WHERE cdp.MaCTDatPhong = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function getByKhachHang(int $maKhachHang): array {
        $db = self::db();
        $stmt = $db->prepare("
            SELECT 
                cdp.*,
                p.MaPhong, lp.TenLoaiPhong, COALESCE(cdp.GiaPhongDat, lp.GiaPhong) AS GiaPhong, p.HinhAnhDaiDien
            FROM CTDATPHONG cdp
            LEFT JOIN PHONG p ON cdp.MaPhong = p.MaPhong
            LEFT JOIN LOAIPHONG lp ON p.MaLoaiPhong = lp.MaLoaiPhong
            WHERE cdp.MaKhachHang = :mkh
            ORDER BY cdp.MaCTDatPhong DESC
        ");
        $stmt->execute([':mkh' => $maKhachHang]);
        return $stmt->fetchAll();
    }

    public static function createBooking(
        int $maKhachHang,
        int $maPhong,
        string $ngayNhan,
        string $ngayTra,
        ?int $maNhanVien = null,
        string $trangThai = 'CHO_ADMIN_DUYET'
    ): int {
        validate_stay($ngayNhan, $ngayTra);
        if (!in_array($trangThai, ['CHO_ADMIN_DUYET', 'DA_XAC_NHAN', 'DANG_SU_DUNG'], true)) throw new DomainException('Trạng thái đặt phòng không hợp lệ.');
        if ($trangThai === 'DANG_SU_DUNG' && $ngayNhan !== date('Y-m-d')) throw new DomainException('Chỉ nhận phòng trong ngày bắt đầu lưu trú.');
        return db_transaction(function (PDO $db) use ($maKhachHang, $maPhong, $ngayNhan, $ngayTra, $maNhanVien, $trangThai): int {
        $room = self::lockRoom($maPhong);
        self::assertAvailable($maPhong, $ngayNhan, $ngayTra);
        $customer = $db->prepare("SELECT TrangThai FROM KHACHHANG WHERE MaKhachHang = ?");
        $customer->execute([$maKhachHang]);
        $status = $customer->fetchColumn();
        if ($status === false || $status === 'KHOA') throw new DomainException('Khách hàng không tồn tại hoặc đã bị khóa.');
        $stmt = $db->prepare("
            INSERT INTO CTDATPHONG (
                NgayThucHien, MaKhachHang, NgayNhan, NgayTra,
                MaPhong, MaNhanVien, TrangThai, payment_status, GiaPhongDat
            ) VALUES (
                CURDATE(), :mkh, :nn, :nt,
                :mp, :mnv, :tt, 'CHUA_TAO', :gia
            )
        ");
        $stmt->execute([
            ':mkh' => $maKhachHang,
            ':nn'  => $ngayNhan,
            ':nt'  => $ngayTra,
            ':mp'  => $maPhong,
            ':mnv' => $maNhanVien,
            ':tt'  => $trangThai,
            ':gia' => $room['GiaPhong']
        ]);
        $id = (int)$db->lastInsertId();
        self::syncRoomStatus($maPhong);
        return $id;
        });
    }

    public static function updateStatus(int $id, string $newStatus, ?int $maNhanVien = null): bool {
        return db_transaction(function (PDO $db) use ($id, $newStatus, $maNhanVien): bool {
            $booking = self::lockBooking($id);
            if ($booking['TrangThai'] === $newStatus) return true;
            $allowed = ['CHO_ADMIN_DUYET' => ['DA_XAC_NHAN', 'DA_HUY'], 'DA_XAC_NHAN' => ['DANG_SU_DUNG', 'DA_HUY'], 'DANG_SU_DUNG' => ['DA_HOAN_THANH']];
            if (!in_array($newStatus, $allowed[$booking['TrangThai']] ?? [], true)) throw new DomainException('Không thể chuyển trạng thái của đơn đặt phòng này.');
            if (in_array($newStatus, ['DA_XAC_NHAN', 'DANG_SU_DUNG'], true)) {
                validate_stay($booking['NgayNhan'], $booking['NgayTra'], true);
                if ($booking['NgayTra'] <= date('Y-m-d')) throw new DomainException('Đơn đặt phòng đã quá ngày trả.');
                self::assertAvailable((int)$booking['MaPhong'], $booking['NgayNhan'], $booking['NgayTra'], $id);
            }
            if ($newStatus === 'DANG_SU_DUNG') {
                if ($booking['NgayNhan'] > date('Y-m-d')) throw new DomainException('Chưa đến ngày nhận phòng.');
                $check = $db->prepare("SELECT COUNT(*) FROM CTDATPHONG WHERE MaPhong = ? AND TrangThai = 'DANG_SU_DUNG' AND MaCTDatPhong <> ?");
                $check->execute([$booking['MaPhong'], $id]);
                if ($check->fetchColumn()) throw new DomainException('Phòng còn khách chưa trả phòng.');
            }
            if ($newStatus === 'DA_HOAN_THANH') {
                $check = $db->prepare("SELECT COUNT(*) FROM HOADON WHERE MaCTDatPhong = ? AND TrangThai = 'DA_THANH_TOAN'");
                $check->execute([$id]);
                if (!$check->fetchColumn()) throw new DomainException('Cần thanh toán hóa đơn trước khi hoàn tất đơn.');
            }
            $stmt = $db->prepare('UPDATE CTDATPHONG SET TrangThai = ?, MaNhanVien = COALESCE(?, MaNhanVien) WHERE MaCTDatPhong = ?');
            $stmt->execute([$newStatus, $maNhanVien, $id]);
            self::syncRoomStatus((int)$booking['MaPhong']);
            return true;
        });
    }

    public static function lockRoom(int $id): array {
        $stmt = self::db()->prepare('SELECT p.*, lp.GiaPhong, lp.DaXoa FROM PHONG p JOIN LOAIPHONG lp ON lp.MaLoaiPhong = p.MaLoaiPhong WHERE p.MaPhong = ? FOR UPDATE');
        $stmt->execute([$id]);
        $room = $stmt->fetch();
        if (!$room) throw new DomainException('Phòng không tồn tại.');
        return $room;
    }

    public static function lockBooking(int $id): array {
        $booking = self::getById($id);
        if (!$booking) throw new DomainException('Đơn đặt phòng không tồn tại.');
        self::lockRoom((int)$booking['MaPhong']);
        $stmt = self::db()->prepare('SELECT * FROM CTDATPHONG WHERE MaCTDatPhong = ? FOR UPDATE');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function assertAvailable(int $roomId, string $arrival, string $departure, int $exceptId = 0): void {
        $room = self::lockRoom($roomId);
        if ($room['TrangThai'] === 'Bảo trì' || $room['DaXoa']) throw new DomainException('Phòng đang bảo trì hoặc loại phòng đã ngừng sử dụng.');
        $stmt = self::db()->prepare("SELECT MaCTDatPhong FROM CTDATPHONG WHERE MaPhong = ? AND MaCTDatPhong <> ? AND TrangThai IN ('CHO_ADMIN_DUYET', 'DA_XAC_NHAN', 'DANG_SU_DUNG') AND NgayNhan < ? AND NgayTra > ? LIMIT 1 FOR UPDATE");
        $stmt->execute([$roomId, $exceptId, $departure, $arrival]);
        if ($stmt->fetchColumn()) throw new DomainException('Phòng đã có lịch đặt trong khoảng ngày này. Vui lòng chọn ngày hoặc phòng khác.');
    }

    public static function assertServiceEditable(int $id): array {
        $booking = self::lockBooking($id);
        if (!in_array($booking['TrangThai'], ['DA_XAC_NHAN', 'DANG_SU_DUNG'], true)) throw new DomainException('Chỉ thêm hoặc sửa dịch vụ cho đơn đã xác nhận hoặc đang sử dụng.');
        $stmt = self::db()->prepare("SELECT COUNT(*) FROM HOADON WHERE MaCTDatPhong = ? AND TrangThai = 'DA_THANH_TOAN'");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn()) throw new DomainException('Không được sửa dịch vụ của hóa đơn đã thanh toán.');
        return $booking;
    }

    public static function syncRoomStatus(int $id): void {
        $stmt = self::db()->prepare("SELECT CASE WHEN EXISTS (SELECT 1 FROM CTDATPHONG WHERE MaPhong = ? AND TrangThai = 'DANG_SU_DUNG') THEN 'Đang thuê' WHEN EXISTS (SELECT 1 FROM CTDATPHONG WHERE MaPhong = ? AND TrangThai = 'DA_XAC_NHAN' AND NgayTra > CURDATE()) THEN 'Đã đặt' ELSE 'Trống' END");
        $stmt->execute([$id, $id]);
        $status = $stmt->fetchColumn();
        self::db()->prepare("UPDATE PHONG SET TrangThai = ? WHERE MaPhong = ? AND (TrangThai IS NULL OR TrangThai <> 'Bảo trì')")->execute([$status, $id]);
    }

    public static function countByStatus(): array {
        $db = self::db();
        $sql = "
            SELECT 
                SUM(CASE WHEN TrangThai = 'CHO_ADMIN_DUYET' THEN 1 ELSE 0 END) AS choDuyet,
                SUM(CASE WHEN TrangThai = 'DA_XAC_NHAN' THEN 1 ELSE 0 END) AS daXacNhan,
                SUM(CASE WHEN TrangThai = 'DANG_SU_DUNG' THEN 1 ELSE 0 END) AS dangSuDung,
                SUM(CASE WHEN TrangThai = 'DA_HOAN_THANH' THEN 1 ELSE 0 END) AS daHoanThanh,
                SUM(CASE WHEN TrangThai = 'DA_HUY' THEN 1 ELSE 0 END) AS daHuy,
                COUNT(*) AS tongDon
            FROM CTDATPHONG
        ";
        return $db->query($sql)->fetch() ?: [];
    }
}
