<?php
// database/seeders/SchoolUserSeeder.php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SchoolUserSeeder extends Seeder
{
    public function run()
    {
        // Pastikan role 'yatim' sudah ada
        $yatimRole = Role::firstOrCreate(['name' => 'yatim']);

        $schools = [
            ['name' => 'SMAN 1 SIDOARJO', 'nik' => '20501706'],
            ['name' => 'SMAN 2 SIDOARJO', 'nik' => '20501702'],
            ['name' => 'SMAN 3 SIDOARJO', 'nik' => '20501701'],
            ['name' => 'SMAN 4 SIDOARJO', 'nik' => '20501700'],
            ['name' => 'SMAN 1 GEDANGAN', 'nik' => '20501862'],
            ['name' => 'SMAN 1 KREMBUNG', 'nik' => '20501846'],
            ['name' => 'SMAN 1 KRIAN', 'nik' => '20501784'],
            ['name' => 'SMAN 1 PORONG', 'nik' => '20501722'],
            ['name' => 'SMAN 1 TAMAN', 'nik' => '20501705'],
            ['name' => 'SMAN 1 TARIK', 'nik' => '20501704'],
            ['name' => 'SMAN 1 WARU', 'nik' => '20501703'],
            ['name' => 'SMAN 1 WONOAYU', 'nik' => '20501698'],
            ['name' => 'SMAN OLAH RAGA SIDOARJO', 'nik' => '20501699']
        ];

        $created = 0;
        $skipped = 0;

        foreach ($schools as $school) {
            // Cek apakah NIK sudah ada
            if (User::where('nik', $school['nik'])->exists()) {
                $this->command->info("Skipped - NIK sudah ada: {$school['name']}");
                $skipped++;
                continue;
            }

            // Generate email unik
            $email = $this->generateUniqueEmail($school['name']);

            try {
                $user = User::create([
                    'nik' => $school['nik'],
                    'nokk' => '1234567890123456', // Default, sesuaikan jika perlu
                    'name' => $school['name'],
                    'nohp' => '081234567890', // Default
                    'email' => $email,
                    'gender' => 'male', // atau 'female'
                    'id_kecamatan' => 1, // Sesuaikan dengan ID kecamatan yang ada
                    'id_kelurahan' => 1, // Sesuaikan dengan ID kelurahan yang ada
                    'codepos' => '61256', // Sesuaikan
                    'rt' => '001',
                    'rw' => '001',
                    'alamat' => $school['name'],
                    'status' => 1,
                    'status_terkirim' => 'false',
                    'status_wa' => 0,
                    'status_email' => 0,
                    'status_finish' => 0,
                    'jenis_verif' => 'belum',
                    'step' => 1,
                    'password' => Hash::make('!pendidikan@2025')
                ]);

                $user->assignRole('yatim');

                $this->command->info("Created: {$school['name']}");
                $created++;
            } catch (\Exception $e) {
                $this->command->error("Error creating {$school['name']}: {$e->getMessage()}");
            }
        }

        $this->command->info("\nSummary:");
        $this->command->info("Created: {$created} users");
        $this->command->info("Skipped: {$skipped} users (already exists)");
    }

    private function generateUniqueEmail($schoolName)
    {
        $baseEmail = strtolower(preg_replace('/[^a-z0-9]/', '', $schoolName));
        $email = $baseEmail . '@example.com';

        $counter = 1;
        while (User::where('email', $email)->exists()) {
            $email = $baseEmail . $counter . '@example.com';
            $counter++;
        }

        return $email;
    }
}
