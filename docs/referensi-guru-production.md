# Data Guru, Waka, dan Kepala Sekolah - Production Simkur

> Diambil dari database production pada 17 Agustus 2026
> Role: guru, waka_kurikulum, kepala_sekolah, dmin

## Kepala Sekolah

| ID | Nama | Singkatan |
|----|------|-----------|
| 25 | Meiranti Trisnaning S., S.Pd | Meiranti |

## Waka Kurikulum

| ID | Nama | Singkatan |
|----|------|-----------|
| 175 | Muhammad Huda Muttaqin, S.Pd.I | Huda |

## Guru (21 orang)

| ID | Nama Lengkap | Singkatan / LIKE search |
|----|-------------|-------------------------|
| 19 | Ade Rua Nur Lemoniar, S.Pd | %ade rua% |
| 29 | Adela Wulan Kurniasari, S.Pd | %adela% |
| 27 | Ari Yunitasari, S.Pd | %ari yunit% |
| 13 | Budi Siswanto, S.Pd.I | %budi sis% |
| 26 | Debby Furi Wijayanti, S.Pd | %debby% |
| 18 | Dewi Wartini, S.Pd | %dewi wart% |
| 21 | Dhani Kisworo Jati, S.Pd | %dhani% |
| 12 | Drs. Suseno | %suseno% |
| 32 | Eko Budhi Lestari, S.Pd.B | %eko bud% |
| 28 | Ervinda Sekar Asmara, S.Pd | %ervinda% |
| 31 | Guru BTQ | %btq% |
| 15 | Ilham Hardiyan P., S.Pd | %ilham% |
| 20 | Liliyana Ayu W., S.Pd | %liliyana% |
| 30 | Marista Bela Octaviana, S.Pd | %marista% |
| 23 | Munisah, S.Pd | %munisah% |
| 17 | Nia Dani Rahayu, S.Pd | %nia dani% |
| 16 | Pancawati Puji L., A.Md | %pancawati% |
| 33 | Rinawati, S.Pd | %rinawati% |
| 22 | Tri Mulyaniningsih, S.E | %tri mulya% |
| 24 | Wiwit Mergi W., A.Md | %wiwit% |
| 14 | Yully Setyo A., S.Pd | %yully% |

## Admin

| ID | Nama |
|----|------|
| 1  | Administrator |

## Catatan Seeder

- Role di production: guru, siswa, waka_kurikulum, kepala_sekolah, dmin
- Cari guru di seeder pakai LIKE: whereRaw('LOWER(name) LIKE ?', ['%nama%'])
- Cari siswa pakai: where('role', 'siswa')->where('major', 'AKL')