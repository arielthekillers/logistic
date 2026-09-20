<?php

function renderSidebarHeader($title = "PT. Barongko Darma Logistik") {
    $currentUri = $_SERVER['REQUEST_URI'] ?? '';
    $user = auth_user();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?></title>
    <!-- Plus Jakarta Sans - Modern Geometric Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <!-- RemixIcon -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- JsBarcode -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <!-- HTML5-QRCode -->
    <script src="https://unpkg.com/html5-qrcode"></script>
    <!-- PWA Manifest & Service Worker -->
    <link rel="manifest" href="<?= url('/manifest.json') ?>">
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('<?= url('/sw.js') ?>')
                    .then(reg => console.log('SW Registered', reg))
                    .catch(err => console.error('SW Registration Failed', err));
            });
        }
    </script>
    
    <!-- QRCode.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        bdl: {
                            red: '#dd2c24',
                            green: '#40bf4e',
                            dark: '#2b2c1e',
                        }
                    },
                    borderRadius: {
                        '2.5xl': '1.25rem',
                        '3xl': '1.5rem',
                        '4xl': '2rem',
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
        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f0f0ee;
            color: #1a1a1a;
            -webkit-font-smoothing: antialiased;
            transition: background-color 0.3s, color 0.3s;
        }
        
        .dark body { background-color: #0f172a; color: #f8fafc; }

        /* ─── Sidebar ─────────────────────────────── */
        #sidebar {
            background: #ffffff;
            border-right: 1px solid #e8e8e4;
            transition: background-color 0.3s, border-color 0.3s;
        }
        .dark #sidebar { background: #1e293b; border-right: 1px solid #334155; }

        .sidebar-brand {
            padding: 20px 20px 16px;
            border-bottom: 1px solid #f0f0ee;
        }
        .dark .sidebar-brand { border-bottom-color: #334155; }

        .sidebar-logo-box {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-logo-icon {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: #2b2c1e;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            overflow: hidden;
        }

        .sidebar-logo-icon img { width: 24px; height: 24px; object-fit: contain; filter: brightness(0) invert(1); }

        .sidebar-logo-text { line-height: 1.2; }
        .sidebar-logo-text .name { font-size: 13px; font-weight: 800; color: #1a1a1a; letter-spacing: -0.3px; }
        .dark .sidebar-logo-text .name { color: #f8fafc; }
        .sidebar-logo-text .sub  { font-size: 10px; font-weight: 500; color: #9a9a90; }
        .dark .sidebar-logo-text .sub { color: #cbd5e1; }

        /* Nav */
        .nav-section-label {
            font-size: 10px;
            font-weight: 700;
            color: #b0b0a8;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 20px 16px 6px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 14px;
            border-radius: 12px;
            font-size: 13.5px;
            font-weight: 500;
            color: #6b6b65;
            text-decoration: none;
            transition: all 0.18s ease;
            margin: 1px 8px;
        }
        .dark .nav-link { color: #94a3b8; }

        .nav-link i { font-size: 16px; flex-shrink: 0; }

        .nav-link:hover {
            background: #f5f5f2;
            color: #1a1a1a;
        }
        .dark .nav-link:hover { background: #334155; color: #f8fafc; }

        .nav-link.active {
            background: #2b2c1e;
            color: #ffffff;
            font-weight: 700;
        }
        .dark .nav-link.active { background: #4f46e5; color: #ffffff; }

        .nav-link.active i { color: #40bf4e; }
        .dark .nav-link.active i { color: #a5b4fc; }

        /* Sidebar User Footer */
        .sidebar-user {
            padding: 14px 16px;
            border-top: 1px solid #f0f0ee;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .dark .sidebar-user { border-top-color: #334155; }

        .sidebar-user-info { flex: 1; min-width: 0; line-height: 1.2; }
        .sidebar-user-info .uname { font-size: 13px; font-weight: 700; color: #1a1a1a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 1px; display: block; }
        .dark .sidebar-user-info .uname { color: #f8fafc; }
        .sidebar-user-info .urole { font-size: 11px; color: #9a9a90; font-weight: 500; display: block; }

        /* ─── Top bar (mobile) ─────────────────────── */
        #mobile-topbar {
            background: #fff;
            border-bottom: 1px solid #e8e8e4;
            padding: 12px 16px;
            z-index: 40;
        }
        .dark #mobile-topbar { background: #1e293b; border-bottom-color: #334155; }
        .dark #mobile-topbar span.text-gray-900 { color: #f8fafc; }
        .dark #mobile-topbar button.text-gray-600 { color: #cbd5e1; }

        /* ─── Main Content ─────────────────────────── */
        #main-content {
            flex: 1;
            min-width: 0;
            padding: 28px;
        }

        @media (max-width: 767px) {
            #main-content { padding: 16px; }
        }

        /* ─── Card base ────────────────────────────── */
        .card {
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid #ebebea;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        }
        .dark .card { background: #1e293b; border-color: #334155; box-shadow: 0 1px 4px rgba(0,0,0,0.4); }

        .card-hover {
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .card-hover:hover {
            box-shadow: 0 6px 24px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }
        .dark .card-hover:hover { box-shadow: 0 6px 24px rgba(0,0,0,0.6); }

        /* ─── Stat Card ─────────────────────────────── */
        .stat-icon {
            width: 44px; height: 44px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        /* ─── Buttons ─────────────────────────────── */
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 20px;
            background: #2b2c1e;
            color: #ffffff;
            border-radius: 12px;
            font-size: 13.5px;
            font-weight: 700;
            border: 1.5px solid transparent;
            cursor: pointer;
            transition: all 0.18s;
            text-decoration: none;
        }
        .btn-primary:hover { background: #3d3f2a; box-shadow: 0 4px 14px rgba(43,44,30,0.25); }
        .dark .btn-primary { background: transparent; color: #a5b4fc; border-color: #6366f1; }
        .dark .btn-primary:hover { background: rgba(99,102,241,0.1); box-shadow: 0 4px 14px rgba(99,102,241,0.2); }

        .btn-accent {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 20px;
            background: #dd2c24;
            color: #ffffff;
            border-radius: 12px;
            font-size: 13.5px;
            font-weight: 700;
            border: 1.5px solid transparent;
            cursor: pointer;
            transition: all 0.18s;
            text-decoration: none;
        }
        .btn-accent:hover { background: #c7221b; box-shadow: 0 4px 14px rgba(221,44,36,0.3); }
        .dark .btn-accent { background: transparent; color: #fca5a5; border-color: #ef4444; }
        .dark .btn-accent:hover { background: rgba(239,68,68,0.1); box-shadow: 0 4px 14px rgba(239,68,68,0.2); }

        .btn-green {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 20px;
            background: #40bf4e;
            color: #ffffff;
            border-radius: 12px;
            font-size: 13.5px;
            font-weight: 700;
            border: 1.5px solid transparent;
            cursor: pointer;
            transition: all 0.18s;
            text-decoration: none;
        }
        .btn-green:hover { background: #34a340; box-shadow: 0 4px 14px rgba(64,191,78,0.3); }
        .dark .btn-green { background: transparent; color: #86efac; border-color: #22c55e; }
        .dark .btn-green:hover { background: rgba(34,197,94,0.1); box-shadow: 0 4px 14px rgba(34,197,94,0.2); }

        .btn-outline {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 20px;
            background: #ffffff;
            color: #1a1a1a;
            border-radius: 12px;
            font-size: 13.5px;
            font-weight: 600;
            border: 1.5px solid #e0e0dc;
            cursor: pointer;
            transition: all 0.18s;
            text-decoration: none;
        }
        .btn-outline:hover { background: #f5f5f2; border-color: #ccc; }
        .dark .btn-outline { background: transparent; color: #f8fafc; border-color: #475569; }
        .dark .btn-outline:hover { background: rgba(71,85,105,0.2); }

        /* ─── Table ───────────────────────────────── */
        .data-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
        .data-table thead th {
            padding: 12px 18px;
            font-size: 11px;
            font-weight: 700;
            color: #9a9a90;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            border-bottom: 1px solid #f0f0ee;
            text-align: left;
            background: #fafaf8;
        }
        .dark .data-table thead th { background: #0f172a; border-bottom-color: #334155; color: #94a3b8; }
        .data-table tbody tr { border-bottom: 1px solid #f5f5f2; transition: background 0.15s; }
        .dark .data-table tbody tr { border-bottom-color: #334155; }
        .data-table tbody tr:last-child { border-bottom: none; }
        .data-table tbody tr:hover { background: #fafaf8; }
        .dark .data-table tbody tr:hover { background: #1e293b; }
        .data-table tbody td { padding: 13px 18px; color: #3a3a35; vertical-align: middle; }
        .dark .data-table tbody td { color: #cbd5e1; }

        /* ─── Form Inputs ─────────────────────────── */
        .form-label {
            display: block;
            font-size: 11.5px;
            font-weight: 700;
            color: #6b6b65;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 6px;
        }
        .dark .form-label { color: #94a3b8; }

        .form-input {
            width: 100%;
            border: 1.5px solid #e8e8e4;
            border-radius: 12px;
            padding: 10px 14px;
            font-size: 13.5px;
            font-weight: 500;
            color: #1a1a1a;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #fafaf8;
            outline: none;
            transition: border-color 0.18s, box-shadow 0.18s;
        }
        .dark .form-input { background: #0f172a; border-color: #334155; color: #f8fafc; }
        
        .form-input:focus {
            border-color: #2b2c1e;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(43,44,30,0.08);
        }
        .dark .form-input:focus { border-color: #4f46e5; background: #1e293b; box-shadow: 0 0 0 3px rgba(79,70,229,0.2); }
        
        .form-input:disabled, .form-input[readonly] {
            background: #f0f0ee;
            color: #9a9a90;
            cursor: not-allowed;
        }
        .dark .form-input:disabled, .dark .form-input[readonly] { background: #1e293b; color: #64748b; border-color: #334155; }

        /* ─── Modal ───────────────────────────────── */
        .modal-overlay {
            position: fixed; inset: 0;
            background: rgba(10,10,8,0.45);
            backdrop-filter: blur(8px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .modal-overlay.hidden { display: none !important; }

        .modal-box {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 24px 64px rgba(0,0,0,0.14), 0 0 0 1px rgba(0,0,0,0.04);
            max-width: 460px;
            width: 100%;
            overflow: hidden;
            animation: modalIn 0.28s cubic-bezier(0.16,1,0.3,1);
        }
        .dark .modal-box { background: #1e293b; box-shadow: 0 24px 64px rgba(0,0,0,0.5), 0 0 0 1px rgba(255,255,255,0.05); }

        @keyframes modalIn {
            from { opacity: 0; transform: translateY(24px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        .modal-head {
            padding: 20px 24px 18px;
            border-bottom: 1px solid #f0f0ee;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .dark .modal-head { border-bottom-color: #334155; }
        
        .modal-head h3 {
            font-size: 16px;
            font-weight: 800;
            color: #1a1a1a;
            letter-spacing: -0.3px;
        }
        .dark .modal-head h3 { color: #f8fafc; }

        .modal-head .close-btn {
            width: 32px; height: 32px;
            border-radius: 8px;
            border: 1.5px solid #e8e8e4;
            background: #fafaf8;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            color: #6b6b65;
            transition: all 0.15s;
        }
        .dark .modal-head .close-btn { background: #0f172a; border-color: #334155; color: #94a3b8; }
        .dark .modal-head .close-btn:hover { background: #334155; color: #f8fafc; }

        .modal-body { padding: 22px 24px; }
        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid #f0f0ee;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .dark .modal-footer { border-top-color: #334155; }

        /* ─── Flash / Alert ───────────────────────── */
        .alert {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            border-radius: 14px;
            font-size: 13.5px;
            font-weight: 600;
        }
        .alert-success { background: #f0fdf3; color: #15803d; border: 1px solid #bbf7d0; }
        .dark .alert-success { background: rgba(21,128,61,0.2); color: #86efac; border-color: rgba(21,128,61,0.5); }
        .alert-error   { background: #fff5f5; color: #b91c1c; border: 1px solid #fecaca; }
        .dark .alert-error { background: rgba(185,28,28,0.2); color: #fca5a5; border-color: rgba(185,28,28,0.5); }
        .alert i { font-size: 18px; flex-shrink: 0; }

        /* ─── Badge ───────────────────────────────── */
        .badge {
            display: inline-flex; align-items: center;
            padding: 3px 10px;
            border-radius: 100px;
            font-size: 11.5px;
            font-weight: 700;
        }

        /* ─── Page heading ────────────────────────── */
        .page-title {
            font-size: 22px;
            font-weight: 800;
            color: #1a1a1a;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }
        .dark .page-title { color: #f8fafc; }
        .page-subtitle {
            font-size: 13px;
            color: #9a9a90;
            font-weight: 400;
            margin-top: 3px;
        }

        /* ─── Universal Dark Mode Helpers ─── */
        .dark .text-gray-900 { color: #f8fafc !important; }
        .dark .text-gray-800 { color: #e2e8f0 !important; }
        .dark .text-gray-700 { color: #cbd5e1 !important; }
        .dark .bg-white { background-color: #1e293b !important; }
        .dark .bg-gray-50 { background-color: #0f172a !important; }
        .dark .border-gray-50, .dark .border-gray-100, .dark .border-gray-200 { border-color: #334155 !important; }
        .dark .bg-blue-50 { background-color: rgba(59,130,246,0.15) !important; }
        .dark .border-blue-300 { border-color: rgba(59,130,246,0.3) !important; }
        .dark [style*="color:#2b2c1e"], .dark [style*="color: #2b2c1e"] { color: #f8fafc !important; }
        .dark [style*="background:#f5f5f0"], .dark [style*="background: #f5f5f0"] { background: #334155 !important; }
        .dark [style*="color:#6b6b65"] { color: #94a3b8 !important; }
        
        /* ─── Scrollbar ───────────────────────────── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d0d0cc; border-radius: 10px; }
        .dark ::-webkit-scrollbar-thumb { background: #475569; }
    </style>
</head>
<body>
<div class="flex min-h-screen">

    <!-- ═══ SIDEBAR ════════════════════════════════════════ -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 flex flex-col transform -translate-x-full md:translate-x-0 transition-transform duration-200 ease-in-out" style="width:240px; box-shadow: none;">

        <!-- Brand -->
        <div class="sidebar-brand">
            <a href="<?= url('/dashboard') ?>" class="sidebar-logo-box">
                <div class="sidebar-logo-icon">
                    <img src="<?= url('/' . COMPANY_LOGO) ?>" alt="BDL">
                </div>
                <div class="sidebar-logo-text">
                    <div class="name">Barongko Logistik</div>
                    <div class="sub">Tracking System</div>
                </div>
            </a>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto py-2">
            <div class="nav-section-label">Utama</div>

            <a href="<?= url('/dashboard') ?>" class="nav-link <?= (strpos($currentUri, '/dashboard') !== false) ? 'active' : '' ?>">
                <i class="ri-home-4-line"></i> Dashboard
            </a>

            <a href="<?= url('/shipments') ?>" class="nav-link <?= (strpos($currentUri, '/shipments') !== false && strpos($currentUri, '/create') === false) ? 'active' : '' ?>">
                <i class="ri-box-3-line"></i> Data Pengiriman
            </a>

            <a href="<?= url('/shipments/create') ?>" class="nav-link <?= (strpos($currentUri, '/shipments/create') !== false) ? 'active' : '' ?>">
                <i class="ri-add-circle-line"></i> Buat Resi Baru
            </a>

            <div class="nav-section-label">Operasional</div>

            <a href="<?= url('/scanner') ?>" class="nav-link <?= (strpos($currentUri, '/scanner') !== false) ? 'active' : '' ?>">
                <i class="ri-qr-scan-2-line"></i> Scan Kamera HP
            </a>

            <?php if (has_role('admin')): ?>
                <div class="nav-section-label mt-6">Administrasi</div>

                <a href="<?= url('/hubs') ?>" class="nav-link <?= (strpos($currentUri, '/hubs') !== false) ? 'active' : '' ?>">
                    <i class="ri-store-2-line"></i> Data Gudang / Hub
                </a>

                <a href="<?= url('/users') ?>" class="nav-link <?= (strpos($currentUri, '/users') !== false) ? 'active' : '' ?>">
                    <i class="ri-group-line"></i> Data Pengguna
                </a>
                
                <a href="<?= url('/reports') ?>" class="nav-link <?= (strpos($currentUri, '/reports') !== false) ? 'active' : '' ?>">
                    <i class="ri-file-excel-2-line"></i> Laporan & Export
                </a>
                
                <a href="<?= url('/settings') ?>" class="nav-link <?= (strpos($currentUri, '/settings') !== false) ? 'active' : '' ?>">
                    <i class="ri-settings-3-line"></i> Pengaturan
                </a>
            <?php endif; ?>

            <div class="nav-section-label">Public</div>

            <a href="<?= url('/tracking') ?>" target="_blank" class="nav-link">
                <i class="ri-map-pin-2-line"></i> Tracking Customer
            </a>
        </nav>

        <!-- User Footer -->
        <div class="sidebar-user">
            <?php if (!empty($user['avatar'])): ?>
                <img src="<?= url('/' . $user['avatar']) ?>" alt="Avatar" class="w-8 h-8 rounded-full object-cover flex-shrink-0">
            <?php else: ?>
                <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center text-white text-sm font-black" style="background:linear-gradient(135deg,#40bf4e,#2b2c1e)">
                    <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
                </div>
            <?php endif; ?>
            <div class="sidebar-user-info">
                <a href="<?= url('/profile') ?>" class="uname hover:underline block"><?= e($user['name'] ?? 'Operator') ?></a>
                <span class="urole"><?= e(ucwords(str_replace('_', ' ', $user['role'] ?? ''))) ?></span>
            </div>
            
            <button onclick="toggleDarkMode()" title="Toggle Dark Mode" class="text-gray-400 hover:text-indigo-500 transition-colors text-lg ml-1" id="theme-toggle">
                <i class="ri-moon-line" id="theme-toggle-icon"></i>
            </button>

            <a href="<?= url('/logout') ?>" title="Logout" class="text-gray-400 hover:text-red-500 transition-colors text-lg ml-2">
                <i class="ri-logout-box-r-line"></i>
            </a>
        </div>
    </aside>

    <!-- ═══ MAIN ════════════════════════════════════════════ -->
    <div class="flex-1 flex flex-col" style="margin-left: 0;" id="page-wrapper">
        <!-- Mobile Top Bar -->
        <div id="mobile-topbar" class="md:hidden flex items-center justify-between sticky top-0">
            <button onclick="toggleSidebar()" class="text-gray-600 hover:text-gray-900 p-1">
                <i class="ri-menu-line text-xl"></i>
            </button>
            <span class="font-black text-sm text-gray-900">BDL Logistik</span>
            <a href="<?= url('/profile') ?>" class="block">
                <?php if (!empty($user['avatar'])): ?>
                    <img src="<?= url('/' . $user['avatar']) ?>" class="w-8 h-8 rounded-full object-cover">
                <?php else: ?>
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-black" style="background:#2b2c1e">
                        <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
                    </div>
                <?php endif; ?>
            </a>
        </div>

        <!-- Main Content -->
        <main id="main-content" class="md:ml-60 flex-1 p-7" style="margin-left:0">
            <!-- Flash data for toast -->
            <?php
                $toastMsg  = '';
                $toastType = '';
                if (!empty($_SESSION['success_flash'])) {
                    $toastMsg  = $_SESSION['success_flash'];
                    $toastType = 'success';
                    unset($_SESSION['success_flash']);
                } elseif (!empty($_SESSION['error_flash'])) {
                    $toastMsg  = $_SESSION['error_flash'];
                    $toastType = 'error';
                    unset($_SESSION['error_flash']);
                }
            ?>
            <?php if ($toastMsg): ?>
            <div id="__toast_data__"
                 data-msg="<?= e($toastMsg) ?>"
                 data-type="<?= $toastType ?>">
            </div>
            <?php endif; ?>
            <!-- actual content goes here -->
<?php
}

function renderSidebarFooter() {
?>
        </main>
        <footer class="md:ml-60 py-4 px-7 text-center border-t border-gray-100 bg-white" style="margin-left:0">
            <p class="text-xs text-gray-400">&copy; <?= date('Y') ?> <strong class="text-gray-600"><?= COMPANY_NAME ?></strong>. All rights reserved.</p>
        </footer>
    </div>
</div>

<!-- Mobile overlay -->
<div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/30 backdrop-blur-sm z-40 md:hidden hidden"></div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

    // Toggle Dark Mode
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

    // Set margin-left for main content on desktop
    function adjustLayout() {
        const pw = document.getElementById('page-wrapper');
        const mc = document.getElementById('main-content');
        const ft = document.querySelector('footer');
        if (window.innerWidth >= 768) {
            pw.style.marginLeft = '240px';
        } else {
            pw.style.marginLeft = '0';
        }
    }
    adjustLayout();
    window.addEventListener('resize', adjustLayout);

    // ═══ Stacking Toast System ═══════════════════
    const toastContainer = document.createElement('div');
    toastContainer.id = 'bdl-toast-container';
    toastContainer.className = 'fixed top-4 right-4 z-[9999] flex flex-col gap-3 pointer-events-none items-end';
    document.body.appendChild(toastContainer);

    window.showToast = function(msg, type = 'success') {
        const toast = document.createElement('div');
        
        // Colors & Icons
        let border = 'border-gray-100';
        let iconHtml = '<i class="ri-information-fill text-blue-500 text-2xl"></i>';

        if (type === 'success') {
            border = 'border-emerald-100';
            iconHtml = '<i class="ri-checkbox-circle-fill text-emerald-500 text-2xl"></i>';
        } else if (type === 'error') {
            border = 'border-red-100';
            iconHtml = '<i class="ri-close-circle-fill text-red-500 text-2xl"></i>';
        } else if (type === 'warning') {
            border = 'border-amber-100';
            iconHtml = '<i class="ri-error-warning-fill text-amber-500 text-2xl"></i>';
        }

        toast.className = `flex items-center gap-3 px-5 py-4 bg-white border ${border} shadow-xl shadow-black/5 rounded-2xl pointer-events-auto transform transition-all duration-300 translate-x-[120%] opacity-0 max-w-sm w-max`;
        
        toast.innerHTML = `
            <div class="flex-shrink-0 flex items-center justify-center">${iconHtml}</div>
            <div class="flex-1 text-[13.5px] font-semibold text-gray-800 leading-snug">${msg}</div>
            <button class="flex-shrink-0 ml-2 text-gray-400 hover:text-gray-900 transition-colors bg-gray-50 hover:bg-gray-100 w-7 h-7 rounded-full flex items-center justify-center" onclick="closeToast(this.parentElement)">
                <i class="ri-close-line"></i>
            </button>
        `;

        // Prepend agar notif terbaru muncul di paling atas (bersusun)
        toastContainer.prepend(toast); 

        // Trigger animation
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                toast.classList.remove('translate-x-[120%]', 'opacity-0');
                toast.classList.add('translate-x-0', 'opacity-100');
            });
        });

        // Auto remove
        const autoRemove = setTimeout(() => {
            closeToast(toast);
        }, 4000);

        // Hover pause
        toast.addEventListener('mouseenter', () => clearTimeout(autoRemove));
    };

    window.closeToast = function(toastEl) {
        if(!toastEl || !toastEl.parentElement) return;
        toastEl.classList.remove('translate-x-0', 'opacity-100');
        toastEl.classList.add('translate-x-[120%]', 'opacity-0');
        setTimeout(() => {
            if(toastEl.parentElement) toastEl.remove();
        }, 300);
    };

    // Proxy SwalToast.fire agar kompatibel dengan kode lama
    window.SwalToast = {
        fire: function(obj) {
            window.showToast(obj.title || obj.text, obj.icon || 'success');
        }
    };

    // Tembak dari PHP flash data
    const __fl = document.getElementById('__toast_data__');
    if (__fl) {
        showToast(__fl.dataset.msg, __fl.dataset.type);
        __fl.remove();
    }

</script>
</body>
</html>
<?php
}
