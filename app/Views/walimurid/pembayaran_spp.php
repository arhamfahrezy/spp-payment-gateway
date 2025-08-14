<!-- C:\xampp\htdocs\payment-gateway\app\Views\walimurid\pembayaran_spp.php -->
<?= $this->extend('walimurid/templates/layout') ?>

<?= $this->section('title') ?>
Formulir Pembayaran SPP
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid p-0">
    <h4 class="mb-4"><i class="bi bi-credit-card-fill me-2"></i>Formulir Pembayaran SPP</h4>

    <form action="<?= site_url('walimurid/pembayaran_spp/prosesPembayaranSPP') ?>" method="post" id="formPembayaranSPP">
        <input type="hidden" name="jumlah_dibayar" id="jumlah_dibayar_numeric_hidden">

        <div class="row">
            <div class="col-lg-7 mb-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light"><h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Data Siswa</h5></div>
                    <div class="card-body">
                        <div class="row mb-2"><label class="col-sm-4 col-form-label">NIS</label><div class="col-sm-8"><input type="text" class="form-control-plaintext ps-2" name="nis" value="<?= esc($nis) ?>" readonly></div></div>
                        <div class="row mb-2"><label class="col-sm-4 col-form-label">Nama Siswa</label><div class="col-sm-8"><input type="text" class="form-control-plaintext ps-2" name="nama_siswa" value="<?= esc($nama_siswa) ?>" readonly></div></div>
                        <div class="row mb-0"><label class="col-sm-4 col-form-label">Kelas</label><div class="col-sm-8"><input type="text" class="form-control-plaintext ps-2" name="kelas" value="<?= esc($kelas) ?>" readonly></div></div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-light"><h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Pilih Tagihan Untuk Dibayar</h5></div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <label for="nama_tagihan" class="col-sm-4 col-form-label">Nama Tagihan</label>
                            <div class="col-sm-8">
                                <select class="form-select" id="nama_tagihan" name="nama_tagihan" required>
                                    <option value="" selected disabled>-- Pilih Jenis Tagihan --</option>
                                    <?php foreach($tagihanList as $tagihan): ?>
                                        <option value="<?= esc($tagihan['tagihan_id']) ?>"
                                            data-jatuh-tempo="<?= esc($tagihan['jatuh_tempo']) ?>"
                                            data-nominal="<?= esc($tagihan['nominal']) ?>"
                                            data-sudah-bayar="<?= esc($tagihan['jumlah_bayar'] ?? 0) ?>"
                                            data-sisa="<?= esc($tagihan['sisa_tagihan']) ?>"
                                            data-status="<?= esc($tagihan['status']) ?>">
                                            <?= esc($tagihan['nama_tagihan']) ?> (Sisa: Rp <?= number_format($tagihan['sisa_tagihan'], 0, ',', '.') ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2"><label class="col-sm-4 col-form-label">Jatuh Tempo</label><div class="col-sm-8"><input type="text" class="form-control-plaintext ps-2" id="jatuh_tempo" name="jatuh_tempo" readonly placeholder="-"></div></div>
                        <div class="row mb-2"><label class="col-sm-4 col-form-label">Status Tagihan</label><div class="col-sm-8"><input type="text" class="form-control-plaintext ps-2" id="status_pembayaran" name="status_pembayaran" readonly placeholder="-"></div></div>
                        <div class="row mb-0"><label class="col-sm-4 col-form-label fw-bold text-danger">Sisa Tagihan</label><div class="col-sm-8"><input type="text" class="form-control-plaintext fw-bold ps-2 text-danger" id="detail_sisa" name="sisa_tagihan" readonly value="Rp 0"></div></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-light"><h5 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Detail Pembayaran</h5></div>
                    <div class="card-body">
                        <div class="mb-2">
                            <label class="form-label fw-bold">Total Tagihan:</label>
                            <input type="text" class="form-control-plaintext form-control-lg ps-2" id="total_tagihan" name="total_tagihan" readonly value="Rp 0">
                        </div>
                        <div class="mb-2" id="info_sudah_bayar" style="display: none;">
                             <label class="form-label fw-bold">Telah Dibayar:</label>
                             <input type="text" class="form-control-plaintext form-control-lg ps-2 text-success" id="sudah_bayar_display" readonly value="Rp 0">
                        </div>
                        <hr>
                        <div class="mb-2">
                            <label for="jumlah_dibayar_display" class="form-label fw-bold">Jumlah yang Akan Dibayar:</label>
                            <input type="text" class="form-control form-control-lg" id="jumlah_dibayar_display" placeholder="Rp 0" required style="text-align: right; font-weight: bold; color: #d63384;">
                            <div class="form-text">Masukkan nominal pembayaran Anda. Bisa dicicil.</div>
                        </div>
                        <div class="mb-0"><label for="catatan" class="form-label fw-bold">Catatan (Opsional):</label><textarea class="form-control" id="catatan" name="catatan" rows="3" placeholder="Tambahkan catatan untuk pembayaran ini..."></textarea></div>
                    </div>
                    <div class="card-footer text-center p-3"><button type="submit" class="btn btn-primary btn-lg w-100 shadow"><i class="bi bi-shield-lock-fill me-2"></i>Lanjutkan ke Pembayaran</button></div>
                </div>
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const namaTagihanSelect = document.getElementById('nama_tagihan');
    const jatuhTempoInput = document.getElementById('jatuh_tempo');
    const totalTagihanInput = document.getElementById('total_tagihan');
    const statusPembayaranInput = document.getElementById('status_pembayaran');
    const jumlahDibayarDisplayInput = document.getElementById('jumlah_dibayar_display');
    const jumlahDibayarHiddenInput = document.getElementById('jumlah_dibayar_numeric_hidden');
    const infoSudahBayarDiv = document.getElementById('info_sudah_bayar');
    const sudahBayarDisplay = document.getElementById('sudah_bayar_display');
    const formPembayaran = document.getElementById('formPembayaranSPP');
    const detailSisaInput = document.getElementById('detail_sisa');

    function formatRupiahDisplay(angkaStr) {
        if (!angkaStr || angkaStr.trim() === '') return 'Rp 0';
        let number_string = angkaStr.replace(/[^\d,]/g, ''), ribuan = number_string.substr(number_string.length % 3).match(/\d{3}/gi);
        return 'Rp ' + (ribuan ? number_string.substr(0, number_string.length % 3) + (number_string.length % 3 ? '.' : '') + ribuan.join('.') : number_string);
    }
    
    function formatRupiahInput(angkaStr) {
        if (!angkaStr) return '';
        let number_string = angkaStr.replace(/[^\d,]/g, ''), ribuan = number_string.substr(number_string.length % 3).match(/\d{3}/gi);
        return ribuan ? number_string.substr(0, number_string.length % 3) + (number_string.length % 3 ? '.' : '') + ribuan.join('.') : number_string;
    }

    function parseRupiah(rupiahValue) {
        return parseInt(String(rupiahValue).replace(/[^\d]/g, ''), 10) || 0;
    }

    // MODIFIKASI: Tambahkan event listener untuk validasi jumlah bayar
    jumlahDibayarDisplayInput.addEventListener('input', function(e) {
        let numericValue = parseRupiah(this.value);
        
        const selectedOption = namaTagihanSelect.options[namaTagihanSelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            const sisaTagihan = parseInt(selectedOption.dataset.sisa, 10);

            if (numericValue > sisaTagihan) {
                // Jika input melebihi sisa, batasi nilainya ke sisa tagihan
                numericValue = sisaTagihan;
                Swal.fire({
                    icon: 'warning',
                    title: 'Jumlah Melebihi Tagihan',
                    text: 'Anda tidak dapat membayar lebih dari sisa tagihan.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            }
        }
        
        this.value = formatRupiahInput(String(numericValue));
        jumlahDibayarHiddenInput.value = numericValue;
    });

    function updateFormFields() {
        const selectedOption = namaTagihanSelect.options[namaTagihanSelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            jatuhTempoInput.value = new Date(selectedOption.dataset.jatuhTempo).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
            
            const nominal = selectedOption.dataset.nominal || '0';
            const sudahBayar = selectedOption.dataset.sudahBayar || '0';
            const sisa = selectedOption.dataset.sisa || '0';

            totalTagihanInput.value = formatRupiahDisplay(nominal);
            detailSisaInput.value = formatRupiahDisplay(sisa);
            
            if (parseInt(sudahBayar) > 0) {
                sudahBayarDisplay.value = formatRupiahDisplay(sudahBayar);
                infoSudahBayarDiv.style.display = 'block';
            } else {
                infoSudahBayarDiv.style.display = 'none';
            }
            
            jumlahDibayarDisplayInput.value = formatRupiahInput(sisa); 
            jumlahDibayarHiddenInput.value = sisa; 
            
            statusPembayaranInput.value = parseInt(sudahBayar) > 0 ? 'Dicicil' : 'Belum Lunas';
        } else {
            jatuhTempoInput.value = '-';
            statusPembayaranInput.value = '-';
            totalTagihanInput.value = 'Rp 0';
            detailSisaInput.value = 'Rp 0'; 
            infoSudahBayarDiv.style.display = 'none';
            jumlahDibayarDisplayInput.value = '';
            jumlahDibayarHiddenInput.value = '';
        }
    }

    namaTagihanSelect.addEventListener('change', updateFormFields);
    const preSelectedTagihanId = "<?= esc($selected_tagihan_id ?? '', 'js') ?>";
    if (preSelectedTagihanId) {
        namaTagihanSelect.value = preSelectedTagihanId;
        namaTagihanSelect.dispatchEvent(new Event('change'));
    } else {
        updateFormFields();
    }
});
</script>
<?= $this->endSection() ?>
