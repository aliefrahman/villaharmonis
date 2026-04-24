<?php
require '../../config/db.php';
require '../../includes/header.php';
require '../../includes/sidebar.php';

if($role !== 'admin') { header("Location: ../dashboard/index.php"); exit; }

if(!isset($_GET['id'])) { header("Location: index.php"); exit; }
$id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$id]);
$target_user = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$target_user) { header("Location: index.php"); exit; }

$error = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $user_role = $_POST['role'];
    $password = $_POST['password'];

    if(empty($name) || empty($email) || empty($user_role)) {
        $error = "Nama, Email, dan Role tidak boleh kosong!";
    } else {
        $email_ok = true;
        if($email !== $target_user['email']) {
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if($stmt->rowCount() > 0) $email_ok = false;
        }

        if(!$email_ok) {
            $error = "Email sudah digunakan oleh pengguna lain!";
        } else {
            if(!empty($password)) {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET name=?, email=?, role=?, password=? WHERE id=?");
                $result = $stmt->execute([$name, $email, $user_role, $hashed, $id]);
            } else {
                $stmt = $conn->prepare("UPDATE users SET name=?, email=?, role=? WHERE id=?");
                $result = $stmt->execute([$name, $email, $user_role, $id]);
            }
            
            if($result) {
                if($id == $_SESSION['user_id']) {
                    $_SESSION['name'] = $name;
                    $_SESSION['role'] = $user_role;
                }
                header("Location: index.php?msg=" . urlencode("Data pengguna berhasil diperbarui."));
                exit;
            } else {
                $error = "Gagal mengupdate data pengguna.";
            }
        }
    }
}
?>
<main class="flex-1 overflow-y-auto bg-slate-50/50 p-4 sm:p-6 lg:p-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Edit Pengguna</h1>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm max-w-xl">
        <?php if($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 text-sm"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-5">
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="<?= htmlspecialchars($target_user['name']) ?>" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition">
            </div>
            
            <div class="mb-5">
                <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($target_user['email']) ?>" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition">
            </div>
            
            <div class="mb-5">
                <label class="block text-sm font-medium text-slate-700 mb-1">Password Baru (Opsional)</label>
                <input type="password" name="password" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition" placeholder="Kosongkan jika tidak ingin mengubah password">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">Role Pengguna</label>
                <select name="role" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition bg-white">
                    <option value="admin" <?= $target_user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="editor" <?= $target_user['role'] == 'editor' ? 'selected' : '' ?>>Editor</option>
                    <option value="kontributor" <?= $target_user['role'] == 'kontributor' ? 'selected' : '' ?>>Kontributor</option>
                    <option value="user" <?= $target_user['role'] == 'user' ? 'selected' : '' ?>>User Biasa</option>
                </select>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="bg-brand-600 text-white font-semibold py-2 px-6 rounded-lg hover:bg-brand-700 transition shadow-sm hover:shadow">Update Pengguna</button>
                <a href="index.php" class="text-slate-500 hover:text-slate-700 font-medium">Batal</a>
            </div>
        </form>
    </div>
</main>
<?php require '../../includes/footer.php'; ?>
