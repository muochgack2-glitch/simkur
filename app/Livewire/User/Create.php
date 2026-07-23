<?php

namespace App\Livewire\User;

use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Subject;
use App\Models\SchoolClass;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Create extends Component
{
    // Common fields
    public $name = '';
    public $username = '';
    public $email = '';
    public $password = 'password';
    public $role = 'guru';
    public $is_active = true;

    // Teacher fields
    public $nip_nuptk = '';
    public $beban_mengajar = null;
    public $taught_majors = [];
    public $subject_ids = [];

    // Student fields
    public $nisn = '';
    public $nis = '';
    public $no_hp = '';
    public $parent_name = '';
    public $parent_phone = '';
    public $grade = null;
    public $major = null;
    public $class_id = null;
    public $is_pkl = false;
    public $is_teaching_factory = false;

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username|alpha_dash',
            'email' => 'nullable|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,waka_kurikulum,kepala_sekolah,guru,siswa',
            'is_active' => 'boolean',
        ];

        // Teacher-specific rules
        if ($this->role === 'guru') {
            $rules['nip_nuptk'] = 'nullable|string|max:20|unique:users,nip_nuptk';
            $rules['beban_mengajar'] = 'nullable|integer|min:0|max:40';
            $rules['taught_majors'] = 'nullable|array';
            $rules['subject_ids'] = 'nullable|array';
        }

        // Student-specific rules
        if ($this->role === 'siswa') {
            $rules['nisn'] = 'nullable|string|max:10|unique:users,nisn';
            $rules['nis'] = 'nullable|string|max:10|unique:users,nis';
            $rules['no_hp'] = 'nullable|string|max:15';
            $rules['parent_name'] = 'nullable|string|max:255';
            $rules['parent_phone'] = 'nullable|string|max:15';
            $rules['grade'] = 'required|in:X,XI,XII';
            $rules['major'] = 'required|in:MPLB,AKL,BUSANA';
            $rules['class_id'] = 'nullable|exists:classes,id';
            $rules['is_pkl'] = 'boolean';
            $rules['is_teaching_factory'] = 'boolean';
        }

        return $rules;
    }

    public function generateUsername()
    {
        if (!empty($this->name)) {
            $username = strtolower(str_replace(' ', '', $this->name));
            $username = preg_replace('/[^a-z0-9_]/', '', $username);
            
            if (strlen($username) > 20) {
                $username = substr($username, 0, 20);
            }
            
            $this->username = $username;
        }
    }

    public function save()
    {
        if (!auth()->user()->canManageUsers()) {
            $this->addError('error', 'Anda tidak memiliki akses untuk menambah user.');
            return;
        }

        $this->validate();

        if (auth()->user()->isKepalaSekolah() && in_array($this->role, ['admin', 'kepala_sekolah'])) {
            $this->addError('role', 'Anda tidak dapat menambahkan user dengan role Admin atau Kepala Sekolah.');
            return;
        }

        $userData = [
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email ?: null,
            'password' => Hash::make($this->password),
            'role' => $this->role,
            'is_active' => $this->is_active,
        ];

        // Add teacher fields
        if ($this->role === 'guru') {
            $userData['nip_nuptk'] = $this->nip_nuptk ?: null;
            $userData['beban_mengajar'] = $this->beban_mengajar ?: null;
            $userData['taught_majors'] = !empty($this->taught_majors) ? $this->taught_majors : null;
        }

        // Add student fields
        if ($this->role === 'siswa') {
            $userData['nisn'] = $this->nisn ?: null;
            $userData['nis'] = $this->nis ?: null;
            $userData['no_hp'] = $this->no_hp ?: null;
            $userData['parent_name'] = $this->parent_name ?: null;
            $userData['parent_phone'] = $this->parent_phone ?: null;
            $userData['grade'] = $this->grade;
            $userData['major'] = $this->major;
            $userData['class_id'] = $this->class_id ?: null;
            $userData['is_pkl'] = $this->is_pkl;
            $userData['is_teaching_factory'] = $this->is_teaching_factory;
        }

        $user = User::create($userData);

        // Attach subjects for teacher
        if ($this->role === 'guru' && !empty($this->subject_ids)) {
            $user->subjects()->attach($this->subject_ids);
        }

        ActivityLog::createLog(
            action: 'create',
            modelType: 'User',
            modelId: $user->id,
            description: "Membuat user baru: {$user->name} ({$user->role})"
        );

        session()->flash('success', 'User berhasil ditambahkan!');
        return redirect()->route('users.index');
    }

    #[Layout('components.layouts.app')]
    #[Title('Tambah Pengguna - SIM Kurikulum SMK PGRI Blora')]
    public function render()
    {
        $subjects = Subject::active()->orderBy('name')->get();
        $classes = SchoolClass::with('academicYear')
            ->whereHas('academicYear', fn($q) => $q->where('is_active', true))
            ->orderBy('grade')
            ->orderBy('major')
            ->get();

        return view('livewire.user.create', [
            'subjects' => $subjects,
            'classes' => $classes,
        ]);
    }
}
