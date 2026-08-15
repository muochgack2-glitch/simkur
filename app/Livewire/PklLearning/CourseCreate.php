<?php

namespace App\Livewire\PklLearning;

use App\Livewire\BaseComponent;
use App\Models\AcademicYear;
use App\Models\Activity;
use App\Models\PklCourse;
use App\Models\PklMaterial;
use App\Models\PklAssignment;
use App\Models\PklQuiz;
use App\Models\PklQuizQuestion;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\TeachingSchedule;
use Livewire\Component;
use Livewire\WithFileUploads;

class CourseCreate extends BaseComponent
{
    use WithFileUploads;

    // Course fields
    public $pkl_period_id = '';
    public $periods = [];
    public $activity_id = '';
    public $subject_id = '';
    public $title = '';
    public $description = '';
    public $competency = '';
    public $target_classes = [];
    public $start_date = '';

    public function updatedPklPeriodId($value)
    {
        if ($value) {
            $period = \App\Models\PklPeriod::find($value);
            if ($period) {
                $this->start_date = $period->start_date->format('Y-m-d');
                $this->deadline = $period->end_date->format('Y-m-d');
            }
        }
    }
    public $deadline = '';

    // Materials
    public $materials = [];
    public $materialFiles = [];

    // Assignments
    public $assignments = [];

    // Quizzes
    public $quizzes = [];

    // Dropdowns
    public $pklActivities = [];
    public $subjects = [];
    public $availableClasses = [];

    public function mount()
    {
        $user = auth()->user();
        $academicYear = AcademicYear::where('is_active', true)->first();
        if (!$academicYear) return;

        // Load periods
        $this->periods = \App\Models\PklPeriod::where('academic_year_id', $academicYear->id)
            ->orderBy('period_number')
            ->get();

        // PKL activities
        $this->pklActivities = Activity::with('activityType')
            ->where('academic_year_id', $academicYear->id)
            ->where('is_active', true)
            ->whereHas('activityType', fn($q) => $q->where('code', 'pkl'))
            ->get();

        // Auto-select if only 1 PKL activity
        if ($this->pklActivities->count() === 1) {
            $this->activity_id = $this->pklActivities->first()->id;
        }

        // Classes currently in PKL = classes with deactivated schedules (via Manajemen PKL)
        $pklClassIds = TeachingSchedule::where('academic_year_id', $academicYear->id)
            ->where('is_active', false)
            ->pluck('class_id')
            ->unique();

        // Fallback: if no deactivated schedules, try is_pkl flag on students
        if ($pklClassIds->isEmpty()) {
            $pklClassIds = SchoolClass::where('academic_year_id', $academicYear->id)
                ->whereHas('students', fn($q) => $q->where('is_pkl', true))
                ->pluck('id');
        }

        // Last fallback: all XII classes
        if ($pklClassIds->isEmpty()) {
            $pklClassIds = SchoolClass::where('academic_year_id', $academicYear->id)
                ->where('grade', 'XII')
                ->pluck('id');
        }

        // Subjects: ONLY from teacher's schedules in PKL classes
        $subjectIds = TeachingSchedule::where('teacher_id', $user->id)
            ->where('academic_year_id', $academicYear->id)
            ->whereIn('class_id', $pklClassIds)
            ->pluck('subject_id')
            ->unique();
        $this->subjects = Subject::whereIn('id', $subjectIds)->orderBy('name')->get();

        $this->availableClasses = SchoolClass::whereIn('id', $pklClassIds)
            ->orderBy('name')
            ->get();

        // Defaults
        $this->addMaterial();
        $this->addAssignment();
    }

    public function addMaterial()
    {
        $this->materials[] = ['title' => '', 'type' => 'pdf', 'external_url' => ''];
    }

    public function removeMaterial($index)
    {
        unset($this->materials[$index]);
        $this->materials = array_values($this->materials);
    }

    public function addAssignment()
    {
        $this->assignments[] = [
            'title' => '', 'description' => '', 'deadline' => '',
            'max_score' => 100, 'allow_late' => false, 'allow_file_upload' => true,
        ];
    }

    public function removeAssignment($index)
    {
        unset($this->assignments[$index]);
        $this->assignments = array_values($this->assignments);
    }

    public function addQuiz()
    {
        $this->quizzes[] = [
            'title' => '', 'description' => '', 'duration_minutes' => 30,
            'deadline' => '', 'shuffle_questions' => false,
            'questions' => [
                ['question_type' => 'multiple_choice', 'question' => '', 'options' => ['', '', '', ''], 'correct_answer' => '', 'score' => 10],
            ],
        ];
    }

    public function removeQuiz($index)
    {
        unset($this->quizzes[$index]);
        $this->quizzes = array_values($this->quizzes);
    }

