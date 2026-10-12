<?php
// models/PhongModel.php

require_once __DIR__ . '/BaseModel.php';

class PhongModel extends BaseModel {
    public static function getAll(
        ?string $keyword = null,
        ?int $maLoaiPhong = null,
        ?int $giaTu = null,
        ?int $giaDen = null,
        ?string $ngayNhan = null,
        ?string $ngayTra = null
    ): array {
        $db = self::db();
        $sql = "
            SELECT p.*, lp.TenLoaiPhong, lp.GiaPhong, lp.MoTa 
            FROM PHONG p
            LEFT JOIN LOAIPHONG lp ON p.MaLoaiPhong = lp.MaLoaiPhong
            WHERE 1=1
        ";
        $params = [];

        if ($keyword !== null && trim($keyword) !== '') {
            $sql .= " AND (p.MaPhong LIKE :kw0 OR lp.TenLoaiPhong LIKE :kw1)";
            $params[':kw0'] = '%' . trim($keyword) . '%';
            $params[':kw1'] = '%' . trim($keyword) . '%';
        }

        if ($maLoaiPhong !== null && $maLoaiPhong > 0) {
            $sql .= " AND p.MaLoaiPhong = :mlp";
            $params[':mlp'] = $maLoaiPhong;
        }

        if ($giaTu !== null && $giaTu > 0) {
            $sql .= " AND lp.GiaPhong >= :giaTu";
            $params[':giaTu'] = $giaTu;
        }

        if ($giaDen !== null && $giaDen > 0) {
            $sql .= " AND lp.GiaPhong <= :giaDen";
            $params[':giaDen'] = $giaDen;
        }

        // Lọc theo khoảng ngày nhận - trả (phải còn trống trong khoảng ngày này)
        if (!empty($ngayNhan) && !empty($ngayTra)) {
            validate_stay($ngayNhan, $ngayTra, true);
            $sql .= " AND (p.TrangThai IS NULL OR p.TrangThai <> 'Bảo trì') AND COALESCE(lp.DaXoa, 0) = 0";
            $sql .= " AND p.MaPhong NOT IN (
                SELECT cdp.MaPhong 
                FROM CTDATPHONG cdp
                WHERE cdp.MaPhong IS NOT NULL
                  AND cdp.TrangThai NOT IN ('DA_HUY', 'DA_HOAN_THANH')
                  AND NOT (cdp.NgayTra <= :ngayNhan OR cdp.NgayNhan >= :ngayTra)
            )";
            $params[':ngayNhan'] = $ngayNhan;
            $params[':ngayTra']  = $ngayTra;
        }

