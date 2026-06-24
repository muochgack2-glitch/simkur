<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // School Information
            [
                'key' => 'school_name',
                'value' => 'SMK Negeri 1 Jakarta',
                'type' => 'string',
                'group' => 'school',
                'description' => 'Nama Sekolah',
            ],
            [
                'key' => 'school_address',
                'value' => 'Jl. Pendidikan No. 123, Jakarta',
                'type' => 'string',
                'group' => 'school',
                'description' => 'Alamat Sekolah',
            ],
            [
                'key' => 'school_phone',
                'value' => '021-1234567',
                'type' => 'string',
                'group' => 'school',
                'description' => 'Telepon Sekolah',
            ],
            [
                'key' => 'school_email',
                'value' => 'info@smkn1jakarta.sch.id',
                'type' => 'string',
                'group' => 'school',
                'description' => 'Email Sekolah',
            ],
            [
                'key' => 'school_logo',
                'value' => '/images/logo.png',
                'type' => 'string',
                'group' => 'school',
                'description' => 'Path Logo Sekolah',
            ],
            [
                'key' => 'principal_name',
                'value' => 'Drs. H. Ahmad Suryadi, M.Pd',
                'type' => 'string',
                'group' => 'school',
                'description' => 'Nama Kepala Sekolah',
            ],
            [
                'key' => 'principal_niy',
                'value' => '123456789',
                'type' => 'string',
                'group' => 'school',
                'description' => 'NIY Kepala Sekolah',
            ],

            // Calendar Settings
            [
                'key' => 'weekend_days',
                'value' => '["saturday","sunday"]',
                'type' => 'json',
                'group' => 'calendar',
                'description' => 'Hari Libur Akhir Pekan',
            ],
            [
                'key' => 'default_calendar_view',
                'value' => 'month',
                'type' => 'string',
                'group' => 'calendar',
                'description' => 'Tampilan Kalender Default (month/year/list)',
            ],

            // System Settings
            [
                'key' => 'session_timeout',
                'value' => '120',
                'type' => 'number',
                'group' => 'system',
                'description' => 'Session Timeout (menit)',
            ],
            [
                'key' => 'items_per_page',
                'value' => '15',
                'type' => 'number',
                'group' => 'system',
                'description' => 'Jumlah Item Per Halaman',
            ],
            [
                'key' => 'date_format',
                'value' => 'd/m/Y',
                'type' => 'string',
                'group' => 'system',
                'description' => 'Format Tanggal',
            ],
            [
                'key' => 'enable_activity_conflict_warning',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'system',
                'description' => 'Aktifkan Peringatan Bentrok Kegiatan',
            ],
            [
                'key' => 'prevent_weekend_activities',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'system',
                'description' => 'Cegah Kegiatan di Hari Weekend (Sabtu/Minggu)',
            ],

            // Import Settings
            [
                'key' => 'max_import_rows',
                'value' => '1000',
                'type' => 'number',
                'group' => 'import',
                'description' => 'Maksimal Baris Import',
            ],
            [
                'key' => 'allowed_import_extensions',
                'value' => '["xlsx","xls"]',
                'type' => 'json',
                'group' => 'import',
                'description' => 'Ekstensi File Import yang Diizinkan',
            ],
            [
                'key' => 'max_import_file_size',
                'value' => '2048',
                'type' => 'number',
                'group' => 'import',
                'description' => 'Maksimal Ukuran File Import (KB)',
            ],

            // Export Settings
            [
                'key' => 'pdf_orientation',
                'value' => 'landscape',
                'type' => 'string',
                'group' => 'export',
                'description' => 'Orientasi PDF (landscape/portrait)',
            ],
            [
                'key' => 'pdf_paper_size',
                'value' => 'a4',
                'type' => 'string',
                'group' => 'export',
                'description' => 'Ukuran Kertas PDF',
            ],
            [
                'key' => 'include_logo_in_export',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'export',
                'description' => 'Sertakan Logo di Export',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }

        $this->command->info('Settings created successfully!');
        $this->command->info('Total: ' . count($settings) . ' settings');
    }
}
