# Fix: Live JP Indicator Hilang Saat Auto-Refresh

## Masalah
Live JP Indicator (floating badge di kanan bawah) hilang setiap kali auto-refresh Livewire terjadi (setiap 2 menit).

## Penyebab
Live JP Indicator berada **di dalam** div utama Livewire (`<div wire:poll.120s="refresh">`). Ketika Livewire melakukan auto-refresh, seluruh konten di dalam div tersebut di-replace, termasuk Live JP Indicator meskipun sudah menggunakan `wire:ignore`.

## Solusi
**Memindahkan Live JP Indicator keluar dari div Livewire**, sehingga tidak terpengaruh oleh update Livewire.

### Struktur Sebelumnya (❌ Salah)
```blade
<div wire:poll.120s="refresh" class="min-h-screen bg-gray-50">
    <!-- Content -->
    
    <!-- Live JP Indicator - MASIH DI DALAM LIVEWIRE -->
    <div wire:ignore id="liveJpIndicator">
        ...
    </div>
    
    <!-- Footer -->
</div>
```

### Struktur Sekarang (✅ Benar)
```blade
<div wire:poll.120s="refresh" class="min-h-screen bg-gray-50">
    <!-- Content -->
    
    <!-- Footer -->
</div>

<!-- Live JP Indicator - SEKARANG DI LUAR LIVEWIRE -->
<div id="liveJpIndicator" class="fixed bottom-6 right-6 z-40 hidden">
    ...
</div>

@push('scripts')
    <!-- JavaScript tetap berfungsi -->

```

## Penjelasan Teknis

1. **Livewire `wire:poll`**: Ketika menggunakan `wire:poll.120s="refresh"`, Livewire akan:
   - Memanggil method `refresh()` di server setiap 2 menit
   - Menerima HTML response dari server
   - **Mengganti seluruh konten** di dalam div yang memiliki `wire:poll`

2. **`wire:ignore` tidak cukup**: Meskipun `wire:ignore` mencegah Livewire meng-update elemen tersebut, tetapi ketika **parent div di-replace**, semua child elements (termasuk yang pakai `wire:ignore`) ikut hilang dari DOM.

3. **Solusi dengan positioning**: Dengan memindahkan Live JP Indicator keluar dari Livewire component, dan menggunakan `position: fixed`, indicator tetap:
   - Tampil di layar pada posisi yang sama
   - Tidak terpengaruh Livewire updates
   - JavaScript tetap berfungsi karena `updateLiveJpIndicator()` menggunakan `getElementById()` yang tidak bergantung pada Livewire

## Fitur Live JP Indicator Tetap Berfungsi

✅ Real-time update setiap 60 detik  
✅ Deteksi JP aktif berdasarkan jam sekarang  
✅ Styling berbeda untuk Jam Pelajaran (merah 🔴) vs Istirahat (orange ☕)  
✅ Countdown sisa waktu  
✅ Auto-hide ketika di luar jam sekolah  
✅ Tidak hilang saat auto-refresh  

## File yang Diubah

- `resources/views/livewire/journal-monitoring/index.blade.php`
  - Memindahkan `<div id="liveJpIndicator">` keluar dari `<div wire:poll.120s="refresh">`
  - JavaScript tetap di `@push('scripts')` dan tetap berfungsi normal

## Testing

1. Buka halaman monitoring: `/monitoring/jurnal-hari-ini`
2. Tunggu 2 menit hingga auto-refresh terjadi
3. Live JP Indicator seharusnya **tetap tampil** dan tidak hilang
4. Indikator tetap update setiap 1 menit sesuai jam aktif

## Catatan

- Live JP Indicator menggunakan `position: fixed` sehingga tidak ikut scroll
- `z-index: 40` memastikan indicator tampil di atas konten lain
- Element `hidden` ketika tidak ada JP aktif (di luar jam sekolah)
