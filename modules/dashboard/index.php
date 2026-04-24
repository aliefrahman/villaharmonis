<?php
require '../../config/db.php';
require '../../includes/header.php';
require '../../includes/sidebar.php';
?>
<!-- Main Scrollable Area -->
<main class="flex-1 overflow-y-auto bg-slate-50/50 p-4 sm:p-6 lg:p-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Dashboard <?= ucfirst($role) ?></h1>
            <p class="text-slate-500">Selamat datang kembali, <?= htmlspecialchars($name) ?>!</p>
        </div>
        <?php if($role === 'kontributor'): ?>
            <a href="../news/create.php" class="bg-brand-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-brand-700 shadow-sm transition inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tulis Berita
            </a>
        <?php endif; ?>
    </div>

    <!-- Role specific widgets -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <?php if($role === 'admin'): ?>
            <?php 
                $total_users = $conn->query("SELECT count(*) FROM users")->fetchColumn();
                $total_news = $conn->query("SELECT count(*) FROM news")->fetchColumn();
            ?>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center gap-4">
                <div class="h-12 w-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Pengguna</p>
                    <p class="text-2xl font-bold text-slate-900"><?= $total_users ?></p>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center gap-4">
                <div class="h-12 w-12 bg-brand-50 text-brand-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H14"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Berita</p>
                    <p class="text-2xl font-bold text-slate-900"><?= $total_news ?></p>
                </div>
            </div>

        <?php elseif($role === 'editor'): ?>
            <?php 
                $pending_news = $conn->query("SELECT count(*) FROM news WHERE status='pending'")->fetchColumn();
            ?>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center gap-4">
                <div class="h-12 w-12 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500">Menunggu Review</p>
                    <p class="text-2xl font-bold text-slate-900"><?= $pending_news ?></p>
                </div>
            </div>
            <div class="col-span-full md:col-span-1 mt-2">
                <a href="../news/review.php" class="inline-block bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">Lihat daftar review &rarr;</a>
            </div>

        <?php elseif($role === 'kontributor'): ?>
            <?php 
                $stmt = $conn->prepare("SELECT count(*) FROM news WHERE author_id=?");
                $stmt->execute([$_SESSION['user_id']]);
                $my_news = $stmt->fetchColumn();
            ?>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center gap-4">
                <div class="h-12 w-12 bg-brand-50 text-brand-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H14"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Berita Saya</p>
                    <p class="text-2xl font-bold text-slate-900"><?= $my_news ?></p>
                </div>
            </div>

        <?php elseif($role === 'user'): ?>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 col-span-1 md:col-span-2">
                <h3 class="font-bold text-slate-800 flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Informasi Profil
                </h3>
                <div class="space-y-4">
                    <div class="pb-4 border-b border-slate-100">
                        <span class="text-sm text-slate-500 block mb-1">Nama Lengkap</span>
                        <span class="font-medium text-slate-900"><?= htmlspecialchars($name) ?></span>
                    </div>
                    <div class="pb-4 border-b border-slate-100">
                        <span class="text-sm text-slate-500 block mb-1">Peran Pengguna</span>
                        <span class="inline-block px-3 py-1 bg-brand-50 text-brand-700 rounded-full text-xs font-semibold capitalize"><?= htmlspecialchars($role) ?></span>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php require '../../includes/footer.php'; ?>
