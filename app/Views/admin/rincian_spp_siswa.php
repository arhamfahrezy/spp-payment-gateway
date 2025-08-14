<?= $this->extend('admin/templates/layout') ?>
<?= $this->section('title') ?>
Rincian SPP per Siswa
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    $dateFormatter = new IntlDateFormatter('id_ID', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
?>
<div class="container-fluid p-0">
    <h4 class="mb-4 fw-bold"><i class="bi bi-list-stars me-2"></i>Rincian SPP per Siswa</h4>
    
    <div class="card shadow-sm">
        <div class="card-header bg-light p-0">
            <ul class="nav nav-tabs nav-fill" id="rincianTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link p-3 <?= ($filter['tab'] == 'aktif' ? 'active' : '') ?>" id="aktif-tab" data-bs-toggle="tab" data-bs-target="#aktif" type="button" role="tab" aria-controls="aktif" aria-selected="true">
                        <i class="bi bi-clock-fill me-2"></i>Tanggungan Siswa 
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link p-3 <?= ($filter['tab'] == 'riwayat' ? 'active' : '') ?>" id="riwayat-tab" data-bs-toggle="tab" data-bs-target="#riwayat" type="button" role="tab" aria-controls="riwayat" aria-selected="false">
                        <i class="bi bi-check-all me-2"></i>Riwayat & Arsip
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body p-4">
            <div class="tab-content" id="rincianTabContent">

                <div class="tab-pane fade <?= ($filter['tab'] == 'aktif' ? 'show active' : '') ?>" id="aktif" role="tabpanel" aria-labelledby="aktif-tab">
                    <h5 class="fw-semibold mb-3">Filter Tanggungan Aktif</h5>
                    <form method="get" action="<?= site_url('admin/rincian_spp_siswa') ?>" class="row g-3 align-items-end mb-4 p-3 border rounded bg-light">
                        <input type="hidden" name="tab" value="aktif">
                        <div class="col-md-3">
                            <label class="form-label">Periode Jatuh Tempo</label>
                            <input type="month" class="form-control form-control-sm" name="periode_aktif" value="<?= esc($filter['periode_aktif'] ?? '') ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Kelas</label>
                            <select class="form-select form-select-sm" name="kelas_aktif">
                                <option value="">Semua Kelas</option>
                                <option value="7" <?= ($filter['kelas_aktif'] ?? '') == '7' ? 'selected' : '' ?>>7</option>
                                <option value="8" <?= ($filter['kelas_aktif'] ?? '') == '8' ? 'selected' : '' ?>>8</option>
                                <option value="9" <?= ($filter['kelas_aktif'] ?? '') == '9' ? 'selected' : '' ?>>9</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status Bayar</label>
                            <select class="form-select form-select-sm" name="status_bayar_aktif">
                                <option value="">Semua</option>
                                <option value="Belum Lunas" <?= ($filter['status_bayar_aktif'] ?? '') == 'Belum Lunas' ? 'selected' : '' ?>>Belum Lunas</option>
                                <option value="Dicicil" <?= ($filter['status_bayar_aktif'] ?? '') == 'Dicicil' ? 'selected' : '' ?>>Dicicil</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Cari (Siswa/Tagihan)</label>
                            <input type="text" class="form-control form-control-sm" name="search_aktif" placeholder="Ketik nama atau nis..." value="<?= esc($filter['search_aktif'] ?? '') ?>">
                        </div>
                        <div class="col-md-1 text-end mt-3">
                            <button type="submit" class="btn btn-primary btn-sm px-4"><i class="bi bi-funnel me-1"></i></button>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="text-center">NIS</th>
                                    <th scope="col">Nama Siswa</th>
                                    <th scope="col" class="text-center">Kelas</th>
                                    <th scope="col">Nama Tagihan</th>
                                    <th scope="col">Jatuh Tempo</th>
                                    <th scope="col" class="text-end">Sisa Tagihan</th>
                                    <th scope="col" class="text-center">Status</th>
                                    <th scope="col" class="text-center" style="width: 10%;">Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($tanggunganAktifList)): ?>
                                    <?php foreach ($tanggunganAktifList as $item): ?>
                                    <tr>
                                        <td class="text-center"><?= esc($item['nis']) ?></td>
                                        <td><?= esc($item['nama']) ?></td>
                                        <td class="text-center"><?= esc($item['kelas']) ?></td>
                                        <td><?= esc($item['nama_tagihan']) ?></td>
                                        <td><?= $dateFormatter->format(strtotime($item['jatuh_tempo'])) ?></td>
                                        <td class="text-end fw-bold">Rp <?= number_format((int)$item['nominal'] - (int)($item['jumlah_bayar'] ?? 0), 0, ',', '.') ?></td>
                                        <td class="text-center">
                                            <?php
                                                $isDicicil = (int)($item['jumlah_bayar'] ?? 0) > 0;
                                                $isOverdue = new DateTime($item['jatuh_tempo']) < new DateTime('today');

                                                if ($isDicicil) {
                                                    // PRIORITAS PERTAMA: Jika sudah dicicil, statusnya selalu Dicicil.
                                                    $statusText = $isOverdue ? 'Dicicil(Jatuh Tempo)' : 'Dicicil';
                                                    echo '<span class="badge bg-info-soft text-info px-2 py-1"><i class="bi bi-pie-chart-fill me-1"></i>'. $statusText .'</span>';
                                                
                                                } elseif ($isOverdue) {
                                                    // Jika belum dicicil DAN sudah jatuh tempo
                                                    echo '<span class="badge bg-danger px-2 py-1"><i class="bi bi-exclamation-triangle-fill me-1"></i>JATUH TEMPO</span>';
                                                
                                                } else {
                                                    // Jika belum dicicil DAN belum jatuh tempo
                                                    echo '<span class="badge bg-danger-soft text-danger px-2 py-1"><i class="bi bi-hourglass-split me-1"></i>Belum Lunas</span>';
                                                }
                                            ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= site_url('admin/bayar_tagihan?nis=' . $item['nis'] . '&tagihan_id=' . $item['master_tagihan_id']) ?>" class="btn btn-outline-primary btn-sm me-1" title="Bayar Manual"><i class="bi bi-cash-coin"></i></a>
                                            <?php
                                                $nomor_wa = preg_replace('/^0/', '62', $item['telepon_orangtua']);
                                                $sisa = (int)$item['nominal'] - (int)($item['jumlah_bayar'] ?? 0);
                                                $pesan = "Yth. Wali dari siswa " . $item['nama'] . ", kami informasikan tagihan SPP '" . $item['nama_tagihan'] . "' dengan sisa Rp " . number_format($sisa, 0, ',', '.') . " akan segera jatuh tempo. Terima kasih.";
                                                $link_wa = "https://wa.me/" . $nomor_wa . "?text=" . urlencode($pesan);
                                            ?>
                                            <a href="<?= $link_wa ?>" class="btn btn-outline-success btn-sm" title="Kirim Notifikasi WhatsApp" target="_blank"><i class="bi bi-whatsapp"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="8" class="text-center p-4"><i class="bi bi-check2-circle fs-3 text-success"></i><br>Tidak ada tanggungan aktif yang cocok dengan filter.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade <?= ($filter['tab'] == 'riwayat' ? 'show active' : '') ?>" id="riwayat" role="tabpanel" aria-labelledby="riwayat-tab">
                    <h5 class="fw-semibold mb-3">Filter Riwayat & Arsip</h5>
                     <form method="get" action="<?= site_url('admin/rincian_spp_siswa') ?>" class="row g-3 align-items-end mb-4 p-3 border rounded bg-light">
                        <input type="hidden" name="tab" value="riwayat">
                        <div class="col-md-3">
                            <label class="form-label">Periode Jatuh Tempo</label>
                            <input type="month" class="form-control form-control-sm" name="periode_riwayat" value="<?= esc($filter['periode_riwayat'] ?? '') ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Kelas</label>
                            <select class="form-select form-select-sm" name="kelas_riwayat">
                                <option value="">Semua Kelas</option>
                                <option value="7" <?= ($filter['kelas_riwayat'] ?? '') == '7' ? 'selected' : '' ?>>7</option>
                                <option value="8" <?= ($filter['kelas_riwayat'] ?? '') == '8' ? 'selected' : '' ?>>8</option>
                                <option value="9" <?= ($filter['kelas_riwayat'] ?? '') == '9' ? 'selected' : '' ?>>9</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status Bayar</label>
                            <select class="form-select form-select-sm" name="status_bayar_riwayat">
                                <option value="">Semua</option>
                                <option value="Lunas" <?= ($filter['status_bayar_riwayat'] ?? '') == 'Lunas' ? 'selected' : '' ?>>Lunas</option>
                                <option value="Diarsipkan" <?= ($filter['status_bayar_riwayat'] ?? '') == 'Diarsipkan' ? 'selected' : '' ?>>Diarsipkan</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Cari (Siswa/Tagihan)</label>
                            <input type="text" class="form-control form-control-sm" name="search_riwayat" placeholder="Ketik nama atau nis..." value="<?= esc($filter['search_riwayat'] ?? '') ?>">
                        </div>
                        <div class="col-md-1 text-end mt-3">
                            <button type="submit" class="btn btn-primary btn-sm px-4"><i class="bi bi-funnel me-1"></i></button>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="text-center">NIS</th>
                                    <th scope="col">Nama Siswa</th>
                                    <th scope="col" class="text-center">Kelas</th>
                                    <th scope="col">Nama Tagihan</th>
                                    <th scope="col" class="text-end">Total Dibayar</th>
                                    <th scope="col" class="text-center">Status</th>
                                    <th scope="col" class="text-center" style="width: 10%;">Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($riwayatList)): ?>
                                    <?php foreach ($riwayatList as $item): ?>
                                    <tr>
                                        <td class="text-center"><?= esc($item['nis']) ?></td>
                                        <td><?= esc($item['nama']) ?></td>
                                        <td class="text-center"><?= esc($item['kelas']) ?></td>
                                        <td><?= esc($item['nama_tagihan']) ?></td>
                                        <td class="text-end fw-bold">Rp <?= number_format($item['jumlah_bayar'], 0, ',', '.') ?></td>
                                        <td class="text-center">
                                            <?php
                                                if ($item['status'] == 'Lunas') {
                                                    echo '<span class="badge bg-success-soft text-success px-2 py-1"><i class="bi bi-check-circle me-1"></i>Lunas</span>';
                                                } else { // Pasti Diarsipkan
                                                    echo '<span class="badge bg-secondary px-2 py-1"><i class="bi bi-archive-fill me-1"></i>Diarsipkan</span>';
                                                }
                                            ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= site_url('admin/rincian_spp_siswa/cetak_invoice/' . $item['id']) ?>" class="btn btn-outline-primary btn-sm" title="Cetak Bukti Bayar" target="_blank"><i class="bi bi-printer-fill"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" class="text-center p-4"><i class="bi bi-info-circle fs-3 text-muted"></i><br>Tidak ada data riwayat atau arsip yang cocok dengan filter.</td></tr>
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
    .bg-success-soft { background-color: rgba(25, 135, 84, 0.1); }
    .text-success { color: #198754 !important; }
    .bg-danger-soft { background-color: rgba(220, 53, 69, 0.1); }
    .text-danger { color: #dc3545 !important; }
    .bg-info-soft { background-color: rgba(13, 202, 240, 0.1); }
    .text-info { color: #0dcaf0 !important; }
    .badge.px-2.py-1 { font-size: 0.8em; }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('#rincianTab button[data-bs-toggle="tab"]');
    tabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', function(event) {
            const currentTabId = event.target.id.replace('-tab', '');
            // Update semua form yang mungkin ada di halaman
            document.querySelectorAll('form input[name="tab"]').forEach(input => {
                input.value = currentTabId;
            });
        });
    });
});
</script>
<?= $this->endSection() ?>

