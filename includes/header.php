<?php
if (session_status() === PHP_SESSION_NONE)
    session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
$role = $_SESSION['role'];
$name = $_SESSION['name'];

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard <?= ucfirst($role) ?> - #VHC</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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

<body class="bg-slate-50 text-slate-900 font-sans antialiased flex h-screen overflow-hidden"
    x-data="{ sidebarOpen: false }">

    <!-- Sidebar Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-slate-900/50 md:hidden"
        style="display: none;"></div>