<!-- Sidebar -->
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:inset-0 flex flex-col shadow-xl md:shadow-none">
    <div class="flex items-center justify-between md:justify-center h-16 border-b border-slate-100 px-4">
        <a href="../../index.php" class="text-xl font-bold text-brand-600 flex items-center gap-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H14">
                </path>
            </svg>
            #VHC
        </a>
        <button @click="sidebarOpen = false" class="md:hidden text-slate-500 hover:text-slate-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <div class="overflow-y-auto flex-1 p-4 space-y-2">
        <a href="../../modules/dashboard/index.php"
            class="<?= $current_page == 'index.php' ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-50' ?> block px-4 py-2 rounded-lg font-medium transition">Dashboard</a>

        <?php if ($role === 'admin'): ?>
            <a href="../../modules/users/index.php"
                class="<?= strpos($_SERVER['REQUEST_URI'], '/modules/users/') !== false ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-50' ?> block px-4 py-2 rounded-lg font-medium transition">Kelola
                User</a>
            <a href="../../modules/categories/index.php"
                class="<?= strpos($_SERVER['REQUEST_URI'], '/modules/categories/') !== false ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-50' ?> block px-4 py-2 rounded-lg font-medium transition">Kategori</a>
        <?php endif; ?>

        <?php if ($role === 'admin' || $role === 'editor' || $role === 'kontributor'): ?>
            <a href="../../modules/news/index.php"
                class="<?= strpos($_SERVER['REQUEST_URI'], '/modules/news/index.php') !== false ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-50' ?> block px-4 py-2 rounded-lg font-medium transition">Kelola
                Berita</a>
        <?php endif; ?>

        <?php if ($role === 'editor' || $role === 'admin'): ?>
            <a href="../../modules/review/index.php"
                class="<?= strpos($_SERVER['REQUEST_URI'], '/modules/review/') !== false ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-50' ?> block px-4 py-2 rounded-lg font-medium transition">Review
                Berita</a>
        <?php endif; ?>

        <?php if ($role === 'kontributor' || $role === 'admin'): ?>
            <a href="../../modules/news/create.php"
                class="<?= $current_page == 'create.php' ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-50' ?> block px-4 py-2 rounded-lg font-medium transition">Tulis
                Berita</a>
        <?php endif; ?>
    </div>

    <div class="p-4 border-t border-slate-100">
        <div class="flex items-center mb-4">
            <div
                class="h-10 w-10 rounded-full bg-gradient-to-tr from-brand-400 to-brand-600 text-white flex items-center justify-center font-bold shadow-inner">
                <?= strtoupper(substr($name, 0, 1)) ?>
            </div>
            <div class="ml-3 overflow-hidden">
                <p class="text-sm font-medium text-slate-900 truncate"><?= htmlspecialchars($name) ?></p>
                <p class="text-xs text-slate-500 capitalize"><?= htmlspecialchars($role) ?></p>
            </div>
        </div>
        <a href="../../modules/auth/logout.php"
            class="block w-full text-center px-4 py-2 border border-red-200 text-red-600 rounded-lg hover:bg-red-50 transition text-sm font-medium">Logout</a>
    </div>
</aside>

<!-- Main Content wrapper -->
<div class="flex-1 flex flex-col min-w-0 overflow-hidden">
    <!-- Top header -->
    <header
        class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 shrink-0">
        <button @click="sidebarOpen = true" class="md:hidden text-slate-500 focus:outline-none hover:text-slate-700">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <div class="ml-auto flex items-center gap-4">
            <a href="../../index.php" target="_blank"
                class="text-sm text-slate-500 hover:text-brand-600 font-medium flex items-center gap-1">
                Lihat Situs
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
            </a>
        </div>
    </header>