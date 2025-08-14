<?php
// C:\xampp\htdocs\payment-gateway\app\Views\walimurid\templates\layout.php
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
    <!-- Midtrans Snap.js -->
    <script type="text/javascript"
      src="https://app.sandbox.midtrans.com/snap/snap.js"
      data-client-key="<?= env('midtrans.clientKey') ?>"></script>

    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-width-collapsed: 70px;
            --sidebar-bg: #0F4258;
            --sidebar-active-bg: #0B5A7D;
            --sidebar-hover-bg: rgba(255, 255, 255, 0.08);
            --sidebar-active-border: #34aadc;
        }
        body { background-color: #f8f9fa; }
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            transition: width 0.3s ease;
            overflow-x: hidden;
        }
        .sidebar.collapsed { width: var(--sidebar-width-collapsed); }
        .sidebar .nav-link {
            display: flex;
            align-items: center;
            border-left: 4px solid transparent;
            padding: 0.8rem 1rem;
            transition: background-color 0.2s ease, border-left-color 0.2s ease;
        }
        .sidebar .nav-link:not(.active):hover { background-color: var(--sidebar-hover-bg); }
        .sidebar .nav-link.active {
            background-color: var(--sidebar-active-bg);
            border-left-color: var(--sidebar-active-border);
            font-weight: 600;
        }
        .sidebar .nav-link .bi {
            font-size: 1.25rem; /* Ukuran ikon diseragamkan */
            min-width: 30px; /* Beri lebar minimum agar rapi */
            text-align: center;
        }
        .sidebar .nav-link .text-label, .sidebar .sidebar-brand-text {
            opacity: 1;
            white-space: nowrap;
            display: inline-block;
            visibility: visible;
            transition: opacity 0.2s ease, visibility 0.2s ease;
        }
        .sidebar.collapsed .nav-link .text-label, .sidebar.collapsed .sidebar-brand-text {
            opacity: 0;
            visibility: hidden;
            width: 0;
        }
        .sidebar.collapsed .nav-link { justify-content: center;padding-left: 0; padding-right: 0; }
        .sidebar.collapsed .nav-link .bi { font-size: 1.5rem; margin-right: 0 !important; }
        .main-content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            transition: margin-left 0.3s ease, width 0.3s ease;
        }
        .main-content.collapsed {
            margin-left: var(--sidebar-width-collapsed);
            width: calc(100% - var(--sidebar-width-collapsed));
        }
        .top-navbar { box-shadow: 0 2px 4px rgba(0,0,0,.08); }
        .toggle-btn { font-size: 24px; cursor: pointer; }
        .navbar .dropdown-toggle::after { display: none; }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div id="sidebar" class="text-white min-vh-100 sidebar position-fixed d-flex flex-column p-2">
        <a href="<?= site_url('walimurid/dashboard') ?>" class="p-1 d-flex align-items-center gap-2 text-white text-decoration-none sidebar-brand">
            <img src="<?= base_url('pondok_kecil_logo.png') ?>" alt="Logo" style="width: 40px; height: 40px;" />
            <div class="sidebar-brand-text flex-grow-1">
                <div class="fw-bold fs-10">Ponpes <br> Darul Quran</div>
            </div>
        </a>
        <hr>
        <ul class="nav flex-column px-1 mt-2">
            <li class="nav-item mb-1"><a class="nav-link text-white <?= ($current_segment == 'dashboard') ? 'active' : '' ?>" href="<?= site_url('walimurid/dashboard') ?>"><i class="bi bi-speedometer2 me-3"></i><span class="text-label">Dashboard</span></a></li>
            <li class="nav-item mb-1"><a class="nav-link text-white <?= ($current_segment == 'tagihan_spp') ? 'active' : '' ?>" href="<?= site_url('walimurid/tagihan_spp') ?>"><i class="bi bi-receipt-cutoff me-3"></i><span class="text-label">Tagihan SPP</span></a></li>
            <li class="nav-item mb-1"><a class="nav-link text-white <?= ($current_segment == 'uang_saku') ? 'active' : '' ?>" href="<?= site_url('walimurid/uang_saku') ?>"><i class="bi bi-wallet2 me-3"></i><span class="text-label">Uang Saku</span></a></li>
            <li class="nav-item mb-1"><a class="nav-link text-white <?= ($current_segment == 'riwayat_pembayaran') ? 'active' : '' ?>" href="<?= site_url('walimurid/riwayat_pembayaran') ?>"><i class="bi bi-clock-history me-3"></i><span class="text-label">Riwayat</span></a></li>
        </ul>
    </div>

    <!-- Main Content Wrapper -->
    <div id="main-content" class="main-content">
        <!-- Top Navbar -->
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
                            <!-- <li><a class="dropdown-item" href="#">Profil</a></li>
                            <li><hr class="dropdown-divider"></li> -->
                            <li><a class="dropdown-item text-danger" href="<?= site_url('logout') ?>">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Page Content -->
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
