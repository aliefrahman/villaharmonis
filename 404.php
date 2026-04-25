<?php
http_response_code(404);
$page_title = '404 Halaman Tidak Ditemukan - #VHC';
require_once 'layout/header.php';
?>

<main class="flex-1 flex items-center justify-center min-h-[70vh] bg-slate-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full text-center space-y-8 relative">
        <div class="relative">
            <h1 class="text-9xl font-extrabold text-brand-600 tracking-widest drop-shadow-sm">404</h1>
            <div class="bg-brand-50 text-brand-600 px-3 py-1 text-sm rounded rotate-12 absolute mx-auto left-0 right-0 w-fit top-1/2 -mt-4 font-bold shadow-sm border border-brand-200">
                Not Found
            </div>
        </div>
        
        <div class="mt-8">
            <h2 class="text-3xl font-bold text-slate-900 mb-4">Ups! Halaman Hilang</h2>
            <p class="text-slate-500 mb-8">Maaf, halaman yang Anda cari tidak dapat ditemukan. Mungkin telah dihapus, dipindahkan, atau URL-nya salah.</p>
            
            <a href="index.php" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-brand-600 hover:bg-brand-700 shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>
</main>

<?php require_once 'layout/footer.php'; ?>
