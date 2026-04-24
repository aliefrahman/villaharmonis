<?php
require '../../config/db.php';
require '../../includes/header.php';
require '../../includes/sidebar.php';

if($role !== 'admin') { header("Location: ../dashboard/index.php"); exit; }

$stmt = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<main class="flex-1 overflow-y-auto bg-slate-50/50 p-4 sm:p-6 lg:p-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Kelola Pengguna</h1>
            <p class="text-slate-500">Daftar semua pengguna terdaftar dalam sistem.</p>
        </div>
        <a href="create.php" class="bg-brand-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-brand-700 shadow-sm transition inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Pengguna
        </a>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div class="bg-green-50 text-green-600 p-4 rounded-lg mb-6 text-sm max-w-5xl">
            <?= htmlspecialchars($_GET['msg']) ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden max-w-6xl">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Pengguna</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Terdaftar Sejak</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    <?php if(count($users) > 0): ?>
                        <?php foreach($users as $usr): ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-slate-900"><?= htmlspecialchars($usr['name']) ?></div>
                                    <div class="text-xs text-slate-500"><?= htmlspecialchars($usr['email']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php 
                                        $roleColors = [
                                            'admin' => 'bg-purple-100 text-purple-800',
                                            'editor' => 'bg-orange-100 text-orange-800',
                                            'kontributor' => 'bg-brand-100 text-brand-800',
                                            'user' => 'bg-slate-100 text-slate-800'
                                        ];
                                        $color = $roleColors[$usr['role']] ?? 'bg-slate-100 text-slate-800';
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium uppercase <?= $color ?>">
                                        <?= htmlspecialchars($usr['role']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                    <?= date('d M Y', strtotime($usr['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="edit.php?id=<?= $usr['id'] ?>" class="text-brand-600 hover:text-brand-900 mr-3">Edit</a>
                                    <?php if($usr['id'] != $_SESSION['user_id']): ?>
                                        <a href="delete.php?id=<?= $usr['id'] ?>" onclick="return confirm('Yakin ingin menghapus pengguna ini? Semua data miliknya mungkin ikut terhapus.')" class="text-red-600 hover:text-red-900">Hapus</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-500 text-sm">Belum ada pengguna.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php require '../../includes/footer.php'; ?>
