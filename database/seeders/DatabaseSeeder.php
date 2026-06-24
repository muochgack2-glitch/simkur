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
        // Choose seeder based on environment
        $environment = app()->environment();
        
        if ($environment === 'production') {
            // Production: Minimal essential data only
            $this->call([
                ProductionSeeder::class,
            ]);
            
            $this->command->info('');
            $this->command->info('✅ Production database seeded successfully!');
            $this->command->info('');
            $this->command->info('Default Login Credentials:');
            $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->command->info('Admin:');
            $this->command->info('  Email: admin@ekaldik.local');
            $this->command->info('  Password: password');
            $this->command->info('');
            $this->command->info('Kurikulum:');
            $this->command->info('  Email: kurikulum@ekaldik.local');
            $this->command->info('  Password: password');
            $this->command->info('');
            $this->command->info('⚠️  IMPORTANT: Change these passwords immediately!');
            $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        } else {
            // Development: Full sample data
            $this->call([
                UserSeeder::class,
                ActivityTypeSeeder::class,
                SettingSeeder::class,
                ActivitySeeder::class,
            ]);

            $this->command->info('');
            $this->command->info('✅ Development database seeded successfully!');
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
}

