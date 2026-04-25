<?php
session_start();
require 'config/db.php';

// Fetch published news
$stmt = $conn->prepare("SELECT news.*, users.name as author_name, categories.name as category_name 
                        FROM news 
                        LEFT JOIN users ON news.author_id = users.id 
                        LEFT JOIN categories ON news.category_id = categories.id 
                        WHERE status = 'published' ORDER BY created_at DESC");
$stmt->execute();
$news_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group news by category
$grouped_news = [];
foreach ($news_list as $news) {
    $category_name = $news['category_name'] ?? 'Uncategorized';
    if (!isset($grouped_news[$category_name])) {
        $grouped_news[$category_name] = [];
    }
    $grouped_news[$category_name][] = $news;
}
?>
<?php require 'layout/header.php'; ?>

<!-- Main Content -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="text-center mb-12">
        <h1 class="text-3xl tracking-tight font-extrabold text-slate-900 sm:text-4xl md:text-5xl">
            <span class="block">Kabar Terbaru,<span class="text-brand-600">Dari Warga</span></span>

        </h1>
        <p class="mt-3 max-w-md mx-auto text-base text-slate-500 sm:text-md md:mt-5 md:text-xl md:max-w-3xl">
            Dapatkan informasi terkini dari warga. menyajikan informasi paling relevan untuk Anda setiap hari.
        </p>
    </div>

    <?php if (count($grouped_news) > 0): ?>
        <?php foreach ($grouped_news as $category_name => $category_news): ?>
            <section class="mb-16">
                <div class="flex items-center justify-between mb-8 border-b border-slate-200 pb-4">
                    <h2 class="text-3xl font-extrabold text-slate-900 flex items-center gap-3">
                        <span class="w-2 h-8 bg-brand-500 rounded-full"></span>
                        <?= htmlspecialchars($category_name) ?>
                    </h2>

                </div>
                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <?php foreach ($category_news as $news): ?>
                        <article
                            class="relative rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden group h-96 flex flex-col justify-end border border-slate-200/50">
                            <!-- Background Image -->
                            <?php if ($news['image']): ?>
                                <img src="uploads/news/<?= htmlspecialchars($news['image']) ?>" alt="Thumbnail"
                                    class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <?php else: ?>
                                <div
                                    class="absolute inset-0 w-full h-full bg-gradient-to-br from-slate-800 to-slate-900 transition-transform duration-700 group-hover:scale-110">
                                </div>
                            <?php endif; ?>

                            <!-- Gradient Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/95 via-slate-900/50 to-transparent"></div>

                            <!-- Card Content -->
                            <div class="relative p-6 flex flex-col justify-end h-full z-10">
                                <div class="flex items-center justify-between mb-4">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-brand-500 text-white shadow-sm border border-brand-400/30">
                                        <?= htmlspecialchars($news['category_name'] ?? 'Uncategorized') ?>
                                    </span>
                                    <span class="text-xs font-medium text-slate-300 drop-shadow-md">
                                        <?= date('d M Y', strtotime($news['created_at'])) ?>
                                    </span>
                                </div>

                                <a href="detail.php?id=<?= $news['id'] ?>" class="block mt-1 flex-1 flex flex-col justify-end">
                                    <h3
                                        class="text-2xl font-bold text-white group-hover:text-brand-300 transition-colors line-clamp-2 drop-shadow-md leading-snug">
                                        <?= htmlspecialchars($news['title']) ?>
                                    </h3>
                                    <p class="mt-2 text-sm text-slate-300 line-clamp-2 drop-shadow">
                                        <?= htmlspecialchars(strip_tags($news['content'])) ?>
                                    </p>
                                </a>

                                <div class="mt-5 flex items-center pt-4 border-t border-white/10">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="h-9 w-9 rounded-full bg-brand-600 border-2 border-white/20 shadow-sm flex items-center justify-center text-white font-bold text-xs">
                                            <?= strtoupper(substr($news['author_name'], 0, 1)) ?>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-slate-200">
                                            <?= htmlspecialchars($news['author_name']) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                aria-hidden="true">
                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-slate-900">Belum ada berita</h3>
            <p class="mt-1 text-sm text-slate-500">Berita yang diterbitkan akan muncul di sini.</p>
        </div>
    <?php endif; ?>
</main>
<?php require 'layout/footer.php'; ?>