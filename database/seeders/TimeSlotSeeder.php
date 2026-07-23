<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimeSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('time_slots')->truncate();

        $timeSlots = [];

        // ========================================
        // SENIN - Alokasi Waktu Senin
        // ========================================
        $timeSlots[] = ['name' => 'Upacara/Apel', 'start_time' => '07:00:00', 'end_time' => '07:30:00', 'order' => 1, 'day_of_week' => 'monday', 'is_active' => true];
        $timeSlots[] = ['name' => 'Jam ke-1', 'start_time' => '07:30:00', 'end_time' => '08:10:00', 'order' => 2, 'day_of_week' => 'monday', 'is_active' => true];
        $timeSlots[] = ['name' => 'Jam ke-2', 'start_time' => '08:10:00', 'end_time' => '08:50:00', 'order' => 3, 'day_of_week' => 'monday', 'is_active' => true];
        $timeSlots[] = ['name' => 'Jam ke-3', 'start_time' => '08:50:00', 'end_time' => '09:30:00', 'order' => 4, 'day_of_week' => 'monday', 'is_active' => true];
        $timeSlots[] = ['name' => 'Istirahat', 'start_time' => '09:30:00', 'end_time' => '09:45:00', 'order' => 5, 'day_of_week' => 'monday', 'is_active' => false];
        $timeSlots[] = ['name' => 'Jam ke-4', 'start_time' => '09:45:00', 'end_time' => '10:25:00', 'order' => 6, 'day_of_week' => 'monday', 'is_active' => true];
        $timeSlots[] = ['name' => 'Jam ke-5', 'start_time' => '10:25:00', 'end_time' => '11:05:00', 'order' => 7, 'day_of_week' => 'monday', 'is_active' => true];
        $timeSlots[] = ['name' => 'Jam ke-6', 'start_time' => '11:05:00', 'end_time' => '11:45:00', 'order' => 8, 'day_of_week' => 'monday', 'is_active' => true];
        $timeSlots[] = ['name' => 'Jam ke-7', 'start_time' => '11:45:00', 'end_time' => '12:25:00', 'order' => 9, 'day_of_week' => 'monday', 'is_active' => true];
        $timeSlots[] = ['name' => 'Istirahat', 'start_time' => '12:25:00', 'end_time' => '12:45:00', 'order' => 10, 'day_of_week' => 'monday', 'is_active' => false];
        $timeSlots[] = ['name' => 'Jam ke-8', 'start_time' => '12:45:00', 'end_time' => '13:25:00', 'order' => 11, 'day_of_week' => 'monday', 'is_active' => true];
        $timeSlots[] = ['name' => 'Jam ke-9', 'start_time' => '13:25:00', 'end_time' => '14:05:00', 'order' => 12, 'day_of_week' => 'monday', 'is_active' => true];
        $timeSlots[] = ['name' => 'BTO', 'start_time' => '14:05:00', 'end_time' => '14:45:00', 'order' => 13, 'day_of_week' => 'monday', 'is_active' => false];

        // ========================================
        // SELASA - KAMIS - Alokasi Waktu Reguler
        // ========================================
        $regularDays = ['tuesday', 'wednesday', 'thursday'];
        foreach ($regularDays as $day) {
            $timeSlots[] = ['name' => 'Kegiatan Pagi', 'start_time' => '07:00:00', 'end_time' => '07:20:00', 'order' => 1, 'day_of_week' => $day, 'is_active' => false];
            $timeSlots[] = ['name' => 'Jam ke-1', 'start_time' => '07:20:00', 'end_time' => '08:00:00', 'order' => 2, 'day_of_week' => $day, 'is_active' => true];
            $timeSlots[] = ['name' => 'Jam ke-2', 'start_time' => '08:00:00', 'end_time' => '08:40:00', 'order' => 3, 'day_of_week' => $day, 'is_active' => true];
            $timeSlots[] = ['name' => 'Jam ke-3', 'start_time' => '08:40:00', 'end_time' => '09:20:00', 'order' => 4, 'day_of_week' => $day, 'is_active' => true];
            $timeSlots[] = ['name' => 'Istirahat', 'start_time' => '09:20:00', 'end_time' => '09:35:00', 'order' => 5, 'day_of_week' => $day, 'is_active' => false];
            $timeSlots[] = ['name' => 'Jam ke-4', 'start_time' => '09:35:00', 'end_time' => '10:15:00', 'order' => 6, 'day_of_week' => $day, 'is_active' => true];
            $timeSlots[] = ['name' => 'Jam ke-5', 'start_time' => '10:15:00', 'end_time' => '10:55:00', 'order' => 7, 'day_of_week' => $day, 'is_active' => true];
            $timeSlots[] = ['name' => 'Jam ke-6', 'start_time' => '10:55:00', 'end_time' => '11:35:00', 'order' => 8, 'day_of_week' => $day, 'is_active' => true];
            $timeSlots[] = ['name' => 'Jam ke-7', 'start_time' => '11:35:00', 'end_time' => '12:15:00', 'order' => 9, 'day_of_week' => $day, 'is_active' => true];
            $timeSlots[] = ['name' => 'Istirahat', 'start_time' => '12:15:00', 'end_time' => '12:45:00', 'order' => 10, 'day_of_week' => $day, 'is_active' => false];
            $timeSlots[] = ['name' => 'Jam ke-8', 'start_time' => '12:45:00', 'end_time' => '13:25:00', 'order' => 11, 'day_of_week' => $day, 'is_active' => true];
            $timeSlots[] = ['name' => 'Jam ke-9', 'start_time' => '13:25:00', 'end_time' => '14:05:00', 'order' => 12, 'day_of_week' => $day, 'is_active' => true];
            $timeSlots[] = ['name' => 'Jam ke-10', 'start_time' => '14:05:00', 'end_time' => '14:45:00', 'order' => 13, 'day_of_week' => $day, 'is_active' => true];
        }

        // ========================================
        // JUM'AT - Alokasi Waktu Jumat
        // ========================================
        $timeSlots[] = ['name' => 'Kegiatan Jumat', 'start_time' => '07:00:00', 'end_time' => '07:30:00', 'order' => 1, 'day_of_week' => 'friday', 'is_active' => false];
        $timeSlots[] = ['name' => 'Jam ke-1', 'start_time' => '07:30:00', 'end_time' => '08:05:00', 'order' => 2, 'day_of_week' => 'friday', 'is_active' => true];
        $timeSlots[] = ['name' => 'Jam ke-2', 'start_time' => '08:05:00', 'end_time' => '08:40:00', 'order' => 3, 'day_of_week' => 'friday', 'is_active' => true];
        $timeSlots[] = ['name' => 'Jam ke-3', 'start_time' => '08:40:00', 'end_time' => '09:15:00', 'order' => 4, 'day_of_week' => 'friday', 'is_active' => true];
        $timeSlots[] = ['name' => 'Istirahat', 'start_time' => '09:15:00', 'end_time' => '09:30:00', 'order' => 5, 'day_of_week' => 'friday', 'is_active' => false];
        $timeSlots[] = ['name' => 'Jam ke-4', 'start_time' => '09:30:00', 'end_time' => '10:05:00', 'order' => 6, 'day_of_week' => 'friday', 'is_active' => true];
        $timeSlots[] = ['name' => 'Jam ke-5', 'start_time' => '10:05:00', 'end_time' => '10:40:00', 'order' => 7, 'day_of_week' => 'friday', 'is_active' => true];
        $timeSlots[] = ['name' => 'Jam ke-6', 'start_time' => '10:40:00', 'end_time' => '11:15:00', 'order' => 8, 'day_of_week' => 'friday', 'is_active' => true];
        $timeSlots[] = ['name' => 'Jam ke-7', 'start_time' => '11:15:00', 'end_time' => '11:50:00', 'order' => 9, 'day_of_week' => 'friday', 'is_active' => true];
        $timeSlots[] = ['name' => 'Istirahat', 'start_time' => '11:50:00', 'end_time' => '12:50:00', 'order' => 10, 'day_of_week' => 'friday', 'is_active' => false];
        $timeSlots[] = ['name' => 'Jam ke-8', 'start_time' => '12:50:00', 'end_time' => '13:25:00', 'order' => 11, 'day_of_week' => 'friday', 'is_active' => true];
        $timeSlots[] = ['name' => 'Jam ke-9', 'start_time' => '13:25:00', 'end_time' => '14:00:00', 'order' => 12, 'day_of_week' => 'friday', 'is_active' => true];
        $timeSlots[] = ['name' => 'Jam ke-10', 'start_time' => '14:00:00', 'end_time' => '14:35:00', 'order' => 13, 'day_of_week' => 'friday', 'is_active' => true];

        // Insert all time slots
        DB::table('time_slots')->insert($timeSlots);

        $this->command->info('✅ Time slots seeded successfully!');
        $this->command->info('📊 Total: ' . count($timeSlots) . ' time slots');
        $this->command->info('   - Senin: 13 slots');
        $this->command->info('   - Selasa-Kamis: 39 slots (13 x 3 days)');
        $this->command->info('   - Jumat: 13 slots');
    }
}
