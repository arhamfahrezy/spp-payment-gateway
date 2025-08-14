<!-- C:\xampp\htdocs\payment-gateway\app\Views\admin\data_uang_saku.php -->
<?= $this->extend('admin/templates/layout') ?>

<?= $this->section('title') ?>
<?= $title ?? 'Manajemen Data Uang Saku' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    // Formatter untuk tanggal dan waktu (e.g., 7 Juli 2025, 20.23)
    $dateTimeFormatter = new IntlDateFormatter('id_ID', IntlDateFormatter::LONG, IntlDateFormatter::SHORT);
?>
<div class="container-fluid p-0">
    <h4 class="mb-4 fw-bold"><i class="bi bi-wallet me-2"></i>Manajemen Data Uang Saku</h4>

    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card h-100 py-2 shadow-sm border-start border-primary border-4">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total Saldo Uang Saku Saat Ini</div>
                            <div class="h4 mb-0 fw-bold text-gray-800">Rp <?= number_format($jumlahTotalSaldoUangSaku ?? 0, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-cash-stack fs-1 text-primary"></i>
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
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">Total Uang Saku Masuk</div>
                            <div class="h4 mb-0 fw-bold text-gray-800">Rp <?= number_format($totalUangSakuMasukHistoris ?? 0, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-box-arrow-in-down fs-1 text-success"></i>
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
                            <div class="text-xs fw-bold text-danger text-uppercase mb-1">Total Uang Saku Diambil</div>
                            <div class="h4 mb-0 fw-bold text-gray-800">Rp <?= number_format($totalUangSakuDiambilHistoris ?? 0, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-box-arrow-up fs-1 text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <a href="<?= site_url('admin/pengambilan_uang_saku') ?>" class="btn btn-primary btn-m shadow-sm">
            <i class="bi bi-dash-circle me-1"></i> Pengambilan Uang Saku
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light p-0">
            <ul class="nav nav-tabs nav-fill" id="uangSakuTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active p-3" id="daftar-saldo-tab" data-bs-toggle="tab" data-bs-target="#daftar-saldo" type="button" role="tab" aria-controls="daftar-saldo" aria-selected="true">
                        <i class="bi bi-person-lines-fill me-2"></i>Daftar Saldo Siswa
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link p-3" id="masuk-tab" data-bs-toggle="tab" data-bs-target="#masuk" type="button" role="tab" aria-controls="masuk" aria-selected="false">
                        <i class="bi bi-arrow-bar-down me-2"></i>Riwayat Uang Masuk
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link p-3" id="diambil-tab" data-bs-toggle="tab" data-bs-target="#diambil" type="button" role="tab" aria-controls="diambil" aria-selected="false">
                        <i class="bi bi-arrow-bar-up me-2"></i>Riwayat Uang Diambil
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body p-4">
            <div class="tab-content" id="uangSakuTabContent">
                <div class="tab-pane fade show active" id="daftar-saldo" role="tabpanel" aria-labelledby="daftar-saldo-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-semibold mb-0">Daftar Saldo Uang Saku per Siswa</h5>
                        <input type="text" id="searchDaftarSaldo" class="form-control form-control-sm" style="width: 300px;" placeholder="Cari (NIS, Nama, Kelas)...">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover table-sm align-middle" id="tabelDaftarSaldo">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>NIS</th>
                                    <th>Nama Siswa</th>
                                    <th class="text-center">Kelas</th>
                                    <th class="text-end">Saldo Saat Ini (Rp)</th>
                                    <th class="text-center" style="width:15%;">Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($daftarUangSaku)): ?>
                                    <?php foreach ($daftarUangSaku as $index => $item): ?>
                                    <tr>
                                        <td class="text-center"><?= $index + 1 ?></td>
                                        <td><?= esc($item['nis']) ?></td>
                                        <td><?= esc($item['nama_siswa']) ?></td>
                                        <td class="text-center"><?= esc($item['kelas']) ?></td>
                                        <td class="text-end fw-bold"><?= number_format($item['saldo'], 0, ',', '.') ?></td>
                                        <td class="text-center">
                                            <a href="<?= site_url('admin/pengambilan_uang_saku?nis='.$item['nis']) ?>" class="btn btn-outline-danger btn-sm me-1" title="Catat Pengambilan">
                                                <i class="bi bi-dash-circle"></i> Ambil
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="text-center p-4"><i class="bi bi-info-circle fs-3 text-muted"></i><br>Belum ada data uang saku siswa.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="masuk" role="tabpanel" aria-labelledby="masuk-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-semibold mb-0">Riwayat Transaksi Uang Saku Masuk</h5>
                        <input type="text" id="searchUangMasuk" class="form-control form-control-sm" style="width: 300px;" placeholder="Cari (NIS, Nama, Tgl, Nominal)...">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover table-sm align-middle" id="tabelUangMasuk">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>NIS</th>
                                    <th>Nama Siswa</th>
                                    <th style="width:18%;">Tanggal Masuk</th>
                                    <th class="text-end">Nominal (Rp)</th>
                                    <th>Catatan</th>
                                    <th class="text-center" style="width:10%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($uangSakuMasukList)): ?>
                                    <?php foreach ($uangSakuMasukList as $index => $item): ?>
                                    <tr>
                                        <td class="text-center"><?= $index + 1 ?></td>
                                        <td><?= esc($item['nis']) ?></td>
                                        <td><?= esc($item['nama_siswa']) ?></td>
                                        <td><?= $dateTimeFormatter->format(strtotime($item['tanggal'])) ?></td>
                                        <td class="text-end text-success fw-bold">+ <?= number_format($item['nominal'], 0, ',', '.') ?></td>
                                        <td><?= esc($item['catatan'] ?? '-') ?></td>
                                        <td class="text-center"> <a href="<?= site_url('admin/data_uang_saku/cetak_transaksi/'.$item['transaksi_id']) ?>" class="btn btn-outline-primary btn-sm" title="Cetak Transaksi" target="_blank">
                                                <i class="bi bi-printer-fill"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="text-center p-4"><i class="bi bi-info-circle fs-3 text-muted"></i><br>Belum ada riwayat uang saku masuk.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="diambil" role="tabpanel" aria-labelledby="diambil-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-semibold mb-0">Riwayat Transaksi Uang Saku Diambil</h5>
                        <input type="text" id="searchUangDiambil" class="form-control form-control-sm" style="width: 300px;" placeholder="Cari (NIS, Nama, Tgl, Nominal)...">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover table-sm align-middle" id="tabelUangDiambil">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>NIS</th>
                                    <th>Nama Siswa</th>
                                    <th style="width:18%;">Tanggal Diambil</th>
                                    <th class="text-end">Nominal (Rp)</th>
                                    <th>Catatan</th>
                                    <th class="text-center" style="width:10%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($uangSakuDiambilList)): ?>
                                    <?php foreach ($uangSakuDiambilList as $index => $item): ?>
                                    <tr>
                                        <td class="text-center"><?= $index + 1 ?></td>
                                        <td><?= esc($item['nis']) ?></td>
                                        <td><?= esc($item['nama_siswa']) ?></td>
                                        <td><?= $dateTimeFormatter->format(strtotime($item['tanggal'])) ?></td>
                                        <td class="text-end text-danger fw-bold">- <?= number_format($item['nominal'], 0, ',', '.') ?></td>
                                        <td><?= esc($item['catatan'] ?? '-') ?></td>
                                        <td class="text-center"> <a href="<?= site_url('admin/data_uang_saku/cetak_transaksi/'.$item['transaksi_id']) ?>" class="btn btn-outline-primary btn-sm" title="Cetak Transaksi" target="_blank">
                                                <i class="bi bi-printer-fill"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="text-center p-4"><i class="bi bi-info-circle fs-3 text-muted"></i><br>Belum ada riwayat uang saku diambil.</td></tr>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    function initializeTableSearch(inputId, tableId, cellIndices) {
    const searchInput = document.getElementById(inputId);
    if (!searchInput) return;

    searchInput.addEventListener('keyup', function() {
        const filter = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#' + tableId + ' tbody tr');

        rows.forEach(row => {
            if (row.querySelector('td[colspan]')) {
                row.style.display = '';
                return;
            }
            let textMatch = false;
            for (const index of cellIndices) {
                const cell = row.cells[index];
                if (cell) {
                    // Ambil teks dari sel, hapus semua titik, lalu bandingkan
                    let cellText = cell.textContent.toLowerCase();
                    let cellTextWithoutDots = cellText.replace(/\./g, '');

                    if (cellTextWithoutDots.includes(filter)) {
                        textMatch = true;
                        break;
                    }
                }
            }
            row.style.display = textMatch ? '' : 'none';
        });
    });
}

    initializeTableSearch('searchDaftarSaldo', 'tabelDaftarSaldo', [1, 2, 3, 4]);
    initializeTableSearch('searchUangMasuk', 'tabelUangMasuk', [1, 2, 3, 4, 5]);
    initializeTableSearch('searchUangDiambil', 'tabelUangDiambil', [1, 2, 3, 4, 5]);
});
</script>
<?= $this->endSection() ?>
