<?php
require '../../config/db.php';
require '../../includes/header.php';
require '../../includes/sidebar.php';

if($role !== 'admin') { header("Location: ../dashboard/index.php"); exit; }

$stmt = $conn->query("SELECT * FROM categories ORDER BY id DESC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<main class="flex-1 overflow-y-auto bg-slate-50/50 p-4 sm:p-6 lg:p-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Kelola Kategori</h1>
            <p class="text-slate-500">Daftar kategori berita yang tersedia.</p>
        </div>
        <a href="create.php" class="bg-brand-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-brand-700 shadow-sm transition inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Kategori
        </a>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div class="bg-green-50 text-green-600 p-4 rounded-lg mb-6 text-sm max-w-4xl">
            <?= htmlspecialchars($_GET['msg']) ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden max-w-4xl">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Nama Kategori</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                <?php if(count($categories) > 0): ?>
                    <?php foreach($categories as $cat): ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">#<?= $cat['id'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-900"><?= htmlspecialchars($cat['name']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="edit.php?id=<?= $cat['id'] ?>" class="text-brand-600 hover:text-brand-900 mr-3">Edit</a>
                                <a href="delete.php?id=<?= $cat['id'] ?>" onclick="return confirm('Yakin ingin menghapus kategori ini? Berita terkait mungkin kehilangan kategorinya.')" class="text-red-600 hover:text-red-900">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-slate-500 text-sm">Belum ada kategori.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php require '../../includes/footer.php'; ?>
