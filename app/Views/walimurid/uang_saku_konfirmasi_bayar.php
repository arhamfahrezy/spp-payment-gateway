<?= $this->extend('walimurid/templates/layout') ?>

<?= $this->section('title') ?>
<?= $title ?? 'Konfirmasi Top Up' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <h3 class="mb-4 text-center text-success fw-bold"><i class="bi bi-shield-check"></i> Konfirmasi Top Up Uang Saku</h3>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white"><i class="bi bi-person-badge me-2"></i>Detail Siswa</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">NIS<span class="fw-bold"><?= esc($nis_siswa ?? '-') ?></span></li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">Nama Siswa<span class="fw-bold"><?= esc($nama_siswa_display ?? '-') ?></span></li>
                </ul>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white"><i class="bi bi-cash-coin me-2"></i> Detail Top Up</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">Deskripsi<span class="fw-bold"><?= esc($item_name_display ?? 'Top Up Uang Saku') ?></span></li>
                    <li class="list-group-item d-flex justify-content-between align-items-center"><strong>Jumlah Top Up</strong><strong class="text-danger fs-5">Rp <?= isset($jumlah_yang_dibayar) ? number_format($jumlah_yang_dibayar, 0, ',', '.') : '-' ?></strong></li>
                    <?php if (!empty($catatan_display)): ?>
                    <li class="list-group-item"><strong>Catatan Anda:</strong><br><p class="mb-0 fst-italic">"<?= nl2br(esc($catatan_display)) ?>"</p></li>
                    <?php endif; ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">Order ID<span class="badge bg-secondary"><?= esc($orderId ?? '-') ?></span></li>
                </ul>
            </div>
            
            <div class="text-center">
                <p class="text-muted small mb-3">Anda akan diarahkan ke halaman pembayaran Midtrans yang aman. Pastikan semua detail sudah benar.</p>
                <button id="pay-button-uangsaku" class="btn btn-lg btn-success shadow px-5"><i class="bi bi-lock-fill me-2"></i> Lanjutkan Pembayaran</button>
                <!-- MODIFIKASI: Tombol batal sekarang menggunakan ID dan akan dihandle oleh SweetAlert2 -->
                <a href="<?= site_url('walimurid/pembayaran_uang_saku/batal/' . esc($orderId ?? '', 'url')) ?>" 
                   id="cancel-button-uangsaku"
                   class="btn btn-lg btn-outline-danger shadow px-4 ms-2">
                   <i class="bi bi-x-circle me-2"></i> Batalkan
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Pastikan SweetAlert2 sudah di-load di layout utama, atau load di sini -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    document.getElementById('pay-button-uangsaku').onclick = function(){
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memproses...';
        snap.pay('<?= esc($snapToken ?? '', 'js') ?>', {
            onSuccess: function(result){
                Swal.fire({icon: 'success', title: 'Top Up Berhasil!', text: 'Saldo akan segera diperbarui.', timer: 2500, showConfirmButton: false})
                .then(() => window.location.href = "<?= site_url('walimurid/uang_saku?order_id=' . esc($orderId ?? '', 'url')) ?>");
            },
            onPending: function(result){
                Swal.fire({icon: 'info', title: 'Pembayaran Tertunda', text: 'Silakan selesaikan pembayaran Anda.'})
                .then(() => window.location.href = "<?= site_url('walimurid/uang_saku') ?>");
            },
            onError: function(result){
                Swal.fire({icon: 'error', title: 'Pembayaran Gagal', text: 'Proses top up gagal atau dibatalkan.'})
                .then(() => window.location.href = "<?= site_url('walimurid/pembayaran_uang_saku') ?>");
            }
        });
    };

    // MODIFIKASI: Script baru untuk konfirmasi pembatalan top up
    document.getElementById('cancel-button-uangsaku').addEventListener('click', function(event) {
        event.preventDefault();
        const href = this.href;

        Swal.fire({
            title: 'Anda Yakin?',
            text: "Anda akan membatalkan proses top up ini.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, batalkan!',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = href;
            }
        });
    });
</script>
<?= $this->endSection() ?>
