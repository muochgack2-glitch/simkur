<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SchoolClass;
use Illuminate\Database\Seeder;

class AssignStudentsToClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all students without class
        $students = User::where('role', 'siswa')
            ->whereNull('class_id')
            ->get();

        if ($students->isEmpty()) {
            $this->command->info('All students are already assigned to classes.');
            return;
        }

        // Get available classes (active classes from current academic year)
        $classes = SchoolClass::where('is_active', true)
            ->with('academicYear')
            ->get();

        if ($classes->isEmpty()) {
            $this->command->error('No active classes found. Please create classes first.');
            return;
        }

        // Distribute students evenly across classes
        $classIndex = 0;
        foreach ($students as $student) {
            $class = $classes[$classIndex];
            
            $student->update([
                'class_id' => $class->id,
            ]);

            $this->command->info("Assigned {$student->name} to {$class->name}");

            // Move to next class (round-robin distribution)
            $classIndex = ($classIndex + 1) % $classes->count();
        }

        $this->command->info("\nSuccessfully assigned {$students->count()} students to classes.");
        
        // Show summary
        $this->command->info("\n=== Summary ===");
        foreach ($classes as $class) {
            $count = $class->students()->count();
            $this->command->info("{$class->name}: {$count} students");
        }
    }
}
