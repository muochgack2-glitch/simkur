<?php

namespace App\Livewire\ClassPromotion;

use App\Models\ClassPromotion;
use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class History extends Component
{
    use WithPagination;

    public $selectedPromotion;
    public $confirmingRollback = false;
    public $rollbackPromotionId;

    public function viewDetail($promotionId)
    {
        $this->selectedPromotion = ClassPromotion::with([
            'fromAcademicYear',
            'toAcademicYear',
            'processedBy'
        ])->find($promotionId);
    }

    public function closeDetail()
    {
        $this->selectedPromotion = null;
    }

    public function confirmRollback($promotionId)
    {
        $this->rollbackPromotionId = $promotionId;
        $this->confirmingRollback = true;
    }

    public function cancelRollback()
    {
        $this->rollbackPromotionId = null;
        $this->confirmingRollback = false;
    }

    public function rollbackPromotion()
    {
        try {
            DB::beginTransaction();

            $promotion = ClassPromotion::with(['fromAcademicYear', 'toAcademicYear'])
                ->findOrFail($this->rollbackPromotionId);

            // Check if can rollback
            if (!$promotion->canRollback()) {
                session()->flash('error', 'Promosi ini tidak dapat di-undo. Hanya promosi terakhir yang bisa di-undo.');
                $this->cancelRollback();
                return;
            }

            // Restore each student to their previous state
            if (!empty($promotion->student_details)) {
                foreach ($promotion->student_details as $detail) {
                    $student = User::find($detail['student_id']);
                    
                    if ($student) {
                        $student->update([
                            'class_id' => $detail['previous_class_id'],
                            'grade' => $detail['previous_grade'],
                            'is_alumni' => $detail['previous_is_alumni'],
                            'graduation_year' => null,
                            'alumni_notes' => null,
                        ]);
                    }
                }
            }

            // Mark promotion as rolled back
            $promotion->update([
                'is_rolled_back' => true,
                'rolled_back_at' => now(),
                'rolled_back_by' => auth()->id(),
            ]);

            // Revert active academic year
            $promotion->toAcademicYear->update(['is_active' => false]);
            $promotion->fromAcademicYear->update(['is_active' => true]);

            DB::commit();

            session()->flash('success', 'Kenaikan kelas berhasil di-undo. Semua siswa dikembalikan ke kelas sebelumnya.');
            $this->cancelRollback();
            $this->closeDetail();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal undo: ' . $e->getMessage());
            $this->cancelRollback();
        }
    }

    #[Layout('components.layouts.app')]
    #[Title('Riwayat Kenaikan Kelas - SIM Kurikulum SMK PGRI Blora')]
    public function render()
    {
        $promotions = ClassPromotion::with([
            'fromAcademicYear',
            'toAcademicYear',
            'processedBy',
            'rolledBackBy'
        ])
        ->orderBy('processed_at', 'desc')
        ->paginate(10);

        return view('livewire.class-promotion.history', [
            'promotions' => $promotions,
        ]);
    }
}
