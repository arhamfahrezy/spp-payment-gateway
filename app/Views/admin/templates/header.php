<!-- C:\xampp\htdocs\payment-gateway\app\Views\admin\templates\header.php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= $title ?? 'Admin' ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        body {
            margin: 0;
            padding: 0;
        }

        .sidebar {
            width: 250px;
            transition: all 0.3s ease;
            z-index: 1000;
            overflow-x: hidden;
        }

        .sidebar-hidden {
            width: 70px !important; /* Saat disembunyikan, tampilkan hanya 70px */
        }

        .sidebar .nav-link {
            white-space: nowrap; /* Agar teks tidak wrap */
            overflow: hidden;
            text-overflow: ellipsis;
            transition: all 0.3s ease;
        }

        .sidebar.sidebar-hidden .text-label {
            display: none;
        }
        
        .sidebar.sidebar-hidden .nav-link span.text-label {
            display: none; /* Sembunyikan teks saat sidebar collapse */
        }

        .sidebar.sidebar-hidden .nav-link {
            text-align: center;
        }

        .sidebar.sidebar-hidden .nav-link .bi {
            font-size: 1.5rem;
        }

        .main-content {
            transition: all 0.3s ease;
        }

        .with-sidebar {
            margin-left: 250px !important;
        }

        .no-sidebar {
            margin-left: 70px !important;
        }

        .toggle-btn {
            font-size: 24px;
            cursor: pointer;
        }

        /* Optional: fix spacing between cards */
        .card {
            margin-bottom: 20px;
        }
    </style>
</head>
<body> -->
