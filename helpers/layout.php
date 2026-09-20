<?php

function renderPublicHeader($title = "PT. BARONGKO DARMA LOGISTIK - Tracking Barang", $description = "Sistem Tracking Barang dan Manajemen Logistik PT. Barongko Darma Logistik") {
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?></title>
    <meta name="description" content="<?= e($description) ?>">
    <meta property="og:title" content="<?= e($title) ?>">
    <meta property="og:description" content="<?= e($description) ?>">
    <meta property="og:type" content="website">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: {
                        bdl: { red: '#dd2c24', green: '#40bf4e', dark: '#2b2c1e' }
                    }
                }
            }
        }
        
        // Initial Dark Mode check
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; -webkit-font-smoothing: antialiased; transition: background-color 0.3s, color 0.3s; }
        .dark .bg-white { background-color: #1e293b !important; }
        .dark .bg-gray-50, .dark .bg-slate-50 { background-color: #0f172a !important; }
        .dark .border-gray-50, .dark .border-gray-100, .dark .border-gray-200 { border-color: #334155 !important; }
        .dark .text-gray-900 { color: #f8fafc !important; }
        .dark .text-gray-500 { color: #94a3b8 !important; }
        .dark .text-gray-400 { color: #64748b !important; }
        .dark .bg-gray-100 { background-color: #334155 !important; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-slate-900 text-gray-900 dark:text-gray-100 min-h-screen flex flex-col transition-colors duration-300">
<?php
}

function renderPublicFooter() {
?>
    <footer class="py-8 border-t border-gray-100 dark:border-slate-800 mt-auto">
        <div class="max-w-5xl mx-auto px-6 text-center space-y-1 relative">
            <p class="font-black text-sm">
                <span style="color:#dd2c24">PT. BARONGKO</span>
                <span style="color:#40bf4e"> DARMA LOGISTIK</span>
            </p>
            <p class="text-xs text-gray-400 dark:text-slate-400"><?= COMPANY_ADDRESS ?> | <?= COMPANY_PHONE ?> | <?= COMPANY_EMAIL ?></p>
            <p class="text-xs text-gray-300 dark:text-slate-500">&copy; <?= date('Y') ?> PT. Barongko Darma Logistik</p>
            
            <button onclick="toggleDarkMode()" title="Toggle Dark Mode" class="absolute right-6 bottom-0 text-gray-400 hover:text-indigo-500 transition-colors text-lg" id="theme-toggle">
                <i class="ri-moon-line" id="theme-toggle-icon"></i>
            </button>
        </div>
    </footer>
    <script>
        function toggleDarkMode() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('color-theme', isDark ? 'dark' : 'light');
            updateThemeIcon();
        }
        function updateThemeIcon() {
            const icon = document.getElementById('theme-toggle-icon');
            if (!icon) return;
            if (document.documentElement.classList.contains('dark')) {
                icon.className = 'ri-sun-line text-yellow-500';
            } else {
                icon.className = 'ri-moon-line text-gray-400';
            }
        }
        updateThemeIcon();
    </script>
</body>
</html>
<?php
}
