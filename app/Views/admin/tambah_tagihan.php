<!-- C:\xampp\htdocs\payment-gateway\app\Views\admin\tambah_tagihan.php -->
<?= $this->extend('admin/templates/layout') ?>

<?= $this->section('title') ?>
<?= $title ?? 'Tambah Tagihan SPP Baru' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid p-0">
    <h4 class="mb-4 fw-bold"><i class="bi bi-journal-plus me-2"></i><?= $title ?? 'Tambah Tagihan SPP' ?></h4>
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php $errors = session()->getFlashdata('errors') ?? []; ?>
    <?php if(!empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> Mohon periksa kembali input Anda:
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header bg-light py-3">
            <h5 class="mb-0 text-primary"><i class="bi bi-pencil-square me-2"></i>Formulir Tagihan</h5>
        </div>
        <div class="card-body p-4">
            <form action="<?= isset($tagihan) ? site_url('admin/tambah_tagihan/update/'.$tagihan['id']) : site_url('admin/tambah_tagihan/simpan') ?>" method="post" id="formTambahTagihan">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="nama_tagihan" class="form-label fw-semibold">Nama Tagihan</label>
                    <input type="text"
                           style="font-size: 17px;"
                           class="form-control form-control-lg <?= isset($errors['nama_tagihan']) ? 'is-invalid' : '' ?>" 
                           id="nama_tagihan" 
                           name="nama_tagihan" 
                           value="<?= old('nama_tagihan', $tagihan['nama_tagihan'] ?? '') ?>"
                           placeholder="Contoh: SPP Juli 2025 Kelas 7"
                           required
                           onblur="formatTitleCase(this)">
                    <?php if (isset($errors['nama_tagihan'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['nama_tagihan']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="kelas" class="form-label fw-semibold">Untuk Kelas</label>
                        <select style="font-size: 17px;" class="form-select form-select-lg <?= isset($errors['kelas']) ? 'is-invalid' : '' ?>" id="kelas" name="kelas" required>
                            <option value="" disabled <?= old('kelas', $tagihan['kelas'] ?? '') ? '' : 'selected' ?>>-- Pilih Kelas --</option>
                            <?php for ($i = 7; $i <= 9; $i++): ?>
                                <option value="<?= $i ?>" <?= old('kelas', $tagihan['kelas'] ?? '') == $i ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                        <?php if (isset($errors['kelas'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['kelas']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="jatuh_tempo" class="form-label fw-semibold">Tanggal Jatuh Tempo</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                            <input type="date" 
                                   style="font-size: 17px;"
                                   class="form-control <?= isset($errors['jatuh_tempo']) ? 'is-invalid' : '' ?>" 
                                   id="jatuh_tempo" 
                                   name="jatuh_tempo" 
                                   value="<?= old('jatuh_tempo', $tagihan['jatuh_tempo'] ?? date('Y-m-d')) ?>" 
                                   required>
                        </div>
                        <?php if (isset($errors['jatuh_tempo'])): ?>
                            <div class="invalid-feedback d-block"><?= esc($errors['jatuh_tempo']) ?></div> 
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="nominal" class="form-label fw-semibold">Nominal Tagihan</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text" style="font-size: 17px;">Rp</span>
                        <input type="text" 
                               style="font-size: 17px;"
                               class="form-control <?= isset($errors['nominal']) ? 'is-invalid' : '' ?>" 
                               id="nominal" 
                               name="nominal" 
                               value="<?= old('nominal', $tagihan['nominal'] ?? '') ?>" 
                               placeholder="0"
                               required
                               oninput="formatRupiah(this)"
                               style="text-align: right;">
                    </div>
                    <div class="form-text">Masukkan nominal tanpa titik atau koma. Akan terformat otomatis.</div>
                    <?php if (isset($errors['nominal'])): ?>
                        <div class="invalid-feedback d-block"><?= esc($errors['nominal']) ?></div> 
                    <?php endif; ?>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi <?= isset($tagihan) ? 'bi-check-circle-fill' : 'bi-file-plus' ?> me-2"></i>
                        <?= isset($tagihan) ? 'Update Tagihan' : 'Simpan Tagihan' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- MODIFIKASI: Tambahkan SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function formatRupiah(input) {
    let value = input.value.replace(/[^,\d]/g, '').toString();
    if (value.length > 1 && value.startsWith('0') && !value.startsWith('0,')) {
        value = value.substring(1);
    }
    
    let split = value.split(',');
    let sisa = split[0].length % 3;
    let rupiah = split[0].substr(0, sisa);
    let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    rupiah = split[1] !== undefined ? rupiah + ',' + split[1].substring(0,2) : rupiah;
    input.value = rupiah;
}

function formatTitleCase(input) {
    let value = input.value.toLowerCase().trim();
    if (!value) return;
    input.value = value.replace(/\b(\w)/g, s => s.toUpperCase());
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formTambahTagihan');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Mencegah form submit secara default
            e.preventDefault(); 
            // --- MODIFIKASI DIMULAI: Logika konfirmasi yang lebih cerdas ---
            const isEditMode = <?= isset($tagihan) ? 'true' : 'false' ?>;
            const originalNominal = <?= isset($tagihan) ? (int)$tagihan['nominal'] : 'null' ?>;
            
            const namaTagihan = document.getElementById('nama_tagihan').value;
            const kelas = document.getElementById('kelas').value;
            const nominalInput = document.getElementById('nominal');
            const nominalDisplay = nominalInput.value; 
            
            // Hapus format rupiah sebelum submit
            const nominalClean = parseInt(nominalDisplay.replace(/\D/g, ''), 10);
            
            let pesanPeringatan = `<p class="mt-3">Tagihan ini akan diterapkan ke semua siswa di kelas ${kelas}. Lanjutkan?</p>`;
            
            // Tambahkan peringatan khusus jika nominal diubah saat mode edit
            if (isEditMode && originalNominal !== nominalClean) {
                pesanPeringatan = `<div class="alert alert-warning mt-3" role="alert">
                                    <h5 class="alert-heading"><i class="bi bi-exclamation-triangle-fill"></i> Peringatan!</h5>
                                    <p>Anda mengubah nominal tagihan. Tindakan ini akan **mengevaluasi ulang status semua siswa** terkait. Siswa yang sudah lunas bisa kembali menjadi 'Belum Lunas'.</p>
                                   </div>` + pesanPeringatan;
            }

            const actionText = isEditMode ? 'memperbarui' : 'membuat';
            const buttonText = isEditMode ? 'Ya, Update!' : 'Ya, Simpan!';
            
            Swal.fire({
                title: 'Konfirmasi Tagihan',
                html: `Anda akan ${actionText} tagihan dengan detail berikut:
                       <ul class="list-group list-group-flush text-start mt-3">
                         <li class="list-group-item"><b>Nama:</b> ${namaTagihan}</li>
                         <li class="list-group-item"><b>Kelas:</b> ${kelas}</li>
                         <li class="list-group-item"><b>Nominal:</b> Rp ${nominalDisplay}</li>
                       </ul>
                       ${pesanPeringatan}`, // Masukkan pesan peringatan di sini
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: buttonText,
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    nominalInput.value = nominalClean;
                    form.submit();
                }
            });
        });
    }
});
</script>
<?= $this->endSection() ?>
