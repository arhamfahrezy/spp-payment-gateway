<?= $this->extend('admin/templates/layout') ?>

<?= $this->section('title') ?>
Pembayaran Tagihan Manual
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
    .select2-container .select2-selection--single {
        height: calc(1.5em + .75rem + 2px) !important;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        line-height: 1.5 !important;
        padding-left: 0.75rem !important;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
        top: 0.375rem !important;
    }
</style>
<?= $this->endSection() ?>


<?= $this->section('content') ?>
<div class="container-fluid p-0">
    <h4 class="mb-4 fw-bold"><i class="bi bi-keyboard me-2"></i>Input Pembayaran Manual</h4>

    <form action="<?= site_url('admin/bayar_tagihan/proses') ?>" method="post" id="formManualPayment">
        <?= csrf_field() ?>
        
        <div class="row">
            <div class="col-lg-7 mb-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light"><h5 class="mb-0"><i class="bi bi-person-check-fill me-2"></i>Data Siswa</h5></div>
                    <div class="card-body">
                         <div class="row mb-3">
                            <label for="pilih_siswa" class="col-sm-4 col-form-label">Pilih Siswa</label>
                            <div class="col-sm-8">
                                <select class="form-select" id="pilih_siswa" name="nis_siswa" required>
                                    <option></option>
                                    <?php foreach($siswaList as $siswa): ?>
                                        <option value="<?= esc($siswa['nis']) ?>" data-kelas="<?= esc($siswa['kelas']) ?>"><?= esc($siswa['nama_siswa']) ?> (NIS: <?= esc($siswa['nis']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div id="detail_siswa_wrapper" style="display: none;">
                            <hr>
                            <div class="row mb-2"><label class="col-sm-4 col-form-label">NIS</label><div class="col-sm-8"><input type="text" class="form-control-plaintext ps-2" id="info_nis" readonly></div></div>
                            <div class="row mb-2"><label class="col-sm-4 col-form-label">Nama Siswa</label><div class="col-sm-8"><input type="text" class="form-control-plaintext ps-2" id="info_nama" readonly></div></div>
                            <div class="row mb-0"><label class="col-sm-4 col-form-label">Kelas</label><div class="col-sm-8"><input type="text" class="form-control-plaintext ps-2" id="info_kelas" readonly></div></div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm" id="card_pilih_tagihan" style="display: none;">
                    <div class="card-header bg-light"><h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Pilih Tagihan Untuk Dibayar</h5></div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <label for="pilih_tagihan" class="col-sm-4 col-form-label">Nama Tagihan</label>
                            <div class="col-sm-8">
                                <select class="form-select" id="pilih_tagihan" name="tagihan_id" required disabled>
                                    <option value="" selected disabled>-- Pilih siswa terlebih dahulu --</option>
                                </select>
                            </div>
                        </div>
                        <div id="detail_tagihan_wrapper" style="display: none;">
                            <hr>
                            <div class="row mb-2"><label class="col-sm-4 col-form-label">Jatuh Tempo</label><div class="col-sm-8"><input type="text" class="form-control-plaintext ps-2" id="info_jatuh_tempo" readonly></div></div>
                            <div class="row mb-2"><label class="col-sm-4 col-form-label">Status Tagihan</label><div class="col-sm-8"><input type="text" class="form-control-plaintext ps-2" id="info_status" readonly></div></div>
                            <div class="row mb-0"><label class="col-sm-4 col-form-label fw-bold text-danger">Sisa Tagihan:</label><div class="col-sm-8"><input type="text" class="form-control-plaintext fw-bold ps-2 text-danger" id="detail_sisa" readonly value="Rp 0"></div></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 mb-4">
                <div class="card shadow-sm" id="card_payment_details" style="display: none;">
                    <div class="card-header bg-light"><h5 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Detail Pembayaran</h5></div>
                    <div class="card-body">
                        <div class="mb-2"><label class="form-label fw-bold">Total Tagihan:</label><input type="text" class="form-control-plaintext form-control-lg ps-2" id="detail_total" readonly value="Rp 0"></div>
                        <div class="mb-2"><label class="form-label fw-bold">Telah Dibayar:</label><input type="text" class="form-control-plaintext form-control-lg ps-2 text-success" id="detail_dibayar" readonly value="Rp 0"></div>
                        <hr>
                        <div class="mb-2"><label for="jumlah_bayar" class="form-label fw-bold">Jumlah yang Akan Dibayar:</label><input type="text" class="form-control form-control-lg" id="jumlah_bayar" name="jumlah_bayar" placeholder="0" required style="text-align: right; font-weight: bold; color: #d63384;"></div>
                        <div class="mb-2"><label for="tanggal_bayar" class="form-label fw-bold">Tanggal Bayar:</label><input type="date" class="form-control form-control-lg" id="tanggal_bayar" name="tanggal_bayar" value="<?= date('Y-m-d') ?>" required></div>
                        <div class="mb-0"><label for="catatan" class="form-label fw-bold">Catatan (Opsional):</label><textarea class="form-control" name="catatan" id="catatan" rows="2" placeholder="Cth: Pembayaran tunai via Bapak..."></textarea></div>
                    </div>
                    <div class="card-footer text-center p-3">
                        <button type="submit" class="btn btn-primary btn-lg w-100 shadow"><i class="bi bi-save-fill me-2"></i>Simpan Pembayaran</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    $('#pilih_siswa').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: '-- Cari dan Pilih Siswa --',
    });

    // Variabel DOM
    const form = document.getElementById('formManualPayment');
    const siswaSelect = document.getElementById('pilih_siswa');
    
    const detailSiswaWrapper = document.getElementById('detail_siswa_wrapper');
    const infoNis = document.getElementById('info_nis');
    const infoNama = document.getElementById('info_nama');
    const infoKelas = document.getElementById('info_kelas');
    
    const tagihanCard = document.getElementById('card_pilih_tagihan');
    const tagihanSelect = document.getElementById('pilih_tagihan');
    const detailTagihanWrapper = document.getElementById('detail_tagihan_wrapper');
    const infoJatuhTempo = document.getElementById('info_jatuh_tempo');
    const infoStatus = document.getElementById('info_status');
    const paymentCard = document.getElementById('card_payment_details');
    const detailTotal = document.getElementById('detail_total');
    const detailDibayar = document.getElementById('detail_dibayar');
    const detailSisa = document.getElementById('detail_sisa');
    const jumlahBayarInput = document.getElementById('jumlah_bayar');

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
    }
    
    jumlahBayarInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/[^0-9]/g, '');
        let numericValue = parseInt(value, 10) || 0;

        const selectedTagihanOption = tagihanSelect.options[tagihanSelect.selectedIndex];
        if (selectedTagihanOption && selectedTagihanOption.value) {
            const sisaTagihan = parseInt(selectedTagihanOption.dataset.sisa, 10);
            if (numericValue > sisaTagihan) {
                numericValue = sisaTagihan;
                Swal.fire({
                    icon: 'warning',
                    title: 'Jumlah Melebihi Sisa Tagihan!',
                    toast: true,
                    position: 'top',
                    showConfirmButton: false,
                    timer: 3000
                });
            }
        }
        e.target.value = numericValue.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    });

    $('#pilih_siswa').on('change', async function() {
        const selectedSiswa = $(this).find(':selected');
        const nis = this.value;
        const nama = selectedSiswa.text().split(' (NIS:')[0];
        const kelas = selectedSiswa.data('kelas');
        
        // Reset
        detailSiswaWrapper.style.display = 'none';
        tagihanCard.style.display = 'none';
        paymentCard.style.display = 'none';
        detailTagihanWrapper.style.display = 'none';

        if (!nis) return;
        
        // Tampilkan detail siswa
        infoNis.value = nis;
        infoNama.value = nama;
        infoKelas.value = kelas;
        detailSiswaWrapper.style.display = 'block';
        tagihanCard.style.display = 'block';

        tagihanSelect.innerHTML = '<option>Memuat tagihan...</option>';
        tagihanSelect.disabled = true;

        try {
            const response = await fetch(`<?= site_url('admin/bayar_tagihan/get_tagihan/') ?>${nis}`);
            if (!response.ok) throw new Error('Network response was not ok.');
            const tagihanList = await response.json();
            
            tagihanSelect.innerHTML = '<option value="" selected disabled>-- Pilih Tagihan --</option>';
            if (tagihanList.length > 0) {
                tagihanList.forEach(tagihan => {
                    const option = document.createElement('option');
                    option.value = tagihan.id;
                    option.textContent = `${tagihan.nama_tagihan} (Sisa: ${formatRupiah(tagihan.sisa_tagihan)})`;
                    option.dataset.nominal = tagihan.nominal;
                    option.dataset.dibayar = tagihan.jumlah_bayar;
                    option.dataset.sisa = tagihan.sisa_tagihan;
                    option.dataset.jatuhTempo = tagihan.jatuh_tempo;
                    option.dataset.status = tagihan.status;
                    tagihanSelect.appendChild(option);
                });
                tagihanSelect.disabled = false;

                const preselectedTagihanId = '<?= esc($preselectedTagihanId ?? '', 'js') ?>';
                if (preselectedTagihanId) {
                    tagihanSelect.value = preselectedTagihanId;
                    $(tagihanSelect).trigger('change');
                }
            } else {
                tagihanSelect.innerHTML = '<option value="" selected disabled>-- Tidak ada tagihan terutang --</option>';
            }
        } catch (error) {
            console.error('Gagal memuat tagihan:', error);
            tagihanSelect.innerHTML = '<option value="" selected disabled>-- Gagal memuat data --</option>';
            Swal.fire('Error', 'Gagal memuat data tagihan. Periksa koneksi atau hubungi developer.', 'error');
        }
    });

    $('#pilih_tagihan').on('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (!selectedOption || !selectedOption.value) {
            paymentCard.style.display = 'none';
            detailTagihanWrapper.style.display = 'none';
            return;
        }
        
        // Tampilkan Detail Tagihan
        const jatuhTempo = new Date(selectedOption.dataset.jatuhTempo).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
        const sudahBayar = parseInt(selectedOption.dataset.dibayar, 10);
        const statusText = sudahBayar > 0 ? 'Dicicil' : 'Belum Lunas';
        
        infoJatuhTempo.value = jatuhTempo;
        infoStatus.value = statusText;
        detailTagihanWrapper.style.display = 'block';
        
        // Tampilkan Detail Pembayaran (Kolom Kanan)
        const nominal = parseInt(selectedOption.dataset.nominal, 10);
        const sisa = parseInt(selectedOption.dataset.sisa, 10);
        
        detailTotal.value = formatRupiah(nominal);
        detailDibayar.value = formatRupiah(sudahBayar);
        detailSisa.value = formatRupiah(sisa);
        
        document.getElementById('jumlah_bayar').value = sisa.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');

        paymentCard.style.display = 'block';
    });

    const preselectedNis = '<?= esc($preselectedNis ?? '', 'js') ?>';
    if (preselectedNis) {
        $('#pilih_siswa').val(preselectedNis).trigger('change');
    }
    
    // --- MODIFIKASI BARU: Tambahkan konfirmasi sebelum submit ---
    form.addEventListener('submit', function(event) {
        event.preventDefault(); // Mencegah form langsung dikirim

        const siswaText = siswaSelect.options[siswaSelect.selectedIndex].text;
        const tagihanText = tagihanSelect.options[tagihanSelect.selectedIndex].text.split(' (Sisa:')[0];
        const jumlahBayarText = formatRupiah(jumlahBayarInput.value.replace(/\D/g, ''));
        const tanggalBayarValue = document.getElementById('tanggal_bayar').value;
        const tanggalBayarFormatted = new Date(tanggalBayarValue).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });

        Swal.fire({
            title: 'Konfirmasi Pembayaran',
            html: `Anda akan menyimpan pembayaran dengan detail berikut:
                   <ul class="list-group list-group-flush text-start mt-3">
                     <li class="list-group-item"><b>Siswa:</b> ${siswaText}</li>
                     <li class="list-group-item"><b>Tagihan:</b> ${tagihanText}</li>
                     <li class="list-group-item"><b>Tanggal Bayar:</b> ${tanggalBayarFormatted}</li>
                     <li class="list-group-item"><b>Jumlah Bayar:</b> <strong class="text-success">${jumlahBayarText}</strong></li>
                   </ul>
                   <p class="mt-3">Pastikan semua data sudah benar. Lanjutkan?</p>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan Pembayaran!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Hapus format titik sebelum mengirim form
                jumlahBayarInput.value = jumlahBayarInput.value.replace(/\D/g, '');
                form.submit(); // Kirim form jika dikonfirmasi
            }
        });
    });
});
</script>
<?= $this->endSection() ?>