    public function addQuestion($quizIndex)
    {
        $this->quizzes[$quizIndex]['questions'][] = [
            'question_type' => 'multiple_choice', 'question' => '',
            'options' => ['', '', '', ''], 'correct_answer' => '', 'score' => 10,
        ];
    }

    public function removeQuestion($quizIndex, $qIndex)
    {
        unset($this->quizzes[$quizIndex]['questions'][$qIndex]);
        $this->quizzes[$quizIndex]['questions'] = array_values($this->quizzes[$quizIndex]['questions']);
    }

    public function save($publish = false)
    {
        $this->validate([
            'activity_id' => 'nullable|exists:activities,id',
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'target_classes' => 'required|array|min:1',
            'start_date' => 'required|date',
            'deadline' => 'required|date|after_or_equal:start_date',
        ]);

        $academicYear = AcademicYear::where('is_active', true)->first();

        // Create course
        $course = PklCourse::create([
            'pkl_period_id' => $this->pkl_period_id ?: null,
            'activity_id' => $this->activity_id,
            'teacher_id' => auth()->id(),
            'subject_id' => $this->subject_id,
            'academic_year_id' => $academicYear->id,
            'title' => $this->title,
            'description' => $this->description,
            'competency' => $this->competency,
            'target_classes' => array_map('intval', $this->target_classes),
            'start_date' => $this->start_date,
            'deadline' => $this->deadline,
            'is_published' => $publish,
        ]);

        // Save materials
        foreach ($this->materials as $i => $mat) {
            if (empty($mat['title'])) continue;

            $filePath = null;
            $fileSize = null;
            if (isset($this->materialFiles[$i]) && is_object($this->materialFiles[$i]) && method_exists($this->materialFiles[$i], 'store')) {
                try {
                    $file = $this->materialFiles[$i];
                    $realPath = $file->getRealPath();
                    if ($realPath && file_exists($realPath)) {
                        $ext = $file->getClientOriginalExtension() ?: 'pdf';
                        $fileName = 'pkl-materials/' . uniqid() . '_' . time() . '.' . $ext;
                        \Storage::disk('public')->put($fileName, file_get_contents($realPath));
                        $filePath = $fileName;
                        $fileSize = \Storage::disk('public')->size($filePath);
                    }
                } catch (\Throwable $e) {
                    $filePath = null;
                    $fileSize = null;
                }
            }

            PklMaterial::create([
                'pkl_course_id' => $course->id,
                'title' => $mat['title'],
                'type' => $mat['type'],
                'file_path' => $filePath,
                'external_url' => $mat['external_url'] ?? null,
                'file_size' => $fileSize,
                'order' => $i,
            ]);
        }

        // Save assignments
        foreach ($this->assignments as $i => $asg) {
            if (empty($asg['title'])) continue;

            PklAssignment::create([
                'pkl_course_id' => $course->id,
                'title' => $asg['title'],
                'description' => $asg['description'] ?? null,
                'deadline' => $asg['deadline'] ?: $this->deadline . ' 23:59:00',
                'max_score' => $asg['max_score'] ?? 100,
                'allow_late' => $asg['allow_late'] ?? false,
                'allow_file_upload' => $asg['allow_file_upload'] ?? true,
                'order' => $i,
            ]);
        }

        // Save quizzes with questions
        foreach ($this->quizzes as $i => $quiz) {
            if (empty($quiz['title'])) continue;

            $pklQuiz = PklQuiz::create([
                'pkl_course_id' => $course->id,
                'title' => $quiz['title'],
                'description' => $quiz['description'] ?? null,
                'duration_minutes' => $quiz['duration_minutes'] ?? null,
                'max_score' => collect($quiz['questions'] ?? [])->sum('score'),
                'deadline' => $quiz['deadline'] ?: $this->deadline . ' 23:59:00',
                'is_published' => $publish,
                'shuffle_questions' => $quiz['shuffle_questions'] ?? false,
                'order' => $i,
            ]);

            foreach ($quiz['questions'] ?? [] as $j => $q) {
                if (empty($q['question'])) continue;
                PklQuizQuestion::create([
                    'pkl_quiz_id' => $pklQuiz->id,
                    'question_type' => $q['question_type'],
                    'question' => $q['question'],
                    'options' => $q['question_type'] === 'multiple_choice' ? ($q['options'] ?? []) : null,
                    'correct_answer' => $q['correct_answer'] ?? null,
                    'score' => $q['score'] ?? 10,
                    'order' => $j,
                ]);
            }
        }

        $msg = $publish ? 'Materi berhasil dipublikasikan!' : 'Materi berhasil disimpan sebagai draft';
        session()->flash('success', $msg);
        return redirect()->route('pkl-learning.dashboard');
    }

    public function render()
    {
        return view('livewire.pkl-learning.course-create');
    }
}
