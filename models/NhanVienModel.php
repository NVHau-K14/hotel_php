<?php
// models/NhanVienModel.php

require_once __DIR__ . '/BaseModel.php';

class NhanVienModel extends BaseModel {
    public static function getAll(): array {
        $db = self::db();
        $sql = "
            SELECT nv.*, cv.TenChucVu 
            FROM NHANVIEN nv
            LEFT JOIN CHUCVU cv ON nv.MaChucVu = cv.MaChucVu
            ORDER BY nv.MaNhanVien ASC
        ";
        return $db->query($sql)->fetchAll();
    }

    public static function getById(int $id): ?array {
        $db = self::db();
        $stmt = $db->prepare("
            SELECT nv.*, cv.TenChucVu 
            FROM NHANVIEN nv
            LEFT JOIN CHUCVU cv ON nv.MaChucVu = cv.MaChucVu
            WHERE nv.MaNhanVien = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(string $username, string $password, string $ten, ?string $ngaySinh, ?string $cmnd, ?string $ngayVaoLam, int $maChucVu): int {
        $db = self::db();
        if ($username === '' || strlen($username) > 20 || $password === '') throw new DomainException('Tên đăng nhập tối đa 20 ký tự và mật khẩu không được để trống.');
        $check = $db->prepare('SELECT COUNT(*) FROM NHANVIEN WHERE Username = ?');
        $check->execute([$username]);
        if ($check->fetchColumn()) throw new DomainException('Tên đăng nhập đã tồn tại.');
        self::validateEmployee($ten, $ngaySinh, $cmnd, $ngayVaoLam);
        $stmt = $db->prepare("
            INSERT INTO NHANVIEN (Username, `Password`, TenNhanVien, NgaySinh, CMND, NgayVaoLam, MaChucVu)
            VALUES (:un, :pw, :ten, :ns, :cmnd, :nvl, :mcv)
        ");
        $stmt->execute([
            ':un'   => $username,
            ':pw'   => password_hash($password, PASSWORD_DEFAULT),
            ':ten'  => $ten,
            ':ns'   => $ngaySinh ?: null,
            ':cmnd' => $cmnd ?: null,
            ':nvl'  => $ngayVaoLam ?: date('Y-m-d'),
            ':mcv'  => $maChucVu
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, string $ten, ?string $ngaySinh, ?string $cmnd, ?string $ngayVaoLam, int $maChucVu, ?string $password = null): bool {
        $db = self::db();
        self::validateEmployee($ten, $ngaySinh, $cmnd, $ngayVaoLam);
        if ((int)($_SESSION['admin_user']['id'] ?? 0) === $id && $maChucVu !== (int)($_SESSION['admin_user']['roleId'] ?? 0)) throw new DomainException('Không được tự thay đổi chức vụ của tài khoản đang đăng nhập.');
        if ($password !== null && trim($password) !== '') {
            $stmt = $db->prepare("
                UPDATE NHANVIEN 
                SET TenNhanVien = :ten, NgaySinh = :ns, CMND = :cmnd, NgayVaoLam = :nvl, MaChucVu = :mcv, `Password` = :pw
                WHERE MaNhanVien = :id
            ");
            return $stmt->execute([
                ':ten'  => $ten,
                ':ns'   => $ngaySinh ?: null,
                ':cmnd' => $cmnd ?: null,
                ':nvl'  => $ngayVaoLam ?: null,
                ':mcv'  => $maChucVu,
                ':pw'   => password_hash($password, PASSWORD_DEFAULT),
                ':id'   => $id
            ]);
        } else {
            $stmt = $db->prepare("
                UPDATE NHANVIEN 
                SET TenNhanVien = :ten, NgaySinh = :ns, CMND = :cmnd, NgayVaoLam = :nvl, MaChucVu = :mcv
                WHERE MaNhanVien = :id
            ");
            return $stmt->execute([
                ':ten'  => $ten,
                ':ns'   => $ngaySinh ?: null,
                ':cmnd' => $cmnd ?: null,
                ':nvl'  => $ngayVaoLam ?: null,
                ':mcv'  => $maChucVu,
                ':id'   => $id
            ]);
        }
    }

    public static function delete(int $id): bool {
        $db = self::db();
        if ((int)($_SESSION['admin_user']['id'] ?? 0) === $id) throw new DomainException('Không được xóa tài khoản đang đăng nhập.');
        $stmt = $db->prepare("DELETE FROM NHANVIEN WHERE MaNhanVien = :id");
        return $stmt->execute([':id' => $id]);
    }

    // --- CHỨC VỤ & PHÂN QUYỀN ---

    public static function getAllChucVu(): array {
        $db = self::db();
        return $db->query("SELECT * FROM CHUCVU ORDER BY MaChucVu ASC")->fetchAll();
    }

    public static function getPermissionsByChucVu(int $maChucVu): array {
        $db = self::db();
        $stmt = $db->prepare("SELECT MaQuyen FROM CHUCVU_QUYEN WHERE MaChucVu = :mcv");
        $stmt->execute([':mcv' => $maChucVu]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function updateChucVuPermissions(int $maChucVu, array $permissions): void {
        db_transaction(function (PDO $db) use ($maChucVu, $permissions): void {
        $stmtDel = $db->prepare("DELETE FROM CHUCVU_QUYEN WHERE MaChucVu = :mcv");
        $stmtDel->execute([':mcv' => $maChucVu]);

        if (!empty($permissions)) {
            $stmtIns = $db->prepare("INSERT INTO CHUCVU_QUYEN (MaChucVu, MaQuyen) VALUES (:mcv, :mq)");
            foreach ($permissions as $p) {
                $stmtIns->execute([':mcv' => $maChucVu, ':mq' => $p]);
            }
        }
        });
    }

    private static function validateEmployee(string $name, ?string $birth, ?string $identity, ?string $start): void {
        if (trim($name) === '' || mb_strlen($name) > 100 || strlen($identity ?? '') > 20) throw new DomainException('Họ tên hoặc số giấy tờ nhân viên không hợp lệ.');
        if (($birth && (!valid_date($birth) || $birth > date('Y-m-d'))) || ($start && !valid_date($start))) throw new DomainException('Ngày sinh hoặc ngày vào làm không hợp lệ.');
    }
}
