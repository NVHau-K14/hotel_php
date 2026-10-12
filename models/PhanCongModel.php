<?php
// models/PhanCongModel.php

require_once __DIR__ . '/BaseModel.php';

class PhanCongModel extends BaseModel {
    /**
     * Lấy danh sách phân công, có hỗ trợ lọc theo từ khóa và ngày
     */
    public static function getAll(?string $keyword = null, ?string $ngay = null): array {
        $db = self::db();
        $sql = "
            SELECT bpc.*, nv.TenNhanVien, nv.Username, cv.TenChucVu
            FROM BANGPHANCONG bpc
            LEFT JOIN NHANVIEN nv ON bpc.MaNhanVien = nv.MaNhanVien
            LEFT JOIN CHUCVU cv ON nv.MaChucVu = cv.MaChucVu
            WHERE 1=1
        ";
        $params = [];

        if (!empty($keyword)) {
            $sql .= " AND bpc.LoaiCongViec LIKE :keyword";
            $params[':keyword'] = '%' . trim($keyword) . '%';
        }

        if (!empty($ngay)) {
            $sql .= " AND bpc.NgayPhanCong = :ngay";
            $params[':ngay'] = trim($ngay);
        }

        $sql .= " ORDER BY bpc.NgayPhanCong DESC, bpc.MaPhanCong DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy phân công của ngày hôm nay
     */
    public static function getByToday(): array {
        return self::getAll(null, date('Y-m-d'));
    }

    /**
     * Lấy chi tiết 1 phân công theo ID
     */
    public static function getById(int $id): ?array {
        $db = self::db();
        $stmt = $db->prepare("
            SELECT bpc.*, nv.TenNhanVien, nv.Username
            FROM BANGPHANCONG bpc
            LEFT JOIN NHANVIEN nv ON bpc.MaNhanVien = nv.MaNhanVien
            WHERE bpc.MaPhanCong = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Thêm mới phân công
     */
    public static function create(string $ngayPhanCong, string $loaiCongViec, ?int $maNhanVien): int {
        $db = self::db();
        if (!valid_date($ngayPhanCong) || trim($loaiCongViec) === '' || mb_strlen($loaiCongViec) > 50) throw new DomainException('Ngày hoặc nội dung phân công không hợp lệ (tối đa 50 ký tự).');
        $stmt = $db->prepare("
            INSERT INTO BANGPHANCONG (NgayPhanCong, LoaiCongViec, MaNhanVien)
            VALUES (:ngay, :congViec, :maNv)
        ");
        $stmt->execute([
            ':ngay'     => $ngayPhanCong,
            ':congViec' => trim($loaiCongViec),
            ':maNv'     => $maNhanVien ?: null
        ]);
        return (int)$db->lastInsertId();
    }

    /**
     * Cập nhật phân công
     */
    public static function update(int $id, string $ngayPhanCong, string $loaiCongViec, ?int $maNhanVien): bool {
        $db = self::db();
        if (!valid_date($ngayPhanCong) || trim($loaiCongViec) === '' || mb_strlen($loaiCongViec) > 50) throw new DomainException('Ngày hoặc nội dung phân công không hợp lệ (tối đa 50 ký tự).');
        $stmt = $db->prepare("
            UPDATE BANGPHANCONG 
            SET NgayPhanCong = :ngay, LoaiCongViec = :congViec, MaNhanVien = :maNv
            WHERE MaPhanCong = :id
        ");
        return $stmt->execute([
            ':ngay'     => $ngayPhanCong,
            ':congViec' => trim($loaiCongViec),
            ':maNv'     => $maNhanVien ?: null,
            ':id'       => $id
        ]);
    }

    /**
     * Xóa phân công
     */
    public static function delete(int $id): bool {
        $db = self::db();
        $stmt = $db->prepare("DELETE FROM BANGPHANCONG WHERE MaPhanCong = :id");
        return $stmt->execute([':id' => $id]);
    }
}
