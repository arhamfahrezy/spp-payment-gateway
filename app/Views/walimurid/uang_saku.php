<!-- C:\xampp\htdocs\payment-gateway\app\Views\walimurid\uang_saku.php -->
<?= $this->extend('walimurid/templates/layout') ?>

<?= $this->section('title') ?>
Uang Saku Siswa
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    $dateTimeFormatter = new IntlDateFormatter('id_ID', IntlDateFormatter::LONG, IntlDateFormatter::SHORT);
?>
<div class="container-fluid p-0">
    <h4 class="mb-4 fw-bold"><i class="bi bi-wallet me-2"></i>Uang Saku <?= isset($namaSiswa) && $namaSiswa !== 'Data Siswa Tidak Ditemukan' ? '- <span class="text-black fw-normal">' . esc($namaSiswa) . '</span>' : '' ?></h4>
    <div class="row mb-1">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card h-100 py-2 shadow border-start border-primary border-4">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">Saldo Saat Ini</div>
                            <div class="h4 mb-0 fw-bold text-gray-800">Rp <?= number_format($saldoUangSaku ?? 0, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-auto"><i class="bi bi-wallet2 fs-1 text-primary"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card h-100 py-2 shadow border-start border-success border-4">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">Total Uang Masuk</div>
                            <div class="h4 mb-0 fw-bold text-gray-800">Rp <?= number_format($uangSakuMasukTotal ?? 0, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-auto"><i class="bi bi-box-arrow-in-down fs-1 text-success"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card h-100 py-2 shadow border-start border-danger border-4">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-danger text-uppercase mb-1">Total Uang Diambil</div>
                            <div class="h4 mb-0 fw-bold text-gray-800">Rp <?= number_format($uangSakuDiambilTotal ?? 0, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-auto"><i class="bi bi-box-arrow-up fs-1 text-danger"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4 d-flex justify-content-between align-items-center">
        <a href="<?= site_url('walimurid/pembayaran_uang_saku') ?>" class="btn btn-primary shadow-sm"><i class="bi bi-plus-circle-dotted me-2"></i> Top Up Saldo Uang Saku</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light p-3">
            <h5 class="mb-3 fw-semibold"><i class="bi bi-filter-circle me-2"></i>Filter Riwayat Transaksi</h5>
            <form method="get" action="<?= site_url('walimurid/uang_saku') ?>" class="row g-3 align-items-center">
                <div class="col-md-3">
                    <label for="periode" class="form-label">Periode Transaksi</label>
                    <input type="month" class="form-control form-control-sm" id="periode" name="periode" value="<?= esc($filter['periode'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label for="tipe" class="form-label">Status Transaksi</label>
                    <select class="form-select form-select-sm" id="tipe" name="tipe">
                        <option value="">Semua Status</option>
                        <option value="Masuk" <?= ($filter['tipe'] ?? '') == 'Masuk' ? 'selected' : '' ?>>Masuk</option>
                        <option value="Keluar" <?= ($filter['tipe'] ?? '') == 'Keluar' ? 'selected' : '' ?>>Keluar</option>
                        <option value="Pending" <?= ($filter['tipe'] ?? '') == 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Gagal" <?= ($filter['tipe'] ?? '') == 'Gagal' ? 'selected' : '' ?>>Gagal</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="search" class="form-label">Cari (Catatan/Jumlah)</label>
                    <input type="text" class="form-control form-control-sm" id="search" name="search" placeholder="Ketik catatan atau jumlah..." value="<?= esc($filter['search'] ?? '') ?>">
                </div>
                <div class="col-md-2 d-flex align-self-end">
                    <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle" id="transaksiTable">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="text-center" style="width:5%">No</th>
                            <th scope="col">Keterangan/Catatan</th>
                            <th scope="col" style="width:20%">Tanggal & Waktu</th>
                            <th scope="col" class="text-end" style="width:20%">Jumlah (Rp)</th>
                            <th scope="col" class="text-center" style="width:15%">Status</th>
                            <th scope="col" class="text-center" style="width:10%">Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($riwayatTransaksi)): ?>
                            <?php foreach ($riwayatTransaksi as $index => $transaksi): ?>
                                <tr>
                                    <?php
                                        // Variabel status kita definisikan di sini agar bisa dipakai oleh kedua kolom
                                        $status = $transaksi['status'];
                                    ?>
                                    <td class="text-center"><?= $index + 1 ?></td>
                                    <td><?= esc($transaksi['catatan'] ?? '-') ?></td>
                                    <td><?= $dateTimeFormatter->format(strtotime($transaksi['tanggal'])) ?></td>
                                    
                                    <td class="text-end fw-bold <?php
                                        if ($status === 'Masuk') {
                                            echo 'text-success';
                                        } elseif ($status === 'Pending') {
                                            echo 'text-warning';
                                        } else { // Untuk status 'Keluar' dan 'Gagal'
                                            echo 'text-danger';
                                        }
                                    ?>">
                                        <?= ($status === 'Keluar' || $status === 'Gagal') ? '-' : '+' ?> <?= number_format($transaksi['nominal'], 0, ',', '.') ?>
                                    </td>

                                    <td class="text-center">
                                        <?php
                                            $badgeClass = 'bg-secondary'; 
                                            $badgeIcon = 'bi-question-circle';
                                            if ($status === 'Masuk') { $badgeClass = 'bg-success-soft text-success'; $badgeIcon = 'bi-arrow-down-short'; }
                                            elseif ($status === 'Keluar') { $badgeClass = 'bg-danger-soft text-danger'; $badgeIcon = 'bi-arrow-up-short'; }
                                            elseif ($status === 'Pending') { $badgeClass = 'bg-warning-soft text-warning'; $badgeIcon = 'bi-hourglass-split'; }
                                            elseif ($status === 'Gagal') { $badgeClass = 'bg-danger'; $badgeIcon = 'bi-x-circle'; }
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><i class="bi <?= $badgeIcon ?>"></i> <?= $status ?></span>
                                    </td>

                                    <td class="text-center">
                                        <?php if ($status === 'Masuk' || $status === 'Keluar'): ?>
                                            <a href="<?= site_url('walimurid/uang_saku/cetak_bukti/' . $transaksi['id']) ?>" target="_blank" class="btn btn-sm btn-outline-info" title="Cetak Bukti"><i class="bi bi-printer"></i></a>
                                        <?php elseif ($status === 'Gagal'): ?>
                                            <a href="<?= site_url('walimurid/pembayaran_uang_saku') ?>" class="btn btn-sm btn-outline-primary" title="Coba Lagi"><i class="bi bi-arrow-clockwise"></i></a>
                                        <?php elseif ($status === 'Pending'): ?>
                                            <a href="<?= site_url('walimurid/pembayaran_uang_saku') ?>" class="btn btn-sm btn-warning" title="Lanjutkan Pembayaran"><i class="bi bi-credit-card"></i></a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center py-4"><i class="bi bi-info-circle me-2"></i>Tidak ada riwayat transaksi sesuai filter yang dipilih.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .text-xs { font-size: .75rem; }
    .text-gray-800 { color: #5a5c69 !important; }
    .border-start.border-4 { border-left-width: 0.25rem !important; }
    .bg-success-soft { background-color: rgba(25, 135, 84, 0.1); }
    .text-success { color: #198754 !important; }
    .bg-danger-soft { background-color: rgba(220, 53, 69, 0.1); }
    .text-danger { color: #dc3545 !important; }
    .bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); }
    .text-warning { color: #ffc107 !important; }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const detailModal = new bootstrap.Modal(document.getElementById('detailTransaksiModal'));
    
    document.querySelectorAll('.btn-detail-keluar').forEach(button => {
        button.addEventListener('click', function() {
            const tanggal = this.dataset.tanggal;
            const nominal = this.dataset.nominal;
            const catatan = this.dataset.catatan;

            document.getElementById('modal-tanggal').textContent = tanggal;
            document.getElementById('modal-nominal').textContent = '-Rp ' + nominal;
            document.getElementById('modal-catatan').textContent = catatan;

            detailModal.show();
        });
    });
});
</script>
<?= $this->endSection() ?>
