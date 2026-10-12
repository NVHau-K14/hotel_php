<?php
// index.php - Router chính phía Khách hàng (Client)

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/config/auth.php';

set_exception_handler('handle_request_error');
$requestedAction = is_string($_GET['a'] ?? null) ? $_GET['a'] : 'index';
guard_request(['delete', 'toggle', 'approve', 'checkin', 'checkout', 'cancel', 'pay', 'removeService', 'logout', 'addService', 'saveToBooking', 'updatePermissions', 'submit'], $requestedAction);

$controllerName = $_GET['c'] ?? 'home';
$actionName     = $_GET['a'] ?? 'index';

switch ($controllerName) {
    case 'home':
        require_once __DIR__ . '/controllers/client/HomeController.php';
        $controller = new HomeController();
        if ($actionName === 'contact') {
            $controller->contact();
        } else {
            $controller->index();
        }
        break;

    case 'contact':
        require_once __DIR__ . '/controllers/client/HomeController.php';
        $controller = new HomeController();
        $controller->contact();
        break;

    case 'room':
        require_once __DIR__ . '/controllers/client/RoomController.php';
        $controller = new RoomController();
        if ($actionName === 'detail') {
            $controller->detail();
        } else {
            $controller->list();
        }
        break;

    case 'booking':
        require_once __DIR__ . '/controllers/client/BookingController.php';
        $controller = new BookingController();
        if ($actionName === 'success') {
            $controller->success();
        } else {
            $controller->create();
        }
        break;

    case 'service':
        require_once __DIR__ . '/controllers/client/ServiceController.php';
        $controller = new ServiceController();
        $controller->list();
        break;

    case 'review':
    case 'reviews':
        require_once __DIR__ . '/controllers/client/ReviewController.php';
        $controller = new ReviewController();
        if ($actionName === 'submit') {
            $controller->submit();
        } else {
            $controller->list();
        }
        break;

    case 'account':
        require_once __DIR__ . '/controllers/client/AccountController.php';
        $controller = new AccountController();
        if ($actionName === 'register') {
            $controller->register();
        } elseif ($actionName === 'profile') {
            $controller->profile();
        } elseif ($actionName === 'history') {
            $controller->history();
        } elseif ($actionName === 'detail') {
            $controller->detail();
        } elseif ($actionName === 'logout') {
            $controller->logout();
        } else {
            $controller->login();
        }
        break;

    default:
        require_once __DIR__ . '/controllers/client/HomeController.php';
        $controller = new HomeController();
        $controller->index();
        break;
}
