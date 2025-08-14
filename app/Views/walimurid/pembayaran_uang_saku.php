<!-- C:\xampp\htdocs\payment-gateway\app\Views\walimurid\pembayaran_uang_saku.php -->
<?= $this->extend('walimurid/templates/layout') ?>

<?= $this->section('title') ?>
<?= $title ?? 'Top Up Uang Saku' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid p-0">
    <h4 class="mb-4 fw-bold"><i class="bi bi-window-plus me-2"></i>Top Up Uang Saku</h4>

    <?php if(session()->getFlashdata('error')): ?><div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div><?php endif; ?>
    <?php if(session()->getFlashdata('info')): ?><div class="alert alert-info alert-dismissible fade show" role="alert"><i class="bi bi-info-circle-fill me-2"></i> <?= session()->getFlashdata('info') ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div><?php endif; ?>
    <?php if(session()->getFlashdata('success')): ?><div class="alert alert-success alert-dismissible fade show" role="alert"><i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div><?php endif; ?>

    <!-- MODIFIKASI: Tambahkan notifikasi jika ada pembayaran pending -->
    <?php if (isset($pending_payment) && $pending_payment): ?>
    <div class="alert alert-warning" role="alert">
        <h5 class="alert-heading"><i class="bi bi-hourglass-split"></i> Anda Memiliki Top Up Tertunda!</h5>
        <p>Anda memiliki proses top up sebesar <strong>Rp <?= number_format($pending_payment['jumlah_bayar'], 0, ',', '.') ?></strong> yang belum selesai. Anda dapat melanjutkan pembayaran tersebut atau membatalkannya dengan mengisi nominal baru di bawah ini.</p>
        <hr>
        <p class="mb-0">Formulir di bawah telah terisi otomatis. Ubah nominal jika Anda ingin membatalkan top up lama dan membuat yang baru.</p>
    </div>
    <?php endif; ?>


    <div class="card shadow-sm">
        <div class="card-header bg-light"><h5 class="mb-0">Formulir Top Up</h5></div>
        <div class="card-body">
            <?php if (isset($nis) && $nis !== '-'): ?>
            <form action="<?= site_url('walimurid/pembayaran_uang_saku/prosesPembayaran') ?>" method="post" id="formTopUpUangSaku">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label">NIS</label><input type="text" class="form-control-plaintext ps-1" name="nis" value="<?= esc($nis) ?>" readonly></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Nama Siswa</label><input type="text" class="form-control-plaintext ps-1" name="nama_siswa" value="<?= esc($nama_siswa_view) ?>" readonly></div>
                    <div class="col-md-6 mb-3">
                        <label for="nominal_display" class="form-label fw-bold">Nominal Top Up</label>
                        <!-- MODIFIKASI: Isi value jika ada data pending -->
                        <div class="input-group"><span class="input-group-text">Rp</span><input type="text" class="form-control form-control-lg" id="nominal_display" placeholder="0" required value="<?= isset($pending_payment) ? number_format($pending_payment['jumlah_bayar'], 0, ',', '.') : '' ?>"></div>
                        <input type="hidden" name="nominal" id="nominal_numeric" value="<?= isset($pending_payment) ? $pending_payment['jumlah_bayar'] : '' ?>">
                        <div class="form-text">Masukkan jumlah nominal top up.</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="catatan" class="form-label fw-bold">Catatan (Opsional)</label>
                        <!-- MODIFIKASI: Isi value jika ada data pending -->
                        <textarea class="form-control" id="catatan" name="catatan" rows="4" placeholder="Misal: Uang saku tambahan Juli"><?= esc($pending_payment['catatan_walimurid'] ?? '') ?></textarea>
                    </div>
                </div>
                <div class="mt-3 text-end"><button type="submit" class="btn btn-primary btn-lg px-4 shadow-sm"><i class="bi bi-send-plus-fill me-2"></i>Lanjutkan ke Pembayaran</button></div>
            </form>
            <?php else: ?>
                <div class="alert alert-warning"><i class="bi bi-exclamation-circle-fill me-2"></i> Tidak dapat melanjutkan. <?= esc($nama_siswa_view ?? 'Data siswa tidak ditemukan.') ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nominalDisplayInput = document.getElementById('nominal_display');
    const nominalHiddenInput = document.getElementById('nominal_numeric');

    function formatRupiahInput(angkaStr) {
        if (!angkaStr) return '';
        let number_string = String(angkaStr).replace(/[^\d]/g, ''), ribuan = number_string.substr(number_string.length % 3).match(/\d{3}/gi);
        return ribuan ? number_string.substr(0, number_string.length % 3) + (number_string.length % 3 ? '.' : '') + ribuan.join('.') : number_string;
    }

    function parseRupiah(rupiahValue) {
        return parseInt(String(rupiahValue).replace(/[^\d]/g, ''), 10) || 0;
    }

    if (nominalDisplayInput) {
        nominalDisplayInput.addEventListener('input', function(e) {
            let numericValue = parseRupiah(this.value);
            this.value = formatRupiahInput(numericValue);
            if (nominalHiddenInput) nominalHiddenInput.value = numericValue;
        });
    }
});
</script>
<?= $this->endSection() ?>