        $sql .= " ORDER BY p.MaPhong ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function getById(int $maPhong): ?array {
        $db = self::db();
        $stmt = $db->prepare("
            SELECT p.*, lp.TenLoaiPhong, lp.GiaPhong, lp.MoTa 
            FROM PHONG p
            LEFT JOIN LOAIPHONG lp ON p.MaLoaiPhong = lp.MaLoaiPhong
            WHERE p.MaPhong = :maPhong
            LIMIT 1
        ");
        $stmt->execute([':maPhong' => $maPhong]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(int $maPhong, int $maLoaiPhong, string $trangThai, ?string $hinhAnh): bool {
        $db = self::db();
        self::validateRoom($maLoaiPhong, $trangThai);
        $stmt = $db->prepare("
            INSERT INTO PHONG (MaPhong, MaLoaiPhong, TrangThai, HinhAnhDaiDien)
            VALUES (:maPhong, :maLoaiPhong, :trangThai, :hinhAnh)
        ");
        return $stmt->execute([
            ':maPhong' => $maPhong,
            ':maLoaiPhong' => $maLoaiPhong,
            ':trangThai' => $trangThai,
            ':hinhAnh' => $hinhAnh
        ]);
    }

    public static function update(int $maPhong, int $maLoaiPhong, string $trangThai, ?string $hinhAnh = null): bool {
        $db = self::db();
        self::validateRoom($maLoaiPhong, $trangThai);
        $current = self::getById($maPhong);
        if (!$current) throw new DomainException('Phòng không tồn tại.');
        $active = $db->prepare("SELECT COUNT(*) FROM CTDATPHONG WHERE MaPhong = ? AND TrangThai IN ('CHO_ADMIN_DUYET', 'DA_XAC_NHAN', 'DANG_SU_DUNG') AND (NgayTra > CURDATE() OR TrangThai = 'DANG_SU_DUNG')");
        $active->execute([$maPhong]);
        if ($active->fetchColumn() && ($trangThai !== $current['TrangThai'] || $maLoaiPhong !== (int)$current['MaLoaiPhong'])) throw new DomainException('Phòng đang có lịch đặt; hãy xử lý đơn trước khi đổi loại hoặc trạng thái phòng.');
        if ($hinhAnh !== null) {
            $stmt = $db->prepare("
                UPDATE PHONG 
                SET MaLoaiPhong = :maLoaiPhong, TrangThai = :trangThai, HinhAnhDaiDien = :hinhAnh
                WHERE MaPhong = :maPhong
            ");
            return $stmt->execute([
                ':maLoaiPhong' => $maLoaiPhong,
                ':trangThai' => $trangThai,
                ':hinhAnh' => $hinhAnh,
                ':maPhong' => $maPhong
            ]);
        } else {
            $stmt = $db->prepare("
                UPDATE PHONG 
                SET MaLoaiPhong = :maLoaiPhong, TrangThai = :trangThai
                WHERE MaPhong = :maPhong
            ");
            return $stmt->execute([
                ':maLoaiPhong' => $maLoaiPhong,
                ':trangThai' => $trangThai,
                ':maPhong' => $maPhong
            ]);
        }
    }

    public static function updateStatus(int $maPhong, string $trangThai): bool {
        $db = self::db();
        $stmt = $db->prepare("UPDATE PHONG SET TrangThai = :trangThai WHERE MaPhong = :maPhong");
        return $stmt->execute([
            ':trangThai' => $trangThai,
            ':maPhong' => $maPhong
        ]);
    }

    public static function delete(int $maPhong): bool {
        $db = self::db();
        $stmt = $db->prepare("DELETE FROM PHONG WHERE MaPhong = :maPhong");
        return $stmt->execute([':maPhong' => $maPhong]);
    }

    public static function countByStatus(): array {
        $db = self::db();
        $sql = "
            SELECT 
                SUM(CASE WHEN TrangThai = 'Trống' OR TrangThai IS NULL THEN 1 ELSE 0 END) AS phongTrong,
                SUM(CASE WHEN TrangThai IN ('Đang thuê', 'Đang sử dụng', 'Đã đặt') THEN 1 ELSE 0 END) AS phongDangThue,
                SUM(CASE WHEN TrangThai = 'Bảo trì' THEN 1 ELSE 0 END) AS phongBaoTri,
                COUNT(*) AS tongPhong
            FROM PHONG
        ";
        return $db->query($sql)->fetch() ?: ['phongTrong' => 0, 'phongDangThue' => 0, 'phongBaoTri' => 0, 'tongPhong' => 0];
    }

    private static function validateRoom(int $type, string $status): void {
        if (!in_array($status, ['Trống', 'Đã đặt', 'Đang thuê', 'Bảo trì'], true)) throw new DomainException('Trạng thái phòng không hợp lệ.');
        $stmt = self::db()->prepare('SELECT COUNT(*) FROM LOAIPHONG WHERE MaLoaiPhong = ? AND COALESCE(DaXoa, 0) = 0');
        $stmt->execute([$type]);
        if (!$stmt->fetchColumn()) throw new DomainException('Loại phòng không tồn tại hoặc đã ngừng sử dụng.');
    }
}
