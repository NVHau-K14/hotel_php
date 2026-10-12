<?php
// views/client/home.php
?>
<main>
    <section class="client-hero">
        <div>
            <p class="eyebrow">Trải nghiệm kỳ nghỉ tuyệt vời</p>
            <h1>Khách sạn Nghỉ dưỡng Vip</h1>
            <p>Chọn phòng, kiểm tra lịch trống và gửi yêu cầu đặt phòng trực tuyến tiện lợi chỉ trong vài bước.</p>
            <div class="hero-actions">
                <a class="btn" href="<?= e(client_url('c=room')) ?>">Khám phá phòng ngay</a>
                <a class="btn secondary" href="<?= e(client_url('c=service')) ?>">Xem dịch vụ</a>
            </div>
        </div>
    </section>

    <!-- PHÒNG NỔI BẬT -->
    <section class="client-section">
        <div class="section-heading">
            <h2>Phòng nổi bật</h2>
            <a href="<?= e(client_url('c=room')) ?>">Xem tất cả phòng &rarr;</a>
        </div>
        <div class="room-card-grid">
            <?php foreach ($phongs as $p): ?>
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
                        <p class="text-muted"><?= e($p['TenLoaiPhong'] ?? 'Bình Dân') ?></p>
                        <strong class="text-primary fs-5"><?= format_currency($p['GiaPhong']) ?> / đêm</strong>
                        <div class="card-actions mt-3">
                            <a class="btn small" href="<?= e(client_url('c=room&a=detail&id=' . $p['MaPhong'])) ?>">Chi tiết</a>
                            <?php if ($p['TrangThai'] !== 'Bảo trì'): ?>
                                <a class="btn small secondary" href="<?= e(client_url('c=booking&maPhong=' . $p['MaPhong'])) ?>">Đặt phòng</a>
                            <?php else: ?>
                                <span class="btn small secondary disabled" style="opacity: 0.6;">Bảo trì</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- DỊCH VỤ -->
    <section class="client-section">
        <div class="section-heading">
            <h2>Dịch vụ tiện ích</h2>
            <a href="<?= e(client_url('c=service')) ?>">Xem tất cả dịch vụ &rarr;</a>
        </div>
        <div class="service-grid service-grid-wide">
            <?php foreach ($dichVus as $dv): ?>
                <article class="client-card service-card split-service-card">
                    <div class="service-info">
                        <h3><?= e($dv['TenDichVu']) ?></h3>
                        <p>Dịch vụ tiêu chuẩn mang lại trải nghiệm tiện nghi nhất cho bạn.</p>
                        <strong class="text-primary"><?= format_currency($dv['GiaDichVu']) ?></strong>
                    </div>
                    <div class="service-visual service-photo">
                        <?php if (!empty($dv['HinhAnh'])): ?>
                            <img src="<?= e(asset($dv['HinhAnh'])) ?>" alt="<?= e($dv['TenDichVu']) ?>">
                        <?php else: ?>
                            <span>DV</span>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>
