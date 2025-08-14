<!-- C:\xampp\htdocs\payment-gateway\app\Views\admin\pengambilan_uang_saku.php -->
<?= $this->extend('admin/templates/layout') ?>

<?= $this->section('title') ?>
<?= $title ?? 'Pengambilan Uang Saku' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid p-0">
    <h4 class="mb-4 fw-bold"><i class="bi bi-file-earmark-minus me-2"></i>Formulir Pengambilan Uang Saku</h4>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php $errors = session()->getFlashdata('errors'); ?>
    <?php if(!empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> Gagal menyimpan data:
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if(session()->getFlashdata('error') && empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>


    <div class="row">
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0 text-primary"><i class="bi bi-search me-2"></i>Cari Siswa</h5>
                </div>
                <div class="card-body">
                    <form id="searchSiswaForm" onsubmit="return false;">
                        <div class="position-relative">
                            <input style="font-size: 1rem;" type="text" id="searchSiswaInput" class="form-control form-control-lg" placeholder="Cari NIS atau Nama Siswa..." autocomplete="off">
                            <div class="position-absolute top-50 end-0 translate-middle-y pe-3">
                                <i class="bi bi-search text-muted"></i>
                            </div>
                            <ul class="list-group position-absolute w-100 mt-1" id="suggestionList" style="z-index: 1000; max-height: 200px; overflow-y: auto; display: none; border: 1px solid #dee2e6; border-top: none;"></ul>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card shadow-sm mt-4" id="displaySiswaCard" style="display: none;">
                <div class="card-header bg-info text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Data Siswa Terpilih</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">NIS</dt>
                        <dd class="col-sm-8 fw-bold" id="displayNisSiswa">-</dd>

                        <dt class="col-sm-4">Nama Siswa</dt>
                        <dd class="col-sm-8 fw-bold" id="displayNamaSiswa">-</dd>
                        
                        <dt class="col-sm-4">Kelas</dt>
                        <dd class="col-sm-8 fw-bold" id="displayKelasSiswa">-</dd>

                        <dt class="col-sm-4">Saldo Saat Ini</dt>
                        <dd class="col-sm-8 fw-bold text-success fs-5" id="displaySaldoSiswa">Rp -</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm" id="formPengambilanCard" style="display: none;">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0 text-primary"><i class="bi bi-pencil-square me-2"></i>Formulir Pengambilan</h5>
                </div>
                <div class="card-body p-4">
                    <form id="formPengambilanUangSaku" method="post" action="<?= site_url('admin/pengambilan_uang_saku/proses_pengambilan') ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="nis" id="inputNis" value="">
                        <input type="hidden" name="jumlah_diambil_numeric" id="jumlah_diambil_numeric_hidden">

                        <div class="mb-3">
                            <label for="jumlah_diambil_display" class="form-label fw-semibold">Jumlah Diambil</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">Rp</span>
                                <input type="text" style="font-size: 1rem;" class="form-control" id="jumlah_diambil_display" placeholder="0" required>
                            </div>
                            <div class="form-text">Masukkan nominal pengambilan. Saldo tidak boleh minus.</div>
                        </div>
                        <div class="mb-3">
                            <label for="catatan" class="form-label fw-semibold">Catatan/Keterangan</label>
                            <textarea class="form-control form-control-lg" style="font-size: 1rem;" id="catatan" name="catatan" rows="3" placeholder="Contoh: Untuk beli buku" required></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-save2-fill me-2"></i>Simpan Pengambilan
                            </button> 
                        </div>
                    </form>
                </div>
            </div>
            <div class="alert alert-info text-center" id="placeholderFormPengambilan" style="min-height: 300px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                <i class="bi bi-info-circle-fill fs-1 mb-3"></i>
                <h5 class="alert-heading">Pilih Siswa Terlebih Dahulu</h5>
                <p>Silakan cari dan pilih siswa untuk melanjutkan proses pengambilan uang saku.</p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- MODIFIKASI: Tambahkan SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ... (Logika autocomplete dan format rupiah tidak berubah)
    const siswaDataForAutocomplete = JSON.parse('<?= $siswaListJson ?? "[]" ?>');
    const preSelectedSiswaDataJson = <?= $selected_siswa_data_json ?? "'null'" ?>; 
    const searchInput = document.getElementById('searchSiswaInput');
    const suggestionList = document.getElementById('suggestionList');
    const displayCard = document.getElementById('displaySiswaCard');
    const displayNis = document.getElementById('displayNisSiswa');
    const displayNama = document.getElementById('displayNamaSiswa');
    const displayKelas = document.getElementById('displayKelasSiswa');
    const displaySaldo = document.getElementById('displaySaldoSiswa');
    const formPengambilanCard = document.getElementById('formPengambilanCard');
    const placeholderForm = document.getElementById('placeholderFormPengambilan');
    const inputNisForm = document.getElementById('inputNis');
    const jumlahDiambilDisplayInput = document.getElementById('jumlah_diambil_display');
    const jumlahDiambilHiddenInput = document.getElementById('jumlah_diambil_numeric_hidden');
    const catatanInput = document.getElementById('catatan');

    function formatRupiahDisplay(angkaStr) {
        if (angkaStr === null || angkaStr === undefined || angkaStr.toString().trim() === '') return 'Rp 0';
        let number_string = angkaStr.toString().replace(/[^,\d]/g, '');
        let rupiah = '';
        if(number_string.length > 0){
            let split = number_string.split(',');
            let sisa = split[0].length % 3;
            rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
            if(ribuan){
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        }
        return 'Rp ' + (rupiah === '' ? '0' : rupiah);
    }

    function formatRupiahInput(angkaStr) {
        if (angkaStr === null || angkaStr === undefined) return '';
        let number_string = angkaStr.toString().replace(/[^,\d]/g, '');
        if(number_string.length === 0) return '';
        let sisa = number_string.length % 3;
        let rupiah = number_string.substr(0, sisa);
        let ribuan = number_string.substr(sisa).match(/\d{3}/gi);
        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        return rupiah;
    }

    function parseRupiah(rupiahValue) {
        if (rupiahValue === null || rupiahValue === undefined) return 0;
        return parseInt(rupiahValue.toString().replace(/[^0-9]/g, ''), 10) || 0;
    }

    function selectSiswaAndPopulateForm(siswaItem) {
        if (!siswaItem) return;

        searchInput.value = `${siswaItem.nama} (${siswaItem.nis})`;
        
        displayNis.textContent = siswaItem.nis;
        displayNama.textContent = siswaItem.nama;
        displayKelas.textContent = siswaItem.kelas || '-';
        const saldoInt = parseInt(siswaItem.saldo) || 0;
        displaySaldo.textContent = formatRupiahDisplay(saldoInt.toString());
        
        inputNisForm.value = siswaItem.nis;

        if (jumlahDiambilDisplayInput) {
            jumlahDiambilDisplayInput.setAttribute('max', saldoInt);
            jumlahDiambilDisplayInput.placeholder = "Maks. " + formatRupiahDisplay(saldoInt.toString());
            jumlahDiambilDisplayInput.value = '';
        }
        if (jumlahDiambilHiddenInput) {
            jumlahDiambilHiddenInput.value = '';
        }
        if(catatanInput) catatanInput.value = '';
        
        displayCard.style.display = 'block';
        formPengambilanCard.style.display = 'block';
        placeholderForm.style.display = 'none';
        suggestionList.style.display = 'none';
        
        if(jumlahDiambilDisplayInput) {
            jumlahDiambilDisplayInput.focus();
        }
    }

    if (jumlahDiambilDisplayInput) {
        jumlahDiambilDisplayInput.addEventListener('input', function(e) {
            let numericValue = parseRupiah(this.value);
            const maxSaldo = parseInt(this.getAttribute('max')) || 0;
            if (numericValue > maxSaldo) {
                numericValue = maxSaldo;
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
            this.value = formatRupiahInput(numericValue.toString());
            if (jumlahDiambilHiddenInput) {
                jumlahDiambilHiddenInput.value = numericValue;
            }
        });
        jumlahDiambilDisplayInput.addEventListener('blur', function() {
            let numericValue = parseRupiah(this.value);
            const maxSaldo = parseInt(this.getAttribute('max')) || 0;
            if (numericValue > maxSaldo) {
                numericValue = maxSaldo;
            }
            this.value = formatRupiahInput(numericValue.toString());
            if (jumlahDiambilHiddenInput) {
                jumlahDiambilHiddenInput.value = numericValue;
            }
            if (this.value === "0" && numericValue === 0 && this.placeholder !== 'Maks. Rp 0') {
                this.value = "";
            }
        });
    }

    searchInput.addEventListener('input', function () {
        const keyword = this.value.toLowerCase().trim();
        suggestionList.innerHTML = '';
        if (keyword.length === 0) {
            suggestionList.style.display = 'none';
            return;
        }

        const results = siswaDataForAutocomplete.filter(s => 
            s.nama.toLowerCase().includes(keyword) || 
            s.nis.toLowerCase().includes(keyword)
        );

        if (results.length > 0) {
            results.forEach(item => {
                const li = document.createElement('li');
                li.className = 'list-group-item list-group-item-action suggestion-item py-2';
                li.innerHTML = `<div class="d-flex justify-content-between">
                                    <span><strong>${item.nama}</strong> (${item.nis}) - Kelas: ${item.kelas || '-'}</span>
                                    <small class="text-muted">Saldo: ${formatRupiahDisplay(item.saldo.toString())}</small>
                                </div>`;
                li.setAttribute('data-nis', item.nis);
                li.setAttribute('data-nama', item.nama);
                li.setAttribute('data-kelas', item.kelas || '-');
                li.setAttribute('data-saldo', item.saldo);
                suggestionList.appendChild(li);
            });
            suggestionList.style.display = 'block';
        } else {
            const li = document.createElement('li');
            li.className = 'list-group-item text-muted text-center py-2';
            li.textContent = 'Siswa tidak ditemukan...';
            suggestionList.appendChild(li);
            suggestionList.style.display = 'block';
        }
    });

    suggestionList.addEventListener('click', function (e) {
        const targetLi = e.target.closest('li.suggestion-item');
        if (targetLi) {
            const siswaItem = {
                nis: targetLi.getAttribute('data-nis'),
                nama: targetLi.getAttribute('data-nama'),
                kelas: targetLi.getAttribute('data-kelas'),
                saldo: targetLi.getAttribute('data-saldo')
            };
            selectSiswaAndPopulateForm(siswaItem);
        }
    });
    
    if (preSelectedSiswaDataJson && preSelectedSiswaDataJson !== 'null') {
        const siswaTerpilih = (typeof preSelectedSiswaDataJson === 'string') ? JSON.parse(preSelectedSiswaDataJson) : preSelectedSiswaDataJson;
        selectSiswaAndPopulateForm(siswaTerpilih);
    }

    document.addEventListener('click', function (e) {
        if (!e.target.closest('#searchSiswaForm') && !e.target.closest('#suggestionList')) {
            suggestionList.style.display = 'none';
        }
    });

        // MODIFIKASI: Tambahkan event listener untuk konfirmasi submit form
    const formPengambilan = document.getElementById('formPengambilanUangSaku');
    if (formPengambilan) {
        formPengambilan.addEventListener('submit', function(event) {
            event.preventDefault(); // Hentikan submit otomatis

            const namaSiswa = document.getElementById('displayNamaSiswa').textContent;
            const jumlahDisplay = document.getElementById('jumlah_diambil_display').value;
            const catatan = document.getElementById('catatan').value;

            if (!jumlahDisplay || parseRupiah(jumlahDisplay) <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Input Tidak Valid',
                    text: 'Jumlah pengambilan harus diisi dan lebih besar dari nol.',
                });
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Pengambilan',
                html: `Anda akan mengambil uang saku untuk siswa:
                       <ul class="list-group list-group-flush text-start mt-3">
                         <li class="list-group-item"><b>Nama:</b> ${namaSiswa}</li>
                         <li class="list-group-item"><b>Jumlah:</b> Rp ${jumlahDisplay}</li>
                         <li class="list-group-item"><b>Catatan:</b> ${catatan}</li>
                       </ul>
                       <p class="mt-3">Pastikan data sudah benar. Lanjutkan?</p>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika dikonfirmasi, submit form secara manual
                    formPengambilan.submit();
                }
            });
        });
    }
});
</script>
<?= $this->endSection() ?>
