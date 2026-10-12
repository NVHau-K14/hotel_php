<?php
// controllers/admin/ThongKeController.php

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../models/ThongKeModel.php';

class ThongKeController {
    public function index(): void {
        Auth::requirePermission('THONG_KE');
        $year = (int)($_GET['year'] ?? date('Y'));
        $summary = ThongKeModel::getSummary();
        $monthlyRevenue = ThongKeModel::getMonthlyRevenue($year);

        render_admin_view('thong-ke/index', [
            'title'          => 'Báo cáo Thống kê',
            'summary'        => $summary,
            'monthlyRevenue' => $monthlyRevenue,
            'year'           => $year
        ]);
    }
}
