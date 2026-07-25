<?php

namespace App\Livewire\ClassPromotion;

use App\Models\ClassPromotion;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class History extends Component
{
    use WithPagination;

    public $selectedPromotion;

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

    #[Layout('components.layouts.app')]
    #[Title('Riwayat Kenaikan Kelas - SIM Kurikulum SMK PGRI Blora')]
    public function render()
    {
        $promotions = ClassPromotion::with([
            'fromAcademicYear',
            'toAcademicYear',
            'processedBy'
        ])
        ->orderBy('processed_at', 'desc')
        ->paginate(10);

        return view('livewire.class-promotion.history', [
            'promotions' => $promotions,
        ]);
    }
}
