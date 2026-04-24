<?php
require '../../config/db.php';
require '../../includes/header.php';
require '../../includes/sidebar.php';

if($role !== 'admin') { header("Location: ../dashboard/index.php"); exit; }

$error = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $user_role = $_POST['role'];

    if(empty($name) || empty($email) || empty($password) || empty($user_role)) {
        $error = "Semua kolom wajib diisi!";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if($stmt->rowCount() > 0) {
            $error = "Email sudah terdaftar!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            if($stmt->execute([$name, $email, $hashed_password, $user_role])) {
                header("Location: index.php?msg=" . urlencode("Pengguna baru berhasil ditambahkan."));
                exit;
            } else {
                $error = "Terjadi kesalahan sistem saat menyimpan data.";
            }
        }
    }
}
?>
<main class="flex-1 overflow-y-auto bg-slate-50/50 p-4 sm:p-6 lg:p-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Tambah Pengguna</h1>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm max-w-xl">
        <?php if($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 text-sm"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-5">
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition" placeholder="John Doe">
            </div>
            
            <div class="mb-5">
                <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                <input type="email" name="email" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition" placeholder="email@contoh.com">
            </div>
            
            <div class="mb-5">
                <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition" placeholder="Minimal 6 karakter">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">Role Pengguna</label>
                <select name="role" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition bg-white">
                    <option value="">-- Pilih Role --</option>
                    <option value="admin">Admin</option>
                    <option value="editor">Editor</option>
                    <option value="kontributor">Kontributor</option>
                    <option value="user">User Biasa</option>
                </select>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="bg-brand-600 text-white font-semibold py-2 px-6 rounded-lg hover:bg-brand-700 transition shadow-sm hover:shadow">Simpan Pengguna</button>
                <a href="index.php" class="text-slate-500 hover:text-slate-700 font-medium">Batal</a>
            </div>
        </form>
    </div>
</main>
<?php require '../../includes/footer.php'; ?>
