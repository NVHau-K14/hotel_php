<?php
// config/helpers.php

date_default_timezone_set('Asia/Ho_Chi_Minh');
if (session_status() === PHP_SESSION_NONE) {
    session_start(['use_strict_mode' => true, 'cookie_httponly' => true, 'cookie_samesite' => 'Lax']);
}

function csrf_token(): string {
    return $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_valid(mixed $token): bool {
    return is_string($token) && isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function guard_request(array $postActions, string $action): void {
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    if (in_array($action, $postActions, true) && $method !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        exit('Thao tác này cần được gửi từ biểu mẫu.');
    }
    if ($method === 'POST' && !csrf_valid($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        exit('Phiên biểu mẫu không hợp lệ. Vui lòng tải lại trang rồi thử lại.');
    }
    // Reject arrays in scalar input fields instead of crashing trim()/typed methods.
    foreach ([$_GET, $_POST] as $input) {
        foreach ($input as $key => $value) {
            if (!is_string($value) && !($key === 'permissions' && is_array($value) && count(array_filter($value, 'is_string')) === count($value))) {
                http_response_code(400);
                exit('Dữ liệu gửi lên không hợp lệ.');
            }
        }
    }
}

function handle_request_error(Throwable $error): void {
    if (isset($GLOBALS['controllerName'])) {
        error_log(get_class($error) . ': ' . $error->getMessage());
    }
    $message = $error instanceof DomainException ? $error->getMessage() : 'Không thể hoàn tất thao tác. Vui lòng thử lại hoặc liên hệ quản trị viên.';
    if ($error instanceof PDOException && ($error->errorInfo[1] ?? 0) === 1451) {
        $message = 'Không thể xóa vì dữ liệu này đang được sử dụng trong lịch sử đặt phòng, hóa đơn hoặc phân công.';
    }
    if ($error instanceof PDOException && ($error->errorInfo[1] ?? 0) === 1062) {
        $message = 'Dữ liệu đã tồn tại. Vui lòng kiểm tra tên đăng nhập, email, số điện thoại hoặc mã bản ghi.';
    }
    if ($error instanceof PDOException && in_array($error->errorInfo[1] ?? 0, [1452, 1406, 1264, 1292], true)) {
        $message = 'Dữ liệu không hợp lệ, quá dài hoặc bản ghi liên quan không còn tồn tại. Vui lòng kiểm tra lại biểu mẫu.';
    }
    http_response_code($error instanceof DomainException || ($error instanceof PDOException && in_array($error->errorInfo[1] ?? 0, [1062, 1451, 1452, 1406, 1264, 1292], true)) ? 422 : 500);
    echo '<!doctype html><html lang="vi"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Không thể hoàn tất</title><body><main style="max-width:640px;margin:3rem auto;padding:1rem;font-family:Arial,sans-serif"><h1>Không thể hoàn tất thao tác</h1><p>' . e($message) . '</p><button onclick="history.back()">Quay lại</button></main></body></html>';
}

function valid_date(string $date): bool {
    $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
    return $parsed !== false && $parsed->format('Y-m-d') === $date;
}

function validate_stay(string $arrival, string $departure, bool $allowPast = false): void {
    if (!valid_date($arrival) || !valid_date($departure) || $departure <= $arrival) {
        throw new DomainException('Ngày nhận/trả không hợp lệ. Ngày trả phải sau ngày nhận.');
    }
    if (!$allowPast && $arrival < date('Y-m-d')) {
        throw new DomainException('Ngày nhận phòng không được nằm trong quá khứ.');
    }
}

function validate_customer(string $name, string $email, string $phone, ?string $address, ?string $identity): void {
    if (trim($name) === '' || mb_strlen($name) > 255 || !preg_match('/^[+0-9][0-9 .()-]{7,19}$/D', $phone)) {
        throw new DomainException('Vui lòng nhập họ tên và số điện thoại hợp lệ.');
    }
    if (($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) || strlen($email) > 254 || mb_strlen($address ?? '') > 255 || strlen($identity ?? '') > 20) {
        throw new DomainException('Email, địa chỉ hoặc số giấy tờ không hợp lệ hoặc quá dài.');
    }
}

function verify_stored_password(string $password, string $stored): bool {
    if ($stored === '') return false;
    return password_get_info($stored)['algo'] !== null ? password_verify($password, $stored) : hash_equals($stored, $password);
}

function upload_image(string $field, string $folder): ?string {
    $file = $_FILES[$field] ?? null;
    if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if (!in_array($folder, ['phong', 'dichvu'], true) || !is_int($file['error'] ?? null) || $file['error'] !== UPLOAD_ERR_OK || ($file['size'] ?? 0) > 5 * 1024 * 1024) {
        throw new DomainException('Không tải được ảnh. Vui lòng chọn ảnh tối đa 5 MB.');
    }
    $tmp = $file['tmp_name'] ?? '';
    $mime = is_string($tmp) && is_uploaded_file($tmp) ? (new finfo(FILEINFO_MIME_TYPE))->file($tmp) : false;
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    if (!isset($extensions[$mime]) || !getimagesize($tmp)) throw new DomainException('Chỉ chấp nhận ảnh JPG, PNG, WebP hoặc GIF hợp lệ.');
    $directory = __DIR__ . '/../uploads/' . $folder;
    if (!is_dir($directory) && !mkdir($directory, 0755, true)) throw new RuntimeException('Không tạo được thư mục ảnh.');
    $name = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
    if (!move_uploaded_file($tmp, $directory . '/' . $name)) throw new RuntimeException('Không lưu được ảnh.');
    return '/uploads/' . $folder . '/' . $name;
}

/**
 * Trả về đường dẫn gốc của ứng dụng web
 */
function base_url(string $path = ''): string {
    static $base = null;
    if ($base === null) {
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        // Tìm vị trí của /admin hoặc index.php để lấy base folder
        $dir = str_replace('\\', '/', dirname($scriptName));
        if (preg_match('#^(.*?/hotel_php)#', $dir, $matches)) {
            $base = $matches[1];
        } elseif ($dir === '/' || $dir === '.') {
            $base = '';
        } else {
            // Loại bỏ /admin nếu đang ở trong admin
            $base = preg_replace('#/admin$#', '', $dir);
        }
    }
    $path = ltrim($path, '/');
    return $path ? ($base ? "$base/$path" : "/$path") : ($base ?: '/');
}

/**
 * Đường dẫn tới asset tĩnh (CSS, JS, Images, Uploads)
 */
function asset(string $path): string {
    return base_url($path);
}

/**
 * Tạo link cho trang Admin
 * Ví dụ: admin_url('c=phong&a=list') => /hotel_php/admin/index.php?c=phong&a=list
 */
function admin_url(string $query = ''): string {
    $url = base_url('admin/index.php');
    if ($query) {
        $url .= '?' . ltrim($query, '?');
    }
    return $url;
}

/**
 * Tạo link cho trang Khách hàng (Client)
 * Ví dụ: client_url('c=room&a=detail&id=101') => /hotel_php/index.php?c=room&a=detail&id=101
 */
function client_url(string $query = ''): string {
    $url = base_url('index.php');
    if ($query) {
        $url .= '?' . ltrim($query, '?');
    }
    return $url;
}

/**
 * Chuyển hướng trang
 */
function redirect(string $url): void {
    header("Location: $url");
    exit;
}

/**
 * Định dạng tiền tệ VNĐ
 */
function format_currency($amount): string {
    if ($amount === null || $amount === '') return '0 đ';
    return number_format((float)$amount, 0, ',', '.') . ' đ';
}

/**
 * Định dạng ngày tháng d/m/Y
 */
function format_date($dateStr): string {
    if (!$dateStr) return '';
    $timestamp = strtotime($dateStr);
    return $timestamp ? date('d/m/Y', $timestamp) : '';
}

/**
 * Set flash message (thông báo tạm thời qua session)
 */
function set_flash(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'danger', 'warning', 'info'
        'message' => $message
    ];
}

/**
 * Lấy flash message và xóa khỏi session
 */
function get_flash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Escape chuỗi HTML an toàn
 */
function e($value): string {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * Render view cho Admin với layout
 */
function render_admin_view(string $viewPath, array $data = []): void {
    extract($data);
    $currentUser = $_SESSION['admin_user'] ?? null;
    $flash = get_flash();

    $headerFile = __DIR__ . '/../views/admin/layouts/header.php';
    $navFile    = __DIR__ . '/../views/admin/layouts/nav.php';
    $footerFile = __DIR__ . '/../views/admin/layouts/footer.php';
    $viewFile   = __DIR__ . '/../views/admin/' . ltrim($viewPath, '/') . '.php';

    if (!file_exists($viewFile)) {
        die("View không tồn tại: $viewFile");
    }

    require $headerFile;
    if (empty($hideNav)) {
        require $navFile;
    }
    require $viewFile;
    require $footerFile;
}

/**
 * Render view cho Khách hàng (Client) với layout
 */
function render_client_view(string $viewPath, array $data = []): void {
    extract($data);
    $currentClient = $_SESSION['client_user'] ?? null;
    $flash = get_flash();

    $headerFile = __DIR__ . '/../views/client/layouts/header.php';
    $footerFile = __DIR__ . '/../views/client/layouts/footer.php';
    $viewFile   = __DIR__ . '/../views/client/' . ltrim($viewPath, '/') . '.php';

    if (!file_exists($viewFile)) {
        die("View không tồn tại: $viewFile");
    }

    require $headerFile;
    require $viewFile;
    require $footerFile;
}
