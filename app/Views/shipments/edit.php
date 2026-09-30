<?php
renderSidebarHeader("Edit Resi Pengiriman - PT. Barongko Darma Logistik");
$user = auth_user();
?>

<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <h1 class="text-2xl font-black text-slate-900 dark:text-white">Edit Form Transaksi Resi</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Edit data pengirim, penerima & rute pengiriman paket BDL.</p>
    </div>

    <?php if (!empty($_SESSION['error_flash'])): ?>
        <div class="bg-rose-50 dark:bg-rose-900/30 border-l-4 border-rose-500 p-4 rounded-r-md">
            <p class="text-sm text-rose-700 dark:text-rose-400 font-medium"><?= e($_SESSION['error_flash']) ?></p>
        </div>
        <?php unset($_SESSION['error_flash']); ?>
    <?php endif; ?>

    <form action="<?= url('/shipments/update') ?>" method="POST" class="bg-white dark:bg-slate-800 p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 space-y-8">
        <input type="hidden" name="id" value="<?= $shipment['id'] ?>">
        
        <!-- Resi & Hub Section -->
        <div>
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4 border-b border-gray-100 dark:border-slate-700 pb-2 flex items-center gap-2">
                <i class="ri-barcode-line text-emerald-600 dark:text-emerald-400"></i> Informasi Resi & Rute Hub
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Nomor Resi</label>
                    <input type="text" name="resi_number" value="<?= e($shipment['resi_number']) ?>" readonly class="w-full bg-gray-100 dark:bg-slate-900 border border-gray-300 dark:border-slate-700 rounded-lg px-3 py-2.5 text-emerald-800 dark:text-emerald-400 font-extrabold text-sm font-mono cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Hub Asal</label>
                    <select name="origin_hub_id" required class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-700 rounded-lg px-3 py-2.5 text-slate-900 dark:text-gray-200 font-bold focus:ring-2 focus:ring-emerald-500 text-sm">
                        <?php foreach ($hubs as $h): ?>
                            <?php $isSelected = ($shipment['origin_hub_id'] == $h['id']) ? 'selected' : ''; ?>
                            <option value="<?= $h['id'] ?>" <?= $isSelected ?>><?= e($h['name']) ?> (<?= e($h['city']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Hub Tujuan</label>
                    <select name="destination_hub_id" required class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-700 rounded-lg px-3 py-2.5 text-slate-900 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm">
                        <?php foreach ($hubs as $h): ?>
                            <?php $isSelected = ($shipment['destination_hub_id'] == $h['id']) ? 'selected' : ''; ?>
                            <option value="<?= $h['id'] ?>" <?= $isSelected ?>><?= e($h['name']) ?> (<?= e($h['city']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Sender & Receiver Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Sender -->
            <div class="space-y-4 bg-gray-50/70 dark:bg-slate-700/50 p-5 rounded-xl border border-gray-100 dark:border-slate-700">
                <h4 class="font-bold text-slate-900 dark:text-white text-sm flex items-center gap-2">
                    <i class="ri-user-shared-line text-emerald-600 dark:text-emerald-400"></i> Data Pengirim
                </h4>

                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Nama Pengirim *</label>
                    <input type="text" name="sender_name" value="<?= e($shipment['sender_name']) ?>" required autofocus class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-slate-900 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">No. HP Pengirim *</label>
                    <input type="text" name="sender_phone" value="<?= e($shipment['sender_phone']) ?>" required class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-slate-900 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Alamat Lengkap Pengirim *</label>
                    <textarea name="sender_address" required rows="3" class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-slate-900 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500"><?= e($shipment['sender_address']) ?></textarea>
                </div>
            </div>

            <!-- Receiver -->
            <div class="space-y-4 bg-gray-50/70 dark:bg-slate-700/50 p-5 rounded-xl border border-gray-100 dark:border-slate-700">
                <h4 class="font-bold text-slate-900 dark:text-white text-sm flex items-center gap-2">
                    <i class="ri-user-received-line text-red-600 dark:text-red-400"></i> Data Penerima
                </h4>

                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Nama Penerima *</label>
                    <input type="text" name="receiver_name" value="<?= e($shipment['receiver_name']) ?>" required class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-slate-900 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">No. HP Penerima *</label>
                    <input type="text" name="receiver_phone" value="<?= e($shipment['receiver_phone']) ?>" required class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-slate-900 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Alamat Lengkap Penerima *</label>
                    <textarea name="receiver_address" required rows="3" class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-slate-900 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500"><?= e($shipment['receiver_address']) ?></textarea>
                </div>
            </div>
        </div>

        <!-- Package Details -->
        <div>
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4 border-b border-gray-100 dark:border-slate-700 pb-2 flex items-center gap-2">
                <i class="ri-scales-3-line text-amber-600 dark:text-amber-400"></i> Rincian Paket & Biaya
            </h3>

            <!-- Dynamic Packaging List -->
            <div class="mb-5 bg-gray-50/50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-700 rounded-xl p-4">
                <div class="flex justify-between items-center mb-3">
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">Daftar Kemasan & Koli</label>
                    <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Total Koli: <strong id="total-koli-label" class="text-gray-900 dark:text-white text-sm"><?= $shipment['koli'] ?></strong></span>
                </div>
                
                <div id="packaging-list" class="space-y-3">
                    <?php 
                    $packageItems = !empty($shipment['package_items']) ? json_decode($shipment['package_items'], true) : [];
                    if (empty($packageItems)) {
                        $packageItems = [['kemasan' => '', 'koli' => $shipment['koli']]];
                    }
                    foreach ($packageItems as $idx => $pkg): 
                    ?>
                    <div class="flex gap-2 sm:gap-3 items-start package-row">
                        <div class="flex-1">
                            <input type="text" name="kemasan[]" value="<?= e($pkg['kemasan']) ?>" required placeholder="Jenis Kemasan (Misal: Karung, Dos)" class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-slate-900 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div class="w-20 sm:w-24 flex-shrink-0">
                            <input type="number" name="koli_item[]" value="<?= e($pkg['koli']) ?>" required min="1" oninput="calculateTotalKoli()" class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-slate-900 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 text-center">
                        </div>
                        <button type="button" onclick="removePackageRow(this)" class="p-2 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded-lg transition-colors <?= count($packageItems) <= 1 ? 'hidden' : '' ?> btn-remove-pkg" title="Hapus">
                            <i class="ri-delete-bin-line text-lg"></i>
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" onclick="addPackageRow()" class="mt-4 px-3 py-1.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-lg text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-slate-600 flex items-center gap-1 shadow-sm transition-colors">
                    <i class="ri-add-line"></i> Tambah Kemasan
                </button>
            </div>

            <!-- Other Details -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Total Berat (kg)</label>
                    <input type="number" step="0.1" name="weight_kg" value="<?= (float)$shipment['weight_kg'] ?>" required class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-slate-900 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Total Biaya Ongkir (Rp)</label>
                    <input type="number" name="total_cost" value="<?= (float)$shipment['total_cost'] ?>" required class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-slate-900 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Catatan Tambahan (Isi Barang)</label>
                    <input type="text" name="notes" value="<?= e($shipment['notes']) ?>" placeholder="Misal: Sparepart Mobil" class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-slate-900 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 border-t border-gray-100 dark:border-slate-700 pt-6">
            <a href="<?= url('/shipments') ?>" class="px-5 py-2.5 border border-gray-300 dark:border-slate-600 rounded-xl text-sm font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700">Batal</a>
            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-md transition-all">Simpan Perubahan</button>
        </div>

    </form>
</div>

<?php renderSidebarFooter(); ?>

<script>
    function updateRemoveButtons() {
        const rows = document.querySelectorAll('.package-row');
        rows.forEach((row, index) => {
            const btn = row.querySelector('.btn-remove-pkg');
            if (rows.length > 1) {
                btn.classList.remove('hidden');
            } else {
                btn.classList.add('hidden');
            }
        });
    }

    function addPackageRow() {
        const list = document.getElementById('packaging-list');
        const firstRow = list.querySelector('.package-row');
        const newRow = firstRow.cloneNode(true);
        
        // Reset values
        newRow.querySelector('input[name="kemasan[]"]').value = '';
        newRow.querySelector('input[name="koli_item[]"]').value = '1';
        
        list.appendChild(newRow);
        updateRemoveButtons();
        calculateTotalKoli();
    }

    function removePackageRow(btn) {
        const rows = document.querySelectorAll('.package-row');
        if (rows.length > 1) {
            btn.closest('.package-row').remove();
            updateRemoveButtons();
            calculateTotalKoli();
        }
    }

    function calculateTotalKoli() {
        let total = 0;
        document.querySelectorAll('input[name="koli_item[]"]').forEach(input => {
            total += parseInt(input.value) || 0;
        });
        document.getElementById('total-koli-label').innerText = total;
    }

    // Initialize
    updateRemoveButtons();
</script>
