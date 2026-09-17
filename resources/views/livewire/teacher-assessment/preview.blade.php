<div class="max-w-2xl mx-auto">

    {{-- Banner: Belum Dimulai --}}
    <div class="mb-6 rounded-xl border-2 border-yellow-300 bg-yellow-50 p-4 flex items-start gap-3">
        <svg class="h-6 w-6 text-yellow-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="font-semibold text-yellow-800">Kuis Belum Dibuka</p>
            <p class="text-sm text-yellow-700 mt-0.5">
                Kuis ini dimulai pada
                <strong>{{ $assessment->start_date->translatedFormat('l, d F Y') }}
                pukul {{ $assessment->start_time ? substr($assessment->start_time, 0, 5) : '00:00' }} WIB</strong>.
                Soal hanya bisa dikerjakan setelah kuis dibuka.
            </p>
            {{-- Countdown --}}
            <div id="countdown-{{ $assessment->id }}"
                class="mt-2 text-lg font-bold text-yellow-800"
                data-target="{{ $assessment->getOpenDatetime()->toIso8601String() }}">
            </div>
        </div>
    </div>

    {{-- Info Asesmen (tanpa soal) --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <h1 class="text-xl font-bold text-gray-800">{{ $assessment->title }}</h1>
        @if($assessment->description)
            <p class="mt-2 text-sm text-gray-600">{{ $assessment->description }}</p>
        @endif

        <div class="mt-4 flex flex-wrap gap-4 text-sm text-gray-500 border-t border-gray-100 pt-4">
            <span class="flex items-center gap-1.5">
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                {{ $questions->count() }} soal
            </span>
            <span class="flex items-center gap-1.5">
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Berakhir: {{ $assessment->end_date->translatedFormat('d M Y') }}
                {{ $assessment->end_time ? substr($assessment->end_time, 0, 5) : '' }}
            </span>
            <span class="flex items-center gap-1.5">
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                {{ $assessment->creator->name ?? '-' }}
            </span>
        </div>
    </div>

    {{-- Pesan keamanan --}}
    <div class="mt-4 rounded-xl border border-gray-200 bg-gray-50 p-4 flex items-start gap-3">
        <svg class="h-5 w-5 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
        <p class="text-sm text-gray-500">
            Soal akan tampil setelah kuis dibuka. Pastikan Anda siap saat kuis dimulai.
        </p>
    </div>

    {{-- Tombol kembali --}}
    <div class="mt-6 flex justify-center">
        <a href="{{ route('student.assessment.index') }}" wire:navigate
           class="rounded-lg border border-gray-300 bg-white px-5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
            ← Kembali ke Daftar Kuis
        </a>
    </div>

</div>

{{-- Countdown script --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const els = document.querySelectorAll('[data-target]');
    els.forEach(el => {
        const target = new Date(el.dataset.target).getTime();
        function update() {
            const diff = target - Date.now();
            if (diff <= 0) {
                el.textContent = 'Kuis sudah dibuka! Refresh halaman.';
                return;
            }
            const d = Math.floor(diff / 86400000);
            const h = Math.floor((diff % 86400000) / 3600000);
            const m = Math.floor((diff % 3600000) / 60000);
            const s = Math.floor((diff % 60000) / 1000);
            el.textContent = 'Dimulai dalam: ' + (d > 0 ? d + ' hari ' : '')
                + String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
            setTimeout(update, 1000);
        }
        update();
    });
});
document.addEventListener('livewire:navigated', function () {
    const els = document.querySelectorAll('[data-target]');
    els.forEach(el => {
        const target = new Date(el.dataset.target).getTime();
        function update() {
            const diff = target - Date.now();
            if (diff <= 0) { el.textContent = 'Kuis sudah dibuka! Refresh halaman.'; return; }
            const d = Math.floor(diff / 86400000);
            const h = Math.floor((diff % 86400000) / 3600000);
            const m = Math.floor((diff % 3600000) / 60000);
            const s = Math.floor((diff % 60000) / 1000);
            el.textContent = 'Dimulai dalam: ' + (d > 0 ? d + ' hari ' : '')
                + String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
            setTimeout(update, 1000);
        }
        update();
    });
});
</script>
