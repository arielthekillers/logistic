<?php
renderPublicHeader("Login - PT. Barongko Darma Logistik");
?>

<div class="min-h-screen flex">

    <!-- ─── Left Branding Panel ──────────────────────────── -->
    <div class="hidden lg:flex lg:w-1/2 flex-col items-center justify-center p-16 relative overflow-hidden"
         style="background:linear-gradient(135deg,#1e1f15 0%,#2b2c1e 60%,#1a3d1c 100%)">
        <!-- Decorative blobs -->
        <div style="position:absolute;top:-80px;right:-80px;width:320px;height:320px;border-radius:50%;background:radial-gradient(circle,rgba(64,191,78,0.2) 0%,transparent 70%)"></div>
        <div style="position:absolute;bottom:-60px;left:-60px;width:240px;height:240px;border-radius:50%;background:radial-gradient(circle,rgba(221,44,36,0.15) 0%,transparent 70%)"></div>

        <div class="relative z-10 text-center space-y-6 max-w-sm">
            <!-- Logo -->
            <div class="w-20 h-20 rounded-3xl flex items-center justify-center mx-auto shadow-2xl" style="background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.15)">
                <img src="<?= url('/' . COMPANY_LOGO) ?>" alt="Logo" class="w-12 h-12 object-contain" style="filter:brightness(0) invert(1)">
            </div>

            <div>
                <h1 class="text-3xl font-black text-white leading-tight" style="letter-spacing:-1px">
                    <span style="color:#dd2c24">Barongko</span><br>
                    <span style="color:#40bf4e">Darma</span> Logistik
                </h1>
                <p class="text-sm mt-3" style="color:rgba(255,255,255,0.5)">Sistem Manajemen Tracking &<br>Operasional Pengiriman Barang</p>
            </div>

            <!-- Feature list -->
            <div class="space-y-3 text-left mt-8">
                <?php foreach ([
                    ['ri-box-3-line','Tracking Resi Real-time'],
                    ['ri-qr-scan-2-line','Scan Barcode via Kamera HP'],
                    ['ri-map-pin-2-line','Riwayat Perjalanan Paket'],
                    ['ri-shield-check-line','Sistem Keamanan Berlapis'],
                ] as [$icon, $label]): ?>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(64,191,78,0.15)">
                        <i class="<?= $icon ?> text-sm" style="color:#40bf4e"></i>
                    </div>
                    <span class="text-sm font-medium" style="color:rgba(255,255,255,0.7)"><?= $label ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- ─── Right Login Form ─────────────────────────────── -->
    <div class="flex-1 flex flex-col items-center justify-center p-6 bg-gray-50">
        <div class="w-full max-w-sm space-y-7">

            <!-- Mobile logo -->
            <div class="lg:hidden flex items-center justify-center gap-3">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center" style="background:#2b2c1e">
                    <img src="<?= url('/' . COMPANY_LOGO) ?>" alt="Logo" class="w-6 h-6 object-contain" style="filter:brightness(0) invert(1)">
                </div>
                <div>
                    <p class="font-black text-sm leading-none" style="color:#dd2c24">BARONGKO</p>
                    <p class="font-black text-sm leading-none" style="color:#40bf4e">DARMA LOGISTIK</p>
                </div>
            </div>

            <!-- Heading -->
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900" style="letter-spacing:-0.5px">Selamat datang 👋</h2>
                <p class="text-sm text-gray-400 mt-1">Masuk untuk mengakses sistem operasional BDL.</p>
            </div>

            <!-- Error -->
            <?php if (!empty($error)): ?>
                <div class="flex items-center gap-3 p-4 rounded-2xl border text-sm font-semibold"
                     style="background:#fff5f5;color:#b91c1c;border-color:#fecaca">
                    <i class="ri-error-warning-fill text-lg flex-shrink-0"></i>
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form action="<?= url('/login') ?>" method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Username</label>
                    <div class="relative">
                        <i class="ri-user-line absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input id="username" name="username" type="text" required
                               placeholder="Masukkan username"
                               class="w-full pl-11 pr-4 py-3 rounded-2xl border text-sm font-medium outline-none transition-all"
                               style="border-color:#e8e8e4;background:#fff;font-family:'Plus Jakarta Sans',sans-serif;color:#1a1a1a"
                               onfocus="this.style.borderColor='#2b2c1e';this.style.boxShadow='0 0 0 3px rgba(43,44,30,0.08)'"
                               onblur="this.style.borderColor='#e8e8e4';this.style.boxShadow='none'">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Password</label>
                    <div class="relative">
                        <i class="ri-lock-line absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input id="password" name="password" type="password" required
                               placeholder="••••••••"
                               class="w-full pl-11 pr-4 py-3 rounded-2xl border text-sm font-medium outline-none transition-all"
                               style="border-color:#e8e8e4;background:#fff;font-family:'Plus Jakarta Sans',sans-serif;color:#1a1a1a"
                               onfocus="this.style.borderColor='#2b2c1e';this.style.boxShadow='0 0 0 3px rgba(43,44,30,0.08)'"
                               onblur="this.style.borderColor='#e8e8e4';this.style.boxShadow='none'">
                    </div>
                </div>

                <button type="submit"
                        class="w-full py-3 rounded-2xl text-white text-sm font-bold flex items-center justify-center gap-2 transition-all"
                        style="background:#2b2c1e;letter-spacing:-0.2px"
                        onmouseover="this.style.background='#3d3f2a';this.style.boxShadow='0 8px 24px rgba(43,44,30,0.3)'"
                        onmouseout="this.style.background='#2b2c1e';this.style.boxShadow='none'">
                    Masuk ke Sistem <i class="ri-arrow-right-line"></i>
                </button>
            </form>

            <div class="text-center pt-2 border-t border-gray-100">
                <a href="<?= url('/tracking') ?>"
                   class="text-sm font-semibold hover:underline"
                   style="color:#40bf4e">
                    <i class="ri-search-line mr-1"></i> Tracking Resi Tanpa Login
                </a>
            </div>
        </div>
    </div>
</div>

<?php renderPublicFooter(); ?>
