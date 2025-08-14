<!-- C:\xampp\htdocs\payment-gateway\app\Views\walimurid\payment_page.php -->
<?= $this->extend('walimurid/templates/layout') ?>

<?= $this->section('title') ?>
Konfirmasi Pembayaran SPP
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    // Formatter untuk tanggal (e.g., 7 Juli 2025)
    $dateFormatter = new IntlDateFormatter('id_ID', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
?>
<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <h4 class="mb-4 text-center text-success"><i class="bi bi-shield-check"></i> Konfirmasi Pembayaran SPP</h4>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-person-badge me-2"></i>Detail Siswa
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">Nomor Induk Siswa (NIS)<span class="fw-bold"><?= esc($nis_siswa ?? '-') ?></span></li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">Nama Lengkap Siswa<span class="fw-bold"><?= esc($nama_siswa_display ?? '-') ?></span></li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">Kelas<span class="fw-bold"><?= esc($kelas_siswa ?? '-') ?></span></li>
                </ul>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <i class="bi bi-receipt me-2"></i>Detail Tagihan
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">Nama Tagihan<span class="fw-bold"><?= esc($nama_tagihan_display ?? '-') ?></span></li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">Jatuh Tempo<span class="fw-bold"><?= isset($jatuh_tempo_display) ? $dateFormatter->format(strtotime($jatuh_tempo_display)) : '-' ?></span></li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">Nominal Tagihan Asli<span class="fw-bold">Rp <?= isset($nominal_asli_tagihan) ? number_format($nominal_asli_tagihan, 0, ',', '.') : '-' ?></span></li>
                </ul>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                   <i class="bi bi-credit-card-2-front-fill me-2"></i> Ringkasan Pembayaran
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Jumlah yang Akan Dibayar</strong>
                    <strong class="text-danger fs-5">Rp <?= isset($jumlah_yang_dibayar) ? number_format($jumlah_yang_dibayar, 0, ',', '.') : '-' ?></strong>
                    </li>
                    <?php if (!empty($catatan_display)): ?>
                    <li class="list-group-item">
                    <strong>Catatan Anda:</strong><br>
                    <p class="mb-0 fst-italic">"<?= nl2br(esc($catatan_display)) ?>"</p>
                    </li>
                    <?php endif; ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                    Order ID (Referensi)
                    <span class="badge bg-secondary"><?= esc($orderId ?? '-') ?></span>
                    </li>
                </ul>
            </div>
            
            <div class="text-center">
                <p class="text-muted small mb-3">
                Anda akan diarahkan ke halaman pembayaran Midtrans yang aman. 
                <br> Pastikan semua detail di atas sudah benar sebelum melanjutkan.</p>
                <button id="pay-button" class="btn btn-lg btn-success shadow px-5">
                <i class="bi bi-lock-fill me-2"></i> Lanjutkan Pembayaran</button>
                
                <!-- MODIFIKASI: Menghapus onclick bawaan dan menambahkan ID untuk JS -->
                <a href="<?= site_url('walimurid/pembayaran_spp/batal/' . esc($orderId ?? '', 'url')) ?>" 
                   id="cancel-button"
                   class="btn btn-lg btn-outline-danger shadow px-4 ms-2">
                   <i class="bi bi-x-circle me-2"></i> Batalkan
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
    // Script Snap.pay tidak ada perubahan, tetap sama seperti sebelumnya
    document.getElementById('pay-button').onclick = function(){
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';

        snap.pay('<?= esc($snapToken ?? '', 'js') ?>', {
            onSuccess: function(result){
                Swal.fire({
                    icon: 'success', title: 'Pembayaran Berhasil!',
                    text: 'Terima kasih, pembayaran Anda telah berhasil kami terima.',
                    showConfirmButton: false, timer: 2500
                }).then(() => {
                    window.location.href = "<?= site_url('walimurid/riwayat_pembayaran?order_id=' . esc($orderId ?? '', 'url')) ?>";
                });
            },
            onPending: function(result){
                 Swal.fire({
                    icon: 'info', title: 'Pembayaran Tertunda',
                    html: `Pembayaran Anda tertunda. Silakan selesaikan pembayaran Anda.<br><small>Order ID: ${result.order_id}</small>`,
                    showConfirmButton: true,
                }).then(() => {
                    window.location.href = "<?= site_url('walimurid/tagihan_spp') ?>";
                });
            },
            onError: function(result){
                 Swal.fire({
                    icon: 'error', title: 'Pembayaran Gagal',
                    text: 'Terjadi kesalahan atau Anda membatalkan pembayaran.',
                    showConfirmButton: true,
                }).then(() => {
                    const payButton = document.getElementById('pay-button');
                    payButton.disabled = false;
                    payButton.innerHTML = '<i class="bi bi-lock-fill me-2"></i> Lanjutkan Pembayaran';
                });
            },
            onClose: function(){
                const payButton = document.getElementById('pay-button');
                if (payButton.disabled) {
                    Swal.fire({
                        icon: 'warning', title: 'Pembayaran Tertunda',
                        text: 'Anda menutup jendela pembayaran sebelum transaksi selesai.',
                        showConfirmButton: true,
                    });
                    payButton.disabled = false;
                    payButton.innerHTML = '<i class="bi bi-lock-fill me-2"></i> Lanjutkan Pembayaran';
                }
            }
        });
    };

    // MODIFIKASI: Script baru untuk konfirmasi pembatalan menggunakan SweetAlert2
    document.getElementById('cancel-button').addEventListener('click', function(event) {
        event.preventDefault(); // Mencegah link berpindah halaman secara langsung
        const href = this.href;

        Swal.fire({
            title: 'Anda Yakin?',
            text: "Anda akan membatalkan proses pembayaran ini. Status akan diubah menjadi 'Gagal'.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, batalkan!',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika pengguna menekan "Ya", arahkan ke URL pembatalan
                window.location.href = href;
            }
        });
    });
</script>
<?= $this->endSection() ?>
