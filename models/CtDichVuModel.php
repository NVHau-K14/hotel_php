<?php
require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/DatPhongModel.php';

class CtDichVuModel extends BaseModel {
    public static function getAll(): array {
        return self::db()->query('SELECT ctdv.*, dv.TenDichVu, COALESCE(ctdv.TongTienDichVu / NULLIF(ctdv.SoLuong, 0), dv.GiaDichVu) AS GiaDichVu, dp.MaPhong, dp.NgayNhan, dp.NgayTra FROM CTDICHVU ctdv LEFT JOIN DICHVU dv ON ctdv.MaDichVu = dv.MaDichVu LEFT JOIN CTDATPHONG dp ON ctdv.MaCTDatPhong = dp.MaCTDatPhong ORDER BY ctdv.MaCTDatPhong DESC, ctdv.MaDichVu ASC')->fetchAll();
    }

    public static function getById(int $maDichVu, int $maCTDatPhong): ?array {
        $stmt = self::db()->prepare('SELECT ctdv.*, dv.TenDichVu, COALESCE(ctdv.TongTienDichVu / NULLIF(ctdv.SoLuong, 0), dv.GiaDichVu) AS GiaDichVu FROM CTDICHVU ctdv JOIN DICHVU dv ON dv.MaDichVu = ctdv.MaDichVu WHERE ctdv.MaDichVu = ? AND ctdv.MaCTDatPhong = ?');
        $stmt->execute([$maDichVu, $maCTDatPhong]);
        return $stmt->fetch() ?: null;
    }

    public static function save(int $maDichVu, int $maCTDatPhong, int $soLuong, ?int $tongTien = null, ?int $maCTDichVu = null): bool {
        return db_transaction(function (PDO $db) use ($maDichVu, $maCTDatPhong, $soLuong, $maCTDichVu): bool {
            DatPhongModel::assertServiceEditable($maCTDatPhong);
            if ($soLuong < 1 || $soLuong > 10000) throw new DomainException('Số lượng dịch vụ phải từ 1 đến 10.000.');
            $priceQuery = $db->prepare('SELECT GiaDichVu FROM DICHVU WHERE MaDichVu = ?');
            $priceQuery->execute([$maDichVu]);
            $price = $priceQuery->fetchColumn();
            if ($price === false) throw new DomainException('Dịch vụ không tồn tại.');
            $existing = self::getById($maDichVu, $maCTDatPhong);
            $price = $existing ? (float)$existing['GiaDichVu'] : (float)$price;
            $total = (int)round($price * $soLuong);
            if ($total > 2147483647) throw new DomainException('Tổng tiền dịch vụ vượt giới hạn cho phép.');
            if ($existing) {
                $db->prepare('UPDATE CTDICHVU SET SoLuong = ?, TongTienDichVu = ? WHERE MaDichVu = ? AND MaCTDatPhong = ?')->execute([$soLuong, $total, $maDichVu, $maCTDatPhong]);
            } else {
                // The actual primary key is (MaDichVu, MaCTDatPhong); this number is display-only.
                $displayId = $maCTDichVu ?: (int)$db->query('SELECT COALESCE(MAX(MaCTDichVu), 0) + 1 FROM CTDICHVU')->fetchColumn();
                $db->prepare('INSERT INTO CTDICHVU (MaCTDichVu, MaDichVu, SoLuong, MaCTDatPhong, TongTienDichVu) VALUES (?, ?, ?, ?, ?)')->execute([$displayId, $maDichVu, $soLuong, $maCTDatPhong, $total]);
            }
            self::refreshInvoice($maCTDatPhong);
            return true;
        });
    }

    public static function addToBooking(int $maCTDatPhong, int $maDichVu, int $soLuong = 1): bool {
        return db_transaction(function () use ($maCTDatPhong, $maDichVu, $soLuong): bool {
            DatPhongModel::assertServiceEditable($maCTDatPhong);
            if ($soLuong < 1) throw new DomainException('Số lượng dịch vụ phải lớn hơn 0.');
            $existing = self::getById($maDichVu, $maCTDatPhong);
            return self::save($maDichVu, $maCTDatPhong, (int)($existing['SoLuong'] ?? 0) + $soLuong);
        });
    }

    public static function delete(int $maDichVu, int $maCTDatPhong): bool {
        return db_transaction(function (PDO $db) use ($maDichVu, $maCTDatPhong): bool {
            DatPhongModel::assertServiceEditable($maCTDatPhong);
            $db->prepare('DELETE FROM CTDICHVU WHERE MaDichVu = ? AND MaCTDatPhong = ?')->execute([$maDichVu, $maCTDatPhong]);
            self::refreshInvoice($maCTDatPhong);
            return true;
        });
    }

    private static function refreshInvoice(int $id): void {
        require_once __DIR__ . '/HoaDonModel.php';
        if (HoaDonModel::getByBookingId($id)) HoaDonModel::createOrUpdateInvoice($id);
    }
}
