# Update: Kalender Publik - Table View untuk Semua Kelas

## Yang Perlu Diubah

Di file `resources/views/kaldik/index.blade.php`, bagian "PAGE 3: PERHITUNGAN HARI EFEKTIF" (sekitar line 590-750), ganti logic card dengan:

### **Ketika "Semua Kelas":**
- Tampilkan **TABLE** dengan 3 baris (X, XI, XII)
- Kolom: Kelas | Periode | Hari Belajar | Minggu Efektif | Persentase
- Summary stats di bawah (Total/Weekend/Libur/Ujian)

### **Ketika pilih grade tertentu (X/XI/XII):**
- Tampilkan **CARD** dengan stat boxes (6 boxes seperti sekarang)
- Plus period info dan badge "Selesai Lebih Cepat"

## Code Structure

```blade
@if(!$selectedGrade && $effectiveDay->byGrades->isNotEmpty())
    <!-- TABLE VIEW -->
    <table>
        @foreach($effectiveDay->byGrades as $gradeData)
            <tr>
                <td>Badge + Kelas X</td>
                <td>15 Jul - 31 Des + badge jika early</td>
                <td>102 hari (besar)</td>
                <td>20.40 minggu</td>
                <td>83.61% + progress bar + status</td>
            </tr>
        @endforeach
    </table>
    
    <!-- Summary: Total/Weekend/Libur/Ujian dalam 4 boxes kecil -->
@else
    <!-- CARD VIEW (existing 6 stat boxes) -->
@endif
```

File terlalu besar untuk di-replace sekaligus. 

Mau saya buatkan file terpisah dengan code lengkapnya, atau langsung saya edit di file aslinya dengan multiple replace?
