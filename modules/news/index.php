<?php
require '../../config/db.php';
require '../../includes/header.php';
require '../../includes/sidebar.php';

// Who can access? Admin, Editor, Kontributor.
if($role === 'user') { header("Location: ../dashboard/index.php"); exit; }

if($role === 'admin' || $role === 'editor') {
    // See all news
    $stmt = $conn->query("SELECT news.*, users.name as author_name, categories.name as category_name 
                          FROM news 
                          LEFT JOIN users ON news.author_id = users.id 
                          LEFT JOIN categories ON news.category_id = categories.id 
                          ORDER BY created_at DESC");
    $news_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // See only own news
    $stmt = $conn->prepare("SELECT news.*, categories.name as category_name 
                            FROM news 
                            LEFT JOIN categories ON news.category_id = categories.id 
                            WHERE author_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $news_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<main class="flex-1 overflow-y-auto bg-slate-50/50 p-4 sm:p-6 lg:p-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900"><?= ($role == 'kontributor') ? 'Berita Saya' : 'Kelola Semua Berita' ?></h1>
        </div>
        <a href="create.php" class="bg-brand-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-brand-700 shadow-sm transition inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tulis Berita
        </a>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div class="bg-green-50 text-green-600 p-4 rounded-lg mb-6 text-sm max-w-4xl">
            <?= htmlspecialchars($_GET['msg']) ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden max-w-5xl">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Judul & Kategori</th>
                        <?php if($role !== 'kontributor'): ?>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Penulis</th>
                        <?php endif; ?>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    <?php if(count($news_list) > 0): ?>
                        <?php foreach($news_list as $news): ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-slate-900"><?= htmlspecialchars($news['title']) ?></div>
                                    <div class="text-xs text-brand-600 mt-1 font-medium bg-brand-50 inline-block px-2 py-0.5 rounded-full"><?= htmlspecialchars($news['category_name']) ?></div>
                                </td>
                                <?php if($role !== 'kontributor'): ?>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-slate-900"><?= htmlspecialchars($news['author_name'] ?? 'Unknown') ?></div>
                                </td>
                                <?php endif; ?>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if($news['status'] == 'published'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Published</span>
                                    <?php elseif($news['status'] == 'pending'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">Pending</span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">Draft</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                    <?= date('d M Y', strtotime($news['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="show.php?id=<?= $news['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3">Lihat</a>
                                    <a href="edit.php?id=<?= $news['id'] ?>" class="text-brand-600 hover:text-brand-900 mr-3">Edit</a>
                                    <a href="delete.php?id=<?= $news['id'] ?>" onclick="return confirm('Yakin ingin menghapus berita ini?')" class="text-red-600 hover:text-red-900">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= ($role == 'kontributor') ? '4' : '5' ?>" class="px-6 py-8 text-center text-slate-500 text-sm">Belum ada berita.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php require '../../includes/footer.php'; ?>
