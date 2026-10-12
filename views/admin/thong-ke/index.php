<?php
// views/admin/thong-ke/index.php
?>
<div class="container admin-wide my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-0">Báo cáo thống kê</h1>
            <p class="text-muted mb-0">Tổng hợp doanh thu và hoạt động kinh doanh khách sạn</p>
        </div>
        <form method="GET" class="d-flex gap-2 align-items-center">
            <input type="hidden" name="c" value="thong-ke">
            <label class="fw-semibold text-nowrap">Năm:</label>
            <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                <?php for ($y = date('Y'); $y >= date('Y') - 4; $y--): ?>
                    <option value="<?= $y ?>" <?= ($year == $y) ? 'selected' : '' ?>>Năm <?= $y ?></option>
                <?php endfor; ?>
            </select>
        </form>
    </div>

    <!-- SUMMARY CARDS -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card p-4 border-0 shadow-sm bg-primary text-white">
                <span class="opacity-75">TỔNG DOANH THU ĐÃ THU</span>
                <h2 class="fw-bold mt-2 mb-0"><?= format_currency($summary['tongDoanhThu'] ?? 0) ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 border-0 shadow-sm bg-success text-white">
                <span class="opacity-75">DOANH THU THÁNG NÀY</span>
                <h2 class="fw-bold mt-2 mb-0"><?= format_currency($summary['doanhThuThangNay'] ?? 0) ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 border-0 shadow-sm bg-info text-white">
                <span class="opacity-75">TỔNG KHÁCH HÀNG</span>
                <h2 class="fw-bold mt-2 mb-0"><?= $summary['tongKhachHang'] ?? 0 ?> khách</h2>
            </div>
        </div>
    </div>

    <!-- CHART CARD -->
    <div class="card border-0 shadow-sm p-4 mb-4">
        <h5 class="fw-bold text-primary mb-3">Biểu đồ doanh thu theo từng tháng (Năm <?= $year ?>)</h5>
        <canvas id="revenueChart" style="max-height: 360px;"></canvas>
    </div>

    <!-- TABLE MONTHLY -->
    <div class="card border-0 shadow-sm p-4">
        <h5 class="fw-bold mb-3">Chi tiết doanh thu 12 tháng (Năm <?= $year ?>)</h5>
        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <th>Tháng <?= $m ?></th>
                        <?php endfor; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <td class="fw-bold <?= ($monthlyRevenue[$m] > 0) ? 'text-success' : 'text-muted' ?>">
                                <?= format_currency($monthlyRevenue[$m]) ?>
                            </td>
                        <?php endfor; ?>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: <?= json_encode(array_values($monthlyRevenue)) ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('vi-VN') + ' đ';
                        }
                    }
                }
            }
        }
    });
</script>
