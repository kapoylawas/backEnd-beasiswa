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
            ['name' => 'SMP NEGERI 1 BALONGBENDO', 'nik' => '20501768'],
            ['name' => 'SMP NEGERI 1 BUDURAN', 'nik' => '20501782'],
            ['name' => 'SMP NEGERI 1 CANDI', 'nik' => '20501781'],
            ['name' => 'SMP NEGERI 1 GEDANGAN', 'nik' => '20501780'],
            ['name' => 'SMP NEGERI 1 JABON', 'nik' => '20501779'],
            ['name' => 'SMP NEGERI 1 KREMBUNG', 'nik' => '20501778'],
            ['name' => 'SMP NEGERI 1 KRIAN', 'nik' => '20501776'],
            ['name' => 'SMP Negeri 1 Porong', 'nik' => '20501775'],
            ['name' => 'SMP NEGERI 1 PRAMBON', 'nik' => '20501774'],
            ['name' => 'SMP NEGERI 1 SEDATI', 'nik' => '20501773'],
            ['name' => 'SMP NEGERI 1 SIDOARJO', 'nik' => '20501772'],
            ['name' => 'SMP NEGERI 1 SUKODONO', 'nik' => '20501771'],
            ['name' => 'SMP NEGERI 1 TAMAN', 'nik' => '20501770'],
            ['name' => 'SMP NEGERI 1 TANGGULANGIN', 'nik' => '20501769'],
            ['name' => 'SMP NEGERI 1 TARIK', 'nik' => '20501753'],
            ['name' => 'SMP NEGERI 1 TULANGAN', 'nik' => '20501752'],
            ['name' => 'SMP NEGERI 1 WARU', 'nik' => '20501736'],
            ['name' => 'SMP NEGERI 1 WONOAYU', 'nik' => '20537106'],
            ['name' => 'SMP NEGERI 2 BALONGBENDO', 'nik' => '20501734'],
            ['name' => 'SMP NEGERI 2 BUDURAN', 'nik' => '20501733'],
            ['name' => 'SMP NEGERI 2 CANDI', 'nik' => '20501732'],
            ['name' => 'SMP NEGERI 2 GEDANGAN', 'nik' => '20501731'],
            ['name' => 'SMP NEGERI 2 JABON', 'nik' => '20540013'],
            ['name' => 'SMP NEGERI 2 KREMBUNG', 'nik' => '20501730'],
            ['name' => 'SMP NEGERI 2 KRIAN', 'nik' => '20501729'],
            ['name' => 'SMP NEGERI 2 PORONG', 'nik' => '20501728'],
            ['name' => 'SMP NEGERI 2 PRAMBON', 'nik' => '70041835'],
            ['name' => 'SMP NEGERI 2 SEDATI', 'nik' => '20501740'],
            ['name' => 'SMP NEGERI 2 SIDOARJO', 'nik' => '20501727'],
            ['name' => 'SMP NEGERI 2 SUKODONO', 'nik' => '20501726'],
            ['name' => 'SMP NEGERI 2 TAMAN', 'nik' => '20501725'],
            ['name' => 'SMP NEGERI 2 TANGGULANGIN', 'nik' => '20501724'],
            ['name' => 'SMP NEGERI 2 TARIK', 'nik' => '20501737'],
            ['name' => 'SMP NEGERI 2 TULANGAN', 'nik' => '70030536'],
            ['name' => 'SMP NEGERI 2 WARU SIDOARJO', 'nik' => '20501738'],
            ['name' => 'SMP NEGERI 2 WONOAYU', 'nik' => '20537107'],
            ['name' => 'SMP NEGERI 3 CANDI', 'nik' => '20501750'],
            ['name' => 'SMP NEGERI 3 KRIAN', 'nik' => '20501749'],
            ['name' => 'SMP NEGERI 3 PORONG', 'nik' => '20501748'],
            ['name' => 'SMP NEGERI 3 SIDOARJO', 'nik' => '20501747'],
            ['name' => 'SMP NEGERI 3 TAMAN', 'nik' => '20501746'],
            ['name' => 'SMP NEGERI 3 WARU', 'nik' => '20501745'],
            ['name' => 'SMP NEGERI 4 SIDOARJO', 'nik' => '20501744'],
            ['name' => 'SMP NEGERI 4 WARU', 'nik' => '20501743'],
            ['name' => 'SMP NEGERI 5 SIDOARJO', 'nik' => '20501742'],
            ['name' => 'SMP NEGERI 6 SIDOARJO', 'nik' => '20501741'],
            ['name' => 'SMPN SATU ATAP BUDURAN', 'nik' => '20546964'],
            ['name' => 'SMPN SATU ATAP JABON', 'nik' => '20554869']
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
