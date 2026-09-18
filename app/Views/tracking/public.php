<?php
renderPublicHeader("Tracking Barang - Logistic Express");
?>

<!-- Header Banner -->
<div class="bg-slate-900 text-white py-12 px-4 sm:px-6 lg:px-8 border-b border-slate-800">
    <div class="max-w-4xl mx-auto text-center space-y-4">
        <div class="inline-flex items-center space-x-2 bg-indigo-500/20 text-indigo-300 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider border border-indigo-500/30">
            <i class="ri-radar-line"></i> Realtime Package Tracking
        </div>
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Lacak Lokasi & Status Paket Anda</h1>
        <p class="text-slate-400 text-sm sm:text-base max-w-xl mx-auto">Masukkan Nomor Resi pengiriman Anda tanpa perlu login untuk memantau perjalanan paket secara realtime.</p>
        
        <!-- Search Box -->
        <form action="<?= url('/tracking') ?>" method="GET" class="mt-6 max-w-2xl mx-auto flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <i class="ri-barcode-line text-xl"></i>
                </div>
                <input type="text" name="resi" value="<?= e($resi) ?>" required placeholder="Contoh: LOG-20260918-DEMO" class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-slate-800 text-white border border-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-base placeholder-slate-500 shadow-inner">
            </div>
            <button type="submit" class="px-7 py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-lg shadow-indigo-600/30 transition-all flex items-center justify-center gap-2">
                <i class="ri-search-line text-lg"></i> Lacak Paket
            </button>
        </form>
    </div>
</div>

<!-- Main Content Area -->
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full flex-1">

    <?php if (!empty($resi) && empty($shipment)): ?>
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-200 text-center space-y-4">
            <div class="w-16 h-16 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto text-3xl">
                <i class="ri-search-eye-line"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900">Nomor Resi Tidak Ditemukan</h3>
            <p class="text-gray-500 text-sm max-w-md mx-auto">Nomor resi <span class="font-semibold text-gray-900"><?= e($resi) ?></span> tidak terdaftar di sistem kami. Pastikan nomor resi yang Anda masukkan sudah benar.</p>
        </div>
    <?php endif; ?>

    <?php if (!empty($shipment)): ?>
        <div class="space-y-6">
            <!-- Shipment Summary Card -->
            <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-gray-100 pb-6 gap-4">
                    <div>
                        <div class="flex items-center space-x-3">
                            <h2 class="text-2xl font-extrabold text-indigo-600"><?= e($shipment['resi_number']) ?></h2>
                            <div><?= get_status_badge($shipment['status']) ?></div>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Dibuat pada: <?= format_datetime($shipment['created_at']) ?></p>
                    </div>
                    <div class="text-left sm:text-right">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Lokasi Terakhir</span>
                        <span class="text-base font-bold text-gray-900 flex items-center sm:justify-end gap-1 mt-0.5">
                            <i class="ri-map-pin-2-fill text-rose-500"></i> <?= e($shipment['current_hub_name'] ?? 'Hub Transit') ?>
                        </span>
                    </div>
                </div>

                <!-- Sender & Receiver Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6">
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 flex items-center">
                            <i class="ri-user-shared-line mr-1.5 text-indigo-500 text-base"></i> Pengirim
                        </div>
                        <p class="font-bold text-gray-900"><?= e($shipment['sender_name']) ?></p>
                        <p class="text-xs text-gray-500 mt-1"><?= e($shipment['sender_address']) ?></p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 flex items-center">
                            <i class="ri-user-received-line mr-1.5 text-emerald-500 text-base"></i> Penerima
                        </div>
                        <p class="font-bold text-gray-900"><?= e($shipment['receiver_name']) ?></p>
                        <p class="text-xs text-gray-500 mt-1"><?= e($shipment['receiver_address']) ?></p>
                    </div>
                </div>
            </div>

            <!-- Timeline Card -->
            <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-200">
                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <i class="ri-history-line text-indigo-600"></i> Timeline Perjalanan Paket
                </h3>

                <?php if (empty($checkpoints)): ?>
                    <p class="text-sm text-gray-400 italic">Belum ada riwayat pergerakan untuk paket ini.</p>
                <?php else: ?>
                    <div class="relative border-l-2 border-indigo-100 ml-4 space-y-8 pb-4">
                        <?php foreach (array_reverse($checkpoints) as $index => $cp): ?>
                            <div class="relative pl-8">
                                <!-- Bullet point -->
                                <div class="absolute -left-[17px] top-0 w-8 h-8 rounded-full flex items-center justify-center text-white <?= $index === 0 ? 'bg-indigo-600 ring-4 ring-indigo-100 shadow-md' : 'bg-gray-300' ?>">
                                    <i class="<?= $index === 0 ? 'ri-map-pin-line' : 'ri-checkbox-circle-line' ?> text-sm"></i>
                                </div>
                                <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-100">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between text-xs text-gray-400 gap-1 mb-1">
                                        <span class="font-semibold text-indigo-600"><?= format_datetime($cp['scanned_at']) ?></span>
                                        <span>Operator: <?= e($cp['user_name'] ?? 'System') ?></span>
                                    </div>
                                    <h4 class="font-bold text-gray-900 text-sm"><?= e($cp['location_name']) ?></h4>
                                    <p class="text-xs text-gray-600 mt-1"><?= e($cp['notes'] ?? 'Paket telah diproses.') ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

</div>

<?php renderPublicFooter(); ?>
