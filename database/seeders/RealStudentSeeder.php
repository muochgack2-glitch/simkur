<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RealStudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data siswa asli dari SMK PGRI Blora
     */
    public function run(): void
    {
        $this->command->info('🎓 Importing real student data...');

        // Get class IDs
        $classXAKL = DB::table('classes')->where('name', 'X AKL')->first();
        $classXMPLB = DB::table('classes')->where('name', 'X MPLB')->first();
        $classXBusana = DB::table('classes')->where('name', 'X Busana')->first();
        $classXIAKL = DB::table('classes')->where('name', 'XI AKL')->first();
        $classXIBusana = DB::table('classes')->where('name', 'XI Busana')->first();
        $classXIMPLB = DB::table('classes')->where('name', 'XI MPLB')->first();
        $classXIIAKL = DB::table('classes')->where('name', 'XII AKL')->first();
        $classXIIBusana = DB::table('classes')->where('name', 'XII Busana')->first();
        $classXIIMPLB = DB::table('classes')->where('name', 'XII MPLB')->first();
        
        if (!$classXAKL) {
            $this->command->error('❌ Kelas X AKL tidak ditemukan!');
            return;
        }
        
        if (!$classXMPLB) {
            $this->command->error('❌ Kelas X MPLB tidak ditemukan!');
            return;
        }
        
        if (!$classXBusana) {
            $this->command->error('❌ Kelas X Busana tidak ditemukan!');
            return;
        }
        
        if (!$classXIAKL) {
            $this->command->error('❌ Kelas XI AKL tidak ditemukan!');
            return;
        }
        
        if (!$classXIBusana) {
            $this->command->error('❌ Kelas XI Busana tidak ditemukan!');
            return;
        }
        
        if (!$classXIMPLB) {
            $this->command->error('❌ Kelas XI MPLB tidak ditemukan!');
            return;
        }
        
        if (!$classXIIAKL) {
            $this->command->error('❌ Kelas XII AKL tidak ditemukan!');
            return;
        }
        
        if (!$classXIIBusana) {
            $this->command->error('❌ Kelas XII Busana tidak ditemukan!');
            return;
        }
        
        if (!$classXIIMPLB) {
            $this->command->error('❌ Kelas XII MPLB tidak ditemukan!');
            return;
        }

        // X AKL - Wali Kelas: Dhani Kisworo Jati, S.Pd.
        $studentsXAKL = [
            ['urut' => 1, 'induk' => '2024', 'name' => 'AHMAD BAGUS NUR HIDAYAT'],
            ['urut' => 2, 'induk' => '2025', 'name' => 'AYUNDA VIVI ANDILLA'],
            ['urut' => 3, 'induk' => '2026', 'name' => 'DWI YANTI WAHYUNINGRUM'],
            ['urut' => 4, 'induk' => '2027', 'name' => 'LELY AYU NOVITASARI'],
            ['urut' => 5, 'induk' => '2028', 'name' => 'LINDA CAHAYA'],
            ['urut' => 6, 'induk' => '2029', 'name' => 'MEYLANI PUTRI PUSPITASARI'],
            ['urut' => 7, 'induk' => '2030', 'name' => 'NABILA UFAIRA'],
            ['urut' => 8, 'induk' => '2031', 'name' => 'NARA ILMALIS SAFALA BIBAH'],
            ['urut' => 9, 'induk' => '2032', 'name' => 'NEYSA AYUNINGRUM'],
            ['urut' => 10, 'induk' => '2033', 'name' => 'NOVI AMBARWATI'],
            ['urut' => 11, 'induk' => '2034', 'name' => 'REZA NANDA NOVELIA CAHAYA'],
            ['urut' => 12, 'induk' => '2035', 'name' => 'SANTI WULAN DHARI'],
        ];

        $insertedCount = 0;

        // Insert X AKL students
        foreach ($studentsXAKL as $student) {
            // Generate username from name (lowercase, no spaces)
            $username = strtolower(str_replace(' ', '', $student['name']));
            
            // Generate NISN (using induk for now, can be updated later with real NISN)
            $nisn = $student['induk'];

            DB::table('users')->insert([
                'name' => $student['name'],
                'username' => $username,
                'email' => $username . '@smkpgriblora.sch.id',
                'password' => Hash::make('password'), // Default password
                'role' => 'siswa',
                'nisn' => $nisn,
                'nis' => $nisn, // Using same as NISN for now
                'class_id' => $classXAKL->id,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $insertedCount++;
        }

        $this->command->info("✅ $insertedCount siswa X AKL berhasil diimport!");

        // X MPLB - Wali Kelas: Pancawati Puji Lestari, A. Md.
        $studentsXMPLB = [
            ['urut' => 1, 'induk' => '2036', 'name' => 'ALISIA CITTA GOTAMI'],
            ['urut' => 2, 'induk' => '2037', 'name' => 'ALYSA KHANZA SAFIRA'],
            ['urut' => 3, 'induk' => '2038', 'name' => 'APPRILIAN TIKA NOFITA PUTRI MAHARANI'],
            ['urut' => 4, 'induk' => '2039', 'name' => 'APRELIA IVA LATIVA'],
            ['urut' => 5, 'induk' => '2040', 'name' => 'APRILIA CANDU PAMUNGKAS'],
            ['urut' => 6, 'induk' => '2041', 'name' => 'AULIA BAROKHATU NIKMA'],
            ['urut' => 7, 'induk' => '2042', 'name' => 'DENIS VIKY APRILIA'],
            ['urut' => 8, 'induk' => '2043', 'name' => 'DHINA CANTIK NURADNI'],
            ['urut' => 9, 'induk' => '2044', 'name' => 'EVA SAPUTRI'],
            ['urut' => 10, 'induk' => '2045', 'name' => 'KEZIA ANASTASIA RIYANTO'],
            ['urut' => 11, 'induk' => '2046', 'name' => 'LAILA SELVI'],
            ['urut' => 12, 'induk' => '2047', 'name' => 'LATISYA PUTRI KIRANA'],
            ['urut' => 13, 'induk' => '2048', 'name' => 'LIVIA SANTIKA'],
            ['urut' => 14, 'induk' => '2049', 'name' => 'LUTHFI NADIFA RAMADHANI'],
            ['urut' => 15, 'induk' => '2050', 'name' => 'MAR SYANTI'],
            ['urut' => 16, 'induk' => '2051', 'name' => 'MESA AYU SAHARANI PUTRI'],
            ['urut' => 17, 'induk' => '2052', 'name' => 'NABILA RAHMA TANIA'],
            ['urut' => 18, 'induk' => '2053', 'name' => 'NAYLA PUTRI APRILIA'],
            ['urut' => 19, 'induk' => '2054', 'name' => 'NAZWA NUR RAMADHANY'],
            ['urut' => 20, 'induk' => '2055', 'name' => 'NOYA SILYA NUR AZIZAH'],
            ['urut' => 21, 'induk' => '2056', 'name' => 'ONE FRANSISCA PUTRY'],
            ['urut' => 22, 'induk' => '2057', 'name' => 'RAFA RIZQI FEBRIAN'],
            ['urut' => 23, 'induk' => '2058', 'name' => 'SOFIA DWI AMELIA'],
            ['urut' => 24, 'induk' => '2059', 'name' => 'SUPARMIATI'],
            ['urut' => 25, 'induk' => '2060', 'name' => 'SYAFA UMI LATIFAH'],
            ['urut' => 26, 'induk' => '2061', 'name' => 'SYARINA JUNIFA QURRATUN AINI'],
            ['urut' => 27, 'induk' => '2062', 'name' => 'ULYA ZAHROTUN NAFISAH'],
            ['urut' => 28, 'induk' => '2063', 'name' => 'WIDIYA'],
            ['urut' => 29, 'induk' => '2064', 'name' => 'YOSEFA AVELINA PUTRI SETIAWATI'],
        ];

        $countMPLB = 0;

        // Insert X MPLB students
        foreach ($studentsXMPLB as $student) {
            $username = strtolower(str_replace(' ', '', $student['name']));
            $nisn = $student['induk'];

            DB::table('users')->insert([
                'name' => $student['name'],
                'username' => $username,
                'email' => $username . '@smkpgriblora.sch.id',
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'nisn' => $nisn,
                'nis' => $nisn,
                'class_id' => $classXMPLB->id,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $countMPLB++;
            $insertedCount++;
        }

        $this->command->info("✅ $countMPLB siswa X MPLB berhasil diimport!");
        
        // X Busana - Wali Kelas: Marista Bela Octaviana, S.Pd
        $studentsXBusana = [
            ['urut' => 1, 'induk' => '2011', 'name' => 'ADELIA MAYLATHULHUSNA UJIYANI'],
            ['urut' => 2, 'induk' => '2012', 'name' => 'ASIH MAHARANI'],
            ['urut' => 3, 'induk' => '2013', 'name' => 'AURELIA MARETA A'],
            ['urut' => 4, 'induk' => '2014', 'name' => 'ELFA DAMIYANTI'],
            ['urut' => 5, 'induk' => '2015', 'name' => 'FATIMAH AZZAHRA'],
            ['urut' => 6, 'induk' => '2016', 'name' => 'JASYINTA PUTRI NIKA'],
            ['urut' => 7, 'induk' => '2017', 'name' => 'MUHAMMAD RAMADHAN'],
            ['urut' => 8, 'induk' => '2018', 'name' => 'NADIA HASNA RAHMATUL LAILI'],
            ['urut' => 9, 'induk' => '2019', 'name' => 'NADIA NUR\'AINI AULIA'],
            ['urut' => 10, 'induk' => '2020', 'name' => 'OKTAVIA ANGGRAINI'],
            ['urut' => 11, 'induk' => '2021', 'name' => 'RIZKA ARIFATUN NISA'],
            ['urut' => 12, 'induk' => '2022', 'name' => 'SHAFA NIA RAMADHANI'],
            ['urut' => 13, 'induk' => '2023', 'name' => 'SIVA LIANA SARI'],
        ];

        $countBusana = 0;

        // Insert X Busana students
        foreach ($studentsXBusana as $student) {
            $username = strtolower(str_replace([' ', '\''], '', $student['name']));
            $nisn = $student['induk'];

            DB::table('users')->insert([
                'name' => $student['name'],
                'username' => $username,
                'email' => $username . '@smkpgriblora.sch.id',
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'nisn' => $nisn,
                'nis' => $nisn,
                'class_id' => $classXBusana->id,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $countBusana++;
            $insertedCount++;
        }

        $this->command->info("✅ $countBusana siswa X Busana berhasil diimport!");
        
        // XI Busana - Wali Kelas: Debby Fury Wijayanti, S. Pd.
        $studentsXIBusana = [
            ['urut' => 1, 'induk' => '1986', 'name' => 'DIAN TIKA ARINI'],
            ['urut' => 2, 'induk' => '1987', 'name' => 'EVINA DWI JULIASARI'],
            ['urut' => 3, 'induk' => '1988', 'name' => 'FENI LISTIANTI'],
            ['urut' => 4, 'induk' => '1989', 'name' => 'ICHA NOVA RIYANTI'],
            ['urut' => 5, 'induk' => '2008', 'name' => 'INTAN PUTRI WIDYASTUTI'],
            ['urut' => 6, 'induk' => '1990', 'name' => 'MELINDA AZ ZAHWA'],
            ['urut' => 7, 'induk' => '1991', 'name' => 'NATHANIA ZULFA DAMAYANTI'],
            ['urut' => 8, 'induk' => '1992', 'name' => 'NAZUA MAULIDA ANATASYA'],
            ['urut' => 9, 'induk' => '1994', 'name' => 'NURUL RISMAWATI'],
            ['urut' => 10, 'induk' => '1995', 'name' => 'RESTIANA MEISYAROH'],
            ['urut' => 11, 'induk' => '1997', 'name' => 'SABRINA YUNIAR PERMATA RAMADHANI'],
            ['urut' => 12, 'induk' => '1998', 'name' => 'SITI FAIQQOTUL HIKMAH'],
            ['urut' => 13, 'induk' => '1999', 'name' => 'SITI NUR AFIDAH'],
            ['urut' => 14, 'induk' => '2000', 'name' => 'TIRTA ATMA NIRMAYA'],
            ['urut' => 15, 'induk' => '2001', 'name' => 'VERI NOFIANA'],
        ];

        $countXIBusana = 0;

        // Insert XI Busana students
        foreach ($studentsXIBusana as $student) {
            $username = strtolower(str_replace([' ', '\''], '', $student['name']));
            $nisn = $student['induk'];

            DB::table('users')->insert([
                'name' => $student['name'],
                'username' => $username,
                'email' => $username . '@smkpgriblora.sch.id',
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'nisn' => $nisn,
                'nis' => $nisn,
                'class_id' => $classXIBusana->id,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $countXIBusana++;
            $insertedCount++;
        }

        $this->command->info("✅ $countXIBusana siswa XI Busana berhasil diimport!");
        
        // XI MPLB - Wali Kelas: Ade Rua Nur Lemoniar, S.Pd.
        $studentsXIMPLB = [
            ['urut' => 1, 'induk' => '1956', 'name' => 'AGUSTINA AJENG RAMADHANI'],
            ['urut' => 2, 'induk' => '1957', 'name' => 'ARTIKA NUR ROHMAH'],
            ['urut' => 3, 'induk' => '1959', 'name' => 'BUNGA SARI RAHMANDANI'],
            ['urut' => 4, 'induk' => '1960', 'name' => 'CHELIZA DEWI SAPUTRI'],
            ['urut' => 5, 'induk' => '1961', 'name' => 'CITRA KHARISA'],
            ['urut' => 6, 'induk' => '1962', 'name' => 'DESIANE SAPUTRI'],
            ['urut' => 7, 'induk' => '1963', 'name' => 'DINDA WULAN SAFITRI'],
            ['urut' => 8, 'induk' => '1964', 'name' => 'DINI YULIANA'],
            ['urut' => 9, 'induk' => '1965', 'name' => 'ERLIANA AZIZAS MITA'],
            ['urut' => 10, 'induk' => '1966', 'name' => 'FINDY FARELLA'],
            ['urut' => 11, 'induk' => '1967', 'name' => 'INTAN SEKAR WULANDARI'],
            ['urut' => 12, 'induk' => '1968', 'name' => 'INTAN TAKSA'],
            ['urut' => 13, 'induk' => '1969', 'name' => 'LEVIANA PUTRI AUDI ASTUTI'],
            ['urut' => 14, 'induk' => '2009', 'name' => 'JIHAN JAUHAROTUN NISA\''],
            ['urut' => 15, 'induk' => '1970', 'name' => 'LILIS'],
            ['urut' => 16, 'induk' => '1971', 'name' => 'MANDA SETIA KINASIH'],
            ['urut' => 17, 'induk' => '1972', 'name' => 'NAISILA ZAIMATUL RIZKIYAH'],
            ['urut' => 18, 'induk' => '1973', 'name' => 'NAURA AZALIA'],
            ['urut' => 19, 'induk' => '2010', 'name' => 'NUR CAHYA ISWANTI'],
            ['urut' => 20, 'induk' => '1974', 'name' => 'OKTAVIA DWI ANDRIANI'],
            ['urut' => 21, 'induk' => '1975', 'name' => 'OLIVIA SEPTIAN PERMATA HERMANSYAH'],
            ['urut' => 22, 'induk' => '1976', 'name' => 'RATNA DWI MEILYA SALSABILA'],
            ['urut' => 23, 'induk' => '1978', 'name' => 'REZKY OKTVIANA PUTRI'],
            ['urut' => 24, 'induk' => '1979', 'name' => 'RINDU ARDELIA CANDRANINGTYAS'],
            ['urut' => 25, 'induk' => '1980', 'name' => 'RIYANA'],
            ['urut' => 26, 'induk' => '1981', 'name' => 'SAFIRA CAHYA INDRIYANI'],
            ['urut' => 27, 'induk' => '1982', 'name' => 'SEPTIANA NADIA RAMADHA'],
            ['urut' => 28, 'induk' => '1984', 'name' => 'SYIFA FAUZIYAH'],
            ['urut' => 29, 'induk' => '1985', 'name' => 'VIDYANA VEGA AULIA NURANI PUTRI'],
        ];

        $countXIMPLB = 0;

        // Insert XI MPLB students
        foreach ($studentsXIMPLB as $student) {
            $username = strtolower(str_replace([' ', '\''], '', $student['name']));
            
            // Check if username already exists, append induk number
            $existingUser = DB::table('users')->where('username', $username)->first();
            if ($existingUser) {
                $username = $username . $student['induk'];
            }
            
            $nisn = $student['induk'];

            DB::table('users')->insert([
                'name' => $student['name'],
                'username' => $username,
                'email' => $username . '@smkpgriblora.sch.id',
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'nisn' => $nisn,
                'nis' => $nisn,
                'class_id' => $classXIMPLB->id,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $countXIMPLB++;
            $insertedCount++;
        }

        $this->command->info("✅ $countXIMPLB siswa XI MPLB berhasil diimport!");
        
        // XI AKL - Wali Kelas: Adela Wulan Kurniasari, S.Pd
        $studentsXIAKL = [
            ['urut' => 1, 'induk' => '2003', 'name' => 'ARESTA LIAN CANTIKA'],
            ['urut' => 2, 'induk' => '2004', 'name' => 'ASHILA MAYDISTI PRAMESWARI'],
            ['urut' => 3, 'induk' => '2005', 'name' => 'LUTFIA NUR\'AINI'],
            ['urut' => 4, 'induk' => '2006', 'name' => 'NAZWA PERMATA OKTAVIA'],
            ['urut' => 5, 'induk' => '2007', 'name' => 'RIZKIYA NURUL SABRINA'],
        ];

        $countXIAKL = 0;

        // Insert XI AKL students
        foreach ($studentsXIAKL as $student) {
            $username = strtolower(str_replace([' ', '\''], '', $student['name']));
            
            // Check if username already exists, append induk number
            $existingUser = DB::table('users')->where('username', $username)->first();
            if ($existingUser) {
                $username = $username . $student['induk'];
            }
            
            $nisn = $student['induk'];

            DB::table('users')->insert([
                'name' => $student['name'],
                'username' => $username,
                'email' => $username . '@smkpgriblora.sch.id',
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'nisn' => $nisn,
                'nis' => $nisn,
                'class_id' => $classXIAKL->id,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $countXIAKL++;
            $insertedCount++;
        }

        $this->command->info("✅ $countXIAKL siswa XI AKL berhasil diimport!");
        
        // XII AKL - Wali Kelas: Liliyana Ayu Widiyaningrum, S.Pd.
        $studentsXIIAKL = [
            ['urut' => 1, 'induk' => '1949', 'name' => 'ANGGUN TRI LESTARI'],
            ['urut' => 2, 'induk' => '1950', 'name' => 'DIANA MUTIARA'],
            ['urut' => 3, 'induk' => '1951', 'name' => 'EKA ANGGRAINI'],
            ['urut' => 4, 'induk' => '1952', 'name' => 'MUHAMAD ADI WALUYO'],
            ['urut' => 5, 'induk' => '1953', 'name' => 'YAHYA FEBRIANI'],
        ];

        $countXIIAKL = 0;

        // Insert XII AKL students
        foreach ($studentsXIIAKL as $student) {
            $username = strtolower(str_replace([' ', '\''], '', $student['name']));
            
            // Check if username already exists, append induk number
            $existingUser = DB::table('users')->where('username', $username)->first();
            if ($existingUser) {
                $username = $username . $student['induk'];
            }
            
            $nisn = $student['induk'];

            DB::table('users')->insert([
                'name' => $student['name'],
                'username' => $username,
                'email' => $username . '@smkpgriblora.sch.id',
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'nisn' => $nisn,
                'nis' => $nisn,
                'class_id' => $classXIIAKL->id,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $countXIIAKL++;
            $insertedCount++;
        }

        $this->command->info("✅ $countXIIAKL siswa XII AKL berhasil diimport!");
        
        // XII Busana - Wali Kelas: Dewi Wartini, S.Pd.
        $studentsXIIBusana = [
            ['urut' => 1, 'induk' => '1940', 'name' => 'ADE UTAMI AMBARWATI'],
            ['urut' => 2, 'induk' => '1941', 'name' => 'DESI LUKMAWATI'],
            ['urut' => 3, 'induk' => '1942', 'name' => 'DESI VRISKA AGUSTINA'],
            ['urut' => 4, 'induk' => '1943', 'name' => 'KHOIRIDA ALVINA RAHMA'],
            ['urut' => 5, 'induk' => '1944', 'name' => 'OLIVIA EKA SAFITRI'],
            ['urut' => 6, 'induk' => '1945', 'name' => 'SITI APRILIA FITRI RAHAYU'],
            ['urut' => 7, 'induk' => '1947', 'name' => 'VANISA ARSYANTI'],
        ];

        $countXIIBusana = 0;

        // Insert XII Busana students
        foreach ($studentsXIIBusana as $student) {
            $username = strtolower(str_replace([' ', '\''], '', $student['name']));
            
            // Check if username already exists, append induk number
            $existingUser = DB::table('users')->where('username', $username)->first();
            if ($existingUser) {
                $username = $username . $student['induk'];
            }
            
            $nisn = $student['induk'];

            DB::table('users')->insert([
                'name' => $student['name'],
                'username' => $username,
                'email' => $username . '@smkpgriblora.sch.id',
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'nisn' => $nisn,
                'nis' => $nisn,
                'class_id' => $classXIIBusana->id,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $countXIIBusana++;
            $insertedCount++;
        }

        $this->command->info("✅ $countXIIBusana siswa XII Busana berhasil diimport!");
        
        // XII MPLB - Wali Kelas: Ervinda Sekar Asmara, S.Pd.
        $studentsXIIMPLB = [
            ['urut' => 1, 'induk' => '1918', 'name' => 'AURA AUDIAVENA'],
            ['urut' => 2, 'induk' => '1919', 'name' => 'AYU SIFA MEYNA PUTRI'],
            ['urut' => 3, 'induk' => '1920', 'name' => 'DELIA ANGGRAENI'],
            ['urut' => 4, 'induk' => '1921', 'name' => 'DEVINA PRATAMA SAPUTRI'],
            ['urut' => 5, 'induk' => '1922', 'name' => 'DIAH AYU PERTIWI'],
            ['urut' => 6, 'induk' => '1923', 'name' => 'ELPI LUPITA RATNA SARI'],
            ['urut' => 7, 'induk' => '1924', 'name' => 'KRISTYN JELITA'],
            ['urut' => 8, 'induk' => '1925', 'name' => 'NABILLA SELVIANA FITRY'],
            ['urut' => 9, 'induk' => '1926', 'name' => 'NISWA KHOIRUN NADA'],
            ['urut' => 10, 'induk' => '1927', 'name' => 'NOVITA WIDIHASTUTI'],
            ['urut' => 11, 'induk' => '1928', 'name' => 'NUR AYNI'],
            ['urut' => 12, 'induk' => '1929', 'name' => 'RAHAYU NOVITA LESTARI'],
            ['urut' => 13, 'induk' => '1930', 'name' => 'RENANSYA ELMA MEILANA REFIS'],
            ['urut' => 14, 'induk' => '1931', 'name' => 'RESTU BAGUS WIDODO'],
            ['urut' => 15, 'induk' => '1932', 'name' => 'REVALINA RISMA NOVITASARI'],
            ['urut' => 16, 'induk' => '1933', 'name' => 'REVNISKA GARMANINA AULIA DEWI'],
            ['urut' => 17, 'induk' => '1934', 'name' => 'RIYANA'],
            ['urut' => 18, 'induk' => '1935', 'name' => 'SHEILA MARCELINA'],
            ['urut' => 19, 'induk' => '1936', 'name' => 'SITI LAILATUL NAZAL'],
            ['urut' => 20, 'induk' => '1937', 'name' => 'TIFANI GRISELDIS'],
            ['urut' => 21, 'induk' => '1938', 'name' => 'WAHYU NINGTYAS DEWI PISCESA'],
            ['urut' => 22, 'induk' => '1939', 'name' => 'WILDA TUSSOLEHA'],
            ['urut' => 23, 'induk' => '1954', 'name' => 'JODY IKHSAN SETYAWAN'],
            ['urut' => 24, 'induk' => '1955', 'name' => 'ANGGIE NUR AINI SOLEHA'],
        ];

        $countXIIMPLB = 0;

        // Insert XII MPLB students
        foreach ($studentsXIIMPLB as $student) {
            $username = strtolower(str_replace([' ', '\''], '', $student['name']));
            
            // Check if username already exists, append induk number
            $existingUser = DB::table('users')->where('username', $username)->first();
            if ($existingUser) {
                $username = $username . $student['induk'];
            }
            
            $nisn = $student['induk'];

            DB::table('users')->insert([
                'name' => $student['name'],
                'username' => $username,
                'email' => $username . '@smkpgriblora.sch.id',
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'nisn' => $nisn,
                'nis' => $nisn,
                'class_id' => $classXIIMPLB->id,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $countXIIMPLB++;
            $insertedCount++;
        }

        $this->command->info("✅ $countXIIMPLB siswa XII MPLB berhasil diimport!");
        $this->command->info('');
        $this->command->info("📊 Total: $insertedCount siswa berhasil diimport!");
        $this->command->info('');
        $this->command->info('📝 Catatan:');
        $this->command->info('   - Username: nama tanpa spasi (lowercase)');
        $this->command->info('   - Email: username@smkpgriblora.sch.id');
        $this->command->info('   - Password default: password');
        $this->command->info('   - NISN: menggunakan nomor induk sementara');
        $this->command->info('');
        $this->command->info('💡 Untuk tambah kelas lain, kirim data siswa dalam format yang sama.');
    }
}
