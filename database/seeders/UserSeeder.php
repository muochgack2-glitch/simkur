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
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        $this->command->info('Users created successfully!');
        $this->command->info('Default credentials:');
        $this->command->info('- Admin: admin / password');
        $this->command->info('- Waka: waka / password');
        $this->command->info('- Guru: guru1 / password');
    }
}
