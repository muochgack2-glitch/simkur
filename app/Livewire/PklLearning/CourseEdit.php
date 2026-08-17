<?php

namespace App\Livewire\PklLearning;

use App\Livewire\BaseComponent;
use App\Models\AcademicYear;
use App\Models\Activity;
use App\Models\PklAssignment;
use App\Models\PklCourse;
use App\Models\PklMaterial;
use App\Models\PklQuiz;
use App\Models\PklQuizQuestion;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\TeachingSchedule;
use Livewire\WithFileUploads;

class CourseEdit extends BaseComponent
{
    use WithFileUploads;

    public $courseId;
    public $pkl_period_id = '';
    public $periods = [];
    public $activity_id = '';
    public $subject_id = '';
    public $title = '';
    public $description = '';
    public $competency = '';
    public $target_classes = [];
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

    public $start_date = '';
    public $deadline = '';

    public $materials = [];
    public $materialFiles = [];
    public $assignments = [];
    public $quizzes = [];

    public $pklActivities = [];
    public $subjects = [];
    public $availableClasses = [];

    // Track existing IDs for update/delete
    public $existingMaterialIds = [];
    public $existingAssignmentIds = [];
    public $existingQuizIds = [];

    public function mount(PklCourse $course)
    {
        $this->courseId = $course->id;
        $user = auth()->user();
        $academicYear = AcademicYear::where('is_active', true)->first();
        if (!$academicYear) return;

        // Load periods
        $this->periods = \App\Models\PklPeriod::where('academic_year_id', $academicYear->id)
            ->orderBy('period_number')->get();

        // Load dropdowns (same as create)
        $this->pklActivities = Activity::with('activityType')
            ->where('academic_year_id', $academicYear->id)
            ->where('is_active', true)
            ->whereHas('activityType', fn($q) => $q->where('code', 'pkl'))
            ->get();

        $pklClassIds = TeachingSchedule::where('academic_year_id', $academicYear->id)
            ->where('is_active', false)
            ->pluck('class_id')
            ->unique();

        if ($pklClassIds->isEmpty()) {
            $pklClassIds = SchoolClass::where('academic_year_id', $academicYear->id)
                ->where('grade', 'XII')
                ->pluck('id');
        }

        if (in_array($user->role, ['admin', 'kepala_sekolah'])) {
            $this->subjects = Subject::orderBy('name')->get();
        } else {
            $subjectIds = TeachingSchedule::where('teacher_id', $user->id)
                ->where('academic_year_id', $academicYear->id)
                ->whereIn('class_id', $pklClassIds)
                ->pluck('subject_id')
                ->unique();
            $this->subjects = Subject::whereIn('id', $subjectIds)->orderBy('name')->get();
            // Fallback: jika kosong, tampilkan semua
            if ($this->subjects->isEmpty()) {
                $this->subjects = Subject::orderBy('name')->get();
            }
        }
        $this->availableClasses = SchoolClass::whereIn('id', $pklClassIds)->orderBy('name')->get();

        // Populate from existing course
        $this->pkl_period_id = $course->pkl_period_id ?? '';
        $this->activity_id = $course->activity_id ?? '';
        $this->subject_id = $course->subject_id;
        $this->title = $course->title;
        $this->description = $course->description ?? '';
        $this->competency = $course->competency ?? '';
        $this->target_classes = array_map('strval', $course->target_classes ?? []);
        $this->start_date = $course->start_date?->format('Y-m-d') ?? '';
        $this->deadline = $course->deadline?->format('Y-m-d') ?? '';

        // Load materials
        $this->materials = [];
        foreach ($course->materials()->orderBy('order')->get() as $mat) {
            $this->existingMaterialIds[] = $mat->id;
            $this->materials[] = [
                'id' => $mat->id,
                'title' => $mat->title,
                'type' => $mat->type,
                'external_url' => $mat->external_url ?? '',
                'existing_file' => $mat->file_path,
            ];
        }

        // Load assignments
        $this->assignments = [];
        foreach ($course->assignments()->orderBy('order')->get() as $asg) {
            $this->existingAssignmentIds[] = $asg->id;
            $this->assignments[] = [
                'id' => $asg->id,
                'title' => $asg->title,
                'description' => $asg->description ?? '',
                'deadline' => $asg->deadline?->format('Y-m-d\TH:i') ?? '',
                'max_score' => $asg->max_score,
                'allow_late' => $asg->allow_late,
                'allow_file_upload' => $asg->allow_file_upload,
            ];
        }

        // Load quizzes with questions
        $this->quizzes = [];
        foreach ($course->quizzes()->with('questions')->orderBy('order')->get() as $quiz) {
            $this->existingQuizIds[] = $quiz->id;
            $questions = [];
            foreach ($quiz->questions()->orderBy('order')->get() as $q) {
                $questions[] = [
                    'id' => $q->id,
                    'question_type' => $q->question_type,
                    'question' => $q->question,
                    'options' => $q->options ?? ['', '', '', ''],
                    'correct_answer' => $q->correct_answer ?? '',
                    'score' => $q->score,
                ];
            }
            $this->quizzes[] = [
                'id' => $quiz->id,
                'title' => $quiz->title,
                'description' => $quiz->description ?? '',
                'duration_minutes' => $quiz->duration_minutes ?? 30,
                'deadline' => $quiz->deadline?->format('Y-m-d\TH:i') ?? '',
                'shuffle_questions' => $quiz->shuffle_questions,
                'questions' => $questions,
            ];
        }

        if (empty($this->materials)) $this->addMaterial();
        if (empty($this->assignments)) $this->addAssignment();
    }

