<?php
require '../../config/db.php';
require '../../includes/header.php';
require '../../includes/sidebar.php';

if($role !== 'admin') { header("Location: ../dashboard/index.php"); exit; }

if(!isset($_GET['id'])) { header("Location: index.php"); exit; }
$id = $_GET['id'];

$error = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    if(empty($name)) {
        $error = "Nama kategori tidak boleh kosong!";
    } else {
        $stmt = $conn->prepare("UPDATE categories SET name=? WHERE id=?");
        try {
            $stmt->execute([$name, $id]);
            header("Location: index.php?msg=" . urlencode("Kategori berhasil diubah."));
            exit;
        } catch(PDOException $e) {
            $error = "Gagal mengubah kategori, mungkin nama sudah ada.";
        }
    }
}

$stmt = $conn->prepare("SELECT * FROM categories WHERE id=?");
$stmt->execute([$id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$category) { header("Location: index.php"); exit; }
?>
<main class="flex-1 overflow-y-auto bg-slate-50/50 p-4 sm:p-6 lg:p-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Edit Kategori</h1>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm max-w-md">
        <?php if($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 text-sm"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-5">
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Kategori</label>
                <input type="text" name="name" value="<?= htmlspecialchars($category['name']) ?>" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition">
            </div>
            <div class="flex items-center gap-4">
                <button type="submit" class="bg-brand-600 text-white font-semibold py-2 px-6 rounded-lg hover:bg-brand-700 transition shadow-sm hover:shadow">Update</button>
                <a href="index.php" class="text-slate-500 hover:text-slate-700 font-medium">Batal</a>
            </div>
        </form>
    </div>
</main>
<?php require '../../includes/footer.php'; ?>
