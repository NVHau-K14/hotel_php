<?php
// models/DichVuModel.php

require_once __DIR__ . '/BaseModel.php';

class DichVuModel extends BaseModel {
    // --- QUẢN LÝ DỊCH VỤ ---

    public static function getAll(): array {
        $db = self::db();
        return $db->query("SELECT * FROM DICHVU ORDER BY MaDichVu ASC")->fetchAll();
    }

    public static function getById(int $id): ?array {
        $db = self::db();
        $stmt = $db->prepare("SELECT * FROM DICHVU WHERE MaDichVu = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(string $ten, int $gia, ?string $hinhAnh): int {
        $db = self::db();
        self::validate($ten, $gia);
        $stmt = $db->prepare("INSERT INTO DICHVU (TenDichVu, GiaDichVu, HinhAnh) VALUES (:ten, :gia, :anh)");
        $stmt->execute([':ten' => $ten, ':gia' => $gia, ':anh' => $hinhAnh]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, string $ten, int $gia, ?string $hinhAnh = null): bool {
        $db = self::db();
        self::validate($ten, $gia);
        if ($hinhAnh !== null) {
            $stmt = $db->prepare("UPDATE DICHVU SET TenDichVu = :ten, GiaDichVu = :gia, HinhAnh = :anh WHERE MaDichVu = :id");
            return $stmt->execute([':ten' => $ten, ':gia' => $gia, ':anh' => $hinhAnh, ':id' => $id]);
        }
        $stmt = $db->prepare("UPDATE DICHVU SET TenDichVu = :ten, GiaDichVu = :gia WHERE MaDichVu = :id");
        return $stmt->execute([':ten' => $ten, ':gia' => $gia, ':id' => $id]);
    }

    public static function delete(int $id): bool {
        $db = self::db();
        $stmt = $db->prepare("DELETE FROM DICHVU WHERE MaDichVu = :id");
        return $stmt->execute([':id' => $id]);
    }

    // --- CHI TIẾT DỊCH VỤ TRONG ĐƠN ĐẶT PHÒNG ---

    public static function getServicesByBooking(int $maCTDatPhong): array {
        $db = self::db();
        $stmt = $db->prepare("
            SELECT ctdv.*, dv.TenDichVu, COALESCE(ctdv.TongTienDichVu / NULLIF(ctdv.SoLuong, 0), dv.GiaDichVu) AS GiaDichVu, dv.HinhAnh
            FROM CTDICHVU ctdv
            JOIN DICHVU dv ON ctdv.MaDichVu = dv.MaDichVu
            WHERE ctdv.MaCTDatPhong = :bookingId
            ORDER BY dv.TenDichVu ASC
        ");
        $stmt->execute([':bookingId' => $maCTDatPhong]);
        return $stmt->fetchAll();
    }

    public static function addServiceToBooking(int $maCTDatPhong, int $maDichVu, int $soLuong): bool {
        require_once __DIR__ . '/CtDichVuModel.php';
        return CtDichVuModel::addToBooking($maCTDatPhong, $maDichVu, $soLuong);
    }

    public static function removeServiceFromBooking(int $maCTDatPhong, int $maDichVu): bool {
        require_once __DIR__ . '/CtDichVuModel.php';
        return CtDichVuModel::delete($maDichVu, $maCTDatPhong);
    }

    private static function validate(string $name, int $price): void {
        if (trim($name) === '' || mb_strlen($name) > 50 || $price <= 0 || $price > 2147483647) throw new DomainException('Tên dịch vụ tối đa 50 ký tự và giá phải lớn hơn 0.');
    }
}
