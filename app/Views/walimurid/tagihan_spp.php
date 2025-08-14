<!-- C:\xampp\htdocs\payment-gateway\app\Views\walimurid\tagihan_spp.php -->
<?= $this->extend('walimurid/templates/layout') ?>
<?= $this->section('title') ?>
Tagihan SPP
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    $dateFormatter = new IntlDateFormatter('id_ID', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
    // Ganti dengan nomor WhatsApp administrasi Anda (gunakan format internasional tanpa + atau 0 di depan)
    $nomor_admin_wa = '6285784794213'; 
?>
<div class="container-fluid p-0">
    <h4 class="mb-3 fw-bold"><i class="bi bi-receipt-cutoff me-2"></i>Tagihan SPP <?= isset($namaSiswa) && $namaSiswa !== 'Siswa tidak ditemukan' ? ' - ' . esc($namaSiswa) : '' ?></h4>

    <div class="mb-4">
        <a href="<?= site_url('walimurid/pembayaran_spp') ?>" class="btn btn-primary shadow-sm">
            <i class="bi bi-credit-card-2-front-fill me-2"></i> Bayar Tagihan SPP
        </a>
    </div>

    <h5 class="mb-3">Daftar Tagihan yang Perlu Diselesaikan</h5>
    <?php if (!empty($daftarTagihan)): ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($daftarTagihan as $item): ?>
                <div class="col">
                    <?php
                        $sisaTagihan = (int)$item['nominal'] - (int)($item['jumlah_bayar'] ?? 0);
                        $isDicicil = (int)($item['jumlah_bayar'] ?? 0) > 0;
                        $isPending = isset($item['status_pembayaran_pending']) && $item['status_pembayaran_pending'] === 'Pending';
                        
                        $today = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
                        $dueDate = new DateTime($item['jatuh_tempo']);
                        $today->setTime(0, 0, 0);
                        $dueDate->setTime(0, 0, 0);
                        $isOverdue = $dueDate < $today;
                        $interval = $today->diff($dueDate);
                        $daysOverdue = $interval->days;

                        // Penentuan Tampilan Visual
                        if ($isOverdue) {
                            $cardHeaderClass = 'bg-danger text-white';
                            $cardBorderClass = 'border-danger';
                            $badgeText = "JATUH TEMPO ($daysOverdue hari)";
                            $badgeIcon = 'bi-exclamation-triangle-fill';
                            $badgeClass = 'bg-danger';
                        } elseif ($isPending) {
                            $cardHeaderClass = 'bg-light';
                            $cardBorderClass = 'border-warning';
                            $badgeText = 'Menunggu Pembayaran';
                            $badgeIcon = 'bi-hourglass-split';
                            $badgeClass = 'bg-warning-soft text-warning';
                        } elseif ($isDicicil) {
                             $cardHeaderClass = 'bg-light';
                             $cardBorderClass = 'border-info';
                            $badgeText = 'Dicicil';
                            $badgeIcon = 'bi-pie-chart-fill';
                            $badgeClass = 'bg-info-soft text-info';
                        } else {
                             $cardHeaderClass = 'bg-light';
                             $cardBorderClass = 'border-danger';
                            $badgeText = 'Belum Lunas';
                            $badgeIcon = 'bi-exclamation-circle-fill';
                            $badgeClass = 'bg-danger-soft text-danger';
                        }
                    ?>
                    <!-- MODIFIKASI: Tambahkan kelas border-start border-4 -->
                    <div class="card h-100 shadow border-start border-4 <?= $cardBorderClass ?>"> 
                        <div class="card-header <?= $cardHeaderClass ?>">
                            <h5 class="card-title fw-bold mb-0"><i class="bi bi-receipt me-2"></i><?= esc($item['nama_tagihan']) ?></h5>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <p class="card-text mb-1"><small class="text-muted">Jatuh Tempo:</small><br><strong class="fs-6"><?= $dateFormatter->format(strtotime($item['jatuh_tempo'])) ?></strong></p>
                            
                            <div class="card-text mb-3">
                                <small class="text-muted">Total Tagihan:</small><br>
                                <strong class="fs-6 text-secondary" style="text-decoration: <?= $isDicicil ? 'line-through' : 'none' ?>;">
                                    Rp <?= number_format($item['nominal'], 0, ',', '.') ?>
                                </strong>
                                <?php if($isDicicil): ?>
                                    <br><small class="text-muted">Sisa Tagihan:</small><br>
                                    <strong class="fs-5 text-danger">Rp <?= number_format($sisaTagihan, 0, ',', '.') ?></strong>
                                <?php endif; ?>
                            </div>

                            <div class="mt-auto">
                                <p class="card-text mb-2"><small class="text-muted">Status:</small><br><span class="badge <?= $badgeClass ?> fs-6 mt-1"><i class="bi <?= $badgeIcon ?> me-1"></i><?= $badgeText ?></span></p>
                                <a href="<?= site_url('walimurid/pembayaran_spp?selected_tagihan_id=' . $item['id_tagihan_untuk_form']) ?>" class="btn btn-sm btn-danger w-100">
                                    <i class="bi bi-credit-card-fill me-1"></i> Bayar Tagihan
                                </a>
                                <?php if($isOverdue): 
                                    $pesan_wa = "Assalamu'alaikum, saya wali dari siswa an. " . urlencode($namaSiswa) . " ingin bertanya mengenai tagihan SPP: '" . urlencode($item['nama_tagihan']) . "' yang telah jatuh tempo.";
                                ?>
                                <a href="https://api.whatsapp.com/send?phone=<?= $nomor_admin_wa ?>&text=<?= $pesan_wa ?>" target="_blank" class="btn btn-sm btn-success w-100 mt-2">
                                    <i class="bi bi-whatsapp me-1"></i> Hubungi Administrasi
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-success text-center shadow-sm" role="alert"> 
            <h5 class="alert-heading"><i class="bi bi-patch-check-fill me-2"></i>Luar Biasa!</h5>
            <p class="mb-0">Tidak ada tagihan SPP yang belum lunas untuk <?= isset($namaSiswa) && $namaSiswa !== 'Siswa tidak ditemukan' ? '<strong>'.esc($namaSiswa).'</strong>' : 'Anda'; ?> saat ini.</p>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .bg-danger-soft { background-color: rgba(220, 53, 69, 0.1); }
    .text-danger { color: #dc3545 !important; }
    .bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); }
    .text-warning { color: #ffc107 !important; }
    .bg-info-soft { background-color: rgba(13, 202, 240, 0.1); }
    .text-info { color: #0dcaf0 !important; }
</style>
<?= $this->endSection() ?>
