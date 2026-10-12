<?php
// models/HoaDonModel.php

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/DatPhongModel.php';
require_once __DIR__ . '/DichVuModel.php';

class HoaDonModel extends BaseModel {
    public static function getAll(): array {
        $db = self::db();
        $sql = "
            SELECT 
                hd.*,
                kh.HoTen AS TenKhachHang, kh.DienThoai AS SdtKhachHang,
                p.MaPhong, lp.TenLoaiPhong,
                nv.TenNhanVien,
                cdp.NgayNhan, cdp.NgayTra
            FROM HOADON hd
            LEFT JOIN CTDATPHONG cdp ON hd.MaCTDatPhong = cdp.MaCTDatPhong
            LEFT JOIN KHACHHANG kh ON cdp.MaKhachHang = kh.MaKhachHang
            LEFT JOIN PHONG p ON cdp.MaPhong = p.MaPhong
            LEFT JOIN LOAIPHONG lp ON p.MaLoaiPhong = lp.MaLoaiPhong
            LEFT JOIN NHANVIEN nv ON hd.MaNhanVien = nv.MaNhanVien
            ORDER BY hd.MaHoaDon DESC
        ";
        return $db->query($sql)->fetchAll();
    }

    public static function getById(int $id): ?array {
        $db = self::db();
        $stmt = $db->prepare("
            SELECT 
                hd.*,
                kh.HoTen AS TenKhachHang, kh.DienThoai AS SdtKhachHang, kh.Email AS EmailKhachHang, kh.CMND, kh.DiaChi,
                p.MaPhong, lp.TenLoaiPhong, lp.GiaPhong,
                nv.TenNhanVien,
                cdp.NgayNhan, cdp.NgayTra, cdp.NgayThucHien, cdp.TrangThai AS TrangThaiDatPhong
            FROM HOADON hd
            LEFT JOIN CTDATPHONG cdp ON hd.MaCTDatPhong = cdp.MaCTDatPhong
            LEFT JOIN KHACHHANG kh ON cdp.MaKhachHang = kh.MaKhachHang
            LEFT JOIN PHONG p ON cdp.MaPhong = p.MaPhong
            LEFT JOIN LOAIPHONG lp ON p.MaLoaiPhong = lp.MaLoaiPhong
            LEFT JOIN NHANVIEN nv ON hd.MaNhanVien = nv.MaNhanVien
            WHERE hd.MaHoaDon = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function getByBookingId(int $maCTDatPhong): ?array {
        $db = self::db();
        $stmt = $db->prepare("SELECT * FROM HOADON WHERE MaCTDatPhong = :bid ORDER BY (TrangThai = 'DA_THANH_TOAN') DESC, MaHoaDon DESC LIMIT 1");
        $stmt->execute([':bid' => $maCTDatPhong]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Tính toán chi tiết hóa đơn (tiền phòng, ngày ở, tiền dịch vụ, tổng cộng)
     */
    public static function calculateBookingTotal(int $maCTDatPhong): array {
        $booking = DatPhongModel::getById($maCTDatPhong);
        if (!$booking) return [];
        $invoice = self::getByBookingId($maCTDatPhong);
        if ($invoice && $invoice['TrangThai'] === 'DA_THANH_TOAN' && !empty($invoice['ChiTietJSON'])) {
            $saved = json_decode($invoice['ChiTietJSON'], true);
            if (is_array($saved)) return $saved + ['booking' => $booking];
        }
        validate_stay($booking['NgayNhan'], $booking['NgayTra'], true);

        $d1 = new DateTime($booking['NgayNhan']);
        $d2 = new DateTime($booking['NgayTra']);
        $diff = $d1->diff($d2);
        $days = max(1, $diff->days);

        $roomRate = (int)($booking['GiaPhong'] ?? 0);
        $roomTotal = $days * $roomRate;

        $services = DichVuModel::getServicesByBooking($maCTDatPhong);
        $serviceTotal = 0;
        foreach ($services as $s) {
            $serviceTotal += (int)($s['TongTienDichVu'] ?? 0);
        }

        $grandTotal = $roomTotal + $serviceTotal;

        return [
            'booking'      => $booking,
            'days'         => $days,
            'roomRate'     => $roomRate,
            'roomTotal'    => $roomTotal,
            'services'     => $services,
            'serviceTotal' => $serviceTotal,
            'grandTotal'   => $grandTotal
        ];
    }

    public static function createOrUpdateInvoice(int $maCTDatPhong, ?int $maNhanVien = null): int {
        return db_transaction(function (PDO $db) use ($maCTDatPhong, $maNhanVien): int {
        $booking = DatPhongModel::lockBooking($maCTDatPhong);
        $existing = self::getByBookingId($maCTDatPhong);
        if ($existing && $existing['TrangThai'] === 'DA_THANH_TOAN') return (int)$existing['MaHoaDon'];
        if (!in_array($booking['TrangThai'], ['DANG_SU_DUNG', 'DA_XAC_NHAN'], true)) throw new DomainException('Không thể lập hóa đơn cho đơn chưa xác nhận, đã hủy hoặc đã kết thúc.');
        $calc = self::calculateBookingTotal($maCTDatPhong);
        $total = $calc['grandTotal'] ?? 0;
        if ($total > 2147483647) throw new DomainException('Tổng hóa đơn vượt giới hạn cho phép.');
        unset($calc['booking']);
        $snapshot = json_encode($calc, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        if ($existing) {
            $stmt = $db->prepare("UPDATE HOADON SET SoTienThu = :tien, ChiTietJSON = :snapshot, MaNhanVien = COALESCE(:mnv, MaNhanVien) WHERE MaHoaDon = :id");
            $stmt->execute([':tien' => $total, ':snapshot' => $snapshot, ':mnv' => $maNhanVien, ':id' => $existing['MaHoaDon']]);
            return (int)$existing['MaHoaDon'];
        } else {
            $stmt = $db->prepare("INSERT INTO HOADON (NgayThuTien, SoTienThu, MaCTDatPhong, MaNhanVien, TrangThai, ChiTietJSON) VALUES (CURDATE(), :tien, :bid, :mnv, 'CHUA_THANH_TOAN', :snapshot)");
            $stmt->execute([':tien' => $total, ':bid' => $maCTDatPhong, ':mnv' => $maNhanVien, ':snapshot' => $snapshot]);
            return (int)$db->lastInsertId();
        }
        });
    }

    public static function payInvoice(int $maHoaDon): bool {
        return db_transaction(function (PDO $db) use ($maHoaDon): bool {
        $invoice = self::getById($maHoaDon);
        if (!$invoice) throw new DomainException('Hóa đơn không tồn tại.');
        $booking = DatPhongModel::lockBooking((int)$invoice['MaCTDatPhong']);
        $invoice = self::getById($maHoaDon);
        if ($invoice['TrangThai'] === 'DA_THANH_TOAN') return true;
        $other = $db->prepare("SELECT COUNT(*) FROM HOADON WHERE MaCTDatPhong = ? AND TrangThai = 'DA_THANH_TOAN' AND MaHoaDon <> ?");
        $other->execute([$invoice['MaCTDatPhong'], $maHoaDon]);
        if ($other->fetchColumn()) throw new DomainException('Đơn này đã được thanh toán bằng hóa đơn khác.');
        if ($booking['TrangThai'] !== 'DANG_SU_DUNG') throw new DomainException('Chỉ thanh toán trả phòng cho đơn đang sử dụng.');
        $calc = self::calculateBookingTotal((int)$invoice['MaCTDatPhong']);
        unset($calc['booking']);
        if ($calc['grandTotal'] > 2147483647) throw new DomainException('Tổng hóa đơn vượt giới hạn cho phép.');
        $stmt = $db->prepare("UPDATE HOADON SET TrangThai = 'DA_THANH_TOAN', NgayThuTien = CURDATE(), SoTienThu = ?, ChiTietJSON = ? WHERE MaHoaDon = ?");
        $res = $stmt->execute([$calc['grandTotal'], json_encode($calc, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR), $maHoaDon]);
        $db->prepare("UPDATE CTDATPHONG SET payment_status = 'DA_THANH_TOAN' WHERE MaCTDatPhong = ?")->execute([$invoice['MaCTDatPhong']]);

        // Cập nhật đơn đặt phòng sang DA_HOAN_THANH
        if ($invoice['MaCTDatPhong']) {
            DatPhongModel::updateStatus($invoice['MaCTDatPhong'], 'DA_HOAN_THANH');
        }

        return $res;
        });
    }
}
