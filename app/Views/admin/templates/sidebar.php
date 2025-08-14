<!-- C:\xampp\htdocs\payment-gateway\app\Views\admin\templates\sidebar.php
<div class="d-flex">
    Sidebar
    <div id="sidebar" class="bg-dark text-white min-vh-100 sidebar position-fixed">
        <div class="p-3 d-flex align-items-center gap-2 justify-content-center">
            Logo Pondok Kecil
            <img src="<?= base_url('pondok_kecil_logo.png') ?>" alt="Logo Pondok Kecil" style="width: 50px; height: auto;" />
            Teks Welcome dan Nama
            <div class="text-label flex-grow-1">
                <div>Welcome,</div>
                <div><?= session()->get('user')['nama'] ?>!</div>
            </div>
        </div>

        <ul class="nav flex-column px-1">
            <li class="nav-item mb-1">
                <a class="nav-link text-white" href="<?= site_url('admin/dashboard') ?>">
                    <i class="bi bi-speedometer2"></i>
                    <span class="text-label ms-2">Dashboard</span>
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link text-white" href="<?= site_url('admin/data_siswa') ?>">
                    <i class="bi bi-people"></i>
                    <span class="text-label ms-2">Data Siswa</span>
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link text-white" href="<?= site_url('admin/tambah_tagihan') ?>">
                    <i class="bi bi-wallet2"></i>
                    <span class="text-label ms-2">Tambah Tagihan</span>
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link text-white" href="<?= site_url('admin/data_spp') ?>">
                    <i class="bi bi-file-text"></i>
                    <span class="text-label ms-2">Data SPP</span>
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link text-white" href="<?= site_url('admin/data_uang_saku') ?>">
                    <i class="bi bi-cash-stack"></i>
                    <span class="text-label ms-2">Data Uang Saku</span>
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link text-white" href="<?= site_url('admin/laporan') ?>">
                    <i class="bi bi-graph-up"></i>
                    <span class="text-label ms-2">Laporan</span>
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link text-white" href="<?= site_url('logout') ?>">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="text-label ms-2">Logout</span>
                </a>
            </li>
        </ul>
    </div>

    Main Content
    <div id="main" class="main-content with-sidebar ms-auto" style="width: 100%;">
        <nav class="navbar navbar-light bg-light px-3">
            <span class="toggle-btn" onclick="toggleSidebar()">&#9776;</span>
            <span class="ms-3 fw-bold">Admin</span>
        </nav> -->
