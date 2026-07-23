<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // The issue: unique constraint on (user_id, semester_id) prevents students from taking
        // multiple assessments in the same semester. We need (user_id, assessment_id) instead.
        
        // Use raw SQL for complete control
        DB::statement('ALTER TABLE student_learning_profiles 
            DROP FOREIGN KEY IF EXISTS student_learning_profiles_user_id_foreign,
            DROP FOREIGN KEY IF EXISTS student_learning_profiles_assessment_id_foreign,
            DROP FOREIGN KEY IF EXISTS student_learning_profiles_academic_year_id_foreign,
            DROP FOREIGN KEY IF EXISTS student_learning_profiles_semester_id_foreign'
        );
        
        // Drop the old unique constraint
        DB::statement('ALTER TABLE student_learning_profiles DROP INDEX IF EXISTS unique_student_semester_profile');
        
        // Add new unique constraint
        DB::statement('ALTER TABLE student_learning_profiles ADD UNIQUE KEY unique_student_assessment_profile (user_id, assessment_id)');
        
        // Recreate foreign keys
        DB::statement('ALTER TABLE student_learning_profiles
            ADD CONSTRAINT student_learning_profiles_user_id_foreign 
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            ADD CONSTRAINT student_learning_profiles_assessment_id_foreign 
                FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE,
            ADD CONSTRAINT student_learning_profiles_academic_year_id_foreign 
                FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE,
            ADD CONSTRAINT student_learning_profiles_semester_id_foreign 
                FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE student_learning_profiles 
            DROP FOREIGN KEY IF EXISTS student_learning_profiles_user_id_foreign,
            DROP FOREIGN KEY IF EXISTS student_learning_profiles_assessment_id_foreign,
            DROP FOREIGN KEY IF EXISTS student_learning_profiles_academic_year_id_foreign,
            DROP FOREIGN KEY IF EXISTS student_learning_profiles_semester_id_foreign'
        );
        
        DB::statement('ALTER TABLE student_learning_profiles DROP INDEX IF EXISTS unique_student_assessment_profile');
        
        DB::statement('ALTER TABLE student_learning_profiles ADD UNIQUE KEY unique_student_semester_profile (user_id, semester_id)');
        
        DB::statement('ALTER TABLE student_learning_profiles
            ADD CONSTRAINT student_learning_profiles_user_id_foreign 
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            ADD CONSTRAINT student_learning_profiles_assessment_id_foreign 
                FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE,
            ADD CONSTRAINT student_learning_profiles_academic_year_id_foreign 
                FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE,
            ADD CONSTRAINT student_learning_profiles_semester_id_foreign 
                FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE'
        );
    }
};
