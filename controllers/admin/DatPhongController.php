<?php
// controllers/admin/DatPhongController.php

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../models/DatPhongModel.php';
require_once __DIR__ . '/../../models/PhongModel.php';
require_once __DIR__ . '/../../models/DichVuModel.php';
require_once __DIR__ . '/../../models/KhachHangModel.php';
require_once __DIR__ . '/../../models/HoaDonModel.php';

class DatPhongController {
    public function list(): void {
        Auth::requirePermission('DAT_PHONG');

        $trangThai = $_GET['trangThai'] ?? null;
        $keyword   = $_GET['keyword'] ?? null;

        $items  = DatPhongModel::getAll($trangThai, $keyword);
        $counts = DatPhongModel::countByStatus();

        render_admin_view('dat-phong/list', [
            'title'     => 'Quản lý Đặt phòng',
            'items'     => $items,
            'counts'    => $counts,
            'trangThai' => $trangThai,
            'keyword'   => $keyword
        ]);
    }

    public function detail(): void {
        Auth::requirePermission('DAT_PHONG');
        $id = (int)($_GET['id'] ?? 0);
        $item = DatPhongModel::getById($id);
        if (!$item) {
            set_flash('danger', 'Không tìm thấy đơn đặt phòng.');
            redirect(admin_url('c=dat-phong'));
        }

        $allServices = DichVuModel::getAll();
        $calc = HoaDonModel::calculateBookingTotal($id);
        $invoice = HoaDonModel::getByBookingId($id);

        render_admin_view('dat-phong/detail', [
            'title'       => "Chi tiết đơn đặt phòng #$id",
            'item'        => $item,
            'calc'        => $calc,
            'allServices' => $allServices,
            'invoice'     => $invoice
        ]);
    }

    public function approve(): void {
        Auth::requirePermission('DAT_PHONG');
        $id = (int)($_GET['id'] ?? 0);
        $admin = Auth::currentAdmin();
        if ($id > 0) {
            DatPhongModel::updateStatus($id, 'DA_XAC_NHAN', $admin['id'] ?? null);
            set_flash('success', "Đã duyệt đơn đặt phòng #$id thành công.");
        }
        redirect(admin_url('c=dat-phong&a=detail&id=' . $id));
    }

    public function checkin(): void {
        Auth::requirePermission('DAT_PHONG');
        $id = (int)($_GET['id'] ?? 0);
        $admin = Auth::currentAdmin();
        if ($id > 0) {
            DatPhongModel::updateStatus($id, 'DANG_SU_DUNG', $admin['id'] ?? null);
            set_flash('success', "Khách đã nhận phòng (Check-in) cho đơn #$id.");
        }
        redirect(admin_url('c=dat-phong&a=detail&id=' . $id));
    }

    public function checkout(): void {
        Auth::requirePermission('DAT_PHONG');
        $id = (int)($_GET['id'] ?? 0);
        $admin = Auth::currentAdmin();
        if ($id > 0) {
            // Tạo hoặc cập nhật hóa đơn
            $booking = DatPhongModel::getById($id);
            if (!$booking || $booking['TrangThai'] !== 'DANG_SU_DUNG') throw new DomainException('Chỉ trả phòng cho đơn đang sử dụng.');
            $invoiceId = HoaDonModel::createOrUpdateInvoice($id, $admin['id'] ?? null);
            set_flash('success', "Đã tạo hóa đơn cho đơn đặt phòng #$id. Vui lòng kiểm tra và xác nhận thanh toán.");
            redirect(admin_url('c=hoa-don&a=detail&id=' . $invoiceId));
        }
        redirect(admin_url('c=dat-phong'));
    }

    public function cancel(): void {
        Auth::requirePermission('DAT_PHONG');
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            DatPhongModel::updateStatus($id, 'DA_HUY');
            set_flash('success', "Đã hủy đơn đặt phòng #$id.");
        }
        redirect(admin_url('c=dat-phong'));
    }

    public function addService(): void {
        Auth::requirePermission('DAT_PHONG');
        $bookingId = (int)($_POST['bookingId'] ?? 0);
        $serviceId = (int)($_POST['serviceId'] ?? 0);
        $quantity  = (int)($_POST['quantity'] ?? 1);

        if ($bookingId > 0 && $serviceId > 0 && $quantity > 0) {
            DichVuModel::addServiceToBooking($bookingId, $serviceId, $quantity);
            // Cập nhật lại số tiền trong hóa đơn nếu đã có hóa đơn
            $admin = Auth::currentAdmin();
            // Existing invoice totals are refreshed atomically in CtDichVuModel.
            set_flash('success', 'Đã thêm dịch vụ vào đơn đặt phòng.');
        }
        redirect(admin_url('c=dat-phong&a=detail&id=' . $bookingId));
    }

    public function removeService(): void {
        Auth::requirePermission('DAT_PHONG');
        $bookingId = (int)($_GET['bookingId'] ?? 0);
        $serviceId = (int)($_GET['serviceId'] ?? 0);

        if ($bookingId > 0 && $serviceId > 0) {
            DichVuModel::removeServiceFromBooking($bookingId, $serviceId);
            $admin = Auth::currentAdmin();
            // Existing invoice totals are refreshed atomically in CtDichVuModel.
            set_flash('success', 'Đã xóa dịch vụ khỏi đơn.');
        }
        redirect(admin_url('c=dat-phong&a=detail&id=' . $bookingId));
    }

    public function add(): void {
        Auth::requirePermission('DAT_PHONG');
        $customers = KhachHangModel::getAll();
        $rooms = PhongModel::getAll();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $maKhachHang = (int)($_POST['maKhachHang'] ?? 0);
            $maPhong     = (int)($_POST['maPhong'] ?? 0);
            $ngayNhan    = $_POST['ngayNhan'] ?? '';
            $ngayTra     = $_POST['ngayTra'] ?? '';
            $trangThai   = $_POST['trangThai'] ?? 'DA_XAC_NHAN';

            if ($maKhachHang <= 0 || $maPhong <= 0 || empty($ngayNhan) || empty($ngayTra)) {
                $error = 'Vui lòng điền đầy đủ khách hàng, phòng, ngày nhận và ngày trả.';
            } elseif (!valid_date($ngayNhan) || !valid_date($ngayTra) || $ngayNhan >= $ngayTra) {
                $error = 'Ngày trả phòng phải sau ngày nhận phòng.';
            } else {
                $admin = Auth::currentAdmin();
                $newId = DatPhongModel::createBooking($maKhachHang, $maPhong, $ngayNhan, $ngayTra, $admin['id'] ?? null, $trangThai);
                set_flash('success', "Tạo đơn đặt phòng #$newId thành công!");
                redirect(admin_url('c=dat-phong&a=detail&id=' . $newId));
            }
        }

        render_admin_view('dat-phong/form', [
            'title'     => 'Tạo đơn đặt phòng mới',
            'customers' => $customers,
            'rooms'     => $rooms,
            'error'     => $error
        ]);
    }
}
