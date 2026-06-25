<?php

namespace App\Livewire\User;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterRole = 'all'; // all, admin, waka_kurikulum, guru

    protected $queryString = [
        'search' => ['except' => ''],
        'filterRole' => ['except' => 'all'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterRole()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        if (!auth()->user()->canManageUsers()) {
            session()->flash('error', 'Anda tidak memiliki akses untuk menghapus user.');
            return;
        }

        $user = User::findOrFail($id);

        // Prevent deleting self
        if ($user->id === auth()->id()) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            return;
        }

        // Prevent deleting if user has activities
        if ($user->activities()->count() > 0) {
            session()->flash('error', "User '{$user->name}' tidak dapat dihapus karena memiliki {$user->activities()->count()} kegiatan yang terkait.");
            return;
        }

        $name = $user->name;
        $user->delete();

        ActivityLog::createLog(
            action: 'delete',
            modelType: 'User',
            modelId: $id,
            description: "Menghapus user: {$name}"
        );

        session()->flash('success', 'User berhasil dihapus!');
    }

    public function resetPassword($id)
    {
        if (!auth()->user()->canManageUsers()) {
            session()->flash('error', 'Anda tidak memiliki akses untuk reset password.');
            return;
        }

        $user = User::findOrFail($id);
        
        // Reset password to default: 'password'
        $user->update([
            'password' => Hash::make('password'),
        ]);

        ActivityLog::createLog(
            action: 'update',
            modelType: 'User',
            modelId: $user->id,
            description: "Reset password user: {$user->name}"
        );

        session()->flash('success', "Password user '{$user->name}' berhasil direset ke 'password'!");
    }

    #[Layout('components.layouts.app')]
    #[Title('Pengguna - e-KALDIK')]
    public function render()
    {
        $query = User::query();

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('username', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        // Filter by role
        if ($this->filterRole !== 'all') {
            $query->where('role', $this->filterRole);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('livewire.user.index', [
            'users' => $users,
        ]);
    }
}
