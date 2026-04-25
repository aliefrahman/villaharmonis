<?php
http_response_code(403);
$page_title = '403 Akses Ditolak - #VHC';
require_once 'layout/header.php';
?>

<main class="flex-1 flex items-center justify-center min-h-[70vh] bg-slate-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full text-center space-y-8 relative">
        <div class="relative">
            <h1 class="text-9xl font-extrabold text-slate-800 tracking-widest drop-shadow-sm">403</h1>
            <div class="bg-red-100 text-red-600 px-3 py-1 text-sm rounded -rotate-12 absolute mx-auto left-0 right-0 w-fit top-1/2 -mt-4 font-bold shadow-sm border border-red-200">
                Forbidden
            </div>
        </div>
        
        <div class="mt-8">
            <h2 class="text-3xl font-bold text-slate-900 mb-4">Akses Ditolak!</h2>
            <p class="text-slate-500 mb-8">Maaf, Anda tidak memiliki izin atau hak akses untuk melihat direktori atau halaman ini. Jika ini adalah sebuah kesalahan, silakan hubungi administrator.</p>
            
            <a href="index.php" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-slate-800 hover:bg-slate-900 shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>
</main>

<?php require_once 'layout/footer.php'; ?>
