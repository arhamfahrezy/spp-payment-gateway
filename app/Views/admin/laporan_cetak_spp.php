<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Laporan SPP') ?></title>
    <style>
        body { font-family: sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; }
        .header p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h1>Laporan Pembayaran SPP</h1>
        <p>Periode: <?= esc($periode_label) ?></p>
    </div>
    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">NIS</th>
                <th>Nama Siswa</th>
                <th class="text-center">Kelas</th>
                <th>Nama Tagihan</th>
                <th class="text-end">Jml. Tagihan</th>
                <th class="text-end">Jml. Bayar</th>
                <th class="text-center">Tgl. Bayar</th>
                <th class="text-center">Status</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($laporanSPP)): ?>
                <?php foreach ($laporanSPP as $index => $row): ?>
                <tr>
                    <td class="text-center"><?= $index + 1 ?></td>
                    <td class="text-center"><?= esc($row['nis']) ?></td>
                    <td><?= esc($row['nama_siswa']) ?></td>
                    <td class="text-center"><?= esc($row['kelas']) ?></td>
                    <td><?= esc($row['nama_tagihan']) ?></td>
                    <td class="text-end"><?= number_format($row['jumlah_tagihan'], 0, ',', '.') ?></td>
                    <td class="text-end"><?= number_format($row['jumlah_bayar'], 0, ',', '.') ?></td>
                    <td class="text-center"><?= $row['tanggal_bayar'] ? date('d-m-Y', strtotime($row['tanggal_bayar'])) : '-' ?></td>
                    <td class="text-center"><?= esc($row['status']) ?></td>
                    <td><?= esc($row['catatan']) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="10" class="text-center">Tidak ada data untuk periode ini.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
