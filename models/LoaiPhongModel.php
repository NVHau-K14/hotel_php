<?php
// models/LoaiPhongModel.php

require_once __DIR__ . '/BaseModel.php';

class LoaiPhongModel extends BaseModel {
    public static function getAll(bool $includeDeleted = false): array {
        $db = self::db();
        $sql = "SELECT * FROM LOAIPHONG";
        if (!$includeDeleted) {
            $sql .= " WHERE DaXoa = 0 OR DaXoa IS NULL";
        }
        $sql .= " ORDER BY MaLoaiPhong ASC";
        return $db->query($sql)->fetchAll();
    }

    public static function getById(int $id): ?array {
        $db = self::db();
        $stmt = $db->prepare("SELECT * FROM LOAIPHONG WHERE MaLoaiPhong = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(string $ten, int $gia, ?string $moTa): int {
        $db = self::db();
        self::validate($ten, $gia, $moTa);
        $stmt = $db->prepare("INSERT INTO LOAIPHONG (TenLoaiPhong, GiaPhong, MoTa, DaXoa) VALUES (:ten, :gia, :moTa, 0)");
        $stmt->execute([
            ':ten' => $ten,
            ':gia' => $gia,
            ':moTa' => $moTa
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, string $ten, int $gia, ?string $moTa): bool {
        $db = self::db();
        self::validate($ten, $gia, $moTa);
        $stmt = $db->prepare("UPDATE LOAIPHONG SET TenLoaiPhong = :ten, GiaPhong = :gia, MoTa = :moTa WHERE MaLoaiPhong = :id");
        return $stmt->execute([
            ':ten' => $ten,
            ':gia' => $gia,
            ':moTa' => $moTa,
            ':id' => $id
        ]);
    }

    public static function delete(int $id): bool {
        $db = self::db();
        // Kiểm tra xem có phòng nào đang dùng loại phòng này không
        $check = $db->prepare("SELECT COUNT(*) FROM PHONG WHERE MaLoaiPhong = :id");
        $check->execute([':id' => $id]);
        if ($check->fetchColumn() > 0) {
            // Soft delete nếu đã có phòng liên kết
            $stmt = $db->prepare("UPDATE LOAIPHONG SET DaXoa = 1 WHERE MaLoaiPhong = :id");
            return $stmt->execute([':id' => $id]);
        }
        // Hard delete nếu chưa có phòng nào
        $stmt = $db->prepare("DELETE FROM LOAIPHONG WHERE MaLoaiPhong = :id");
        return $stmt->execute([':id' => $id]);
    }

    private static function validate(string $name, int $price, ?string $description): void {
        if (trim($name) === '' || mb_strlen($name) > 20 || $price <= 0 || $price > 2147483647 || mb_strlen($description ?? '') > 255) throw new DomainException('Tên loại phòng tối đa 20 ký tự, mô tả tối đa 255 ký tự và giá phải lớn hơn 0.');
    }
}
