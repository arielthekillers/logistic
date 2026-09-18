<?php

function renderPublicHeader($title = "PT. BARONGKO DARMA LOGISTIK - Tracking Barang") {
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: {
                        bdl: { red: '#dd2c24', green: '#40bf4e', dark: '#2b2c1e' }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; -webkit-font-smoothing: antialiased; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col">
<?php
}

function renderPublicFooter() {
?>
    <footer class="py-8 border-t border-gray-100 mt-auto">
        <div class="max-w-5xl mx-auto px-6 text-center space-y-1">
            <p class="font-black text-sm">
                <span style="color:#dd2c24">PT. BARONGKO</span>
                <span style="color:#40bf4e"> DARMA LOGISTIK</span>
            </p>
            <p class="text-xs text-gray-400"><?= COMPANY_ADDRESS ?> | <?= COMPANY_PHONE ?> | <?= COMPANY_EMAIL ?></p>
            <p class="text-xs text-gray-300">&copy; <?= date('Y') ?> PT. Barongko Darma Logistik</p>
        </div>
    </footer>
</body>
</html>
<?php
}
