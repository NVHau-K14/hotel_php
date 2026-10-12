<?php
// models/ThongKeModel.php

require_once __DIR__ . '/BaseModel.php';

class ThongKeModel extends BaseModel {
    public static function getSummary(): array {
        $db = self::db();

        $tongDoanhThu = (int)$db->query("SELECT COALESCE(SUM(SoTienThu), 0) FROM HOADON WHERE TrangThai = 'DA_THANH_TOAN'")->fetchColumn();
        $doanhThuThangNay = (int)$db->query("
            SELECT COALESCE(SUM(SoTienThu), 0) 
            FROM HOADON 
            WHERE TrangThai = 'DA_THANH_TOAN' 
              AND MONTH(NgayThuTien) = MONTH(CURRENT_DATE()) 
              AND YEAR(NgayThuTien) = YEAR(CURRENT_DATE())
        ")->fetchColumn();

        $tongPhong = (int)$db->query("SELECT COUNT(*) FROM PHONG")->fetchColumn();
        $phongTrong = (int)$db->query("SELECT COUNT(*) FROM PHONG WHERE TrangThai = 'Trống' OR TrangThai IS NULL")->fetchColumn();
        $phongDangThue = (int)$db->query("SELECT COUNT(*) FROM PHONG WHERE TrangThai IN ('Đang thuê', 'Đang sử dụng', 'Đã đặt')")->fetchColumn();
        $phongBaoTri = (int)$db->query("SELECT COUNT(*) FROM PHONG WHERE TrangThai = 'Bảo trì'")->fetchColumn();

        $donChoDuyet = (int)$db->query("SELECT COUNT(*) FROM CTDATPHONG WHERE TrangThai = 'CHO_ADMIN_DUYET'")->fetchColumn();
        $tongKhachHang = (int)$db->query("SELECT COUNT(*) FROM KHACHHANG")->fetchColumn();
        $tongNhanVien = (int)$db->query("SELECT COUNT(*) FROM NHANVIEN")->fetchColumn();

        return [
            'tongDoanhThu'     => $tongDoanhThu,
            'doanhThuThangNay' => $doanhThuThangNay,
            'tongPhong'        => $tongPhong,
            'phongTrong'       => $phongTrong,
            'phongDangThue'    => $phongDangThue,
            'phongBaoTri'      => $phongBaoTri,
            'donChoDuyet'      => $donChoDuyet,
            'tongKhachHang'    => $tongKhachHang,
            'tongNhanVien'     => $tongNhanVien
        ];
    }

    public static function getMonthlyRevenue(int $year): array {
        $db = self::db();
        $stmt = $db->prepare("
            SELECT MONTH(NgayThuTien) as thang, COALESCE(SUM(SoTienThu), 0) as doanhThu
            FROM HOADON
            WHERE TrangThai = 'DA_THANH_TOAN' AND YEAR(NgayThuTien) = :year
            GROUP BY MONTH(NgayThuTien)
            ORDER BY thang ASC
        ");
        $stmt->execute([':year' => $year]);
        $rows = $stmt->fetchAll();

        // Chuẩn hóa 12 tháng
        $monthly = array_fill(1, 12, 0);
        foreach ($rows as $r) {
            $monthly[(int)$r['thang']] = (int)$r['doanhThu'];
        }
        return $monthly;
    }
}
