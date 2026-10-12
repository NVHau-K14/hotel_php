<?php
// controllers/admin/DashboardController.php

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../models/ThongKeModel.php';
require_once __DIR__ . '/../../models/DatPhongModel.php';
require_once __DIR__ . '/../../models/PhongModel.php';

class DashboardController {
    public function index(): void {
        Auth::requireAdminLogin();
        Auth::requirePermission('DASHBOARD');

        $summary = ThongKeModel::getSummary();
        $donChoDuyet = DatPhongModel::getAll('CHO_ADMIN_DUYET');
        $phongStats = PhongModel::countByStatus();

        render_admin_view('dashboard/index', [
            'title'       => 'Bảng điều khiển - Quản trị Khách sạn',
            'summary'     => $summary,
            'donChoDuyet' => $donChoDuyet,
            'phongStats'  => $phongStats
        ]);
    }
}
