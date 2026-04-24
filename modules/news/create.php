<?php
require '../../config/db.php';
require '../../includes/header.php';
require '../../includes/sidebar.php';
require '../../functions/upload.php';

if ($role !== 'kontributor' && $role !== 'admin') {
    header("Location: ../dashboard/index.php");
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category_id = $_POST['category_id'];
    $image_caption = trim($_POST['image_caption'] ?? '');

    if (empty($title) || empty($content) || empty($category_id)) {
        $error = "Semua kolom wajib diisi!";
    } else {
        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $uploaded = handleNewsImageUpload($_FILES['image']);
            if ($uploaded) {
                $image_path = $uploaded;
            } else {
                $error = "Format gambar tidak didukung atau gagal dikompres.";
            }
        }

        if (empty($error)) {
            $stmt = $conn->prepare("INSERT INTO news (title, image, image_caption, content, category_id, author_id, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
            if ($stmt->execute([$title, $image_path, $image_caption, $content, $category_id, $_SESSION['user_id']])) {
                $success = "Berita berhasil dikirim dan menunggu review editor.";
            } else {
                $error = "Terjadi kesalahan sistem.";
            }
        }
    }
}

$categories = $conn->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
?>
<main class="flex-1 overflow-y-auto bg-slate-50/50 p-4 sm:p-6 lg:p-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Tulis Berita Baru</h1>
        <p class="text-slate-500">Kirimkan artikel untuk direview oleh editor.</p>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm max-w-2xl">
        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 text-sm">
                <?= $error ?>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-50 text-green-600 p-4 rounded-lg mb-6 text-sm">
                <?= $success ?> <a href="index.php" class="font-bold underline">Kembali ke kelola berita</a>
            </div>
        <?php endif; ?>

        <form id="newsForm" method="POST" action="" enctype="multipart/form-data">
            <div class="mb-5">
                <label class="block text-sm font-medium text-slate-700 mb-1">Judul Berita</label>
                <input type="text" name="title" required
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                    placeholder="Masukkan judul yang menarik">
            </div>

            <div class="mb-5">
                <label class="block text-sm font-medium text-slate-700 mb-1">Kategori</label>
                <select name="category_id" required
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition bg-white">
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-5">
                <label class="block text-sm font-medium text-slate-700 mb-1">Thumbnail Berita (Opsional)</label>
                <input type="file" name="image" accept="image/*"
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition bg-white text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                <p class="text-xs text-slate-500 mt-1">Gambar akan dikompres secara otomatis.</p>
            </div>

            <div class="mb-5">
                <label class="block text-sm font-medium text-slate-700 mb-1">Keterangan Gambar (Caption)</label>
                <input type="text" name="image_caption"
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                    placeholder="Tuliskan keterangan gambar (opsional)">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">Isi Berita</label>
                <div class="bg-white rounded-lg overflow-hidden border border-slate-300">
                    <div id="quill-editor" class="min-h-[400px]"></div>
                </div>
                <input type="hidden" name="content" id="content_input">
            </div>

            <div class="flex items-center gap-4">
                <button type="button" onclick="submitNews()"
                    class="bg-brand-600 text-white font-semibold py-2 px-6 rounded-lg hover:bg-brand-700 transition shadow-sm hover:shadow">
                    Kirim Berita
                </button>
                <a href="index.php" class="text-slate-500 hover:text-slate-700 font-medium">Batal</a>
            </div>
        </form>
    </div>
</main>

<link href="../../node_modules/quill/dist/quill.snow.css" rel="stylesheet">
<script src="../../node_modules/quill/dist/quill.js"></script>
<script>
    var quill = new Quill('#quill-editor', {
        theme: 'snow',
        placeholder: 'Tulis konten berita di sini...',
        modules: {
            toolbar: [
                [{ 'font': [] }, { 'size': [] }],
                [{ 'header': [2, 3, 4, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                ['blockquote'],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                ['link', 'image'],
                ['clean']
            ]
        }
    });

    function submitNews() {
        var content = quill.root.innerHTML;
        if (quill.getText().trim().length === 0 && !content.includes('<img')) {
            alert('Isi berita tidak boleh kosong!');
            return;
        }
        document.getElementById('content_input').value = content;
        document.getElementById('newsForm').submit();
    }
</script>

<?php require '../../includes/footer.php'; ?>