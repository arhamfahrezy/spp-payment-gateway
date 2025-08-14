<!-- C:\xampp\htdocs\payment-gateway\app\Views\walimurid\templates\sidebar.php -->
<div class="d-flex">
    <!-- Sidebar -->
    <div id="sidebar" class="background-color: #0F4258;  text-white min-vh-100 sidebar position-fixed">
        <div class="p-3 d-flex align-items-center gap-2 justify-content-center">
            <!-- Logo Pondok Kecil -->
            <img src="<?= base_url('pondok_kecil_logo.png') ?>" alt="Logo Pondok Kecil" style="width: 50px; height: auto;" />
            <!-- Teks Welcome dan Nama -->
            <div class="text-label flex-grow-1">
                <div>Ponpes</div>
                <div>Banyuwangi!</div>
            </div>
        </div>
        
        <ul class="nav flex-column px-1">
            <li class="nav-item mb-1">
                <a class="nav-link text-white" href="<?= site_url('walimurid/dashboard') ?>">
                    <i class="bi bi-speedometer2"></i>
                    <span class="text-label ms-2">Dashboard</span>
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link text-white" href="<?= site_url('walimurid/tagihan_spp') ?>">
                    <i class="bi bi-wallet2"></i>
                    <span class="text-label ms-2">Tagihan SPP</span>
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link text-white" href="<?= site_url('walimurid/uang_saku') ?>">
                    <i class="bi bi-cash-stack"></i>
                    <span class="text-label ms-2">Uang Saku</span>
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link text-white" href="<?= site_url('walimurid/riwayat_pembayaran') ?>">
                    <i class="bi bi-clock-history"></i>
                    <span class="text-label ms-2">Riwayat Pembayaran</span>
                </a>
            </li>
            <li class="nav-item mt-auto">
                <a class="nav-link text-white" href="<?= site_url('logout') ?>">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="text-label ms-2">Logout</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div id="main" class="main-content with-sidebar ms-auto" style="width: 100%;">
        <nav class="navbar navbar-light bg-light px-3">
            <span class="toggle-btn" onclick="toggleSidebar()">&#9776;</span>
            <!-- Optional: bisa ditambahkan nama wali murid atau logo -->
            <div class="ms-auto d-flex align-items-center">
                <i class="bi bi-person-circle fs-4 me-2 text-muted"></i>
                <span class="navbar-text fw-semibold" style="font-size: 19px;">
                    <?= esc(session()->get('user')['nama']) ?>
                </span>
            </div>
        </nav>
