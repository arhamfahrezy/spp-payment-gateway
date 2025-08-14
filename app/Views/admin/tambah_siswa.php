<!-- C:\xampp\htdocs\payment-gateway\app\Views\admin\tambah_siswa.php -->
<?= $this->extend('admin/templates/layout') ?>

<?= $this->section('title') ?>
<?= isset($siswa) ? 'Edit Data Siswa' : 'Tambah Siswa Baru' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid p-0">
    <h4 class="mb-4 fw-bold">
        <i class="bi <?= isset($siswa) ? 'bi-pencil-square' : 'bi-person-plus-fill' ?> me-2"></i>
        <?= isset($siswa) ? 'Edit Data Siswa' : 'Tambah Siswa Baru' ?>
    </h4>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php $errors = session()->getFlashdata('errors') ?? []; ?>
    <?php if(!empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            Mohon periksa kembali input Anda:
            <ul class="mb-0 mt-2">
                <?php foreach ($errors as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header bg-light p-0">
            <ul class="nav nav-tabs nav-fill" id="tabForm" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active p-3" id="tab1-tab" data-bs-toggle="tab" data-bs-target="#tab1" type="button" role="tab" aria-controls="tab1" aria-selected="true">
                        <i class="bi bi-person-vcard me-2"></i>Biodata Siswa
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link p-3" id="tab2-tab" data-bs-toggle="tab" data-bs-target="#tab2" type="button" role="tab" aria-controls="tab2" aria-selected="false">
                        <i class="bi bi-people me-2"></i>Data Orang Tua & Akun
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link p-3" id="tab3-tab" data-bs-toggle="tab" data-bs-target="#tab3" type="button" role="tab" aria-controls="tab3" aria-selected="false">
                        <i class="bi bi-geo-alt-fill me-2"></i>Data Alamat
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body p-4">
            <form action="<?= isset($siswa) ? site_url('admin/tambah_siswa/update/'.$siswa['nis']) : site_url('admin/tambah_siswa/simpan') ?>" method="post">
                <?= csrf_field() ?>
                <div class="tab-content" id="tabContent">
                    <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
                        <h5 class="mb-3 fw-semibold">Lengkapi Biodata Siswa</h5>
                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <label for="nis" class="form-label">NIS (Nomor Induk Siswa)</label>
                                <input type="text" style="font-size: 17px;" class="form-control form-control-lg <?= isset($errors['nis']) ? 'is-invalid' : '' ?>" id="nis" name="nis" 
                                    value="<?= old('nis', $siswa['nis'] ?? '') ?>" 
                                    <?= isset($siswa) ? 'readonly' : '' ?> placeholder="Contoh: 2025001" required
                                    maxlength="20" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                <?php if (isset($errors['nis'])): ?><div class="invalid-feedback"><?= esc($errors['nis']) ?></div><?php endif; ?>
                            </div>
                            <div class="col-md-7 mb-3">
                                <label for="nama_siswa" class="form-label">Nama Lengkap Siswa</label>
                                <input type="text" style="font-size: 17px;" class="form-control form-control-lg <?= isset($errors['nama_siswa']) ? 'is-invalid' : '' ?>" id="nama_siswa" name="nama_siswa" 
                                    value="<?= old('nama_siswa', $siswa['nama_siswa'] ?? '') ?>" placeholder="Masukkan nama lengkap" required
                                    pattern="[a-zA-Z\s]+" oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')">
                                <?php if (isset($errors['nama_siswa'])): ?><div class="invalid-feedback"><?= esc($errors['nama_siswa']) ?></div><?php endif; ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <label for="kelas" class="form-label">Kelas</label>
                                <select style="font-size: 17px;" class="form-select form-select-lg <?= isset($errors['kelas']) ? 'is-invalid' : '' ?>" id="kelas" name="kelas" required>
                                    <option value="" disabled <?= !old('kelas', $siswa['kelas'] ?? '') ? 'selected' : '' ?>>-- Pilih Kelas --</option>
                                    <?php foreach ([7, 8, 9] as $kelas_option): ?>
                                        <option value="<?= $kelas_option ?>" <?= old('kelas', $siswa['kelas'] ?? '') == $kelas_option ? 'selected' : '' ?>><?= $kelas_option ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['kelas'])): ?><div class="invalid-feedback"><?= esc($errors['kelas']) ?></div><?php endif; ?>
                            </div>
                            <div class="col-md-7 mb-3">
                                <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                                <input type="date" style="font-size: 17px;" class="form-control form-control-lg <?= isset($errors['tanggal_lahir']) ? 'is-invalid' : '' ?>" id="tanggal_lahir" name="tanggal_lahir" 
                                        value="<?= old('tanggal_lahir', $siswa['tanggal_lahir'] ?? '') ?>" required>
                                <?php if (isset($errors['tanggal_lahir'])): ?><div class="invalid-feedback d-block"><?= esc($errors['tanggal_lahir']) ?></div><?php endif; ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                            <select style="font-size: 17px;" class="form-select form-select-lg <?= isset($errors['jenis_kelamin']) ? 'is-invalid' : '' ?>" id="jenis_kelamin" name="jenis_kelamin" required>
                                <option value="" disabled <?= !old('jenis_kelamin', $siswa['jenis_kelamin'] ?? '') ? 'selected' : '' ?>>-- Pilih Jenis Kelamin --</option>
                                <option value="Laki-laki" <?= old('jenis_kelamin', $siswa['jenis_kelamin'] ?? '') === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="Perempuan" <?= old('jenis_kelamin', $siswa['jenis_kelamin'] ?? '') === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                            <?php if (isset($errors['jenis_kelamin'])): ?><div class="invalid-feedback"><?= esc($errors['jenis_kelamin']) ?></div><?php endif; ?>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-primary btn-lg" id="nextToTab2">Selanjutnya <i class="bi bi-arrow-right"></i></button>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                        <h5 class="mb-3 fw-semibold">Lengkapi Data Orang Tua & Akun Wali Murid</h5>
                        <p class="text-muted small">Pilih salah satu orang tua yang akan dijadikan sebagai wali murid.</p>
                        
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-8">
                                <label for="nama_ayah" class="form-label">Nama Ayah</label>
                                
                                <input type="text" style="font-size: 17px;" class="form-control form-control-lg <?= isset($errors['nama_ayah']) ? 'is-invalid' : '' ?>" id="nama_ayah" name="nama_ayah" 
                                        value="<?= old('nama_ayah', $siswa['nama_ayah'] ?? '') ?>" placeholder="Nama lengkap ayah" required
                                        pattern="[a-zA-Z\s]+" oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')">
                                <?php if (isset($errors['nama_ayah'])): ?><div class="invalid-feedback"><?= esc($errors['nama_ayah']) ?></div><?php endif; ?>
                            
                            </div>
                            <div class="col-md-4 pt-md-4">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="radio" name="wali_murid_pilihan" id="wali_ayah" value="ayah" 
                                            <?= old('wali_murid_pilihan', $wali_murid ?? '') == 'ayah' ? 'checked' : '' ?> required>
                                    <label class="form-check-label" for="wali_ayah">Jadikan Wali Murid</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <div class="col-md-8">
                                <label for="nama_ibu" class="form-label">Nama Ibu</label>
                                <input type="text" style="font-size: 17px;" class="form-control form-control-lg <?= isset($errors['nama_ibu']) ? 'is-invalid' : '' ?>" id="nama_ibu" name="nama_ibu" 
                                        value="<?= old('nama_ibu', $siswa['nama_ibu'] ?? '') ?>" placeholder="Nama lengkap ibu" required
                                        pattern="[a-zA-Z\s]+" oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')">
                                <?php if (isset($errors['nama_ibu'])): ?><div class="invalid-feedback"><?= esc($errors['nama_ibu']) ?></div><?php endif; ?>
                            </div>
                            <div class="col-md-4 pt-md-4">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="radio" name="wali_murid_pilihan" id="wali_ibu" value="ibu"
                                            <?= old('wali_murid_pilihan', $wali_murid ?? '') == 'ibu' ? 'checked' : '' ?> required>
                                    <label class="form-check-label" for="wali_ibu">Jadikan Wali Murid</label>
                                </div>
                            </div>
                        </div>
                        <?php if (isset($errors['wali_murid_pilihan'])): ?><div class="text-danger small mb-2"><?= esc($errors['wali_murid_pilihan']) ?></div><?php endif; ?>

                        <hr class="my-4">
                        <h6 class="mb-3 text-primary">Detail Akun Wali Murid (Untuk Login)</h6>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email_orangtua" class="form-label">Email Wali Murid</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                    <input type="email" style="font-size: 17px;" class="form-control <?= isset($errors['email_orangtua']) ? 'is-invalid' : '' ?>" id="email_orangtua" name="email_orangtua" 
                                            value="<?= old('email_orangtua', $email_orangtua ?? '') ?>" placeholder="email@example.com" required>
                                </div>
                                <?php if (isset($errors['email_orangtua'])): ?><div class="invalid-feedback d-block"><?= esc($errors['email_orangtua']) ?></div><?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="telepon_orangtua" class="form-label">Nomor Telepon Wali Murid</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                                    <input type="text" style="font-size: 17px;" class="form-control <?= isset($errors['telepon_orangtua']) ? 'is-invalid' : '' ?>" id="telepon_orangtua" name="telepon_orangtua" 
                                            value="<?= old('telepon_orangtua', $siswa['telepon_orangtua'] ?? '') ?>" placeholder="08xxxxxxxxxx"
                                            maxlength="15" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                </div>
                                <?php if (isset($errors['telepon_orangtua'])): ?><div class="invalid-feedback d-block"><?= esc($errors['telepon_orangtua']) ?></div><?php endif; ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password_orangtua" class="form-label">Password Akun Wali Murid</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                                <input type="password" style="font-size: 17px;" class="form-control <?= isset($errors['password_orangtua']) ? 'is-invalid' : '' ?>" id="password_orangtua" name="password_orangtua" 
                                        placeholder="<?= isset($siswa) ? 'Kosongkan jika tidak ingin mengubah password' : 'Masukkan password' ?>"
                                        <?= !isset($siswa) ? 'required' : '' ?>>
                            </div>
                            <div class="form-text"><?= isset($siswa) ? 'Isi hanya jika ingin mengubah password.' : '' ?></div>
                            <?php if (isset($errors['password_orangtua'])): ?><div class="invalid-feedback d-block"><?= esc($errors['password_orangtua']) ?></div><?php endif; ?>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary btn-lg" id="prevToTab1"><i class="bi bi-arrow-left"></i> Kembali</button>
                            <button type="button" class="btn btn-primary btn-lg" id="nextToTab3">Selanjutnya <i class="bi bi-arrow-right"></i></button>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab3" role="tabpanel" aria-labelledby="tab3-tab">
                        <h5 class="mb-3 fw-semibold">Lengkapi Data Alamat Siswa</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="provinsi" class="form-label">Provinsi</label>
                                <input type="text" style="font-size: 17px;" class="form-control form-control-lg <?= isset($errors['provinsi']) ? 'is-invalid' : '' ?>" id="provinsi" name="provinsi" 
                                    value="<?= old('provinsi', $siswa['provinsi'] ?? '') ?>" placeholder="Contoh: Jawa Timur" required
                                    pattern="[a-zA-Z\s]+" oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')">
                                <?php if (isset($errors['provinsi'])): ?><div class="invalid-feedback"><?= esc($errors['provinsi']) ?></div><?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="kota" class="form-label">Kota/Kabupaten</label>
                                <input type="text" style="font-size: 17px;" class="form-control form-control-lg <?= isset($errors['kota']) ? 'is-invalid' : '' ?>" id="kota" name="kota" 
                                    value="<?= old('kota', $siswa['kota'] ?? '') ?>" placeholder="Contoh: Malang" required
                                    pattern="[a-zA-Z\s]+" oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')">
                                <?php if (isset($errors['kota'])): ?><div class="invalid-feedback"><?= esc($errors['kota']) ?></div><?php endif; ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="kecamatan" class="form-label">Kecamatan</label>
                                <input type="text" style="font-size: 17px;" class="form-control form-control-lg <?= isset($errors['kecamatan']) ? 'is-invalid' : '' ?>" id="kecamatan" name="kecamatan" 
                                    value="<?= old('kecamatan', $siswa['kecamatan'] ?? '') ?>" placeholder="Contoh: Sukun" required
                                    pattern="[a-zA-Z\s]+" oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')">
                                <?php if (isset($errors['kecamatan'])): ?><div class="invalid-feedback"><?= esc($errors['kecamatan']) ?></div><?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="kelurahan" class="form-label">Kelurahan/Desa</label>
                                <input type="text" style="font-size: 17px;" class="form-control form-control-lg <?= isset($errors['kelurahan']) ? 'is-invalid' : '' ?>" id="kelurahan" name="kelurahan" 
                                    value="<?= old('kelurahan', $siswa['kelurahan'] ?? '') ?>" placeholder="Contoh: Karangbesuki" required
                                    pattern="[a-zA-Z\s]+" oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')">
                                <?php if (isset($errors['kelurahan'])): ?><div class="invalid-feedback"><?= esc($errors['kelurahan']) ?></div><?php endif; ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="alamat_detail" class="form-label">Alamat Lengkap (Nama Jalan, No Rumah, RT/RW)</label>
                            <textarea style="font-size: 17px;" class="form-control form-control-lg <?= isset($errors['alamat_detail']) ? 'is-invalid' : '' ?>" id="alamat_detail" name="alamat_detail" rows="3" required placeholder="Contoh: Jl. Raya  No. 1, RT 01 RW 02, Karangbesuki, Sukun."><?= old('alamat_detail', $siswa['alamat_detail'] ?? '') ?></textarea>
                            <?php if (isset($errors['alamat_detail'])): ?><div class="invalid-feedback"><?= esc($errors['alamat_detail']) ?></div><?php endif; ?>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary btn-lg" id="prevToTab2"><i class="bi bi-arrow-left"></i> Kembali</button>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi <?= isset($siswa) ? 'bi-check-circle-fill' : 'bi-person-check-fill' ?> me-2"></i>
                                <?= isset($siswa) ? 'Update Data Siswa' : 'Simpan Data Siswa' ?>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    function validateTab(tabId) {
        const currentTabPane = document.getElementById(tabId);
        if (!currentTabPane) return true;

        const inputs = currentTabPane.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;
        for (let input of inputs) {
            if (!input.checkValidity()) {
                input.classList.add('is-invalid');
                let feedback = input.nextElementSibling;
                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.style.display = 'block';
                    feedback.textContent = input.validationMessage;
                }
                if (isValid) input.focus();
                isValid = false;
            } else {
                input.classList.remove('is-invalid');
            }
        }
        return isValid;
    }
    
    document.querySelectorAll('.tab-pane input, .tab-pane select, .tab-pane textarea').forEach(input => {
        input.addEventListener('input', function() {
            if (this.checkValidity()) {
                this.classList.remove('is-invalid');
            }
        });
    });

    const tabTriggers = {
        'nextToTab2': { current: 'tab1', next: 'tab2-tab' },
        'nextToTab3': { current: 'tab2', next: 'tab3-tab' },
        'prevToTab1': { prev: 'tab1-tab' },
        'prevToTab2': { prev: 'tab2-tab' }
    };

    for (const btnId in tabTriggers) {
        const button = document.getElementById(btnId);
        if (button) {
            button.addEventListener('click', function() {
                const config = tabTriggers[btnId];
                if (config.next) {
                    if (validateTab(config.current)) {
                        const nextTab = new bootstrap.Tab(document.getElementById(config.next));
                        nextTab.show();
                    } else {
                        // MODIFIKASI: Ganti alert dengan SweetAlert2
                        Swal.fire({
                            icon: 'error',
                            title: 'Input Tidak Lengkap',
                            text: 'Mohon lengkapi semua field yang wajib diisi pada tab saat ini sebelum melanjutkan.',
                        });
                    }
                } else if (config.prev) {
                    const prevTab = new bootstrap.Tab(document.getElementById(config.prev));
                    prevTab.show();
                }
            });
        }
    }

    const mainForm = document.querySelector('form');
    if (mainForm) {
        mainForm.addEventListener('submit', function (e) {
            if (!validateTab('tab1') || !validateTab('tab2') || !validateTab('tab3')) {
                e.preventDefault();
                // MODIFIKASI: Ganti alert dengan SweetAlert2
                Swal.fire({
                    icon: 'error',
                    title: 'Form Belum Lengkap',
                    text: 'Pastikan semua data di setiap tab telah diisi dengan benar dan lengkap sebelum menyimpan.',
                });
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
