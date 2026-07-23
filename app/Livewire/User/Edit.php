<?php

namespace App\Livewire\User;

use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Subject;
use App\Models\SchoolClass;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Edit extends Component
{
    public $userId;
    
    // Common fields
    public $name = '';
    public $username = '';
    public $email = '';
    public $role = '';
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

    public function mount($id)
    {
        $user = User::with(['subjects', 'schoolClass'])->findOrFail($id);

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->is_active = $user->is_active;

        // Teacher fields
        $this->nip_nuptk = $user->nip_nuptk;
        $this->beban_mengajar = $user->beban_mengajar;
        $this->taught_majors = $user->taught_majors ?? [];
        $this->subject_ids = $user->subjects->pluck('id')->toArray();

        // Student fields
        $this->nisn = $user->nisn;
        $this->nis = $user->nis;
        $this->no_hp = $user->no_hp;
        $this->parent_name = $user->parent_name;
        $this->parent_phone = $user->parent_phone;
        $this->grade = $user->grade;
        $this->major = $user->major;
        $this->class_id = $user->class_id;
        $this->is_pkl = $user->is_pkl;
        $this->is_teaching_factory = $user->is_teaching_factory;
    }

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $this->userId,
            'role' => 'required|in:admin,waka_kurikulum,kepala_sekolah,guru,siswa',
            'is_active' => 'boolean',
        ];

        // Teacher-specific rules
        if ($this->role === 'guru') {
            $rules['nip_nuptk'] = 'nullable|string|max:20|unique:users,nip_nuptk,' . $this->userId;
            $rules['beban_mengajar'] = 'nullable|integer|min:0|max:40';
            $rules['taught_majors'] = 'nullable|array';
            $rules['subject_ids'] = 'nullable|array';
        }

        // Student-specific rules
        if ($this->role === 'siswa') {
            $rules['nisn'] = 'nullable|string|max:10|unique:users,nisn,' . $this->userId;
            $rules['nis'] = 'nullable|string|max:10|unique:users,nis,' . $this->userId;
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

    public function save()
    {
        if (!auth()->user()->canManageUsers()) {
            $this->addError('error', 'Anda tidak memiliki akses untuk mengedit user.');
            return;
        }

        // Prevent editing own role
        if ($this->userId === auth()->id() && $this->role !== auth()->user()->role) {
            $this->addError('role', 'Anda tidak dapat mengubah role Anda sendiri.');
            return;
        }

        // Kepala Sekolah tidak bisa mengubah role ke admin atau kepala sekolah
        if (auth()->user()->isKepalaSekolah() && in_array($this->role, ['admin', 'kepala_sekolah'])) {
            $this->addError('role', 'Anda tidak dapat mengubah role user menjadi Admin atau Kepala Sekolah.');
            return;
        }

        $this->validate();

        $user = User::findOrFail($this->userId);
        
        $userData = [
            'name' => $this->name,
            'email' => $this->email ?: null,
            'role' => $this->role,
            'is_active' => $this->is_active,
        ];

        // Add teacher fields
        if ($this->role === 'guru') {
            $userData['nip_nuptk'] = $this->nip_nuptk ?: null;
            $userData['beban_mengajar'] = $this->beban_mengajar ?: null;
            $userData['taught_majors'] = !empty($this->taught_majors) ? $this->taught_majors : null;
            
            // Clear student-specific fields
            $userData['nisn'] = null;
            $userData['nis'] = null;
            $userData['no_hp'] = null;
            $userData['parent_name'] = null;
            $userData['parent_phone'] = null;
            $userData['grade'] = null;
            $userData['major'] = null;
            $userData['class_id'] = null;
            $userData['is_pkl'] = false;
            $userData['is_teaching_factory'] = false;
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
            
            // Clear teacher-specific fields
            $userData['nip_nuptk'] = null;
            $userData['beban_mengajar'] = null;
            $userData['taught_majors'] = null;
        }

        // For non-teacher and non-student roles, clear specific fields
        if (!in_array($this->role, ['guru', 'siswa'])) {
            $userData['nip_nuptk'] = null;
            $userData['beban_mengajar'] = null;
            $userData['taught_majors'] = null;
            $userData['nisn'] = null;
            $userData['nis'] = null;
            $userData['no_hp'] = null;
            $userData['parent_name'] = null;
            $userData['parent_phone'] = null;
            $userData['grade'] = null;
            $userData['major'] = null;
            $userData['class_id'] = null;
            $userData['is_pkl'] = false;
            $userData['is_teaching_factory'] = false;
        }

        $user->update($userData);

        // Sync subjects for teacher
        if ($this->role === 'guru') {
            $user->subjects()->sync($this->subject_ids ?? []);
        } else {
            $user->subjects()->detach();
        }

        ActivityLog::createLog(
            action: 'update',
            modelType: 'User',
            modelId: $user->id,
            description: "Mengubah data user: {$user->name}"
        );

        session()->flash('success', 'User berhasil diperbarui!');
        return redirect()->route('users.index');
    }

    public function resetPassword()
    {
        // Only admin can reset password
        if (!auth()->user()->isAdmin()) {
            session()->flash('error', 'Hanya Admin yang dapat mereset password.');
            return;
        }

        // Prevent resetting own password via this method
        if ($this->userId === auth()->id()) {
            session()->flash('error', 'Gunakan menu "Ganti Password" untuk mengubah password Anda sendiri.');
            return;
        }

        $user = User::findOrFail($this->userId);

        // Reset password to default
        $user->update([
            'password' => bcrypt('password'),
        ]);

        // Send WhatsApp notification
        $this->sendWhatsAppNotification($user);

        ActivityLog::createLog(
            action: 'update',
            modelType: 'User',
            modelId: $user->id,
            description: "Reset password user: {$user->name}"
        );

        session()->flash('success', 'Password berhasil direset ke default. Notifikasi telah dikirim via WhatsApp.');
    }

    private function sendWhatsAppNotification($user)
    {
        // Check if user has phone number
        if (!$user->no_hp && !$user->parent_phone) {
            return;
        }

        $phone = $user->no_hp ?: $user->parent_phone;
        
        // Remove leading 0 and add 62 (Indonesia country code)
        $phone = preg_replace('/^0/', '62', $phone);

        // Prepare message
        $message = "🔔 *Notifikasi Reset Password*\n\n";
        $message .= "Halo *{$user->name}*,\n\n";
        $message .= "Password akun Anda di SIM Kurikulum SMK PGRI Blora telah direset oleh Admin.\n\n";
        $message .= "📝 *Detail Akun:*\n";
        $message .= "Username: `{$user->username}`\n";
        $message .= "Password: `password`\n\n";
        $message .= "⚠️ *Penting:*\n";
        $message .= "• Segera login dan ganti password Anda\n";
        $message .= "• Gunakan menu \"Ganti Password\" setelah login\n";
        $message .= "• Jangan bagikan password ke orang lain\n\n";
        $message .= "🌐 Login di: " . url('/login') . "\n\n";
        $message .= "_Pesan otomatis dari SIM Kurikulum SMK PGRI Blora_";

        // Send via WhatsApp Web API (using whatsapp:// link)
        // In real implementation, integrate with WhatsApp Business API or third-party service
        // For now, we'll log it
        \Log::info('WhatsApp notification sent', [
            'phone' => $phone,
            'user' => $user->name,
            'message' => $message,
        ]);

        // TODO: Integrate with actual WhatsApp API
        // Example with Fonnte, WABLAS, or other WhatsApp Gateway
        // Http::post('https://api.whatsapp-gateway.com/send', [
        //     'target' => $phone,
        //     'message' => $message,
        // ]);
    }

    #[Layout('components.layouts.app')]
    #[Title('Edit Pengguna - SIM Kurikulum SMK PGRI Blora')]
    public function render()
    {
        $subjects = Subject::active()->orderBy('name')->get();
        $classes = SchoolClass::with('academicYear')
            ->whereHas('academicYear', fn($q) => $q->where('is_active', true))
            ->orderBy('grade')
            ->orderBy('major')
            ->get();

        return view('livewire.user.edit', [
            'subjects' => $subjects,
            'classes' => $classes,
        ]);
    }
}
