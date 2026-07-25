<?php

namespace App\Livewire\ClassPromotion;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\User;
use App\Models\ClassPromotion;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    public $step = 1; // Wizard step: 1=Select Years, 2=Preview, 3=Confirm
    public $fromAcademicYearId;
    public $toAcademicYearId;
    public $previewData = [];
    public $notes = '';

    public function mount()
    {
        // Auto-select active academic year as source
        $activeYear = AcademicYear::active()->first();
        if ($activeYear) {
            $this->fromAcademicYearId = $activeYear->id;
        }
    }

    public function goToPreview()
    {
        $this->validate([
            'fromAcademicYearId' => 'required|exists:academic_years,id',
            'toAcademicYearId' => 'required|exists:academic_years,id|different:fromAcademicYearId',
        ], [
            'fromAcademicYearId.required' => 'Tahun ajaran sumber harus dipilih',
            'toAcademicYearId.required' => 'Tahun ajaran tujuan harus dipilih',
            'toAcademicYearId.different' => 'Tahun ajaran tujuan harus berbeda dengan sumber',
        ]);

        // Check if target year already has classes
        $toYear = AcademicYear::find($this->toAcademicYearId);
        $classCount = SchoolClass::where('academic_year_id', $toYear->id)->count();
        
        if ($classCount === 0) {
            // Auto-generate classes for new academic year
            SchoolClass::autoGenerateClasses($toYear->id);
        }

        // Generate preview data
        $this->generatePreviewData();
        $this->step = 2;
    }

    private function generatePreviewData()
    {
        $fromYear = AcademicYear::find($this->fromAcademicYearId);
        $toYear = AcademicYear::find($this->toAcademicYearId);

        $grades = ['X', 'XI', 'XII'];
        $majors = ['MPLB', 'AKL', 'BUSANA'];
        
        $preview = [];
        $totalPromoted = 0;
        $totalGraduated = 0;

        foreach ($grades as $grade) {
            foreach ($majors as $major) {
                // Get source class
                $sourceClass = SchoolClass::where('academic_year_id', $fromYear->id)
                    ->where('grade', $grade)
                    ->where('major', $major)
                    ->first();

                if (!$sourceClass) continue;

                // Count active students in this class
                $studentCount = User::where('class_id', $sourceClass->id)
                    ->where('role', 'siswa')
                    ->where('is_active', true)
                    ->where('is_alumni', false)
                    ->count();

                if ($studentCount === 0) continue;

                // Determine target
                if ($grade === 'XII') {
                    // Graduating students
                    $preview[] = [
                        'source_class' => $sourceClass->name,
                        'source_grade' => $grade,
                        'major' => $major,
                        'student_count' => $studentCount,
                        'target' => 'ALUMNI',
                        'target_class' => null,
                        'action' => 'graduate',
                    ];
                    $totalGraduated += $studentCount;
                } else {
                    // Promoting students
                    $nextGrade = $grade === 'X' ? 'XI' : 'XII';
                    
                    // Get or create target class
                    $targetClass = SchoolClass::where('academic_year_id', $toYear->id)
                        ->where('grade', $nextGrade)
                        ->where('major', $major)
                        ->first();

                    $preview[] = [
                        'source_class' => $sourceClass->name,
                        'source_grade' => $grade,
                        'major' => $major,
                        'student_count' => $studentCount,
                        'target' => $nextGrade,
                        'target_class' => $targetClass ? $targetClass->name : null,
                        'target_class_id' => $targetClass ? $targetClass->id : null,
                        'action' => 'promote',
                    ];
                    $totalPromoted += $studentCount;
                }
            }
        }

        $this->previewData = [
            'from_year' => $fromYear->name,
            'to_year' => $toYear->name,
            'items' => $preview,
            'total_promoted' => $totalPromoted,
            'total_graduated' => $totalGraduated,
            'total_students' => $totalPromoted + $totalGraduated,
        ];
    }

    public function processPromotion()
    {
        $this->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $fromYear = AcademicYear::find($this->fromAcademicYearId);
            $toYear = AcademicYear::find($this->toAcademicYearId);
            $currentYear = date('Y');

            $promoted = 0;
            $graduated = 0;
            $summary = [];

            foreach ($this->previewData['items'] as $item) {
                // Get students from source class
                $sourceClass = SchoolClass::where('academic_year_id', $fromYear->id)
                    ->where('grade', $item['source_grade'])
                    ->where('major', $item['major'])
                    ->first();

                if (!$sourceClass) continue;

                $students = User::where('class_id', $sourceClass->id)
                    ->where('role', 'siswa')
                    ->where('is_active', true)
                    ->where('is_alumni', false)
                    ->get();

                if ($item['action'] === 'graduate') {
                    // Graduate students (kelas XII)
                    foreach ($students as $student) {
                        $student->update([
                            'is_alumni' => true,
                            'graduation_year' => $currentYear,
                            'class_id' => null,
                            'grade' => 'XII', // Keep grade XII
                        ]);
                        $graduated++;
                    }

                    $summary[] = [
                        'source' => $item['source_class'],
                        'target' => 'ALUMNI',
                        'count' => $students->count(),
                    ];
                } else {
                    // Promote students (kelas X & XI)
                    $targetClass = SchoolClass::find($item['target_class_id']);
                    
                    if ($targetClass) {
                        foreach ($students as $student) {
                            $student->update([
                                'class_id' => $targetClass->id,
                                'grade' => $item['target'],
                            ]);
                            $promoted++;
                        }

                        $summary[] = [
                            'source' => $item['source_class'],
                            'target' => $item['target_class'],
                            'count' => $students->count(),
                        ];
                    }
                }
            }

            // Create promotion record
            ClassPromotion::create([
                'from_academic_year_id' => $this->fromAcademicYearId,
                'to_academic_year_id' => $this->toAcademicYearId,
                'processed_by' => auth()->id(),
                'total_promoted' => $promoted,
                'total_graduated' => $graduated,
                'promotion_summary' => $summary,
                'notes' => $this->notes,
                'processed_at' => now(),
            ]);

            // Activate new academic year
            $toYear->update(['is_active' => true]);

            DB::commit();

            session()->flash('success', "Kenaikan kelas berhasil! {$promoted} siswa naik kelas, {$graduated} siswa lulus.");
            $this->step = 3;

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reset()
    {
        $this->step = 1;
        $this->previewData = [];
        $this->notes = '';
        $this->mount();
    }

    #[Layout('components.layouts.app')]
    #[Title('Kenaikan Kelas - SIM Kurikulum SMK PGRI Blora')]
    public function render()
    {
        $academicYears = AcademicYear::notArchived()
            ->orderBy('start_date', 'desc')
            ->get();

        return view('livewire.class-promotion.index', [
            'academicYears' => $academicYears,
        ]);
    }
}
