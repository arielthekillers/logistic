<?php renderSidebarHeader("Pengaturan Sistem"); ?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Pengaturan Sistem</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola preferensi dan profil aplikasi Anda.</p>
    </div>
</div>

<?php if (isset($_SESSION['success_flash'])): ?>
    <div class="mb-4 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 p-4 rounded-xl text-sm font-medium border border-emerald-100 dark:border-emerald-800/50 flex items-center gap-2">
        <i class="ri-checkbox-circle-fill text-lg"></i>
        <?= e($_SESSION['success_flash']) ?>
    </div>
    <?php unset($_SESSION['success_flash']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_flash'])): ?>
    <div class="mb-4 bg-rose-50 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 p-4 rounded-xl text-sm font-medium border border-rose-100 dark:border-rose-800/50 flex items-center gap-2">
        <i class="ri-error-warning-fill text-lg"></i>
        <?= e($_SESSION['error_flash']) ?>
    </div>
    <?php unset($_SESSION['error_flash']); ?>
<?php endif; ?>

<div class="card p-0 overflow-hidden">
    <!-- Tabs Header -->
    <div class="flex border-b border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50">
        <button class="tab-btn active px-6 py-4 text-sm font-bold text-slate-900 dark:text-white border-b-2 border-slate-900 dark:border-white transition-colors flex items-center gap-2" data-target="tab-umum">
            <i class="ri-building-line text-lg"></i> Profil Umum
        </button>
        <button class="tab-btn px-6 py-4 text-sm font-bold text-gray-500 dark:text-gray-400 border-b-2 border-transparent hover:text-slate-900 dark:hover:text-white transition-colors flex items-center gap-2" data-target="tab-resi">
            <i class="ri-barcode-box-line text-lg"></i> Resi & Scan
        </button>
    </div>

    <!-- Tabs Content -->
    <form action="<?= url('/settings/update') ?>" method="POST" class="p-6 sm:p-8">
        
        <!-- TAB 1: UMUM -->
        <div id="tab-umum" class="tab-content block space-y-6">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white border-b border-gray-100 dark:border-slate-700 pb-2">Informasi Perusahaan</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 -mt-4 mb-4">Informasi ini akan ditampilkan pada kop surat resi dan cetakan lainnya.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Nama Perusahaan</label>
                    <input type="text" name="company_name" value="<?= e($settings['company_name'] ?? '') ?>" required class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-700 text-slate-900 dark:text-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-500 focus:border-slate-900 dark:focus:border-slate-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Nomor Telepon / CS</label>
                    <input type="text" name="company_phone" value="<?= e($settings['company_phone'] ?? '') ?>" required class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-700 text-slate-900 dark:text-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-500 focus:border-slate-900 dark:focus:border-slate-500 outline-none transition-all">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Alamat Lengkap</label>
                    <textarea name="company_address" rows="3" required class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-700 text-slate-900 dark:text-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-500 focus:border-slate-900 dark:focus:border-slate-500 outline-none transition-all"><?= e($settings['company_address'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- TAB 2: RESI & SCAN -->
        <div id="tab-resi" class="tab-content hidden space-y-6">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white border-b border-gray-100 dark:border-slate-700 pb-2">Pengaturan Resi & Scan</h3>
            
            <div class="mt-4">
                <label class="block text-sm font-bold text-gray-800 dark:text-gray-200 mb-3">Pilih Format Kode Cetak di Resi</label>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Option 1: 1D Barcode -->
                    <label class="cursor-pointer">
                        <input type="radio" name="barcode_type" value="1d" class="peer sr-only" <?= ($settings['barcode_type'] ?? '1d') === '1d' ? 'checked' : '' ?>>
                        <div class="p-5 rounded-2xl border-2 border-gray-200 dark:border-slate-700 peer-checked:border-slate-900 dark:peer-checked:border-slate-400 peer-checked:bg-slate-50 dark:peer-checked:bg-slate-700/50 transition-all hover:bg-gray-50 dark:hover:bg-slate-700/50 flex flex-col items-center text-center">
                            <i class="ri-barcode-line text-4xl text-slate-800 dark:text-gray-300 mb-2"></i>
                            <span class="font-bold text-slate-900 dark:text-white block">Kode Batang (1D)</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 block">CODE128 standard. Bentuk memanjang.</span>
                        </div>
                    </label>

                    <!-- Option 2: QR Code -->
                    <label class="cursor-pointer">
                        <input type="radio" name="barcode_type" value="qr" class="peer sr-only" <?= ($settings['barcode_type'] ?? '') === 'qr' ? 'checked' : '' ?>>
                        <div class="p-5 rounded-2xl border-2 border-gray-200 dark:border-slate-700 peer-checked:border-slate-900 dark:peer-checked:border-slate-400 peer-checked:bg-slate-50 dark:peer-checked:bg-slate-700/50 transition-all hover:bg-gray-50 dark:hover:bg-slate-700/50 flex flex-col items-center text-center">
                            <i class="ri-qr-code-line text-4xl text-slate-800 dark:text-gray-300 mb-2"></i>
                            <span class="font-bold text-slate-900 dark:text-white block">QR Code (2D)</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 block">Bentuk kotak. Mudah dipindai kamera HP.</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-slate-700 flex justify-end">
            <button type="submit" class="btn-primary">
                <i class="ri-save-3-line"></i> Simpan Pengaturan
            </button>
        </div>
    </form>
</div>

<script>
    document.querySelectorAll('.tab-btn').forEach(button => {
        button.addEventListener('click', () => {
            // Remove active classes
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('active', 'text-slate-900', 'border-slate-900', 'dark:text-white', 'dark:border-white');
                b.classList.add('text-gray-500', 'border-transparent', 'dark:text-gray-400');
            });
            document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
            
            // Add active class to clicked
            button.classList.add('active', 'text-slate-900', 'border-slate-900', 'dark:text-white', 'dark:border-white');
            button.classList.remove('text-gray-500', 'border-transparent', 'dark:text-gray-400');
            
            // Show target
            document.getElementById(button.dataset.target).classList.remove('hidden');
        });
    });
</script>

<?php renderSidebarFooter(); ?>
