<?php
// C:\xampp\htdocs\payment-gateway\app\Views\admin\cetak_invoice.php
// Helper untuk format Rupiah
    if (!function_exists('format_rupiah')) {
        function format_rupiah($number) {
            return 'Rp ' . number_format($number, 0, ',', '.');
        }
    }

    // Helper untuk format Tanggal Indonesia
    $dateFormatter = new IntlDateFormatter('id_ID', IntlDateFormatter::FULL, IntlDateFormatter::NONE, 'Asia/Jakarta', IntlDateFormatter::GREGORIAN);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pembayaran - <?= esc($tagihan['nama_tagihan']) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .receipt-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2.5rem;
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 2rem;
            border-bottom: 2px dashed #adb5bd;
            padding-bottom: 0.5rem;
        }
        .receipt-header h2 {
            font-weight: 600;
            color: #212529;
        }
        .receipt-header p {
            font-size: 1rem;
            color: #6c757d;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }
        .info-box {
            width: 100%;
        }
        @media (min-width: 768px) {
            .info-box {
                width: 48%;
            }
        }
        .info-box h5 {
            font-weight: 600;
            color: #495057;
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            gap: 1rem;
        }
        .info-item span:first-child {
            color: #6c757d;
            flex-shrink: 0;
        }
        .info-item span:last-child {
            font-weight: 500;
            text-align: right;
        }
        .status-lunas {
            color: #198754;
            font-weight: 600;
            font-size: 1.2rem;
        }
        .table thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            vertical-align: middle;
        }
        .table td {
            vertical-align: middle;
        }
        .receipt-footer {
            text-align: center;
            margin-top: 2.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 0.9rem;
        }

        @media print {
            body {
                background-color: #fff;
            }
            .receipt-container {
                box-shadow: none;
                margin: 0;
                max-width: 100%;
                border: none;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="receipt-container">
    <div class="receipt-header">
        <h2>Bukti Pembayaran SPP</h2>
        <p>Ponpes Mts Darul Quran</p> </div>

    <div class="info-section">
        <div class="info-box mb-3 mb-md-0">
            <h5>Informasi Siswa</h5>
            <div class="info-item"><span>Nama Siswa</span> <span><?= esc($siswa['nama_siswa']) ?></span></div>
            <div class="info-item"><span>NIS</span> <span><?= esc($siswa['nis']) ?></span></div>
            <div class="info-item"><span>Kelas</span> <span><?= esc($siswa['kelas']) ?></span></div>
        </div>
        <div class="info-box">
            <h5>Detail Tagihan</h5>
            <div class="info-item"><span>Deskripsi</span> <span><?= esc($tagihan['nama_tagihan']) ?></span></div>
            
            <?php
                $isLunas = ($tagihan['status'] === 'Lunas');
                $isDiarsipkan = ($tagihan['status_tagihan'] === 'Diarsipkan');
                
                $statusText = '';
                $statusClass = '';
                $tanggalLabel = '';
                $tanggalValue = '';

                if ($isLunas) {
                    $statusText = 'LUNAS';
                    $statusClass = 'status-lunas';
                    $tanggalLabel = 'Tanggal Lunas';
                    $tanggalValue = esc($dateFormatter->format(strtotime($tagihan['tanggal_bayar'])));
                } elseif ($isDiarsipkan) {
                    $statusText = 'DICICIL (DIARSIPKAN)';
                    $statusClass = 'text-info fw-bold';
                    $tanggalLabel = 'Tanggal';
                    $tanggalValue = esc($dateFormatter->format(strtotime($tagihan['updated_at'])));
                } else { // Jika belum lunas dan masih aktif
                    $statusText = 'BELUM LUNAS';
                    $statusClass = 'text-danger fw-bold';
                    $tanggalLabel = 'Jatuh Tempo';
                    $tanggalValue = esc($dateFormatter->format(strtotime($tagihan['jatuh_tempo'])));
                }
            ?>

            <div class="info-item">
                <span><?= $tanggalLabel ?></span> 
                <span><?= $tanggalValue ?></span>
            </div>
            <div class="info-item">
                <span>Status</span> 
                <span class="<?= $statusClass ?>"><?= $statusText ?></span>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Ringkasan Pembayaran</h5>
        </div>
        <div class="card-body p-4">
             <div class="d-flex justify-content-between fs-6">
                <strong class="text-muted">TOTAL TAGIHAN</strong>
                <strong class="text-primary"><?= format_rupiah($tagihan['total_tagihan']) ?></strong>
            </div>
             <hr>
             <div class="d-flex justify-content-between fs-6">
                <strong class="text-muted">TOTAL DIBAYAR</strong>
                <strong class="text-success"><?= format_rupiah($tagihan['jumlah_bayar']) ?></strong>
            </div>
        </div>
    </div>

    <h5>Rincian Pembayaran</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th class="text-center">No</th>
                    <th>Tanggal Pembayaran</th>
                    <th>Metode Pembayaran</th>
                    <th class="text-end">Jumlah</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($cicilan)): ?>
                    <?php foreach ($cicilan as $index => $item): ?>
                        <tr>
                            <td class="text-center"><?= $index + 1 ?></td>
                            <td><?= esc($dateFormatter->format(strtotime($item['tanggal_bayar']))) ?></td>
                            <td><?= esc(ucwords(str_replace('_', ' ', $item['payment_type'] ?? 'N/A'))) ?></td>
                            <td class="text-end"><?= format_rupiah($item['jumlah_bayar']) ?></td>
                            <td><?= esc($item['catatan'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center">Belum ada rincian pembayaran yang sukses.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="receipt-footer">
        <p>Ini adalah bukti pembayaran yang sah dan dibuat secara otomatis oleh sistem.</p>
        <p>Dicetak pada: <?= esc($dateFormatter->format(time())) ?></p>
    </div>
    
    <div class="text-center mt-4 no-print">
        <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer me-2"></i>Cetak Invoice</button>
        <a href="<?= site_url('admin/rincian_spp_siswa') ?>" class="btn btn-secondary">Kembali ke Rincian</a>
    </div>

</div>

</body>
</html>