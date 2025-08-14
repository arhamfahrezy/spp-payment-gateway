<?= $this->extend('admin/templates/layout') ?>

<?= $this->section('title') ?>
Manajemen Data Siswa
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid p-0">
    <h4 class="mb-4 fw-bold"><i class="bi bi-people-fill me-2"></i>Manajemen Data Siswa</h4>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card h-100 py-2 shadow border-start border-primary border-4">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total Semua Siswa</div>
                            <div class="h4 mb-0 fw-bold text-gray-800"><?= esc($jumlahSiswa ?? 0) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people-fill fs-1 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card h-100 py-2 shadow border-start border-info border-4">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">Jumlah Siswa Laki-Laki</div>
                            <div class="h4 mb-0 fw-bold text-gray-800"><?= esc($jumlahLaki ?? 0) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-gender-male fs-1 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card h-100 py-2 shadow border-start custom-border-pink border-5">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold custom-text-pink text-uppercase mb-1">Jumlah Siswi Perempuan</div>
                            <div class="h4 mb-0 fw-bold text-gray-800"><?= esc($jumlahPerempuan ?? 0) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-gender-female fs-1 custom-text-pink"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <a href="<?= site_url('admin/tambah_siswa') ?>" class="btn btn-primary btn-m shadow-sm">
            <i class="bi bi-person-plus-fill me-1"></i> Tambah Siswa Baru
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light p-3 p-md-4">
            <h5 class="mb-3 fw-semibold"><i class="bi bi-filter-circle me-2"></i>Filter Data Siswa</h5>
            <form method="get" action="<?= site_url('admin/data_siswa') ?>" class="row g-3 align-items-center">
                <div class="col-md-3">
                    <label for="kelas" class="form-label">Kelas</label>
                    <!-- MODIFIKASI: Mengubah input teks menjadi dropdown -->
                    <select class="form-select form-select-sm" id="kelas" name="kelas">
                        <option value="">Semua Kelas</option>
                        <option value="7" <?= (isset($filter['kelas']) && $filter['kelas'] === '7') ? 'selected' : '' ?>>Kelas 7</option>
                        <option value="8" <?= (isset($filter['kelas']) && $filter['kelas'] === '8') ? 'selected' : '' ?>>Kelas 8</option>
                        <option value="9" <?= (isset($filter['kelas']) && $filter['kelas'] === '9') ? 'selected' : '' ?>>Kelas 9</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                    <select class="form-select form-select-sm" id="jenis_kelamin" name="jenis_kelamin">
                        <option value="" <?= empty($filter['jenis_kelamin']) ? 'selected' : '' ?>>Semua Jenis Kelamin</option>
                        <option value="Laki-laki" <?= (isset($filter['jenis_kelamin']) && $filter['jenis_kelamin'] === 'Laki-laki') ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="Perempuan" <?= (isset($filter['jenis_kelamin']) && $filter['jenis_kelamin'] === 'Perempuan') ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="search" class="form-label">Cari (NIS, Nama, Ortu)</label>
                    <input type="text" class="form-control form-control-sm" id="search" name="search" placeholder="Ketik NIS, nama siswa, atau nama ortu..." value="<?= esc($filter['search'] ?? '') ?>">
                </div>
                <div class="col-md-2 d-flex align-self-end">
                    <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover table-sm align-middle" id="siswaTable">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="text-center">NIS</th>
                            <th scope="col">Nama Siswa</th>
                            <th scope="col" class="text-center">Kelas</th>
                            <th scope="col">Nama Ayah</th>
                            <th scope="col">Nama Ibu</th>
                            <th scope="col">Telepon Orang Tua</th>
                            <th scope="col" class="text-center">Jenis Kelamin</th>
                            <th scope="col" class="text-center" style="width: 12%;">Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($siswaList)): ?>
                        <?php foreach ($siswaList as $siswa): ?>
                        <tr>
                            <td class="text-center"><?= esc($siswa['nis']) ?></td>
                            <td><?= esc($siswa['nama_siswa']) ?></td>
                            <td class="text-center"><?= esc($siswa['kelas']) ?></td>
                            <td><?= esc($siswa['nama_ayah'] ?? '-') ?></td>
                            <td><?= esc($siswa['nama_ibu'] ?? '-') ?></td>
                            <td><?= esc($siswa['telepon_orangtua'] ?? '-') ?></td>
                            <td class="text-center">
                                <?php if ($siswa['jenis_kelamin'] == 'Laki-laki'): ?>
                                    <span class="badge bg-info-soft text-info"><i class="bi bi-gender-male me-1"></i>Laki-laki</span>
                                <?php elseif ($siswa['jenis_kelamin'] == 'Perempuan'): ?>
                                    <span class="badge custom-bg-pink-soft custom-text-pink"><i class="bi bi-gender-female me-1"></i>Perempuan</span>
                                <?php else: ?>
                                    <?= esc($siswa['jenis_kelamin']) ?>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="<?= site_url('admin/tambah_siswa/edit/'.$siswa['nis']) ?>" class="btn btn-warning btn-sm me-1" title="Edit Data Siswa">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <a href="<?= site_url('admin/data_siswa/hapus/' . $siswa['nis']) ?>" 
                                class="btn btn-danger btn-sm btn-delete" data-nama-siswa="<?= esc($siswa['nama_siswa']) ?>" title="Hapus Data Siswa">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center p-5">
                                <i class="bi bi-info-circle fs-3 text-muted"></i><br>
                                Tidak ada data siswa yang cocok dengan filter.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>


<?= $this->section('styles') ?>
<style>
    .text-xs { font-size: .75rem !important; }
    .text-gray-800 { color: #5a5c69 !important; }
    .border-start.border-4 { border-left-width: 0.25rem !important; }
    .custom-border-pink { border-color: #e83e8c !important; }
    .custom-text-pink { color: #e83e8c !important; }
    .custom-bg-pink-soft { background-color: rgba(232, 62, 140, 0.1) !important; }
    .bg-info-soft { background-color: rgba(13, 202, 240, 0.1); }
    .text-info { color: #0dcaf0 !important; }
    .table th, .table td { vertical-align: middle; }
    .card-header h5 { font-size: 1.1rem; }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.btn-delete');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();

            const deleteUrl = this.href;
            const namaSiswa = this.dataset.namaSiswa;
            
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: `Data siswa dengan nama "${namaSiswa}" akan dihapus secara permanen!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = deleteUrl;
                }
            });
        });
    });
});
</script>
<?= $this->endSection() ?>
