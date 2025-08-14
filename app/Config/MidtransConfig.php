<?php
// C:\xampp\htdocs\payment-gateway\app\Config\MidtransConfig.php
namespace Config;

use CodeIgniter\Config\BaseConfig;

class MidtransConfig extends BaseConfig
{
    // Server key Midtrans (ambil dari dashboard Midtrans)
    public string $serverKey = 'SB-Mid-server-y6B1hD_TZ4vSZ9g8sjM-UItQ';

    // Client key Midtrans
    public string $clientKey = 'SB-Mid-client-Z1pBwx-Gg8jvw2mW';

    // Mode produksi atau sandbox. Saat deploy ke live, ubah jadi true untuk mode production
    public bool $isProduction = false;

    // Sanitasi data input
    public bool $isSanitized = true;

    // Enable 3DS transaction
    public bool $is3ds = true;

    // Untuk Produksi: 'https://app.midtrans.com/snap/snap.js';
    public string $snapJsUrl = 'https://app.sandbox.midtrans.com/snap/snap.js'; 
}
