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
        $yatimRole = Role::where('name', 'admindinsos')->first();

        if (!$yatimRole) {
            $this->command->error('Role "yatim" tidak ditemukan!');
            return;
        }

        // 2. Data Madrasah Ibtidaiyah (MI) dengan role yatim
        $sekolahData = [
            ['name' => 'Santa Wira Kartika Putri Wihendra, S.Tr.IP', 'nik' => '200306232024092001'],
            ['name' => 'M. Sudhiro, S.E.', 'nik' => '198108302009101001'],
            ['name' => 'Sonny Aditya Darma, S.Sos', 'nik' => '199010292023211018'],
            ['name' => 'Muhammad Shodiqil Khafili Djakfar', 'nik' => '199407122023211009'],
            ['name' => 'Mahatma Byamoga', 'nik' => '3515080102000009'],
            ['name' => 'Hindy Alfri Ajisprasetya, S.Pd.', 'nik' => '199205022015021001'],
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
                    'password'  => bcrypt('pendidikan@1234')
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
        $this->command->info("Password: swasta@1234");

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
