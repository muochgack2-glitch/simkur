<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'email' => 'admin@smk.sch.id',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ],
            [
                'name' => 'Budi Santoso',
                'username' => 'waka',
                'email' => 'waka@smk.sch.id',
                'password' => Hash::make('password'),
                'role' => 'waka_kurikulum',
                'is_active' => true,
            ],
            [
                'name' => 'Siti Nurhaliza',
                'username' => 'guru1',
                'email' => 'siti@smk.sch.id',
                'password' => Hash::make('password'),
                'role' => 'guru',
                'is_active' => true,
            ],
            [
                'name' => 'Ahmad Hidayat',
                'username' => 'guru2',
                'email' => 'ahmad@smk.sch.id',
                'password' => Hash::make('password'),
                'role' => 'guru',
                'is_active' => true,
            ],
            // Siswa Kelas X
            [
                'name' => 'Rizki Pratama',
                'username' => 'rizki',
                'email' => 'rizki@smk.sch.id',
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'grade' => 'X',
                'is_active' => true,
            ],
            [
                'name' => 'Dewi Lestari',
                'username' => 'dewi',
                'email' => 'dewi@smk.sch.id',
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'grade' => 'X',
                'is_active' => true,
            ],
            // Siswa Kelas XI
            [
                'name' => 'Andi Wijaya',
                'username' => 'andi',
                'email' => 'andi@smk.sch.id',
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'grade' => 'XI',
                'is_active' => true,
            ],
            [
                'name' => 'Sari Indah',
                'username' => 'sari',
                'email' => 'sari@smk.sch.id',
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'grade' => 'XI',
                'is_active' => true,
            ],
            // Siswa Kelas XII
            [
                'name' => 'Budi Nugraha',
                'username' => 'budi',
                'email' => 'budi@smk.sch.id',
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'grade' => 'XII',
                'is_active' => true,
            ],
            [
                'name' => 'Fitri Handayani',
                'username' => 'fitri',
                'email' => 'fitri@smk.sch.id',
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'grade' => 'XII',
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        $this->command->info('Users created successfully!');
        $this->command->info('Default credentials:');
        $this->command->info('- Admin: admin / password');
        $this->command->info('- Waka: waka / password');
        $this->command->info('- Guru: guru1 / password');
        $this->command->info('- Siswa: rizki, dewi, andi, sari, budi, fitri / password');
    }
}