    public function addMaterial() { $this->materials[] = ['title' => '', 'type' => 'pdf', 'external_url' => '']; }
    public function removeMaterial($i) { unset($this->materials[$i]); $this->materials = array_values($this->materials); }
    public function addAssignment() { $this->assignments[] = ['title' => '', 'description' => '', 'deadline' => '', 'max_score' => 100, 'allow_late' => false, 'allow_file_upload' => true]; }
    public function removeAssignment($i) { unset($this->assignments[$i]); $this->assignments = array_values($this->assignments); }
    public function addQuiz() { $this->quizzes[] = ['title' => '', 'description' => '', 'duration_minutes' => 30, 'deadline' => '', 'shuffle_questions' => false, 'questions' => [['question_type' => 'multiple_choice', 'question' => '', 'options' => ['','','',''], 'correct_answer' => '', 'score' => 10]]]; }
    public function removeQuiz($i) { unset($this->quizzes[$i]); $this->quizzes = array_values($this->quizzes); }
    public function addQuestion($qi) { $this->quizzes[$qi]['questions'][] = ['question_type' => 'multiple_choice', 'question' => '', 'options' => ['','','',''], 'correct_answer' => '', 'score' => 10]; }
    public function removeQuestion($qi, $qj) { unset($this->quizzes[$qi]['questions'][$qj]); $this->quizzes[$qi]['questions'] = array_values($this->quizzes[$qi]['questions']); }

