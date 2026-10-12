<?php
// views/client/rooms.php
?>
<main class="container my-4">
    <div class="mb-4 text-center">
        <h1 class="fw-bold">Danh sách phòng nghỉ</h1>
        <p class="text-muted">Chọn phòng phù hợp nhất cho chuyến đi của bạn</p>
    </div>

    <!-- FILTER BAR -->
    <div class="card p-4 border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <form method="GET" action="<?= e(client_url()) ?>" class="row g-3">
            <input type="hidden" name="c" value="room">

            <div class="col-md-3">
                <label class="form-label small fw-semibold">Loại phòng</label>
                <select name="maLoaiPhong" class="form-select form-select-sm">
                    <option value="">Tất cả loại phòng</option>
                    <?php foreach ($loaiPhongs as $lp): ?>
                        <option value="<?= $lp['MaLoaiPhong'] ?>" <?= ($maLoaiPhong == $lp['MaLoaiPhong']) ? 'selected' : '' ?>>
                            <?= e($lp['TenLoaiPhong']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold">Giá từ</label>
                <input type="number" name="giaTu" class="form-control form-control-sm" placeholder="Từ" value="<?= e($giaTu ?? '') ?>">
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold">Giá đến</label>
                <input type="number" name="giaDen" class="form-control form-control-sm" placeholder="Đến" value="<?= e($giaDen ?? '') ?>">
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold">Ngày nhận</label>
                <input type="date" name="ngayNhan" class="form-control form-control-sm" value="<?= e($ngayNhan ?? '') ?>">
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold">Ngày trả</label>
                <input type="date" name="ngayTra" class="form-control form-control-sm" value="<?= e($ngayTra ?? '') ?>">
            </div>

            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-sm btn-primary w-100 py-1">Tìm</button>
            </div>
        </form>
    </div>

    <?php if (!empty($dateError)): ?>
        <div class="alert alert-danger"><?= e($dateError) ?></div>
    <?php endif; ?>

    <!-- ROOM GRID -->
    <div class="room-card-grid">
        <?php if (!empty($items)): ?>
            <?php foreach ($items as $p): ?>
                <article class="client-card room-card">
                    <div class="room-visual image-visual">
                        <?php if (!empty($p['HinhAnhDaiDien'])): ?>
                            <img src="<?= e(asset($p['HinhAnhDaiDien'])) ?>" alt="Phòng <?= $p['MaPhong'] ?>">
                        <?php else: ?>
                            <span>P.<?= $p['MaPhong'] ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="room-card-body">
                        <h3>Phòng <?= $p['MaPhong'] ?></h3>
                        <p class="text-muted"><?= e($p['TenLoaiPhong']) ?></p>
                        <strong class="text-primary fs-5"><?= format_currency($p['GiaPhong']) ?> / đêm</strong>
                        <div class="card-actions mt-3">
                            <a class="btn small" href="<?= e(client_url('c=room&a=detail&id=' . $p['MaPhong'])) ?>">Chi tiết</a>
                            <?php if ($p['TrangThai'] !== 'Bảo trì'): ?>
                                <a class="btn small secondary" href="<?= e(client_url('c=booking&maPhong=' . $p['MaPhong'] . (!empty($ngayNhan) ? "&ngayNhan=$ngayNhan" : '') . (!empty($ngayTra) ? "&ngayTra=$ngayTra" : ''))) ?>">
                                    Đặt ngay
                                </a>
                            <?php else: ?>
                                <span class="btn small secondary disabled" style="opacity: 0.6;">Bảo trì</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-5 w-100">
                <i class="fa-solid fa-hotel fa-3x text-muted mb-3 d-block"></i>
                <h5>Không tìm thấy phòng nào phù hợp với yêu cầu tìm kiếm.</h5>
                <p class="text-muted">Vui lòng thử chọn khoảng ngày khác hoặc tìm kiếm với mức giá rộng hơn.</p>
                <a href="<?= e(client_url('c=room')) ?>" class="btn btn-outline-primary">Xem tất cả phòng</a>
            </div>
        <?php endif; ?>
    </div>
</main>
