<?php
session_start();
require '../../config/db.php';
require '../../functions/auth_security.php';

if (isset($_SESSION['user_id'])) {
    header("Location: ../dashboard/index.php");
    exit;
}

$error = '';
$success = '';
$csrf_token = generate_csrf_token();
$old_name = '';
$old_email = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Verifikasi CSRF Token
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("Invalid CSRF token! Permintaan ditolak.");
    }

    // 2. Cegah Spam / Rate Limiting Pendaftaran
    if (!check_login_attempts($_SERVER['REMOTE_ADDR'] ?? '')) {
        $error = 'Terlalu banyak percobaan. Silakan tunggu 5 menit.';
    } else {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        $old_name = $name;
        $old_email = $email;

        if ($password !== $confirm_password) {
            $error = "Password tidak cocok!";
            record_failed_login();
        } else {
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                $error = "Email sudah terdaftar!";
                record_failed_login();
            } else {
                // 3. Password Hashing
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
                if ($stmt->execute([$name, $email, $hashed_password])) {
                    $success = "Pendaftaran berhasil! Silakan login.";
                    reset_login_attempts();
                    $old_name = '';
                    $old_email = '';
                } else {
                    $error = "Terjadi kesalahan sistem.";
                    record_failed_login();
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - #VHC</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style type="text/tailwindcss">
        @theme {
            --color-brand-50: #EEF5DB;
            --color-brand-100: #E6EFCD;
            --color-brand-200: #B8D8D8;
            --color-brand-300: #9ABDBD;
            --color-brand-400: #7A9E9F;
            --color-brand-500: #4F6367;
            --color-brand-600: #FE5F55;
            --color-brand-700: #E35248;
            --color-brand-800: #C2453D;
            --color-brand-900: #A13832;
            --font-sans: "Plus Jakarta Sans", ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        }
    </style>
</head>

<body class="bg-slate-50 flex items-center justify-center min-h-screen py-10">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border border-slate-100">
        <div class="text-center mb-8">
            <a href="../../index.php" class="inline-flex items-center gap-2 text-2xl font-bold text-brand-600 mb-6">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H14">
                    </path>
                </svg>
                #VHC
            </a>
            <h2 class="text-3xl font-bold text-slate-900">Daftar Akun Baru</h2>
            <p class="text-slate-500 mt-2">Bergabunglah untuk dapatkan berita terkini</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 text-sm">
                <?= $error ?>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-50 text-green-600 p-4 rounded-lg mb-6 text-sm">
                <?= $success ?> <a href="login.php" class="font-bold underline">Login di sini</a>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="<?= htmlspecialchars($old_name) ?>" required
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                    placeholder="John Doe">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($old_email) ?>" required
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                    placeholder="email@contoh.com">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                    placeholder="••••••••">
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password</label>
                <input type="password" name="confirm_password" required
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                    placeholder="••••••••">
            </div>
            <button type="submit"
                class="w-full bg-brand-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-brand-700 transition shadow-sm hover:shadow">
                Daftar
            </button>
        </form>
        <div class="mt-6 text-center text-sm text-slate-500">
            Sudah punya akun? <a href="login.php" class="text-brand-600 hover:underline">Masuk sekarang</a>
        </div>
    </div>
</body>

</html>