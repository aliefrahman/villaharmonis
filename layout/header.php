<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) : 'Berita.VHC - Dapatkan informasi yang viral dari warga. menyajikan informasi paling relevan untuk Anda setiap hari' ?></title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style type="text/tailwindcss">
        @theme {
            --color-brand-500: #2563eb;
            --color-brand-600: #1d4ed8;
        }
    </style>
    <?= $additional_head ?? '' ?>
</head>

<body class="bg-slate-50 text-slate-900 font-sans antialiased" x-data="{ mobileMenuOpen: false }">

    <!-- Navigation -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="index.php" class="text-2xl font-bold text-brand-600 flex items-center gap-2">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H14">
                            </path>
                        </svg>
                        Berita.VHC
                    </a>
                </div>
                <div class="hidden sm:flex sm:items-center sm:space-x-8">
                    <a href="index.php" class="text-slate-700 hover:text-brand-600 font-medium transition">Beranda</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="modules/dashboard/index.php"
                            class="text-slate-700 hover:text-brand-600 font-medium transition">Dashboard</a>
                        <a href="modules/auth/logout.php"
                            class="bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2 rounded-lg font-medium transition">Logout</a>
                    <?php else: ?>
                        <a href="modules/auth/login.php"
                            class="text-slate-700 hover:text-brand-600 font-medium transition">Login</a>
                        <a href="modules/auth/register.php"
                            class="bg-brand-600 text-white hover:bg-brand-700 px-4 py-2 rounded-lg font-medium transition shadow-sm hover:shadow">Daftar</a>
                    <?php endif; ?>
                </div>
                <!-- Mobile menu button -->
                <div class="flex items-center sm:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                        class="text-slate-500 hover:text-slate-700 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M6 18L18 6M6 6l12 12" style="display: none;" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" class="sm:hidden border-t border-slate-100" style="display: none;">
            <div class="pt-2 pb-3 space-y-1">
                <a href="index.php"
                    class="block pl-3 pr-4 py-2 border-l-4 border-brand-500 text-brand-700 bg-brand-50 font-medium">Beranda</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="modules/dashboard/index.php"
                        class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-slate-600 hover:text-slate-800 hover:bg-slate-50 font-medium">Dashboard</a>
                    <a href="modules/auth/logout.php"
                        class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-red-600 hover:text-red-800 hover:bg-red-50 font-medium">Logout</a>
                <?php else: ?>
                    <a href="modules/auth/login.php"
                        class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-slate-600 hover:text-slate-800 hover:bg-slate-50 font-medium">Login</a>
                    <a href="modules/auth/register.php"
                        class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-brand-600 hover:text-brand-800 hover:bg-brand-50 font-medium">Daftar</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>