    public function save($publish = false)
    {
        $this->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'target_classes' => 'required|array|min:1',
            'start_date' => 'required|date',
            'deadline' => 'required|date|after_or_equal:start_date',
        ]);

        $course = PklCourse::findOrFail($this->courseId);

        // Update course
        $course->update([
            'pkl_period_id' => $this->pkl_period_id ?: null,
            'activity_id' => $this->activity_id ?: null,
            'subject_id' => $this->subject_id,
            'title' => $this->title,
            'description' => $this->description,
            'competency' => $this->competency,
            'target_classes' => array_map('intval', $this->target_classes),
            'start_date' => $this->start_date,
            'deadline' => $this->deadline,
            'is_published' => $publish ? true : $course->is_published,
        ]);

        // Sync materials: delete removed, update existing, create new
        $keepMaterialIds = [];
        foreach ($this->materials as $i => $mat) {
            if (empty($mat['title'])) continue;

            $filePath = $mat['existing_file'] ?? null;
            $fileSize = null;
            if (isset($this->materialFiles[$i])) {
                $file = $this->materialFiles[$i];
                if (is_object($file) && method_exists($file, 'store')) {
                    try {
                        $realPath = $file->getRealPath();
                        if ($realPath && file_exists($realPath)) {
                            $ext = $file->getClientOriginalExtension() ?: 'pdf';
                            $fileName = 'pkl-materials/' . uniqid() . '_' . time() . '.' . $ext;
                            \Storage::disk('public')->put($fileName, file_get_contents($realPath));
                            $filePath = $fileName;
                            $fileSize = \Storage::disk('public')->size($filePath);
                            \Log::info('PKL Material file uploaded', ['path' => $filePath, 'size' => $fileSize]);
                        } else {
                            \Log::error('PKL Material: temp file not found', ['path' => $realPath]);
                        }
                    } catch (\Throwable $e) {
                        \Log::error('PKL Material upload failed', ['error' => $e->getMessage()]);
                    }
                }
            }

            if (isset($mat['id'])) {
                $updateData = [
                    'title' => $mat['title'], 'type' => $mat['type'],
                    'external_url' => $mat['external_url'] ?? null,
                    'file_path' => $filePath, 'order' => $i,
                ];
                if ($fileSize) $updateData['file_size'] = $fileSize;
                PklMaterial::where('id', $mat['id'])->update($updateData);
                $keepMaterialIds[] = $mat['id'];
            } else {
                $m = PklMaterial::create([
                    'pkl_course_id' => $course->id, 'title' => $mat['title'],
                    'type' => $mat['type'], 'file_path' => $filePath,
                    'external_url' => $mat['external_url'] ?? null,
                    'file_size' => $fileSize, 'order' => $i,
                ]);
                $keepMaterialIds[] = $m->id;
            }
        }
        PklMaterial::where('pkl_course_id', $course->id)->whereNotIn('id', $keepMaterialIds)->delete();

        // Sync assignments
        $keepAsgIds = [];
        foreach ($this->assignments as $i => $asg) {
            if (empty($asg['title'])) continue;
            $data = [
                'title' => $asg['title'], 'description' => $asg['description'] ?? null,
                'deadline' => $asg['deadline'] ?: $this->deadline . ' 23:59:00',
                'max_score' => $asg['max_score'] ?? 100,
                'allow_late' => $asg['allow_late'] ?? false,
                'allow_file_upload' => $asg['allow_file_upload'] ?? true, 'order' => $i,
            ];
            if (isset($asg['id'])) {
                PklAssignment::where('id', $asg['id'])->update($data);
                $keepAsgIds[] = $asg['id'];
            } else {
                $a = PklAssignment::create(array_merge($data, ['pkl_course_id' => $course->id]));
                $keepAsgIds[] = $a->id;
            }
        }
        PklAssignment::where('pkl_course_id', $course->id)->whereNotIn('id', $keepAsgIds)->delete();

        // Sync quizzes
        $keepQuizIds = [];
        foreach ($this->quizzes as $i => $quiz) {
            if (empty($quiz['title'])) continue;
            $quizData = [
                'title' => $quiz['title'], 'description' => $quiz['description'] ?? null,
                'duration_minutes' => $quiz['duration_minutes'] ?? null,
                'max_score' => collect($quiz['questions'] ?? [])->sum('score'),
                'deadline' => $quiz['deadline'] ?: $this->deadline . ' 23:59:00',
                'is_published' => $publish ? true : false,
                'shuffle_questions' => $quiz['shuffle_questions'] ?? false, 'order' => $i,
            ];
            if (isset($quiz['id'])) {
                PklQuiz::where('id', $quiz['id'])->update($quizData);
                $pklQuiz = PklQuiz::find($quiz['id']);
                $keepQuizIds[] = $quiz['id'];
            } else {
                $pklQuiz = PklQuiz::create(array_merge($quizData, ['pkl_course_id' => $course->id]));
                $keepQuizIds[] = $pklQuiz->id;
            }
            // Sync questions
            $keepQIds = [];
            foreach ($quiz['questions'] ?? [] as $j => $q) {
                if (empty($q['question'])) continue;
                $qData = [
                    'question_type' => $q['question_type'], 'question' => $q['question'],
                    'options' => $q['question_type'] === 'multiple_choice' ? ($q['options'] ?? []) : null,
                    'correct_answer' => $q['correct_answer'] ?? null, 'score' => $q['score'] ?? 10, 'order' => $j,
                ];
                if (isset($q['id'])) {
                    PklQuizQuestion::where('id', $q['id'])->update($qData);
                    $keepQIds[] = $q['id'];
                } else {
                    $newQ = PklQuizQuestion::create(array_merge($qData, ['pkl_quiz_id' => $pklQuiz->id]));
                    $keepQIds[] = $newQ->id;
                }
            }
            PklQuizQuestion::where('pkl_quiz_id', $pklQuiz->id)->whereNotIn('id', $keepQIds)->delete();
        }
        PklQuiz::where('pkl_course_id', $course->id)->whereNotIn('id', $keepQuizIds)->delete();

        session()->flash('success', 'Materi berhasil diupdate!');
        return redirect()->route('pkl-learning.dashboard');
    }

    public function render()
    {
        return view('livewire.pkl-learning.course-edit');
    }
}