<?php
// admin/index.php - Router cho trang Quản trị Admin

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../config/auth.php';

set_exception_handler('handle_request_error');
$requestedAction = is_string($_GET['a'] ?? null) ? $_GET['a'] : 'index';
guard_request(['delete', 'toggle', 'approve', 'checkin', 'checkout', 'cancel', 'pay', 'removeService', 'logout', 'addService', 'saveToBooking', 'updatePermissions', 'submit'], $requestedAction);

$controllerName = $_GET['c'] ?? 'dashboard';
$actionName     = $_GET['a'] ?? 'index';

// Định tuyến Controller
switch ($controllerName) {
    case 'auth':
        require_once __DIR__ . '/../controllers/admin/AuthController.php';
        $controller = new AuthController();
        if ($actionName === 'logout') {
            $controller->logout();
        } elseif ($actionName === 'changePassword') {
            $controller->changePassword();
        } else {
            $controller->login();
        }
        break;

    case 'dashboard':
        require_once __DIR__ . '/../controllers/admin/DashboardController.php';
        $controller = new DashboardController();
        $controller->index();
        break;

    case 'phong':
        require_once __DIR__ . '/../controllers/admin/PhongController.php';
        $controller = new PhongController();
        if ($actionName === 'add') {
            $controller->add();
        } elseif ($actionName === 'edit') {
            $controller->edit();
        } elseif ($actionName === 'delete') {
            $controller->delete();
        } else {
            $controller->list();
        }
        break;

    case 'loai-phong':
        require_once __DIR__ . '/../controllers/admin/LoaiPhongController.php';
        $controller = new LoaiPhongController();
        if ($actionName === 'add') {
            $controller->add();
        } elseif ($actionName === 'edit') {
            $controller->edit();
        } elseif ($actionName === 'delete') {
            $controller->delete();
        } else {
            $controller->list();
        }
        break;

    case 'dat-phong':
        require_once __DIR__ . '/../controllers/admin/DatPhongController.php';
        $controller = new DatPhongController();
        if ($actionName === 'detail') {
            $controller->detail();
        } elseif ($actionName === 'approve') {
            $controller->approve();
        } elseif ($actionName === 'checkin') {
            $controller->checkin();
        } elseif ($actionName === 'checkout') {
            $controller->checkout();
        } elseif ($actionName === 'cancel') {
            $controller->cancel();
        } elseif ($actionName === 'addService') {
            $controller->addService();
        } elseif ($actionName === 'removeService') {
            $controller->removeService();
        } elseif ($actionName === 'add') {
            $controller->add();
        } else {
            $controller->list();
        }
        break;

    case 'dich-vu':
        require_once __DIR__ . '/../controllers/admin/DichVuController.php';
        $controller = new DichVuController();
        if ($actionName === 'add') {
            $controller->add();
        } elseif ($actionName === 'edit') {
            $controller->edit();
        } elseif ($actionName === 'delete') {
            $controller->delete();
        } else {
            $controller->list();
        }
        break;

    case 'hoa-don':
        require_once __DIR__ . '/../controllers/admin/HoaDonController.php';
        $controller = new HoaDonController();
        if ($actionName === 'detail') {
            $controller->detail();
        } elseif ($actionName === 'pay') {
            $controller->pay();
        } else {
            $controller->list();
        }
        break;

    case 'khach-hang':
        require_once __DIR__ . '/../controllers/admin/KhachHangController.php';
        $controller = new KhachHangController();
        if ($actionName === 'toggle') {
            $controller->toggle();
        } elseif ($actionName === 'history') {
            $controller->history();
        } else {
            $controller->list();
        }
        break;

    case 'nhan-vien':
        require_once __DIR__ . '/../controllers/admin/NhanVienController.php';
        $controller = new NhanVienController();
        if ($actionName === 'add') {
            $controller->add();
        } elseif ($actionName === 'edit') {
            $controller->edit();
        } elseif ($actionName === 'delete') {
            $controller->delete();
        } else {
            $controller->list();
        }
        break;

    case 'chuc-vu':
        require_once __DIR__ . '/../controllers/admin/ChucVuController.php';
        $controller = new ChucVuController();
        if ($actionName === 'updatePermissions') {
            $controller->updatePermissions();
        } else {
            $controller->list();
        }
        break;

    case 'ct-dich-vu':
        require_once __DIR__ . '/../controllers/admin/CtDichVuController.php';
        $controller = new CtDichVuController();
        if ($actionName === 'add') {
            $controller->add();
        } elseif ($actionName === 'addToBooking') {
            $controller->addToBooking();
        } elseif ($actionName === 'saveToBooking') {
            $controller->saveToBooking();
        } elseif ($actionName === 'delete') {
            $controller->delete();
        } else {
            $controller->list();
        }
        break;

    case 'phan-cong':
        require_once __DIR__ . '/../controllers/admin/PhanCongController.php';
        $controller = new PhanCongController();
        if ($actionName === 'add') {
            $controller->add();
        } elseif ($actionName === 'edit') {
            $controller->edit();
        } elseif ($actionName === 'delete') {
            $controller->delete();
        } elseif ($actionName === 'homNay') {
            $controller->homNay();
        } else {
            $controller->list();
        }
        break;

    case 'danh-gia':
        require_once __DIR__ . '/../controllers/admin/DanhGiaController.php';
        $controller = new DanhGiaController();
        if ($actionName === 'add') {
            $controller->add();
        } elseif ($actionName === 'edit') {
            $controller->edit();
        } elseif ($actionName === 'delete') {
            $controller->delete();
        } else {
            $controller->list();
        }
        break;

    case 'thong-ke':
        require_once __DIR__ . '/../controllers/admin/ThongKeController.php';
        $controller = new ThongKeController();
        $controller->index();
        break;

    default:
        redirect(admin_url('c=dashboard'));
        break;
}
