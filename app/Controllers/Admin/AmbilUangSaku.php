<?php
// app/Controllers/Admin/AmbilUangSaku.php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SiswaModel;
use App\Models\UangSakuModel;
use App\Models\TransaksiUangSakuModel;

class AmbilUangSaku extends BaseController
{
    protected $siswaModel;
    protected $uangSakuModel;
    protected $transaksiUangSakuModel;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->uangSakuModel = new UangSakuModel();
        $this->transaksiUangSakuModel = new TransaksiUangSakuModel();
    } 

    public function index()
    {
        // Ambil semua data siswa untuk autocomplete
        $siswaList = $this->siswaModel
            ->select('siswa.nis, siswa.nama_siswa, siswa.kelas, uang_saku.saldo')
            ->join('uang_saku', 'uang_saku.nis = siswa.nis', 'left')
            ->orderBy('siswa.nama_siswa', 'ASC')
            ->findAll();

        $dataUntukView = [
            'title' => 'Pengambilan Uang Saku Siswa',
            'siswaListJson' => json_encode(array_map(function ($siswa) {
                return [
                    'nis' => $siswa['nis'],
                    'nama' => $siswa['nama_siswa'],
                    'kelas' => $siswa['kelas'] ?? '-', // Handle jika kelas bisa null
                    'saldo' => $siswa['saldo'] ?? 0
                ];
            }, $siswaList)),
            'selected_siswa_data_json' => 'null' // Default jika tidak ada NIS dari URL
        ];

        // Cek apakah ada NIS yang dikirim via GET request (dari tombol "Ambil")
        $nisFromGet = $this->request->getGet('nis');
        if ($nisFromGet) {
            $selectedSiswaData = $this->siswaModel
                ->select('siswa.nis, siswa.nama_siswa, siswa.kelas, uang_saku.saldo')
                ->join('uang_saku', 'uang_saku.nis = siswa.nis', 'left')
                ->where('siswa.nis', $nisFromGet)
                ->first();
            
            if ($selectedSiswaData) {
                $dataUntukView['selected_siswa_data_json'] = json_encode([
                    'nis' => $selectedSiswaData['nis'],
                    'nama' => $selectedSiswaData['nama_siswa'],
                    'kelas' => $selectedSiswaData['kelas'] ?? '-',
                    'saldo' => $selectedSiswaData['saldo'] ?? 0
                ]);
            } else {
                session()->setFlashdata('error', 'Siswa dengan NIS ' . esc($nisFromGet) . ' tidak ditemukan.');
            }
        }

        return view('admin/pengambilan_uang_saku', $dataUntukView);
    }

    public function prosesPengambilan()
    {
        // ... (Kode prosesPengambilan Anda yang sudah ada dan sudah diperbaiki) ...
        // 1. Validasi Input
        $validationRules = [
            'nis' => 'required|alpha_numeric',
            'jumlah_diambil_numeric' => [
                'label'  => 'Jumlah Diambil',
                'rules'  => 'required|numeric|greater_than[0]',
                'errors' => [
                    'required' => 'Kolom {field} harus diisi.',
                    'numeric' => 'Kolom {field} hanya boleh berisi angka.',
                    'greater_than' => 'Kolom {field} harus lebih besar dari 0.'
                ]
            ],
            'catatan' => [
                'label'  => 'Catatan',
                'rules'  => 'required|min_length[3]',
                'errors' => [
                    'required' => 'Kolom {field} harus diisi.',
                    'min_length' => 'Kolom {field} minimal {param} karakter.'
                ]
            ],
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nis = $this->request->getPost('nis');
        $jumlahDiambil = (int) $this->request->getPost('jumlah_diambil_numeric'); 
        $catatan = $this->request->getPost('catatan');

        $siswa = $this->siswaModel->find($nis);
        if (!$siswa) {
            return redirect()->back()->withInput()->with('error', 'Data siswa dengan NIS (' . esc($nis) . ') tersebut tidak ditemukan.');
        }

        $uangSakuData = $this->uangSakuModel->find($nis);
        $saldoSaatIni = $uangSakuData ? (int)$uangSakuData['saldo'] : 0;

        if ($jumlahDiambil > $saldoSaatIni) {
            return redirect()->back()->withInput()->with('error', 'Saldo siswa (' . esc($siswa['nama_siswa']) . ') tidak mencukupi. Saldo saat ini: Rp ' . number_format($saldoSaatIni) . '. Jumlah diambil: Rp ' . number_format($jumlahDiambil));
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $saldoBaru = $saldoSaatIni - $jumlahDiambil;
            $this->uangSakuModel->update($nis, ['saldo' => $saldoBaru]);

            $this->transaksiUangSakuModel->insert([
                'nis' => $nis,
                'tipe_transaksi' => 'Keluar',
                'nominal' => $jumlahDiambil,
                'catatan' => $catatan,
                'tanggal' => date('Y-m-d H:i:s')
            ]);

            if ($db->transStatus() === false) {
                $db->transRollback();
                session()->setFlashdata('error', 'Gagal menyimpan data pengambilan karena masalah database.');
            } else {
                $db->transCommit();
                session()->setFlashdata('success', 'Pengambilan uang saku untuk siswa ' . esc($siswa['nama_siswa']) . ' (NIS: ' . esc($nis) . ') sejumlah Rp ' . number_format($jumlahDiambil) . ' berhasil dicatat.');
            }
        
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error saat proses pengambilan uang saku: ' . $e->getMessage() . ' - Data: ' . json_encode($this->request->getPost()));
            session()->setFlashdata('error', 'Terjadi kesalahan fatal saat menyimpan data pengambilan.');
        }
        return redirect()->to('admin/data_uang_saku');
    }
}