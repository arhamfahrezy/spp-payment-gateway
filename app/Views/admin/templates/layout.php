<?php
    // C:\xampp\htdocs\payment-gateway\app\Views\admin\templates\layout.php
    $uri = service('uri');
    $current_segment = $uri->getSegment(2) ?? 'dashboard'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $this->renderSection('title') ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-width-collapsed: 70px;
            --sidebar-bg: #0F4258;
            --sidebar-active-bg: #0B5A7D;
            --sidebar-hover-bg: rgba(255, 255, 255, 0.08);
            --sidebar-active-border: #34aadc;
            --dropdown-bg: #0B5A7D;
        }
        body { background-color: #f8f9fa; }
        .sidebar { width: var(--sidebar-width); background-color: var(--sidebar-bg); transition: width 0.3s ease; overflow-x: hidden; }
        .sidebar.collapsed { width: var(--sidebar-width-collapsed); }
        .sidebar .nav-link { display: flex; align-items: center; border-left: 4px solid transparent; padding-top: 0.75rem; padding-bottom: 0.75rem; transition: background-color 0.2s ease, border-left-color 0.2s ease; }
        .sidebar .nav-link:not(.active):hover { background-color: var(--sidebar-hover-bg); }
        .sidebar .nav-link.active { background-color: var(--sidebar-active-bg); border-left-color: var(--sidebar-active-border); font-weight: 600; }
        .sidebar .nav-link .bi {
        font-size: 1.25rem; /* Ukuran ikon diseragamkan */
        min-width: 30px; /* Beri lebar minimum agar teks tidak bergeser */
        text-align: center; /* Pastikan ikon di tengah area-nya */
    }
        .sidebar .nav-link .text-label,
        .sidebar .sidebar-brand-text { 
            white-space: nowrap;
            display: inline-block; 
            transition: opacity 0.2s ease, visibility 0.2s ease; 
            opacity: 1; 
            visibility: visible; }
        .sidebar.collapsed .nav-link .text-label,
        .sidebar.collapsed .sidebar-brand-text { opacity: 0; visibility: hidden; width: 0; overflow: hidden; }
        .sidebar.collapsed .nav-link { justify-content: center; padding-left: 0; padding-right: 0; }
        .sidebar.collapsed .nav-link .bi { font-size: 1.5rem; margin-right: 0 !important;  }
        .main-content { margin-left: var(--sidebar-width); width: calc(100% - var(--sidebar-width)); transition: margin-left 0.3s ease, width 0.3s ease; }
        .main-content.collapsed { margin-left: var(--sidebar-width-collapsed); width: calc(100% - var(--sidebar-width-collapsed)); }
        .top-navbar { box-shadow: 0 2px 4px rgba(0,0,0,.08); }
        .toggle-btn { font-size: 24px; cursor: pointer; }
        .navbar .dropdown-toggle::after { display: none; }
        
        /* MODIFIKASI: Style untuk dropdown di sidebar */
        .sidebar .dropdown-menu { background-color: var(--dropdown-bg); border: none; }
        .sidebar .dropdown-item { color: #fff; padding-top: 0.6rem; padding-bottom: 0.6rem; padding-left: 3.5rem;  }
        .sidebar .dropdown-item:hover { background-color: var(--sidebar-hover-bg); }
        .sidebar .dropdown-toggle::after { display: inline-block; margin-left: auto; transition: transform 0.3s ease; }
        .sidebar .dropdown-toggle[aria-expanded="true"]::after { transform: rotate(90deg); }
        .sidebar.collapsed .dropdown-toggle::after { display: none; /* Sembunyikan panah dropdown saat diciutkan */}
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body>

<div class="d-flex">
    <div id="sidebar" class="text-white min-vh-100 sidebar position-fixed d-flex flex-column p-2">
        
        <a href="<?= site_url('admin/dashboard') ?>" class="p-2 d-flex align-items-center gap-2 text-white text-decoration-none sidebar-brand">
            <img src="<?= base_url('pondok_kecil_logo.png') ?>" alt="Logo" style="width: 40px; height: 40px;" />
            <div class="sidebar-brand-text flex-grow-1">
                <div class="fw-bold fs-10">Ponpes <br> Darul Quran</div>
            </div>
        </a>
        <hr>

        <ul class="nav flex-column px-1 mt-2">
            <li class="nav-item mb-1"><a class="nav-link text-white <?= ($current_segment == 'dashboard') ? 'active' : '' ?>" href="<?= site_url('admin/dashboard') ?>"><i class="bi bi-speedometer2 me-3"></i><span class="text-label">Dashboard</span></a></li>
            
            <li class="nav-item mb-1 dropdown">
                <a class="nav-link text-white dropdown-toggle <?= in_array($current_segment, ['tambah_tagihan', 'bayar_tagihan']) ? 'active' : '' ?>" href="#" role="button" data-bs-toggle="collapse" data-bs-target="#tagihan-submenu" aria-expanded="false" aria-controls="tagihan-submenu">
                    <i class="bi bi-wallet2 me-3"></i>
                    <span class="text-label">Tagihan</span>
                </a>
                <div class="collapse <?= in_array($current_segment, ['tambah_tagihan', 'bayar_tagihan']) ? 'show' : '' ?>" id="tagihan-submenu">
                    <ul class="nav flex-column ps-3">
                        <li><a class="nav-link text-white" href="<?= site_url('admin/tambah_tagihan') ?>">Tambah Tagihan</a></li>
                        <li><a class="nav-link text-white" href="<?= site_url('admin/bayar_tagihan') ?>">Bayar Tagihan Manual</a></li>
                    </ul>
                </div>
            </li>
            <?php
                // Logika baru untuk menentukan menu SPP aktif
                $spp_segments = ['manajemen_spp', 'rincian_spp_siswa'];
                $is_spp_page_active = in_array($uri->getSegment(2), $spp_segments);
            ?>
            <li class="nav-item mb-1 dropdown">
                <a class="nav-link text-white dropdown-toggle <?= $is_spp_page_active ? 'active' : '' ?>" href="#" role="button" data-bs-toggle="collapse" data-bs-target="#spp-submenu" aria-expanded="<?= $is_spp_page_active ? 'true' : 'false' ?>" aria-controls="spp-submenu">
                    <i class="bi bi-receipt-cutoff me-3"></i>
                    <span class="text-label">Data SPP</span>
                </a>
                <div class="collapse <?= $is_spp_page_active ? 'show' : '' ?>" id="spp-submenu">
                    <ul class="nav flex-column ps-3">
                        <li><a class="nav-link text-white" href="<?= site_url('admin/manajemen_spp') ?>">Kelola Data SPP</a></li>
                        <li><a class="nav-link text-white" href="<?= site_url('admin/rincian_spp_siswa') ?>">SPP per Siswa</a></li>
                    </ul>
                </div>
            </li>
            <li class="nav-item mb-1"><a class="nav-link text-white <?= ($current_segment == 'data_siswa' || $current_segment == 'tambah_siswa') ? 'active' : '' ?>" href="<?= site_url('admin/data_siswa') ?>"><i class="bi bi-people me-3"></i><span class="text-label">Data Siswa</span></a></li>
            <li class="nav-item mb-1"><a class="nav-link text-white <?= ($current_segment == 'data_uang_saku' || $current_segment == 'pengambilan_uang_saku') ? 'active' : '' ?>" href="<?= site_url('admin/data_uang_saku') ?>"><i class="bi bi-cash-stack me-3"></i><span class="text-label">Data Uang Saku</span></a></li>
            <li class="nav-item mb-1"><a class="nav-link text-white <?= ($current_segment == 'laporan') ? 'active' : '' ?>" href="<?= site_url('admin/laporan') ?>"><i class="bi bi-graph-up me-3"></i><span class="text-label">Laporan</span></a></li>
        </ul>
    </div>

    <div id="main-content" class="main-content">
        <nav class="navbar navbar-expand-lg navbar-light bg-white top-navbar sticky-top px-3">
            <span class="toggle-btn" onclick="toggleSidebar()">&#9776;</span>
            <div class="ms-auto">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle fs-4 me-2"></i>
                            <span class="d-none d-sm-inline"><?= esc(session()->get('user')['nama']) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item text-danger" href="<?= site_url('logout') ?>">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        <main class="p-4">
            <?= $this->renderSection('content') ?>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('collapsed');
        document.getElementById('main-content').classList.toggle('collapsed');
    }
</script>
<?= $this->renderSection('scripts') ?>

</body>
</html>