{{-- Gambar soal: posisi above/below/beside_left --}}
@if($question->image_path)
    @php $imgUrl = Storage::url($question->image_path); @endphp
    @if($question->image_position === 'beside_left')
        {{-- Layout flex: gambar kiri, teks kanan --}}
        <div class="flex gap-4 items-start">
            <img src="{{ $imgUrl }}" alt="Gambar soal"
                 class="rounded-lg border border-gray-200 object-contain max-h-48 w-48 shrink-0 shadow-sm">
            <div class="flex-1">
                {{ $slot }}
            </div>
        </div>
    @elseif($question->image_position === 'below')
        {{ $slot }}
        <img src="{{ $imgUrl }}" alt="Gambar soal"
             class="mt-3 rounded-lg border border-gray-200 object-contain max-h-64 w-full shadow-sm">
    @else {{-- above (default) --}}
        <img src="{{ $imgUrl }}" alt="Gambar soal"
             class="mb-3 rounded-lg border border-gray-200 object-contain max-h-64 w-full shadow-sm">
        {{ $slot }}
    @endif
@else
    {{ $slot }}
@endif