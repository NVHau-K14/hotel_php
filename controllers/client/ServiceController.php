<?php
// controllers/client/ServiceController.php

require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../models/DichVuModel.php';

class ServiceController {
    public function list(): void {
        $services = DichVuModel::getAll();
        render_client_view('services', [
            'title'    => 'Dịch vụ tiện ích tại Khách sạn',
            'services' => $services
        ]);
    }
}
