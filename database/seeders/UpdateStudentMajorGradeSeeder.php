<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\SchoolClass;

class UpdateStudentMajorGradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Update student major and grade based on their class
     */
    public function run(): void
    {
        $this->command->info('🔄 Updating student major and grade data...');

        // Get all students with their classes
        $students = User::where('role', 'siswa')
            ->with('schoolClass')
            ->whereNotNull('class_id')
            ->get();

        $updatedCount = 0;

        foreach ($students as $student) {
            if (!$student->schoolClass) {
                continue;
            }

            $className = $student->schoolClass->name;
            
            // Extract grade and major from class name
            // Format: "X AKL", "XI MPLB", "XII Busana"
            if (preg_match('/^(X|XI|XII)\s+(.+)$/i', $className, $matches)) {
                $grade = strtoupper($matches[1]);
                $majorRaw = trim($matches[2]);
                
                // Normalize major name
                $major = match(strtoupper($majorRaw)) {
                    'AKL' => 'AKL',
                    'MPLB' => 'MPLB',
                    'BUSANA' => 'BUSANA',
                    default => null,
                };

                if ($major && $grade) {
                    $student->update([
                        'grade' => $grade,
                        'major' => $major,
                    ]);
                    
                    $updatedCount++;
                }
            }
        }

        $this->command->info("✅ $updatedCount siswa berhasil diupdate dengan data grade dan major!");
    }
}
