<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Transaksi Uang Saku - #<?= esc($transaksi['id']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --accent-color: #0d6efd;
            --font-color: #333;
            --border-color: #dee2e6;
        }
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        .invoice-container { max-width: 600px; margin: 2rem auto; background: #ffffff; border-radius: 10px; box-shadow: 0 0 20px rgba(0, 0, 0, 0.05); overflow: hidden; }
        .invoice-header { text-align: center; padding: 1.2rem; background-color: var(--accent-color); color: white; }
        .invoice-header img { width: 50px; margin-bottom: 10px; }
        .invoice-header h2 { margin: 0; font-size: 1.2rem; }
        .invoice-body { padding: 1.5rem 2rem; font-size: 1rem; }
        .invoice-body h3 { text-align: center; margin-bottom: 1.5rem; font-weight: 600; color: #444; }
        .receipt-table { width: 100%; }
        .receipt-table td { padding: 8px 0; border-bottom: 1px dotted #ccc; }
        .receipt-table tr:last-child td { border-bottom: none; }
        .receipt-table .label { color: #555; }
        .receipt-table .value { text-align: right; font-weight: 600; }
        .receipt-total .value { font-size: 1.2rem; border-top: 2px solid #333; padding-top: 10px; margin-top: 10px; }
        .invoice-footer { text-align: center; padding: 1rem; font-size: 0.8rem; color: #999; }
        .print-area { padding: 2rem; text-align: center; }

        @media print {
            body { background-color: #fff; }
            .invoice-container { box-shadow: none; border: none; margin: 0; padding: 0; max-width: 100%; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <?php
        $dateTimeFormatter = new IntlDateFormatter('id_ID', IntlDateFormatter::LONG, IntlDateFormatter::SHORT);
    ?>
    <div class="invoice-container">
        <header class="invoice-header">
            <img src="<?= base_url('pondok_kecil_logo.png') ?>" alt="Logo">
            <h2>Ponpes Mts Darul Quran</h2>
        </header>

        <main class="invoice-body">
            <h3>BUKTI TRANSAKSI UANG SAKU</h3>
            <table class="receipt-table">
                <tr>
                    <td class="label">ID Transaksi</td>
                    <td class="value">#<?= esc($transaksi['id']) ?></td>
                </tr>
                <tr>
                    <td class="label">Tanggal</td>
                    <td class="value"><?= $dateTimeFormatter->format(strtotime($transaksi['tanggal'])) ?></td>
                </tr>
                <tr>
                    <td class="label">Nama Siswa</td>
                    <td class="value"><?= esc($siswa['nama_siswa']) ?></td>
                </tr>
                <tr>
                    <td class="label">NIS / Kelas</td>
                    <td class="value"><?= esc($siswa['nis']) ?> / <?= esc($siswa['kelas']) ?></td>
                </tr>
                <tr>
                    <td class="label">Tipe Transaksi</td>
                    <td class="value fw-bold <?= $transaksi['tipe_transaksi'] == 'Masuk' ? 'text-success' : 'text-danger' ?>">
                        <?= esc($transaksi['tipe_transaksi']) ?>
                    </td>
                </tr>
                 <tr>
                    <td class="label">Catatan</td>
                    <td class="value"><?= esc($transaksi['catatan'] ?? '-') ?></td>
                </tr>
                <tr class="receipt-total">
                    <td class="label">Nominal</td>
                    <td class="value">Rp <?= number_format($transaksi['nominal'], 0, ',', '.') ?></td>
                </tr>
            </table>
        </main>
        
        <footer class="invoice-footer">
            <p>Simpan bukti ini sebagai referensi.</p>
        </footer>
    </div>

    <div class="print-area no-print">
        <button onclick="window.print();" class="btn btn-primary btn-lg"><i class="bi bi-printer-fill me-2"></i>Cetak Bukti Transaksi</button>
    </div>
</body>
</html>
