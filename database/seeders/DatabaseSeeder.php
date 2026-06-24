<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ActivityTypeSeeder::class,
            SettingSeeder::class,
            ActivitySeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('');
        $this->command->info('Default Login Credentials:');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('Admin:');
        $this->command->info('  Username: admin');
        $this->command->info('  Password: password');
        $this->command->info('');
        $this->command->info('Waka Kurikulum:');
        $this->command->info('  Username: waka');
        $this->command->info('  Password: password');
        $this->command->info('');
        $this->command->info('Guru:');
        $this->command->info('  Username: guru1');
        $this->command->info('  Password: password');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}

