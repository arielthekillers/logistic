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
    <!-- QRCode.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        tailwind.config = {
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
    </script>

    <style>
        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f0f0ee;
            color: #1a1a1a;
            -webkit-font-smoothing: antialiased;
        }

        /* ─── Sidebar ─────────────────────────────── */
        #sidebar {
            background: #ffffff;
            border-right: 1px solid #e8e8e4;
            width: 240px;
        }

        .sidebar-brand {
            padding: 20px 20px 16px;
            border-bottom: 1px solid #f0f0ee;
        }

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
        .sidebar-logo-text .sub  { font-size: 10px; font-weight: 500; color: #9a9a90; }

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

        .nav-link i { font-size: 16px; flex-shrink: 0; }

        .nav-link:hover {
            background: #f5f5f2;
            color: #1a1a1a;
        }

        .nav-link.active {
            background: #2b2c1e;
            color: #ffffff;
            font-weight: 700;
        }

        .nav-link.active i { color: #40bf4e; }

        /* Sidebar User Footer */
        .sidebar-user {
            padding: 14px 16px;
            border-top: 1px solid #f0f0ee;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-user-info { flex: 1; min-width: 0; line-height: 1.2; }
        .sidebar-user-info .uname { font-size: 13px; font-weight: 700; color: #1a1a1a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 1px; display: block; }
        .sidebar-user-info .urole { font-size: 11px; color: #9a9a90; font-weight: 500; display: block; }

        /* ─── Top bar (mobile) ─────────────────────── */
        #mobile-topbar {
            background: #fff;
            border-bottom: 1px solid #e8e8e4;
            padding: 12px 16px;
            z-index: 40;
        }

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

        .card-hover {
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .card-hover:hover {
            box-shadow: 0 6px 24px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }

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
            border: none;
            cursor: pointer;
            transition: all 0.18s;
            text-decoration: none;
        }
        .btn-primary:hover { background: #3d3f2a; box-shadow: 0 4px 14px rgba(43,44,30,0.25); }

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
            border: none;
            cursor: pointer;
            transition: all 0.18s;
            text-decoration: none;
        }
        .btn-accent:hover { background: #c7221b; box-shadow: 0 4px 14px rgba(221,44,36,0.3); }

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
            border: none;
            cursor: pointer;
            transition: all 0.18s;
            text-decoration: none;
        }
        .btn-green:hover { background: #34a340; box-shadow: 0 4px 14px rgba(64,191,78,0.3); }

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
        .data-table tbody tr { border-bottom: 1px solid #f5f5f2; transition: background 0.15s; }
        .data-table tbody tr:last-child { border-bottom: none; }
        .data-table tbody tr:hover { background: #fafaf8; }
        .data-table tbody td { padding: 13px 18px; color: #3a3a35; vertical-align: middle; }

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
        .form-input:focus {
            border-color: #2b2c1e;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(43,44,30,0.08);
        }
        .form-input:disabled, .form-input[readonly] {
            background: #f0f0ee;
            color: #9a9a90;
            cursor: not-allowed;
        }

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
        .modal-head h3 {
            font-size: 16px;
            font-weight: 800;
            color: #1a1a1a;
            letter-spacing: -0.3px;
        }
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
        .modal-head .close-btn:hover { background: #f0f0ee; color: #1a1a1a; }

        .modal-body { padding: 22px 24px; }
        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid #f0f0ee;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

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
        .alert-error   { background: #fff5f5; color: #b91c1c; border: 1px solid #fecaca; }
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
        .page-subtitle {
            font-size: 13px;
            color: #9a9a90;
            font-weight: 400;
            margin-top: 3px;
        }

        /* ─── Scrollbar ───────────────────────────── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d0d0cc; border-radius: 10px; }
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
            <a href="<?= url('/logout') ?>" title="Logout" class="text-gray-400 hover:text-red-500 transition-colors text-lg">
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

    // ═══ SweetAlert2 Toast + Confirm System ═══════════════════
    const SwalToast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true,
        customClass: {
            popup: 'swal-bdl-toast',
        },
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    // Global showToast — gunakan dari mana saja
    window.showToast = function(msg, type = 'success') {
        const icons  = { success: 'success', error: 'error', warning: 'warning', info: 'info' };
        SwalToast.fire({
            icon: icons[type] || 'success',
            title: msg,
        });
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
