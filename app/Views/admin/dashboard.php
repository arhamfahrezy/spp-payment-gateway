<!-- C:\xampp\htdocs\payment-gateway\app\Views\admin\dashboard.php -->
<?= $this->extend('admin/templates/layout') ?>
<?= $this->section('title') ?>
<?= $title ?? 'Dashboard Admin' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    // Formatter untuk tanggal (e.g., 7 Juli 2025)
    $dateFormatter = new IntlDateFormatter('id_ID', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
?>
<div class="container-fluid p-0">
    <h4 class="mb-4 fw-bold"><i class="bi bi-speedometer2 me-2"></i>Dashboard Admin</h4>

    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card h-100 py-2 shadow border-start border-primary border-4">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total Siswa Aktif</div>
                            <div class="h4 mb-0 fw-bold text-gray-800"><?= esc($totalSiswa ?? 0) ?> Siswa</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people-fill fs-1 text-primary"></i>
                        </div>
                    </div>
                </div>
                <a href="<?= site_url('admin/data_siswa') ?>" class="card-footer text-decoration-none text-primary stretched-link d-flex justify-content-between align-items-center">
                    <span class="small">Lihat Detail Siswa</span>
                    <i class="bi bi-arrow-right-circle small"></i>
                </a>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card h-100 py-2 shadow border-start border-success border-4">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">Pemasukan SPP (Bulan Ini)</div>
                            <div class="h4 mb-0 fw-bold text-gray-800">Rp <?= number_format($rekapBulanan ?? 0, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-wallet-fill fs-1 text-success"></i>
                        </div>
                    </div>
                </div>
                 <a href="<?= site_url('admin/laporan') ?>" class="card-footer text-decoration-none text-success stretched-link d-flex justify-content-between align-items-center">
                    <span class="small">Lihat Laporan Keuangan</span>
                    <i class="bi bi-arrow-right-circle small"></i>
                </a>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card h-100 py-2 shadow border-start border-danger border-4">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-danger text-uppercase mb-1">Total Tanggungan SPP</div>
                            <div class="h4 mb-0 fw-bold text-gray-800">Rp <?= number_format($jumlahTanggungan ?? 0, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-file-earmark-excel-fill fs-1 text-danger"></i>
                        </div>
                    </div>
                </div>
                 <a href="<?= site_url('admin/manajemen_spp#daftar') ?>" class="card-footer text-decoration-none text-danger stretched-link d-flex justify-content-between align-items-center">
                    <span class="small">Lihat Detail Tanggungan</span>
                    <i class="bi bi-arrow-right-circle small"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row my-2"> 
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light py-3 d-flex flex-row align-items-center justify-content-between">
                    <h5 class="mb-0 text-primary"><i class="bi bi-journals me-2"></i>Tagihan SPP Aktif</h5>
                    <a href="<?= site_url('admin/tambah_tagihan') ?>" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Tagihan Baru
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 415px; overflow-y: auto;"> 
                        <table class="table table-sm table-bordered table-hover mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="text-center" style="width: 5%;">No</th>
                                    <th>Nama Tagihan</th>
                                    <th class="text-center" style="width: 7%;">Kelas</th>
                                    <th style="width: 25%;">Jatuh Tempo</th>
                                    <th class="text-end" style="width: 22%;">Nominal (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($sppAktifList)): ?>
                                    <?php foreach ($sppAktifList as $index => $tagihan): ?>
                                        <tr>
                                            <td class="text-center"><?= $index + 1 ?></td>
                                            <td><?= esc($tagihan['nama_tagihan']) ?></td>
                                            <td class="text-center"><?= esc($tagihan['kelas']) ?></td>
                                            <td><?= $dateFormatter->format(strtotime($tagihan['jatuh_tempo'])) ?></td>
                                            <td class="text-end"><?= number_format($tagihan['nominal'], 0, ',', '.') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center p-4">
                                            <i class="bi bi-info-circle fs-3 text-muted"></i><br>
                                            Tidak ada tagihan aktif.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                 <a href="<?= site_url('admin/manajemen_spp#aktif') ?>" class="card-footer text-decoration-none text-primary d-flex justify-content-between align-items-center">
                    <span class="small">Kelola Tagihan</span>
                    <i class="bi bi-arrow-right-circle small"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0 text-primary"><i class="bi bi-pie-chart-fill me-2"></i>Ringkasan Keuangan</h5>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="text-center mb-3">
                        <canvas id="paymentDonutChart" style="max-height: 220px; width: 100% !important;"></canvas>
                    </div>
                    <!-- MODIFIKASI: Tambahkan list item untuk SPP Dicicil -->
                    <ul class="list-group list-group-flush mt-auto">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            SPP Lunas
                            <span class="badge bg-success rounded-pill fs-6">Rp <?= number_format($jumlahLunas ?? 0, 0, ',', '.') ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            SPP Dicicil (Sudah Masuk)
                            <span class="badge bg-info rounded-pill fs-6">Rp <?= number_format($jumlahDicicil ?? 0, 0, ',', '.') ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Sisa Tanggungan
                            <span class="badge bg-danger rounded-pill fs-6">Rp <?= number_format($jumlahBelumLunas ?? 0, 0, ',', '.') ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Uang Saku Masuk
                            <span class="badge bg-primary rounded-pill fs-6">Rp <?= number_format($totalUangMasuk ?? 0, 0, ',', '.') ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Uang Saku Keluar
                            <span class="badge bg-warning text-dark rounded-pill fs-6">Rp <?= number_format($totalUangKeluar ?? 0, 0, ',', '.') ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .text-xs { font-size: .75rem !important; }
    .text-gray-800 { color: #5a5c69 !important; }
    .border-start.border-4 { border-left-width: 0.25rem !important; }

    .card .card-footer {
        background-color: #f8f9fc;
        border-top: 1px solid #e3e6f0;
        padding: 0.65rem 1.25rem;
        font-size: 0.8rem;
    }
    .card .card-footer:hover {
        background-color: #e9ecef;
    }
    .table-sm th, .table-sm td { padding: 0.4rem; }
    .list-group-item { border-left: 0; border-right: 0; }
    .badge.fs-6 { padding: 0.4em 0.65em; }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('paymentDonutChart');
        if (ctx) {
            new Chart(ctx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    // MODIFIKASI: Update labels, data, dan colors
                    labels: ['SPP Lunas', 'Sisa Tanggungan', 'SPP Dicicil', 'Uang Saku Masuk', 'Uang Saku Keluar'],
                    datasets: [{
                        label: 'Jumlah (Rp)',
                        data: [
                            <?= esc($jumlahLunas ?? 0, 'js') ?>, 
                            <?= esc($jumlahBelumLunas ?? 0, 'js') ?>, 
                            <?= esc($jumlahDicicil ?? 0, 'js') ?>,
                            <?= esc($totalUangMasuk ?? 0, 'js') ?>, 
                            <?= esc($totalUangKeluar ?? 0, 'js') ?>
                        ],
                        backgroundColor: [
                            '#198754', // Hijau (Lunas)
                            '#dc3545', // Merah (Tanggungan)
                            '#0dcaf0', // Biru Info (Dicicil)
                            '#0d6efd', // Biru Primary (Uang Saku Masuk)
                            '#ffc107'  // Kuning (Uang Saku Keluar)
                        ],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { 
                            position: 'bottom',
                            labels: { padding: 15, usePointStyle: true, font: { size: 10 } }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) { label += ': '; }
                                    if (context.parsed !== null) {
                                        label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    cutout: '60%'
                }
            });
        }
    });
</script>
<?= $this->endSection() ?>
