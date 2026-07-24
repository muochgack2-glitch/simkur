<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class CredentialsController extends Controller
{
    public function index()
    {
        // Check if password is verified in session
        if (!session('credentials_verified')) {
            return view('credentials-login');
        }

        // Get all students grouped by class
        $students = User::where('role', 'siswa')
            ->with('schoolClass.homeroomTeacher')
            ->orderBy('class_id')
            ->orderBy('name')
            ->get()
            ->groupBy(function($student) {
                return $student->schoolClass ? $student->schoolClass->name : 'Belum Ada Kelas';
            });

        // Get all teachers
        $teachers = User::where('role', 'guru')
            ->orderBy('name')
            ->get();

        // Get e-Rapor users from index.php data
        $eraporUsers = [
            ['nama' => 'ADE RUA NUR LEMONIAR, S.Pd',       'user' => 'Aderuanurlemoniar95@gmail.com',    'password' => 'haeehcsn'],
            ['nama' => 'ADELA WULAN K., S.PD.',             'user' => 'kurniawanalfarizki84@gmail.com',   'password' => 'swhnngbu'],
            ['nama' => 'ARI YUNITASARI, S.Pd',              'user' => 'ariyunitasari@yahoo.co.id',        'password' => 'emstzu0v'],
            ['nama' => 'BUDI SISWANTO, S.Pd.I',             'user' => 'budisiswanto120@gmail.com',        'password' => 'mnluatkl'],
            ['nama' => 'DEBBY FURY WIJAYANTI, S.PD',        'user' => 'debbyfury@gmail.com',              'password' => 'ugfsqqob'],
            ['nama' => 'DEWI WARTINI, S.Pd',                'user' => 'wartinidewi10@yahoo.com',          'password' => 'jgqi1sl2'],
            ['nama' => 'DHANI KISWORO JATI, S.Pd',          'user' => 'dhanikiswono24@gmail.com',         'password' => 'kdbsxmr9'],
            ['nama' => 'Drs. SUSENO',                       'user' => 'gelandangsukma@gmail.com',         'password' => 'drbltakw'],
            ['nama' => 'ERVINDA SEKAR ASMARA, S.PD.',       'user' => 'ervindasekar07@gmail.com',         'password' => 'kvx769m3'],
            ['nama' => 'ILHAM HARDIYAN PRABOWO, S.Pd',      'user' => 'ilhamm979@gmail.com',              'password' => 'mh8ecugs'],
            ['nama' => 'LILIYANA AYU WIDYANINGRUM, S.Pd',   'user' => 'Liliyanaayuw@gmail.com',           'password' => 'mqpbi0ws'],
            ['nama' => 'MUHAMMAD HUDA MUTTAQIN, S.Pd.I',    'user' => 'muochgack2@gmail.com',             'password' => 'custom'],
            ['nama' => 'MUNISAH, S.Pd',                     'user' => 'munisah234@gmail.com',             'password' => 'muttkm3t'],
            ['nama' => 'PANCAWATI PUJI LESTARI, A.Md',      'user' => 'pancawatipujlestari@yahoo.com',    'password' => 'ivw01cgh'],
            ['nama' => 'NIA DANI RAHAYU, S.Pd',             'user' => 'niadanirahayu@gmail.com',          'password' => 'eimn5uyp'],
            ['nama' => 'TRI MULYANININGSIH, S.E',            'user' => 'inay_tri96@yahoo.com',             'password' => 'gnniiyo1'],
            ['nama' => 'WIWIT MERGI WIJAYANTI, A.MD.',      'user' => 'mergiwijayanti@gmail.com',         'password' => '771p102z'],
            ['nama' => 'YULLY SETYO ANGRENGGANI, S.Pd',     'user' => 'yullysang973@gmail.com',           'password' => 'ngtm1hfo'],
        ];

        return view('credentials', compact('students', 'teachers', 'eraporUsers'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $correctPassword = env('CREDENTIALS_PASSWORD', 'smkpgri2026');

        if ($request->password === $correctPassword) {
            session(['credentials_verified' => true]);
            return redirect()->route('credentials');
        }

        return back()->withErrors([
            'password' => 'Password salah. Silakan coba lagi.',
        ]);
    }
}
