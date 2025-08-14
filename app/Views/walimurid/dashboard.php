<?= $this->extend('walimurid/templates/layout') ?>

<?= $this->section('title') ?>
Dashboard Wali Murid
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    $dateFormatter = new IntlDateFormatter('id_ID', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
    $dateTimeFormatter = new IntlDateFormatter('id_ID', IntlDateFormatter::LONG, IntlDateFormatter::SHORT);
?>
<div class="container-fluid p-0">
    <h4 class="mb-3 fw-bold"><i class="bi bi-speedometer2 me-2"></i>Dashboard Wali Murid</h4>

    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card h-100 py-2 shadow border-start border-danger border-4">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-danger text-uppercase mb-1">Sisa Tanggungan</div>
                            <div class="h4 mb-0 fw-bold text-gray-800">Rp <?= number_format($sisaTanggungan ?? 0, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-receipt-cutoff fs-1 text-danger"></i>
                        </div>
                    </div>
                </div>
                <a href="<?= site_url('walimurid/tagihan_spp') ?>" class="card-footer text-decoration-none text-danger d-flex justify-content-between align-items-center">
                    <span class="small">Lihat Detail Tanggungan</span>
                    <i class="bi bi-arrow-right-circle small"></i>
                </a>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card h-100 py-2 shadow border-start border-primary border-4">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">Sisa Uang Saku</div>
                            <div class="h4 mb-0 fw-bold text-gray-800">Rp <?= number_format($sisaUangSaku ?? 0, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-wallet2 fs-1 text-primary"></i>
                        </div>
                    </div>
                </div>
                <a href="<?= site_url('walimurid/uang_saku') ?>" class="card-footer text-decoration-none text-primary d-flex justify-content-between align-items-center">
                    <span class="small">Lihat Detail Uang Saku</span>
                    <i class="bi bi-arrow-right-circle small"></i>
                </a>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card h-100 py-2 shadow border-start border-info border-4">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">Wali Dari Siswa</div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?= esc($waliDari ?? '-') ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-check-fill fs-1 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row my-2">
        <div class="col-md-7 mb-4">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0"><i class="bi bi-receipt-cutoff"></i> Daftar Tanggungan Belum Lunas - <?= esc($waliDari) ?></h5>
                </div>
                <div class="card-body p-3">
                    <?php if (!empty($daftarTanggunganSiswa)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Nama Tagihan</th>
                                        <th scope="col">Jatuh Tempo</th>
                                        <th scope="col" class="text-center">Status</th>
                                        <th scope="col" class="text-end">Sisa Tagihan (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($daftarTanggunganSiswa as $t): ?>
                                        <?php
                                            $sisa_tagihan = (int)$t['nominal_tagihan'] - (int)($t['jumlah_bayar'] ?? 0);
                                            $is_dicicil = (int)($t['jumlah_bayar'] ?? 0) > 0;
                                            
                                            // MODIFIKASI: Logika untuk cek jatuh tempo
                                            $today = new DateTime();
                                            $dueDate = new DateTime($t['jatuh_tempo']);
                                            $today->setTime(0, 0, 0);
                                            $dueDate->setTime(0, 0, 0);
                                            $isOverdue = $dueDate < $today;
                                        ?>
                                        <tr>
                                            <td><?= esc($t['nama_tagihan']); ?></td>
                                            <td><?= $dateFormatter->format(strtotime($t['jatuh_tempo'])); ?></td>
                                            <td class="text-center">
                                                <?php if($isOverdue): ?>
                                                    <span class="badge bg-danger">JATUH TEMPO</span>
                                                <?php elseif($is_dicicil): ?>
                                                    <span class="badge bg-info-soft text-info">Dicicil</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger-soft text-danger">Belum Lunas</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end fw-bold"><?= number_format($sisa_tagihan, 0, ',', '.'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="<?= site_url('walimurid/tagihan_spp'); ?>" class="btn btn-outline-danger btn-sm">
                                Lihat Semua & Lakukan Pembayaran <i class="bi bi-arrow-right-circle"></i>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-success mb-0" role="alert">
                            <i class="bi bi-check-circle-fill"></i> Luar biasa! Tidak ada tanggungan yang belum lunas untuk <?= esc($waliDari); ?> saat ini.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-5 mb-4">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0"><i class="bi bi-cash-stack"></i> Riwayat Pengambilan Uang Saku</h5>
                </div>
                <div class="card-body p-3">
                    <?php if (!empty($riwayatPengambilanUangSaku)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Tanggal</th>
                                        <th scope="col" class="text-end">Nominal(Rp)</th>
                                        <th scope="col">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($riwayatPengambilanUangSaku as $transaksi): ?>
                                        <tr>
                                            <td><?= $dateTimeFormatter->format(strtotime($transaksi['tanggal'])); ?></td>
                                            <td class="text-end text-danger">-<?= number_format($transaksi['nominal'], 0, ',', '.'); ?></td>
                                            <td><?= esc($transaksi['catatan'] ?? '-'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if (count($riwayatPengambilanUangSaku) >= 10): ?>
                            <div class="text-center mt-3">
                                <a href="<?= site_url('walimurid/uang_saku'); ?>" class="btn btn-outline-primary btn-sm">
                                    Lihat Semua Riwayat Uang Saku <i class="bi bi-arrow-right-circle"></i>
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-info mb-0" role="alert">
                           <i class="bi bi-info-circle-fill"></i> Belum ada riwayat pengambilan uang saku.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .text-xs { font-size: .8rem; }
    .text-gray-800 { color: #5a5c69 !important; }
    .border-start.border-4 { border-left-width: 0.25rem !important; }
    .card .card-footer { background-color: #f8f9fc; border-top: 1px solid #e3e6f0; padding: 0.65rem 1.25rem; font-size: 0.8rem; }
    .card .card-footer:hover { background-color: #e9ecef; }
    .table-sm th, .table-sm td { padding: 0.5rem; }
    .bg-danger-soft { background-color: rgba(220, 53, 69, 0.1); }
    .text-danger { color: #dc3545 !important; }
    .bg-info-soft { background-color: rgba(13, 202, 240, 0.1); }
    .text-info { color: #0dcaf0 !important; }
</style>
<?= $this->endSection() ?>
