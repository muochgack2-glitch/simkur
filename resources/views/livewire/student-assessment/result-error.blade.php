<div class="max-w-2xl mx-auto p-6">
    <div class="bg-red-50 border border-red-200 rounded-lg p-8 text-center">
        <svg class="mx-auto h-16 w-16 text-red-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        
        <h2 class="text-2xl font-bold text-gray-900 mb-2">
            Profil Tidak Ditemukan
        </h2>
        
        <p class="text-gray-600 mb-6">
            Profil pembelajaran Anda belum tersedia. Pastikan Anda sudah menyelesaikan asesmen.
        </p>
        
        <a href="{{ route('student.assessment.index') }}" 
           class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar Asesmen
        </a>
    </div>
</div>
