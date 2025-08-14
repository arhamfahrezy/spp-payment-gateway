<?= $this->extend('admin/templates/layout') ?>

<?= $this->section('title') ?>
<?= $title ?? 'Laporan Keuangan' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    $dateFormatter = new IntlDateFormatter('id_ID', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
    $dateTimeFormatter = new IntlDateFormatter('id_ID', IntlDateFormatter::LONG, IntlDateFormatter::SHORT);
?>
<div class="container-fluid p-0">
    <h4 class="mb-4 fw-bold"><i class="bi bi-file-earmark-text-fill me-2"></i>Laporan Keuangan</h4>

    <div class="card shadow-sm">
        <div class="card-header bg-light p-0">
            <ul class="nav nav-tabs nav-fill" id="laporanTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= ($filter['tab'] == 'spp' ? 'active' : '') ?> p-3" id="spp-tab" data-bs-toggle="tab" data-bs-target="#spp" type="button" role="tab" aria-controls="spp" aria-selected="<?= ($filter['tab'] == 'spp' ? 'true' : 'false') ?>">
                        <i class="bi bi-receipt me-2"></i>Laporan SPP
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= ($filter['tab'] == 'uang-saku' ? 'active' : '') ?> p-3" id="uang-saku-tab" data-bs-toggle="tab" data-bs-target="#uang-saku" type="button" role="tab" aria-controls="uang-saku" aria-selected="<?= ($filter['tab'] == 'uang-saku' ? 'true' : 'false') ?>">
                        <i class="bi bi-wallet2 me-2"></i>Laporan Uang Saku
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body p-3 p-md-4">
            <div class="tab-content" id="laporanTabContent">
                <div class="tab-pane fade <?= ($filter['tab'] == 'spp' ? 'show active' : '') ?>" id="spp" role="tabpanel" aria-labelledby="spp-tab">
                    <h5 class="mb-3 fw-semibold"><i class="bi bi-filter-circle me-2"></i>Filter Laporan SPP</h5>
                    <form method="get" action="<?= site_url('admin/laporan') ?>" class="row g-3 align-items-end mb-4 p-3 border rounded bg-light">
                        <input type="hidden" name="tab" value="spp">
                        <div class="col-md-3">
                            <label for="periode_tipe_spp" class="form-label">Tipe Periode</label>
                            <select class="form-select form-select-sm" id="periode_tipe_spp" name="periode_tipe_spp">
                                <option value="bulanan" <?= ($filter['periode_tipe_spp'] === 'bulanan') ? 'selected' : '' ?>>Bulanan</option>
                                <option value="tahunan" <?= ($filter['periode_tipe_spp'] === 'tahunan') ? 'selected' : '' ?>>Tahunan</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="kolom_periode_bulan_spp">
                            <label for="periode_bulan_spp" class="form-label">Pilih Bulan</label>
                            <input type="month" class="form-control form-control-sm" id="periode_bulan_spp" name="periode_bulan_spp" value="<?= esc($filter['periode_bulan_spp'] ?? date('Y-m')) ?>">
                        </div>
                        <div class="col-md-3" id="kolom_periode_tahun_spp" style="display:none;">
                            <label for="periode_tahun_spp" class="form-label">Pilih Tahun</label>
                            <input type="number" class="form-control form-control-sm" id="periode_tahun_spp" name="periode_tahun_spp" value="<?= esc($filter['periode_tahun_spp'] ?? date('Y')) ?>" min="2020" max="<?= date('Y') + 5 ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="status_spp" class="form-label">Status</label>
                            <select class="form-select form-select-sm" id="status_spp" name="status_spp">
                                <option value="">Semua Status</option>
                                <option value="Lunas" <?= ($filter['status_spp'] ?? '') == 'Lunas' ? 'selected' : '' ?>>Lunas</option>
                                <option value="Belum Lunas" <?= ($filter['status_spp'] ?? '') == 'Belum Lunas' ? 'selected' : '' ?>>Belum Lunas</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="search_spp" class="form-label">Cari</label>
                            <input type="text" class="form-control form-control-sm" id="search_spp" name="search_spp" placeholder="Cari ..." value="<?= esc($filter['search_spp'] ?? '') ?>">
                        </div>
                        <div class="col-md-1 d-flex align-self-end">
                            <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-funnel"></i></button>
                        </div>
                    </form>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0 fw-semibold">Data Laporan SPP</h5>
                        <a href="<?= site_url('admin/laporan/cetak_spp?' . http_build_query(array_intersect_key($filter, array_flip(['periode_tipe_spp', 'periode_bulan_spp', 'periode_tahun_spp', 'status_spp', 'search_spp'])))) ?>" target="_blank" class="btn btn-primary btn-sm">
                            <i class="bi bi-printer-fill me-1"></i> Cetak Laporan
                        </a>
                    </div>
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-bordered table-striped table-hover table-sm align-middle mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">NIS</th>
                                    <th>Nama Siswa</th>
                                    <th class="text-center">Kelas</th>
                                    <th>Nama Tagihan</th>
                                    <th class="text-end">Jml. Tagihan</th>
                                    <th class="text-end">Jml. Bayar</th>
                                    <th class="text-center">Tgl. Bayar</th>
                                    <th class="text-center">Status</th>
                                    <th style="width:12%">Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($laporanSPP)): ?>
                                    <?php foreach ($laporanSPP as $index => $row): ?>
                                    <tr>
                                        <td class="text-center"><?= $index + 1 ?></td>
                                        <td class="text-center"><?= esc($row['nis']) ?></td>
                                        <td><?= esc($row['nama_siswa']) ?></td>
                                        <td class="text-center"><?= esc($row['kelas']) ?></td>
                                        <td><?= esc($row['nama_tagihan']) ?></td>
                                        <td class="text-end"><?= number_format($row['jumlah_tagihan'], 0, ',', '.') ?></td>
                                        <td class="text-end fw-bold <?= ($row['status'] == 'Lunas') ? 'text-success' : 'text-muted' ?>"><?= number_format($row['jumlah_bayar'], 0, ',', '.') ?></td>
                                        <td class="text-center"><?= $row['tanggal_bayar'] ? $dateFormatter->format(strtotime($row['tanggal_bayar'])) : '-' ?></td>
                                        <td class="text-center">
                                            <span class="badge <?= $row['status'] == 'Lunas' ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' ?>"><i class="bi <?= $row['status'] == 'Lunas' ? 'bi-check-circle' : 'bi-hourglass-split' ?> me-1"></i><?= $row['status'] ?></span>
                                        </td>
                                        <td><small><?= esc($row['catatan'] ?? '-') ?></small></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="10" class="text-center p-4"><i class="bi bi-info-circle fs-3 text-muted"></i><br>Tidak ada data laporan SPP untuk filter yang dipilih.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade <?= ($filter['tab'] == 'uang-saku' ? 'show active' : '') ?>" id="uang-saku" role="tabpanel" aria-labelledby="uang-saku-tab">
                    <h5 class="mb-3 fw-semibold"><i class="bi bi-filter-circle me-2"></i>Filter Laporan Uang Saku</h5>
                    <form method="get" action="<?= site_url('admin/laporan') ?>" class="row g-3 align-items-end mb-4 p-3 border rounded bg-light">
                        <input type="hidden" name="tab" value="uang-saku">
                        <div class="col-md-3">
                            <label for="periode_tipe_us" class="form-label">Tipe Periode</label>
                            <select class="form-select form-select-sm" id="periode_tipe_us" name="periode_tipe_us">
                                <option value="bulanan" <?= ($filter['periode_tipe_us'] === 'bulanan') ? 'selected' : '' ?>>Bulanan</option>
                                <option value="tahunan" <?= ($filter['periode_tipe_us'] === 'tahunan') ? 'selected' : '' ?>>Tahunan</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="kolom_periode_bulan_us">
                            <label for="periode_bulan_us" class="form-label">Pilih Bulan</label>
                            <input type="month" class="form-control form-control-sm" id="periode_bulan_us" name="periode_bulan_us" value="<?= esc($filter['periode_bulan_us'] ?? date('Y-m')) ?>">
                        </div>
                        <div class="col-md-3" id="kolom_periode_tahun_us" style="display:none;">
                            <label for="periode_tahun_us" class="form-label">Pilih Tahun</label>
                            <input type="number" class="form-control form-control-sm" id="periode_tahun_us" name="periode_tahun_us" value="<?= esc($filter['periode_tahun_us'] ?? date('Y')) ?>" min="2020" max="<?= date('Y') + 5 ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="status_us" class="form-label">Status</label>
                            <select class="form-select form-select-sm" id="status_us" name="status_us">
                                <option value="">Semua Status</option>
                                <option value="Masuk" <?= ($filter['status_us'] ?? '') == 'Masuk' ? 'selected' : '' ?>>Masuk</option>
                                <option value="Keluar" <?= ($filter['status_us'] ?? '') == 'Keluar' ? 'selected' : '' ?>>Keluar</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="search_us" class="form-label">Cari</label>
                            <input type="text" class="form-control form-control-sm" id="search_us" name="search_us" placeholder="Cari..." value="<?= esc($filter['search_us'] ?? '') ?>">
                        </div>
                        <div class="col-md-1 d-flex align-self-end">
                            <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-funnel"></i></button>
                        </div>
                    </form>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0 fw-semibold">Data Laporan Uang Saku</h5>
                        <a href="<?= site_url('admin/laporan/cetak_uang_saku?' . http_build_query(array_intersect_key($filter, array_flip(['periode_tipe_us', 'periode_bulan_us', 'periode_tahun_us', 'status_us', 'search_us'])))) ?>" target="_blank" class="btn btn-primary btn-sm">
                            <i class="bi bi-printer-fill me-1"></i> Cetak Laporan
                        </a>
                    </div>
                    <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                        <table class="table table-bordered table-striped table-hover table-sm align-middle mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">NIS</th>
                                    <th>Nama Siswa</th>
                                    <th class="text-center">Kelas</th>
                                    <th class="text-center">Tanggal & Waktu</th>
                                    <th class="text-end">Nominal (Rp)</th>
                                    <th class="text-center">Status</th>
                                    <th style="width:12%">Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($laporanUangSaku)): ?>
                                    <?php foreach ($laporanUangSaku as $index => $row): ?>
                                    <tr>
                                        <td class="text-center"><?= $index + 1 ?></td>
                                        <td class="text-center"><?= esc($row['nis']) ?></td>
                                        <td><?= esc($row['nama_siswa']) ?></td>
                                        <td class="text-center"><?= esc($row['kelas']) ?></td>
                                        <td class="text-center"><?= $dateTimeFormatter->format(strtotime($row['tanggal'])) ?></td>
                                        <td class="text-end fw-bold <?= ($row['status'] == 'Keluar') ? 'text-danger' : 'text-success' ?>">
                                            <?= ($row['status'] == 'Keluar') ? '-' : '+' ?> <?= number_format($row['nominal'], 0, ',', '.') ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge <?= $row['status'] == 'Masuk' ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' ?>"><i class="bi <?= $row['status'] == 'Masuk' ? 'bi-arrow-down-short' : 'bi-arrow-up-short' ?> me-1"></i><?= $row['status'] ?></span>
                                        </td>
                                        <td><small><?= esc($row['catatan'] ?? '-') ?></small></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="8" class="text-center p-4"><i class="bi bi-info-circle fs-3 text-muted"></i><br>Tidak ada data laporan uang saku untuk filter yang dipilih.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .table-sm th, .table-sm td { padding: 0.5rem; }
    .sticky-top { top: -1px; }
    .nav-tabs .nav-link {
        font-weight: 500;
        color: #6c757d;
    }
    .nav-tabs .nav-link.active {
        color: #0d6efd;
        border-bottom-width: 3px;
    }
    .bg-success-soft { background-color: rgba(25, 135, 84, 0.1); }
    .text-success { color: #198754 !important; }
    .bg-danger-soft { background-color: rgba(220, 53, 69, 0.1); }
    .text-danger { color: #dc3545 !important; }
    .badge { font-size: 0.85em; padding: 0.4em 0.6em;}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fungsi untuk mengelola tampilan input periode
    function setupPeriodeToggle(tipeId, bulanId, tahunId) {
        const tipeSelect = document.getElementById(tipeId);
        const kolomBulan = document.getElementById(bulanId);
        const kolomTahun = document.getElementById(tahunId);

        function toggleView() {
            if (tipeSelect.value === 'tahunan') {
                kolomBulan.style.display = 'none';
                kolomTahun.style.display = 'block';
            } else {
                kolomBulan.style.display = 'block';
                kolomTahun.style.display = 'none';
            }
        }
        tipeSelect.addEventListener('change', toggleView);
        toggleView(); // Panggil saat halaman dimuat
    }

    // Inisialisasi untuk kedua form
    setupPeriodeToggle('periode_tipe_spp', 'kolom_periode_bulan_spp', 'kolom_periode_tahun_spp');
    setupPeriodeToggle('periode_tipe_us', 'kolom_periode_bulan_us', 'kolom_periode_tahun_us');
    
    // Skrip untuk menjaga tab tetap aktif
    const activeTab = new URLSearchParams(window.location.search).get('tab') || 'spp';
    const tabButton = document.getElementById(activeTab + '-tab');
    if (tabButton) {
        new bootstrap.Tab(tabButton).show();
    }

    // Skrip untuk memperbarui nilai 'tab' di form saat tab diganti
    document.querySelectorAll('#laporanTab button[data-bs-toggle="tab"]').forEach(button => {
        button.addEventListener('shown.bs.tab', function (event) {
            const activeTabId = event.target.id.replace('-tab', '');
            document.querySelectorAll('form input[name="tab"]').forEach(input => {
                input.value = activeTabId;
            });
        });
    });
});
</script>
<?= $this->endSection() ?>