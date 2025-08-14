<?= $this->extend('admin/templates/layout') ?>
<?= $this->section('title') ?>
Kelola Master Tagihan SPP
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    $dateFormatter = new IntlDateFormatter('id_ID', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
?>
<div class="container-fluid p-0">
    <h4 class="mb-4 fw-bold"><i class="bi bi-receipt-cutoff me-2"></i>Kelola Master Tagihan SPP</h4>

    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card h-100 py-2 shadow-sm border-start border-primary border-4">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total SPP Aktif</div>
                            <div class="h4 mb-0 fw-bold text-gray-800"><?= esc($sppAktif ?? 0) ?> Tagihan</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-journal-check fs-1 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card h-100 py-2 shadow-sm border-start border-success border-4">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">Tagihan Lunas Per Siswa</div>
                            <div class="h4 mb-0 fw-bold text-gray-800"><?= esc($sppLunas ?? 0) ?> Tagihan</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-patch-check-fill fs-1 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card h-100 py-2 shadow-sm border-start border-danger border-4">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-danger text-uppercase mb-1">Tagihan Belum Lunas Per Siswa</div>
                            <div class="h4 mb-0 fw-bold text-gray-800"><?= esc($sppTanggungan ?? 0) ?> Tagihan</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-exclamation-diamond-fill fs-1 text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <a href="<?= site_url('admin/tambah_tagihan') ?>" class="btn btn-primary btn-m shadow-sm">
            <i class="bi bi-plus-circle me-2"></i> Buat Tagihan Baru
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light p-0">
            <ul class="nav nav-tabs nav-fill" id="sppTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active p-3" id="aktif-tab" data-bs-toggle="tab" data-bs-target="#aktif" type="button" role="tab" aria-controls="aktif" aria-selected="true">
                        <i class="bi bi-journals me-2"></i>Tagihan Aktif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link p-3" id="arsip-tab" data-bs-toggle="tab" data-bs-target="#arsip" type="button" role="tab" aria-controls="arsip" aria-selected="false">
                        <i class="bi bi-archive-fill me-2"></i>Arsip Tagihan
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body p-4">
            <div class="tab-content" id="sppTabContent">
                <div class="tab-pane fade show active" id="aktif" role="tabpanel" aria-labelledby="aktif-tab">
                    <h5 class="fw-semibold mb-3">Filter Tagihan SPP</h5>
                    <form method="get" action="<?= site_url('admin/manajemen_spp') ?>" class="row g-3 align-items-center mb-4 p-3 border rounded bg-light">
                        <input type="hidden" name="tab" value="aktif">
                        <div class="col-md-3">
                            <label for="periode_aktif" class="form-label">Periode Jatuh Tempo</label>
                            <input type="month" class="form-control form-control-sm" id="periode_aktif" name="periode_aktif" value="<?= esc($filter['periode_aktif'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="kelas_aktif" class="form-label">Kelas</label>
                            <select class="form-select form-select-sm" id="kelas_aktif" name="kelas_aktif">
                                <option value="">Semua Kelas</option>
                                <option value="7" <?= ($filter['kelas_aktif'] ?? '') == '7' ? 'selected' : '' ?>>Kelas 7</option>
                                <option value="8" <?= ($filter['kelas_aktif'] ?? '') == '8' ? 'selected' : '' ?>>Kelas 8</option>
                                <option value="9" <?= ($filter['kelas_aktif'] ?? '') == '9' ? 'selected' : '' ?>>Kelas 9</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="search_aktif" class="form-label">Cari (Nama/Nominal)</label>
                            <input type="text" class="form-control form-control-sm" id="search_aktif" name="search_aktif" placeholder="Ketik nama tagihan atau nominal..." value="<?= esc($filter['search_aktif'] ?? '') ?>">
                        </div>
                        <div class="col-md-2 d-flex align-self-end">
                            <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="text-center" style="width: 5%;">No</th>
                                    <th scope="col">Nama Tagihan</th>
                                    <th scope="col" class="text-center" style="width: 7%;">Kelas</th>
                                    <th scope="col" style="width: 15%;">Jatuh Tempo</th>
                                    <th scope="col" class="text-center" style="width: 20%;">Progres Pembayaran</th>
                                    <th scope="col" class="text-end">Nominal (Rp)</th>
                                    <th scope="col" class="text-center">Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($sppAktifList)): ?>
                                    <?php foreach ($sppAktifList as $index => $item): ?>
                                        <tr>
                                            <td class="text-center"><?= $index + 1 ?></td>
                                            <td><?= esc($item['nama_tagihan']) ?></td>
                                            <td class="text-center"><?= esc($item['kelas']) ?></td>
                                            <td><?= $dateFormatter->format(strtotime($item['jatuh_tempo'])) ?></td>
                                            <td>
                                                <?php
                                                    $totalSiswa = $item['total_siswa'] ?? 0;
                                                    $lunasSiswa = $item['lunas_siswa'] ?? 0;
                                                    $persentase = ($totalSiswa > 0) ? ($lunasSiswa / $totalSiswa) * 100 : 0;
                                                ?>
                                                <div class="d-flex justify-content-between">
                                                    <small>Lunas</small>
                                                    <small><?= $lunasSiswa ?>/<?= $totalSiswa ?></small>
                                                </div>
                                                <div class="progress" style="height: 10px;">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $persentase ?>%;" aria-valuenow="<?= $persentase ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </td>
                                            <td class="text-end"><?= number_format($item['nominal'], 0, ',', '.') ?></td>
                                            <td class="text-center">
                                                <a href="<?= site_url('admin/tambah_tagihan/edit/'.$item['id']) ?>" class="btn btn-outline-warning btn-sm me-1" title="Edit Tagihan"><i class="bi bi-pencil-fill"></i></a>
                                                <a href="<?= site_url('admin/spp/archive/'.$item['id']) ?>" class="btn btn-outline-secondary btn-sm btn-archive-tagihan" data-nama-tagihan="<?= esc($item['nama_tagihan']) ?>" title="Arsipkan Tagihan"><i class="bi bi-archive-fill"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" class="text-center p-4"><i class="bi bi-info-circle fs-3 text-muted"></i><br>Belum ada data SPP aktif.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="tab-pane fade" id="arsip" role="tabpanel" aria-labelledby="arsip-tab">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="text-center" style="width: 5%;">No</th>
                                    <th scope="col">Nama Tagihan</th>
                                    <th scope="col" class="text-center">Kelas</th>
                                    <th scope="col">Jatuh Tempo</th>
                                    <th scope="col" class="text-end">Nominal (Rp)</th>
                                    <th scope="col" class="text-center">Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($sppArsipList)): ?>
                                    <?php foreach ($sppArsipList as $index => $item): ?>
                                        <tr>
                                            <td class="text-center"><?= $index + 1 ?></td>
                                            <td><?= esc($item['nama_tagihan']) ?></td>
                                            <td class="text-center"><?= esc($item['kelas']) ?></td>
                                            <td><?= $dateFormatter->format(strtotime($item['jatuh_tempo'])) ?></td>
                                            <td class="text-end"><?= number_format($item['nominal'], 0, ',', '.') ?></td>
                                            <td class="text-center">
                                                <a href="<?= site_url('admin/spp/reactivate/'.$item['id']) ?>" class="btn btn-outline-success btn-sm btn-reactivate-tagihan" data-nama-tagihan="<?= esc($item['nama_tagihan']) ?>" title="Aktifkan Kembali"><i class="bi bi-arrow-counterclockwise"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="text-center p-4"><i class="bi bi-archive fs-3 text-muted"></i><br>Arsip tagihan masih kosong.</td></tr>
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
    .text-xs { font-size: .75rem !important; }
    .text-gray-800 { color: #5a5c69 !important; }
    .border-start.border-4 { border-left-width: 0.25rem !important; }
    .table-sm th, .table-sm td { padding: 0.5rem; }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // SweetAlert untuk Arsip Tagihan
    document.querySelectorAll('.btn-archive-tagihan').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const archiveUrl = this.href;
            const namaTagihan = this.dataset.namaTagihan;
            Swal.fire({
                title: 'Anda Yakin?',
                text: `Tagihan "${namaTagihan}" akan diarsipkan. Anda bisa mengaktifkannya kembali nanti.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Arsipkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = archiveUrl;
                }
            });
        });
    });

    // SweetAlert untuk Aktifkan Kembali Tagihan
    document.querySelectorAll('.btn-reactivate-tagihan').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const reactivateUrl = this.href;
            const namaTagihan = this.dataset.namaTagihan;
            Swal.fire({
                title: 'Aktifkan Kembali Tagihan?',
                text: `Tagihan "${namaTagihan}" akan muncul kembali di daftar tagihan aktif.`,
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Aktifkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = reactivateUrl;
                }
            });
        });
    });

    // Skrip untuk menjaga tab tetap aktif setelah ada aksi atau filter
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab') || 'aktif';
    if (activeTab) {
        const tabButton = document.getElementById(activeTab + '-tab');
        if (tabButton) {
            const tab = new bootstrap.Tab(tabButton);
            tab.show();
        }
    }

    const tabButtons = document.querySelectorAll('#sppTab button[data-bs-toggle="tab"]');
    tabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', function(event) {
            const activeTabValue = event.target.id.replace('-tab', '');
            const activeForm = document.querySelector('#' + activeTabValue + ' form');
            if(activeForm) {
                const tabInput = activeForm.querySelector('input[name="tab"]');
                if(tabInput) {
                    tabInput.value = activeTabValue;
                }
            }
        });
    });
});
</script>
<?= $this->endSection() ?>