<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Database\Seeder;

class UpdateSemesterDatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder updates semester dates to match the official academic calendar
     * based on Kalender_Pendidikan_2026_2027.xlsx
     */
    public function run(): void
    {
        // Find academic year 2026/2027
        $academicYear = AcademicYear::where('year', '2026/2027')->first();
        
        if (!$academicYear) {
            $this->command->error('Academic year 2026/2027 not found!');
            return;
        }
        
        // Update Semester Ganjil (Semester 1)
        $semesterGanjil = Semester::where('academic_year_id', $academicYear->id)
            ->where('type', 'ganjil')
            ->first();
            
        if ($semesterGanjil) {
            $semesterGanjil->update([
                'start_date' => '2026-07-13', // Senin, 13 Juli 2026 (hari pertama masuk)
                'end_date' => '2026-12-20',   // Minggu, 20 Desember 2026 (sebelum libur semester)
            ]);
            
            $this->command->info('✓ Updated Semester Ganjil: 13 Jul 2026 - 20 Dec 2026');
        }
        
        // Update Semester Genap (Semester 2)
        $semesterGenap = Semester::where('academic_year_id', $academicYear->id)
            ->where('type', 'genap')
            ->first();
            
        if ($semesterGenap) {
            $semesterGenap->update([
                'start_date' => '2027-01-05', // Selasa, 5 Januari 2027 (hari pertama masuk)
                'end_date' => '2027-06-20',   // Minggu, 20 Juni 2027 (sebelum libur kenaikan kelas)
            ]);
            
            $this->command->info('✓ Updated Semester Genap: 05 Jan 2027 - 20 Jun 2027');
        }
        
        $this->command->info('');
        $this->command->info('Semester dates updated successfully!');
        $this->command->info('Next step: Run "php artisan ekaldik:calculate-days" to recalculate effective days');
    }
}
