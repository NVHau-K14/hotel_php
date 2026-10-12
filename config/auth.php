<?php
// config/auth.php

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/helpers.php';

class Auth {
    // --- ADMIN / NHAN VIEN AUTH ---

    public static function attemptAdminLogin(string $username, string $password): bool {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT nv.*, cv.TenChucVu 
            FROM NHANVIEN nv
            LEFT JOIN CHUCVU cv ON nv.MaChucVu = cv.MaChucVu
            WHERE nv.Username = :username
            LIMIT 1
        ");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        if ($user && verify_stored_password($password, (string)$user['Password'])) {
            if (password_needs_rehash((string)$user['Password'], PASSWORD_DEFAULT)) {
                $db->prepare('UPDATE NHANVIEN SET Password = ? WHERE MaNhanVien = ?')->execute([password_hash($password, PASSWORD_DEFAULT), $user['MaNhanVien']]);
            }
            session_regenerate_id(true);
            // Lấy danh sách quyền
            $permStmt = $db->prepare("SELECT MaQuyen FROM CHUCVU_QUYEN WHERE MaChucVu = :maChucVu");
            $permStmt->execute([':maChucVu' => $user['MaChucVu']]);
            $permissions = $permStmt->fetchAll(PDO::FETCH_COLUMN);

            // Lưu session
            $_SESSION['admin_user'] = [
                'id' => $user['MaNhanVien'],
                'username' => $user['Username'],
                'fullName' => $user['TenNhanVien'],
                'roleId' => $user['MaChucVu'],
                'roleName' => $user['TenChucVu']
            ];
            $_SESSION['admin_permissions'] = $permissions;
            return true;
        }
        return false;
    }

    public static function isAdminLoggedIn(): bool {
        static $checked = false;
        if (empty($_SESSION['admin_user'])) return false;
        if (!$checked) {
            $checked = true;
            $stmt = getDB()->prepare('SELECT nv.*, cv.TenChucVu FROM NHANVIEN nv LEFT JOIN CHUCVU cv ON cv.MaChucVu = nv.MaChucVu WHERE MaNhanVien = ?');
            $stmt->execute([$_SESSION['admin_user']['id']]);
            $user = $stmt->fetch();
            if (!$user) { self::adminLogout(); return false; }
            $_SESSION['admin_user'] = ['id' => $user['MaNhanVien'], 'username' => $user['Username'], 'fullName' => $user['TenNhanVien'], 'roleId' => $user['MaChucVu'], 'roleName' => $user['TenChucVu']];
            $stmt = getDB()->prepare('SELECT MaQuyen FROM CHUCVU_QUYEN WHERE MaChucVu = ?');
            $stmt->execute([$user['MaChucVu']]);
            $_SESSION['admin_permissions'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
        }
        return true;
    }

    public static function currentAdmin(): ?array {
        return $_SESSION['admin_user'] ?? null;
    }

    public static function hasPermission(string $permission): bool {
        if (!self::isAdminLoggedIn()) return false;
        // Nếu là Quản lý thì có toàn quyền
        if (($_SESSION['admin_user']['roleName'] ?? '') === 'Quản lý') {
            return true;
        }
        $perms = $_SESSION['admin_permissions'] ?? [];
        return in_array($permission, $perms);
    }

    public static function requireAdminLogin(): void {
        if (!self::isAdminLoggedIn()) {
            set_flash('danger', 'Vui lòng đăng nhập để tiếp tục.');
            redirect(admin_url('c=auth&a=login'));
        }
    }

    public static function requirePermission(string $permission): void {
        self::requireAdminLogin();
        if (!self::hasPermission($permission)) {
            http_response_code(403);
            die("<div style='text-align:center;margin-top:50px;font-family:sans-serif;'>
                <h2>403 - Truy cập bị từ chối</h2>
                <p>Bạn không có quyền thực hiện chức năng này ($permission).</p>
                <a href='" . admin_url('c=dashboard') . "'>Quay lại bảng điều khiển</a>
            </div>");
        }
    }

    public static function adminLogout(): void {
        unset($_SESSION['admin_user']);
        unset($_SESSION['admin_permissions']);
    }

    // --- CLIENT / KHACH HANG AUTH ---

    public static function attemptClientLogin(string $account, string $password): bool {
        $db = getDB();
        // Cho phép đăng nhập bằng Email hoặc Số điện thoại
        $stmt = $db->prepare("
            SELECT * FROM KHACHHANG 
            WHERE (Email = :email OR DienThoai = :phone)
            LIMIT 1
        ");
        $stmt->execute([':email' => $account, ':phone' => $account]);
        $client = $stmt->fetch();

        if ($client && verify_stored_password($password, (string)$client['MatKhau'])) {
            if (($client['TrangThai'] ?? '') === 'KHOA') {
                set_flash('danger', 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ khách sạn.');
                return false;
            }

            if (password_needs_rehash((string)$client['MatKhau'], PASSWORD_DEFAULT)) {
                $db->prepare('UPDATE KHACHHANG SET MatKhau = ? WHERE MaKhachHang = ?')->execute([password_hash($password, PASSWORD_DEFAULT), $client['MaKhachHang']]);
            }
            session_regenerate_id(true);
            $_SESSION['client_user'] = [
                'id' => $client['MaKhachHang'],
                'fullName' => $client['HoTen'],
                'email' => $client['Email'],
                'phone' => $client['DienThoai'],
                'address' => $client['DiaChi'],
                'cmnd' => $client['CMND']
            ];
            return true;
        }
        return false;
    }

    public static function isClientLoggedIn(): bool {
        static $checked = false;
        if (empty($_SESSION['client_user'])) return false;
        if (!$checked) {
            $checked = true;
            $stmt = getDB()->prepare('SELECT * FROM KHACHHANG WHERE MaKhachHang = ?');
            $stmt->execute([$_SESSION['client_user']['id']]);
            $client = $stmt->fetch();
            if (!$client || $client['TrangThai'] === 'KHOA') { self::clientLogout(); return false; }
            $_SESSION['client_user'] = ['id' => $client['MaKhachHang'], 'fullName' => $client['HoTen'], 'email' => $client['Email'], 'phone' => $client['DienThoai'], 'address' => $client['DiaChi'], 'cmnd' => $client['CMND']];
        }
        return true;
    }

    public static function currentClient(): ?array {
        return self::isClientLoggedIn() ? $_SESSION['client_user'] : null;
    }

    public static function requireClientLogin(): void {
        if (!self::isClientLoggedIn()) {
            set_flash('warning', 'Vui lòng đăng nhập tài khoản khách hàng để tiếp tục.');
            redirect(client_url('c=account&a=login'));
        }
    }

    public static function clientLogout(): void {
        unset($_SESSION['client_user'], $_SESSION['guest_booking_ids'], $_SESSION['guest_customer_id']);
    }
}
