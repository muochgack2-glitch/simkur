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
    public $step = 1; // Wizard step: 1=Select Years, 1.5=Configure Rombel, 2=Preview, 3=Confirm
    public $fromAcademicYearId;
    public $toAcademicYearId;
    public $gradeXRombelConfig = []; // ['MPLB' => 1, 'AKL' => 1, 'BUSANA' => 1]
    public $previewData = [];
    public $notes = '';

    public function mount()
    {
        // Auto-select active academic year as source
        $activeYear = AcademicYear::active()->first();
        if ($activeYear) {
            $this->fromAcademicYearId = $activeYear->id;
        }
        
        // Initialize default rombel config (1 rombel per major)
        $this->gradeXRombelConfig = [
            'MPLB' => 1,
            'AKL' => 1,
            'BUSANA' => 1,
        ];
    }

    public function goToRombelConfig()
    {
        $this->validate([
            'fromAcademicYearId' => 'required|exists:academic_years,id',
            'toAcademicYearId' => 'required|exists:academic_years,id|different:fromAcademicYearId',
        ], [
            'fromAcademicYearId.required' => 'Tahun ajaran sumber harus dipilih',
            'toAcademicYearId.required' => 'Tahun ajaran tujuan harus dipilih',
            'toAcademicYearId.different' => 'Tahun ajaran tujuan harus berbeda dengan sumber',
        ]);

        $this->step = 1.5; // Go to rombel configuration
    }

    public function goToPreview()
    {
        $this->validate([
            'gradeXRombelConfig.MPLB' => 'required|integer|min:1|max:10',
            'gradeXRombelConfig.AKL' => 'required|integer|min:1|max:10',
            'gradeXRombelConfig.BUSANA' => 'required|integer|min:1|max:10',
        ], [
            'gradeXRombelConfig.*.required' => 'Jumlah rombel harus diisi',
            'gradeXRombelConfig.*.integer' => 'Jumlah rombel harus berupa angka',
            'gradeXRombelConfig.*.min' => 'Minimal 1 rombel',
            'gradeXRombelConfig.*.max' => 'Maksimal 10 rombel',
        ]);

        // Check if target year already has classes
        $toYear = AcademicYear::find($this->toAcademicYearId);
        $classCount = SchoolClass::where('academic_year_id', $toYear->id)->count();
        
        if ($classCount === 0) {
            // Auto-generate classes for new academic year with rombel config
            SchoolClass::autoGenerateClasses($toYear->id, $this->gradeXRombelConfig);
        }

        // Generate preview data
        $this->generatePreviewData();
        $this->step = 2;
    }

    private function generatePreviewData()
    {
        $fromYear = AcademicYear::find($this->fromAcademicYearId);
        $toYear = AcademicYear::find($this->toAcademicYearId);

        $preview = [];
        $totalPromoted = 0;
        $totalGraduated = 0;

        // Get all classes from source year (including all rombel)
        $sourceClasses = SchoolClass::where('academic_year_id', $fromYear->id)
            ->orderBy('grade')
            ->orderBy('major')
            ->orderBy('rombel')
            ->get();

        foreach ($sourceClasses as $sourceClass) {
            // Count active students in this class
            $studentCount = User::where('class_id', $sourceClass->id)
                ->where('role', 'siswa')
                ->where('is_active', true)
                ->where('is_alumni', false)
                ->count();

            if ($studentCount === 0) continue;

            // Determine target
            if ($sourceClass->grade === 'XII') {
                // Graduating students
                $preview[] = [
                    'source_class' => $sourceClass->name,
                    'source_class_id' => $sourceClass->id,
                    'source_grade' => $sourceClass->grade,
                    'major' => $sourceClass->major,
                    'rombel' => $sourceClass->rombel,
                    'student_count' => $studentCount,
                    'target' => 'ALUMNI',
                    'target_class' => null,
                    'target_class_id' => null,
                    'action' => 'graduate',
                ];
                $totalGraduated += $studentCount;
            } else {
                // Promoting students (X → XI, XI → XII)
                $nextGrade = $sourceClass->grade === 'X' ? 'XI' : 'XII';
                
                // Find target class with same major and rombel
                $targetClass = SchoolClass::where('academic_year_id', $toYear->id)
                    ->where('grade', $nextGrade)
                    ->where('major', $sourceClass->major)
                    ->where(function($q) use ($sourceClass) {
                        if ($sourceClass->rombel !== null) {
                            $q->where('rombel', $sourceClass->rombel);
                        } else {
                            $q->whereNull('rombel');
                        }
                    })
                    ->first();

                // If not found, create it (maintain rombel structure)
                if (!$targetClass) {
                    $targetClass = SchoolClass::create([
                        'academic_year_id' => $toYear->id,
                        'grade' => $nextGrade,
                        'major' => $sourceClass->major,
                        'rombel' => $sourceClass->rombel,
                        'name' => SchoolClass::generateClassName($nextGrade, $sourceClass->major, $sourceClass->rombel),
                        'capacity' => 36,
                        'is_active' => true,
                    ]);
                }

                $preview[] = [
                    'source_class' => $sourceClass->name,
                    'source_class_id' => $sourceClass->id,
                    'source_grade' => $sourceClass->grade,
                    'major' => $sourceClass->major,
                    'rombel' => $sourceClass->rombel,
                    'student_count' => $studentCount,
                    'target' => $nextGrade,
                    'target_class' => $targetClass->name,
                    'target_class_id' => $targetClass->id,
                    'action' => 'promote',
                ];
                $totalPromoted += $studentCount;
            }
        }

        $this->previewData = [
            'from_year' => $fromYear->year,
            'to_year' => $toYear->year,
            'items' => $preview,
            'total_promoted' => $totalPromoted,
            'total_graduated' => $totalGraduated,
            'total_students' => $totalPromoted + $totalGraduated,
            'rombel_config' => $this->gradeXRombelConfig,
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
                $sourceClass = SchoolClass::find($item['source_class_id']);

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

    public function resetPromotion()
    {
        $this->step = 1;
        $this->previewData = [];
        $this->notes = '';
        $this->gradeXRombelConfig = [
            'MPLB' => 1,
            'AKL' => 1,
            'BUSANA' => 1,
        ];
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
