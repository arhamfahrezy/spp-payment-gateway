<?php
use CodeIgniter\Router\RouteCollection;

/** * @var RouteCollection $routes */

$routes->get('/', 'Auth::index');
$routes->post('login', 'Auth::login');
$routes->get('logout', 'Auth::logout');
$routes->post('midtrans/notification', 'MidtransNotification::index'); 
$routes->group('admin', ['filter' => 'adminfilter'], function($routes) {
    $routes->get('dashboard', 'Admin\\Dashboard::index');
    // Data Siswa
    $routes->get('data_siswa', 'Admin\\DataSiswa::index');
    $routes->get('tambah_siswa', 'Admin\\TambahSiswa::index');
    $routes->post('tambah_siswa/simpan', 'Admin\\TambahSiswa::simpan');
    $routes->get('tambah_siswa/edit/(:segment)', 'Admin\\TambahSiswa::edit/$1');
    $routes->post('tambah_siswa/update/(:segment)', 'Admin\\TambahSiswa::update/$1');
    $routes->get('data_siswa/hapus/(:segment)', 'Admin\\DataSiswa::hapus/$1');
    // Data SPP
    $routes->get('manajemen_spp', 'Admin\\ManajemenSPP::index');
    ;$routes->get('spp/archive/(:num)', 'Admin\\ManajemenSPP::archive/$1');
    $routes->get('spp/reactivate/(:num)', 'Admin\\ManajemenSPP::reactivate/$1');
    $routes->get('rincian_spp_siswa', 'Admin\\RincianSppSiswa::index');
    $routes->get('rincian_spp_siswa/cetak_invoice/(:num)', 'Admin\\RincianSppSiswa::cetak_invoice/$1');
    $routes->get('tambah_tagihan', 'Admin\\TambahTagihan::index');
    $routes->post('tambah_tagihan/simpan', 'Admin\\TambahTagihan::simpan');
    $routes->get('tambah_tagihan/edit/(:segment)', 'Admin\\TambahTagihan::edit/$1');
    $routes->post('tambah_tagihan/update/(:segment)', 'Admin\\TambahTagihan::update/$1');
    $routes->get('bayar_tagihan', 'Admin\\BayarTagihan::index');
    $routes->post('bayar_tagihan/proses', 'Admin\\BayarTagihan::proses_pembayaran');
    $routes->get('bayar_tagihan/get_tagihan/(:segment)', 'Admin\\BayarTagihan::get_tagihan_siswa/$1');
    // Data Uang Saku
    $routes->get('data_uang_saku', 'Admin\\DataUangSaku::index');
    $routes->get('pengambilan_uang_saku', 'Admin\\AmbilUangSaku::index');
    $routes->post('pengambilan_uang_saku/proses_pengambilan', 'Admin\\AmbilUangSaku::prosesPengambilan');
    $routes->get('data_uang_saku/cetak_transaksi/(:num)', 'Admin\\DataUangSaku::cetak_transaksi/$1'); 
    // Laporan
    $routes->get('laporan', 'Admin\\Laporan::index'); 
    $routes->get('laporan/cetak_spp', 'Admin\\Laporan::cetak_spp');   
    $routes->get('laporan/cetak_uang_saku', 'Admin\\Laporan::cetak_uang_saku'); 
});

$routes->group('walimurid', ['filter' => 'walimuridfilter'], function($routes) {
    $routes->get('dashboard', 'WaliMurid\\Dashboard::index');
    // SPP
    $routes->get('tagihan_spp', 'WaliMurid\\TagihanSPP::index');
    $routes->get('pembayaran_spp', 'WaliMurid\\PembayaranSPP::index');  
    $routes->post('pembayaran_spp/prosesPembayaranSPP', 'WaliMurid\\PembayaranSPP::prosesPembayaranSPP');
    $routes->get('pembayaran_spp/batal/(:segment)', 'WaliMurid\\PembayaranSPP::batalPembayaran/$1');
    // Uang Saku
    $routes->get('uang_saku', 'WaliMurid\\UangSaku::index');
    $routes->get('pembayaran_uang_saku', 'WaliMurid\\PembayaranUangSaku::index');  
    $routes->post('pembayaran_uang_saku/prosesPembayaran', 'WaliMurid\\PembayaranUangSaku::prosesPembayaran');  
    $routes->get('pembayaran_uang_saku/batal/(:segment)', 'WaliMurid\\PembayaranUangSaku::batalPembayaran/$1');
    $routes->get('uang_saku/cetak_bukti/(:num)', 'WaliMurid\\UangSaku::cetakBukti/$1');
    // Laporan
    $routes->get('riwayat_pembayaran', 'WaliMurid\\RiwayatPembayaran::index');
    $routes->get('riwayat_pembayaran/cetak_bukti_spp/(:num)', 'WaliMurid\\RiwayatPembayaran::cetakBuktiSpp/$1');
});
