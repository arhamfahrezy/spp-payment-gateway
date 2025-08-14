<!-- C:\xampp\htdocs\payment-gateway\app\Views\walimurid\riwayat_pembayaran.php -->
<?= $this->extend('walimurid/templates/layout') ?>
<?= $this->section('title') ?>
Riwayat Pembayaran SPP
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    $dateTimeFormatter = new IntlDateFormatter('id_ID', IntlDateFormatter::LONG, IntlDateFormatter::SHORT);
?>
<div class="container-fluid p-0">
    <h4 class="mb-3 fw-bold"><i class="bi bi-clock-history me-2"></i>Riwayat Pembayaran SPP - <?= esc($namaSiswa) ?></h4>

    <div class="card shadow-sm">
        <div class="card-header bg-light">
             <h5 class="mb-0"><i class="bi bi-filter-circle me-2"></i>Filter Riwayat</h5>
        </div>
        <div class="card-body p-3 p-md-4">
            <form action="<?= site_url('walimurid/riwayat_pembayaran') ?>" method="get" class="row g-3 align-items-end mb-4">
                <div class="col-md-3">
                    <label for="periode_tipe_spp" class="form-label">Tipe Periode</label>
                    <select class="form-select" id="periode_tipe_spp" name="periode_tipe_spp">
                        <option value="bulanan" <?= ($sppFilter['periode_tipe'] === 'bulanan') ? 'selected' : '' ?>>Bulanan</option>
                        <option value="tahunan" <?= ($sppFilter['periode_tipe'] === 'tahunan') ? 'selected' : '' ?>>Tahunan</option>
                    </select>
                </div>
                <div class="col-md-3" id="kolom_periode_bulan_spp">
                    <label for="periode_bulan_spp" class="form-label">Pilih Bulan</label>
                    <input type="month" class="form-control" id="periode_bulan_spp" name="periode_bulan_spp" value="<?= esc($sppFilter['periode_bulan'] ?? date('Y-m')) ?>">
                </div>
                <div class="col-md-3" id="kolom_periode_tahun_spp" style="display:none;">
                    <label for="periode_tahun_spp" class="form-label">Pilih Tahun</label>
                    <input type="number" class="form-control" id="periode_tahun_spp" name="periode_tahun_spp" value="<?= esc($sppFilter['periode_tahun'] ?? date('Y')) ?>" min="2020" max="<?= date('Y') + 5 ?>">
                </div>
                <div class="col-md-3">
                    <label for="status_spp" class="form-label">Status</label>
                    <select class="form-select" id="status_spp" name="status_spp">
                        <option value="">Semua Status</option>
                        <option value="Lunas" <?= ($sppFilter['status'] === 'Lunas') ? 'selected' : '' ?>>Lunas</option>
                        <option value="Dicicil" <?= ($sppFilter['status'] === 'Dicicil') ? 'selected' : '' ?>>Dicicil</option>
                        <option value="Pending" <?= ($sppFilter['status'] === 'Pending') ? 'selected' : '' ?>>Pending</option>
                        <option value="Gagal" <?= ($sppFilter['status'] === 'Gagal') ? 'selected' : '' ?>>Gagal</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-self-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-2"></i>Tampilkan</button>
                </div>
            </form>
            
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th>Nama Tagihan</th>
                            <th>Tanggal Update</th>
                            <th class="text-end">Total Tagihan</th>
                            <th class="text-end">Jumlah Dibayar</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($riwayatSPP)): ?>
                            <?php foreach ($riwayatSPP as $index => $item): ?>
                            <tr>
                                <td class="text-center"><?= $index + 1 ?></td>
                                <td><?= esc($item['nama_tagihan'] ?? '-') ?></td>
                                <td><?= $dateTimeFormatter->format(strtotime($item['tanggal'])) ?></td>
                                <td class="text-end text-muted"><?= number_format($item['total_tagihan'], 0, ',', '.') ?></td>
                                <td class="text-end fw-bold text-success">+ <?= number_format($item['jumlah'], 0, ',', '.') ?></td>
                                
                                <?php
                                    // ================== LOGIKA FINAL DIMULAI DI SINI ==================
                                    $isDiarsipkan = ($item['status_tagihan'] === 'Diarsipkan');
                                    $statusText = $item['status'];
                                    $statusClass = 'bg-secondary';
                                    
                                    if ($isDiarsipkan) {
                                        if ($statusText === 'Dicicil') {
                                            $statusText = 'Dicicil (Diarsipkan)';
                                            $statusClass = 'bg-info';
                                        } else { // Untuk 'Pending' dan 'Gagal' yang diarsipkan
                                            $statusText = 'Gagal';
                                            $statusClass = 'bg-danger';
                                        }
                                    } else {
                                        // Logika status normal jika tidak diarsipkan
                                        if ($statusText === 'Lunas') { $statusClass = 'bg-success'; }
                                        elseif ($statusText === 'Dicicil') { $statusClass = 'bg-info'; }
                                        elseif ($statusText === 'Pending') { $statusClass = 'bg-warning text-dark'; }
                                        elseif ($statusText === 'Gagal') { $statusClass = 'bg-danger'; }
                                    }
                                ?>

                                <td class="text-center">
                                    <span class="badge <?= $statusClass ?>"><?= esc($statusText) ?></span>
                                </td>
                                <td class="text-center">
                                    <?php
                                        // Tombol Opsi untuk status LUNAS
                                        if ($item['status'] === 'Lunas') {
                                            echo '<a href="'.site_url('walimurid/riwayat_pembayaran/cetak_bukti_spp/' . $item['transaksi_id']).'" target="_blank" class="btn btn-sm btn-outline-info" title="Cetak Bukti"><i class="bi bi-printer"></i></a>';
                                        
                                        // Tombol Opsi untuk status DICICIL
                                        } elseif ($item['status'] === 'Dicicil') {
                                            if ($isDiarsipkan) { // Jika diarsipkan, tombol menjadi cetak bukti
                                                echo '<a href="'.site_url('walimurid/riwayat_pembayaran/cetak_bukti_spp/' . $item['transaksi_id']).'" target="_blank" class="btn btn-sm btn-outline-info" title="Cetak Bukti Pembayaran Sebagian"><i class="bi bi-printer"></i></a>';
                                            } else { // Jika aktif, tombol untuk bayar lagi
                                                echo '<a href="'.site_url('walimurid/pembayaran_spp?selected_tagihan_id=' . $item['id_tagihan_untuk_form']).'" class="btn btn-sm btn-warning" title="Lanjutkan Pembayaran"><i class="bi bi-credit-card"></i></a>';
                                            }
                                        
                                        // Tombol Opsi untuk status PENDING
                                        } elseif ($item['status'] === 'Pending') {
                                            if (!$isDiarsipkan) { // Hanya tampilkan jika tidak diarsipkan
                                                echo '<a href="'.site_url('walimurid/pembayaran_spp?selected_tagihan_id=' . $item['id_tagihan_untuk_form']).'" class="btn btn-sm btn-warning" title="Lanjutkan Pembayaran"><i class="bi bi-credit-card"></i></a>';
                                            } else { echo '-'; } // Tampilkan strip jika diarsipkan

                                        // Tombol Opsi untuk status GAGAL
                                        } elseif ($item['status'] === 'Gagal') {
                                            if (!$isDiarsipkan) { // Hanya tampilkan jika tidak diarsipkan
                                                echo '<a href="'.site_url('walimurid/pembayaran_spp?selected_tagihan_id=' . $item['id_tagihan_untuk_form']).'" class="btn btn-sm btn-outline-primary" title="Coba Lagi"><i class="bi bi-arrow-clockwise"></i></a>';
                                            } else { echo '-'; } // Tampilkan strip jika diarsipkan
                                        } else {
                                            echo '-';
                                        }
                                    ?>
                                </td>
                                 </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center py-4"><i class="bi bi-info-circle me-2"></i>Tidak ada riwayat pembayaran SPP untuk filter yang dipilih.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    function setupPeriodeToggle(tipeId, bulanId, tahunId) {
        const tipeSelect = document.getElementById(tipeId);
        if (!tipeSelect) return;
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
        toggleView();
    }

    setupPeriodeToggle('periode_tipe_spp', 'kolom_periode_bulan_spp', 'kolom_periode_tahun_spp');
});
</script>
<?= $this->endSection() ?>
