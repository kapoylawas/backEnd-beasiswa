<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Str;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ambil role yatim yang sudah ada
        $yatimRole = Role::where('name', 'yatim')->first();

        if (!$yatimRole) {
            $this->command->error('Role "yatim" tidak ditemukan!');
            return;
        }

        // 2. Data Madrasah Ibtidaiyah (MI) dengan role yatim
        $sekolahData = [
            ['name' => 'PKBM ABK STAR KIDS', 'nik' => 'P9997658'],
            ['name' => 'PKBM ESYA', 'nik' => 'P9996645'],
            ['name' => 'PKBM PRIMA', 'nik' => 'P9934487'],
            ['name' => 'PKBM AL WAFA', 'nik' => 'P9997225'],
            ['name' => 'PKBM Delta Insan Cita', 'nik' => 'P9996413'],
            ['name' => 'PKBM FAUZUL FALAH', 'nik' => 'P9998967'],
            ['name' => 'PKBM AL BADRIYAH', 'nik' => 'P9970535'],
            ['name' => 'PKBM AL MUHAJIRIN ISLAMIC SCHOOL', 'nik' => 'P9999794'],
            ['name' => 'PKBM DIPONEGORO', 'nik' => 'P2961883'],
            ['name' => 'PKBM NURUR ROHMAN', 'nik' => 'P9999929'],
            ['name' => 'PKBM PELITA AUSHAF INDONESIA', 'nik' => 'P9998555'],
            ['name' => 'PKBM JAWAHIRUL ULUM', 'nik' => 'P2961700'],
            ['name' => 'PKBM MELATI INDONESIA', 'nik' => 'P9934573'],
            ['name' => 'PKBM DARUL FALAH', 'nik' => 'P9968069'],
            ['name' => 'PKBM NURUL HUDA', 'nik' => 'P9998559'],
            ['name' => 'PKBM SEKOLAH ALAM RAYA BONEKA TANAH', 'nik' => 'P9999141'],
            ['name' => 'PKBM WIRA BAKTI', 'nik' => 'P2961702'],
            ['name' => 'PKBM BUMI DAMAI', 'nik' => 'P9999225'],
            ['name' => 'PKBM PANGERAN DIPONEGORO', 'nik' => 'P2961407'],
            ['name' => 'PKBM HOMESCHOOLING HSPG SIDOARJO', 'nik' => 'P2961707'],
            ['name' => 'PKBM IBADURRAHMAN', 'nik' => 'P9997216'],
            ['name' => 'PKBM JATI MULYA', 'nik' => 'P9908344'],
            ['name' => 'PKBM LENTERA FAJAR INDONESIA', 'nik' => 'P9996279'],
            ['name' => 'PKBM PERMATA SUNNAH', 'nik' => 'P9952507'],
            ['name' => 'PKBM AL HIKMAH', 'nik' => 'P2961703'],
            ['name' => 'PKBM Alam Rumah Matahari', 'nik' => 'P9962802'],
            ['name' => 'PKBM BOARDING SCHOOL AL KAUTSAR', 'nik' => 'P9984425'],
            ['name' => 'PKBM DARUL QURAN WAL ILMI', 'nik' => 'P9999809'],
            ['name' => 'PKBM SAKA', 'nik' => 'P9997816'],
            ['name' => 'PKBM WAHANA ILMU', 'nik' => 'P9984819'],
            ['name' => 'PKBM AL HASYIMI II', 'nik' => 'P9997439'],
            ['name' => 'PKBM BUDI UTOMO', 'nik' => 'P2961705'],
            ['name' => 'PKBM DARUNNAJAH', 'nik' => 'P9997915'],
            ['name' => 'PKBM Firdaus Islamic School', 'nik' => 'P9999980'],
            ['name' => 'PKBM IMAM MUSLIM ISLAMIC SCHOOL', 'nik' => 'P9996968'],
            ['name' => 'PKBM KHALIFAH CENDIKIA', 'nik' => 'P9996529'],
            ['name' => 'PKBM THINK INDONESIA SCHOOL', 'nik' => 'P9997905'],
            ['name' => 'PKBM AL UMM', 'nik' => 'P9997400'],
            ['name' => 'PKBM PELITA', 'nik' => 'P2961699'],
            ['name' => 'PKBM FADJAR SHAFIRA', 'nik' => 'P9999787'],
            ['name' => 'PKBM SUMBER ILMU', 'nik' => 'P2961690'],
            ['name' => 'UPT SPNF SKB SIDOARJO', 'nik' => 'P9970534'],
            ['name' => 'PKBM ANEKA', 'nik' => 'P2961692'],
            ['name' => 'PKBM FLEXI SCHOOL', 'nik' => 'P9998878'],
            ['name' => 'PKBM IBNU ALI', 'nik' => 'P9908345'],
            ['name' => 'PKBM MAMBAUL ULUM', 'nik' => 'P9980040'],
            ['name' => 'PKBM RAUDLATUL JANNAH', 'nik' => 'P9998943'],
            ['name' => 'PKBM MAHAD IBNU KATSIR WONOAYU', 'nik' => 'P2970429'],
            ['name' => 'PKBM MANDIRI', 'nik' => 'P2961974'],
            ['name' => 'PKBM RABEL', 'nik' => 'P2961706'],
            ['name' => 'PKBM ROUDLOTUL ULUM', 'nik' => 'P9970476'],
        ];

        $this->command->info('Memulai pembuatan user dispenduk dengan role dispenduk...');
        $this->command->info('Total data: ' . count($sekolahData) . ' dispenduk');

        $createdCount = 0;
        $skippedCount = 0;
        $usedNokk = []; // Untuk menyimpan nokk yang sudah digunakan

        // 3. Fungsi untuk generate NOKK random yang unik
        function generateUniqueNokk(&$usedNokk)
        {
            do {
                // Format: 16 digit angka random
                $nokk = '';
                for ($i = 0; $i < 16; $i++) {
                    $nokk .= rand(0, 9);
                }
            } while (in_array($nokk, $usedNokk) || User::where('nokk', $nokk)->exists());

            $usedNokk[] = $nokk;
            return $nokk;
        }

        // 4. Loop untuk membuat user dengan role yatim
        foreach ($sekolahData as $index => $data) {
            // Skip jika NIK kosong
            if (empty($data['nik'])) {
                $this->command->warn("Skipped - NIK kosong: {$data['name']}");
                $skippedCount++;
                continue;
            }

            // Check jika NIK sudah ada
            if (User::where('nik', $data['nik'])->exists()) {
                $this->command->warn("Skipped - NIK sudah ada: {$data['name']} ({$data['nik']})");
                $skippedCount++;
                continue;
            }

            // Generate email random - menggunakan domain khusus MI
            $cleanName = preg_replace('/[^A-Za-z0-9\s]/', '', $data['name']);
            $email = Str::slug($cleanName, '_') . '@mi.sch.id';

            // Pastikan email unik
            $counter = 1;
            $originalEmail = $email;
            while (User::where('email', $email)->exists()) {
                $email = Str::slug($cleanName, '_') . $counter . '@mi.sch.id';
                $counter++;
            }

            // Generate NOKK random yang unik
            $nokk = generateUniqueNokk($usedNokk);

            // Buat user
            try {
                $user = User::create([
                    'nik'      => $data['nik'],
                    'nokk'      => $nokk,
                    'name'      => $data['name'],
                    'nohp'      => '08' . rand(100000000, 999999999),
                    'email'     => $email,
                    'gender'     => rand(0, 1) ? 'L' : 'P',
                    'codepos'     => '612' . rand(10, 99),
                    'rt'     => str_pad(rand(1, 20), 2, '0', STR_PAD_LEFT),
                    'rw'     => str_pad(rand(1, 10), 2, '0', STR_PAD_LEFT),
                    'alamat'     => 'Jl. ' . $data['name'] . ' No.' . rand(1, 100) . ' Sidoarjo',
                    'status_terkirim'     => 'false',
                    'status'     => 1,
                    'password'  => bcrypt('password')
                ]);

                // Assign role yatim
                $user->assignRole($yatimRole->name);

                $createdCount++;

                // Tampilkan progress
                if (($index + 1) % 10 === 0) {
                    $progress = round(($index + 1) / count($sekolahData) * 100, 1);
                    $this->command->info("Progress: {$progress}% - {$createdCount} created, {$skippedCount} skipped");
                }
            } catch (\Exception $e) {
                $this->command->error("Error creating user {$data['name']}: " . $e->getMessage());
                $skippedCount++;
            }
        }

        // 5. Tampilkan summary
        $this->command->line('========================================');
        $this->command->info('SEEDER COMPLETED');
        $this->command->line('========================================');
        $this->command->info("Total data processed: " . count($sekolahData));
        $this->command->info("Successfully created: {$createdCount} users");
        $this->command->info("Skipped (duplicate/empty NIK): {$skippedCount} users");
        $this->command->info("Domain email: @mi.sch.id");
        $this->command->info("Password: password");

        // 6. Tampilkan statistik
        $this->command->line("\nStatistics:");
        $this->command->info("- Total data dari file: " . count($sekolahData));
        $this->command->info("- Berhasil dibuat: {$createdCount}");
        $this->command->info("- Dilewati: {$skippedCount}");

        if ($skippedCount > 0) {
            $this->command->warn("\nCatatan: Beberapa user dilewati karena:");
            $this->command->warn("1. NIK sudah ada di database");
            $this->command->warn("2. NIK kosong (1 data: MI TARBIYATUS SALAFIYAH)");
        }

        // 7. Tampilkan beberapa contoh user yang berhasil dibuat
        if ($createdCount > 0) {
            $this->command->line("\nContoh 5 user yang berhasil dibuat:");
            $sampleUsers = User::where('email', 'like', '%@mi.sch.id')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(['name', 'email', 'nik']);

            foreach ($sampleUsers as $user) {
                $this->command->info("✓ {$user->name} - {$user->email} (NIK: {$user->nik})");
            }
        }

        $this->command->line('========================================');
    }
}
