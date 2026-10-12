<?php
// models/KhachHangModel.php

require_once __DIR__ . '/BaseModel.php';

class KhachHangModel extends BaseModel {
    public static function getAll(?string $keyword = null): array {
        $db = self::db();
        $sql = "SELECT * FROM KHACHHANG WHERE 1=1";
        $params = [];
        if ($keyword !== null && trim($keyword) !== '') {
            $sql .= " AND (HoTen LIKE :kw0 OR Email LIKE :kw1 OR DienThoai LIKE :kw2 OR CMND LIKE :kw3)";
            $params[':kw0'] = '%' . trim($keyword) . '%';
            $params[':kw1'] = '%' . trim($keyword) . '%';
            $params[':kw2'] = '%' . trim($keyword) . '%';
            $params[':kw3'] = '%' . trim($keyword) . '%';
        }
        $sql .= " ORDER BY MaKhachHang DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function getById(int $id): ?array {
        $db = self::db();
        $stmt = $db->prepare("SELECT * FROM KHACHHANG WHERE MaKhachHang = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function getByEmail(string $email): ?array {
        $db = self::db();
        $stmt = $db->prepare("SELECT * FROM KHACHHANG WHERE Email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(
        string $hoTen,
        string $email,
        string $dienThoai,
        ?string $diaChi = null,
        ?string $cmnd = null,
        string $matKhau = ''
    ): int {
        $db = self::db();
        validate_customer($hoTen, $email, $dienThoai, $diaChi, $cmnd);
        $check = $db->prepare("SELECT COUNT(*) FROM KHACHHANG WHERE DienThoai = ? OR (Email <> '' AND Email = ?)");
        $check->execute([$dienThoai, $email]);
        if ($check->fetchColumn()) throw new DomainException('Số điện thoại hoặc email đã được đăng ký. Vui lòng đăng nhập tài khoản này.');
        $stmt = $db->prepare("
            INSERT INTO KHACHHANG (HoTen, Email, DiaChi, DienThoai, CMND, MatKhau, TrangThai)
            VALUES (:hoten, :email, :diachi, :sdt, :cmnd, :pass, 'HOAT_DONG')
        ");
        $stmt->execute([
            ':hoten'  => $hoTen,
            ':email'  => $email,
            ':diachi' => $diaChi,
            ':sdt'    => $dienThoai,
            ':cmnd'   => $cmnd,
            ':pass'   => $matKhau === '' ? null : password_hash($matKhau, PASSWORD_DEFAULT)
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, string $hoTen, string $email, string $dienThoai, ?string $diaChi, ?string $cmnd): bool {
        $db = self::db();
        validate_customer($hoTen, $email, $dienThoai, $diaChi, $cmnd);
        $check = $db->prepare("SELECT COUNT(*) FROM KHACHHANG WHERE MaKhachHang <> ? AND (DienThoai = ? OR (Email <> '' AND Email = ?))");
        $check->execute([$id, $dienThoai, $email]);
        if ($check->fetchColumn()) throw new DomainException('Số điện thoại hoặc email đã thuộc tài khoản khác.');
        $stmt = $db->prepare("
            UPDATE KHACHHANG 
            SET HoTen = :hoten, Email = :email, DienThoai = :sdt, DiaChi = :diachi, CMND = :cmnd
            WHERE MaKhachHang = :id
        ");
        return $stmt->execute([
            ':hoten'  => $hoTen,
            ':email'  => $email,
            ':sdt'    => $dienThoai,
            ':diachi' => $diaChi,
            ':cmnd'   => $cmnd,
            ':id'     => $id
        ]);
    }

    public static function updatePassword(int $id, string $newPassword): bool {
        $db = self::db();
        $stmt = $db->prepare("UPDATE KHACHHANG SET MatKhau = :pass WHERE MaKhachHang = :id");
        return $stmt->execute([':pass' => password_hash($newPassword, PASSWORD_DEFAULT), ':id' => $id]);
    }

    public static function toggleStatus(int $id): bool {
        $db = self::db();
        $customer = self::getById($id);
        if (!$customer) return false;
        $newStatus = ($customer['TrangThai'] === 'HOAT_DONG') ? 'KHOA' : 'HOAT_DONG';
        $stmt = $db->prepare("UPDATE KHACHHANG SET TrangThai = :st WHERE MaKhachHang = :id");
        return $stmt->execute([':st' => $newStatus, ':id' => $id]);
    }
}
