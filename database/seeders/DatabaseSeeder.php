<?php

namespace Database\Seeders;

use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Admin & Pengelola Users
        User::firstOrCreate(
            ['email' => 'admin@pamsimas.id'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'pengelola@pamsimas.id'],
            [
                'name' => 'Pengelola PAMSIMAS',
                'password' => Hash::make('password'),
                'role' => 'pengelola',
            ]
        );

        // 2. Seed All System & Landing Page Content Seeders
        $this->call([
            LandingPageSeeder::class,
            LandingContentSeeder::class,
            LayananSeeder::class,
            MapSettingSeeder::class,
            PenarikSeeder::class,
        ]);

        // 3. Create Sample Pelanggan
        $pelangganData = [
            [
                'id_pelanggan' => 'PLG001',
                'nama_pelanggan' => 'Budi Santoso',
                'no_whatsapp' => '081234567801',
                'rt' => '001',
                'rw' => '001',
                'kategori' => 'umum',
                'status_aktif' => true,
                'latitude' => -6.200100,
                'longitude' => 106.816700,
            ],
            [
                'id_pelanggan' => 'PLG002',
                'nama_pelanggan' => 'Siti Aminah',
                'no_whatsapp' => '081234567802',
                'rt' => '001',
                'rw' => '001',
                'kategori' => 'sosial',
                'status_aktif' => true,
                'latitude' => -6.200200,
                'longitude' => 106.816800,
            ],
            [
                'id_pelanggan' => 'PLG003',
                'nama_pelanggan' => 'Ahmad Wijaya',
                'no_whatsapp' => '081234567803',
                'rt' => '002',
                'rw' => '001',
                'kategori' => 'umum',
                'status_aktif' => true,
                'latitude' => -6.200300,
                'longitude' => 106.816900,
            ],
            [
                'id_pelanggan' => 'PLG004',
                'nama_pelanggan' => 'Dewi Lestari',
                'no_whatsapp' => '081234567804',
                'rt' => '002',
                'rw' => '001',
                'kategori' => 'umum',
                'status_aktif' => true,
                'latitude' => -6.200400,
                'longitude' => 106.817000,
            ],
            [
                'id_pelanggan' => 'PLG005',
                'nama_pelanggan' => 'Bambang Sutrisno',
                'no_whatsapp' => '081234567805',
                'rt' => '003',
                'rw' => '002',
                'kategori' => 'sosial',
                'status_aktif' => true,
                'latitude' => -6.200500,
                'longitude' => 106.817100,
            ],
            [
                'id_pelanggan' => 'PLG006',
                'nama_pelanggan' => 'Ratih Kusuma',
                'no_whatsapp' => '081234567806',
                'rt' => '003',
                'rw' => '002',
                'kategori' => 'umum',
                'status_aktif' => false,
                'latitude' => null,
                'longitude' => null,
            ],
            [
                'id_pelanggan' => 'PLG007',
                'nama_pelanggan' => 'Hendra Gunawan',
                'no_whatsapp' => '081234567807',
                'rt' => '004',
                'rw' => '002',
                'kategori' => 'umum',
                'status_aktif' => true,
                'latitude' => -6.200700,
                'longitude' => 106.817300,
            ],
            [
                'id_pelanggan' => 'PLG008',
                'nama_pelanggan' => 'Fitri Handayani',
                'no_whatsapp' => '081234567808',
                'rt' => '004',
                'rw' => '002',
                'kategori' => 'umum',
                'status_aktif' => true,
                'latitude' => null,
                'longitude' => null,
            ],
        ];

        foreach ($pelangganData as $data) {
            Pelanggan::firstOrCreate(['id_pelanggan' => $data['id_pelanggan']], $data);
        }

        // 4. Seed Pembayaran & TagihanBulanan (12 Bulan Historis untuk AI Anomaly Detection)
        $pelangganList = Pelanggan::where('status_aktif', true)->get();

        foreach ($pelangganList as $pelanggan) {
            $baseMeteran = 100;
            
            // Seed 12 bulan historis pemakaian (Januari 2025 - Desember 2025)
            for ($month = 1; $month <= 12; $month++) {
                $bulanStr = sprintf('2025-%02d', $month);
                $usage = rand(15, 25); // Pemakaian wajar 15-25 m3 per bulan
                $meteranSebelum = $baseMeteran;
                $meteranSesudah = $baseMeteran + $usage;
                $baseMeteran = $meteranSesudah;

                // Create TagihanBulanan
                \App\Models\TagihanBulanan::firstOrCreate(
                    [
                        'pelanggan_id' => $pelanggan->id,
                        'bulan' => $bulanStr,
                    ],
                    [
                        'meteran_sebelum' => $meteranSebelum,
                        'meteran_sesudah' => $meteranSesudah,
                        'pemakaian_kubik' => $usage,
                        'tarif_per_kubik' => 2000,
                        'ada_abunemen' => true,
                        'biaya_abunemen' => 5000,
                        'total_tagihan' => ($usage * 2000) + 5000,
                        'jumlah_terbayar' => ($usage * 2000) + 5000,
                        'status_bayar' => 'SUDAH_BAYAR',
                        'status_validasi' => 'NORMAL',
                        'anomaly_score' => 0.10,
                        'ocr_confidence' => 95.0,
                        'catatan_anomali' => 'PEMAKAIAN NORMAL: Pemakaian wajar sesuai historis.',
                    ]
                );

                // Create Pembayaran record
                Pembayaran::firstOrCreate(
                    [
                        'pelanggan_id' => $pelanggan->id,
                        'bulan_bayar' => $bulanStr,
                    ],
                    [
                        'tanggal_bayar' => "2025-" . sprintf('%02d', $month) . "-15",
                        'jumlah_bayar' => ($usage * 2000) + 5000,
                        'keterangan' => "Pembayaran Lunas Periode {$bulanStr}",
                    ]
                );
            }
        }
    }
}
