<?php
renderSidebarHeader("Data Pengiriman - PT. Barongko Darma Logistik");
?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Data Pengiriman Barang (Resi)</h1>
            <p class="text-sm text-gray-500">Kelola dan lihat seluruh manifes resi pengiriman BDL.</p>
        </div>
        <div>
            <a href="<?= url('/shipments/create') ?>" class="inline-flex items-center justify-center px-4 py-2.5 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition-all">
                <i class="ri-add-line text-lg mr-2"></i> Buat Resi Pengiriman
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200">
        <form method="GET" action="<?= url('/shipments') ?>" class="flex flex-col xl:flex-row gap-3">
            <!-- Search -->
            <div class="flex-1">
                <input type="text" name="search" value="<?= e($search ?? '') ?>" placeholder="Cari No Resi / Nama..." class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none transition-all">
            </div>
            <!-- Date Filters -->
            <div class="w-full xl:w-auto flex items-center gap-2">
                <input type="date" name="start_date" value="<?= e($start_date ?? '') ?>" class="bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none transition-all" title="Tanggal Mulai">
                <span class="text-gray-400 text-xs">s/d</span>
                <input type="date" name="end_date" value="<?= e($end_date ?? '') ?>" class="bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none transition-all" title="Tanggal Akhir">
            </div>
            <!-- Status -->
            <div class="w-full xl:w-48">
                <select name="status" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none transition-all">
                    <option value="">-- Semua Status --</option>
                    <option value="DRAFT" <?= ($status ?? '') === 'DRAFT' ? 'selected' : '' ?>>Draft</option>
                    <option value="RECEIVED_AT_HUB" <?= ($status ?? '') === 'RECEIVED_AT_HUB' ? 'selected' : '' ?>>Tiba di Hub</option>
                    <option value="SORTED" <?= ($status ?? '') === 'SORTED' ? 'selected' : '' ?>>Disortir</option>
                    <option value="IN_TRANSIT" <?= ($status ?? '') === 'IN_TRANSIT' ? 'selected' : '' ?>>Dalam Perjalanan</option>
                    <option value="OUT_FOR_DELIVERY" <?= ($status ?? '') === 'OUT_FOR_DELIVERY' ? 'selected' : '' ?>>Dalam Pengantaran</option>
                    <option value="DELIVERED" <?= ($status ?? '') === 'DELIVERED' ? 'selected' : '' ?>>Diterima</option>
                    <option value="PROBLEM" <?= ($status ?? '') === 'PROBLEM' ? 'selected' : '' ?>>Bermasalah</option>
                    <option value="CANCELLED" <?= ($status ?? '') === 'CANCELLED' ? 'selected' : '' ?>>Dibatalkan</option>
                </select>
            </div>
            <!-- Actions -->
            <div class="flex items-center gap-2">
                <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl shadow-sm transition-colors text-sm">
                    Cari
                </button>
                <?php if (!empty($search) || !empty($status) || !empty($start_date) || !empty($end_date)): ?>
                    <a href="<?= url('/shipments') ?>" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-slate-700 font-bold rounded-xl transition-colors text-sm">
                        Reset
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider border-b border-gray-100">
                        <th class="py-3.5 px-6 font-semibold">Resi</th>
                        <th class="py-3.5 px-6 font-semibold">Pengirim</th>
                        <th class="py-3.5 px-6 font-semibold">Penerima</th>
                        <th class="py-3.5 px-6 font-semibold">Hub Asal & Tujuan</th>
                        <th class="py-3.5 px-6 font-semibold">Berat / Biaya</th>
                        <th class="py-3.5 px-6 font-semibold">Status</th>
                        <th class="py-3.5 px-6 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-slate-700">
                    <?php if (empty($shipments)): ?>
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-400">
                                Belum ada data pengiriman. Silakan klik <strong>Buat Resi Pengiriman</strong>.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($shipments as $item): ?>
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="py-4 px-6 font-bold text-emerald-700 font-mono">
                                    <a href="<?= url('/shipments/detail?id=' . $item['id']) ?>" class="hover:underline">
                                        <?= e($item['resi_number']) ?>
                                    </a>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="font-bold text-slate-900"><?= e($item['sender_name']) ?></div>
                                    <div class="text-xs text-gray-400"><?= e($item['sender_phone']) ?></div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="font-bold text-slate-900"><?= e($item['receiver_name']) ?></div>
                                    <div class="text-xs text-gray-400"><?= e($item['receiver_phone']) ?></div>
                                </td>
                                <td class="py-4 px-6 text-xs text-slate-600">
                                    <div><span class="text-gray-400">Asal:</span> <?= e($item['origin_hub_name'] ?? '-') ?></div>
                                    <div><span class="text-gray-400">Tujuan:</span> <?= e($item['destination_hub_name'] ?? '-') ?></div>
                                </td>
                                <td class="py-4 px-6 text-xs">
                                    <div class="font-bold text-slate-900"><?= number_format($item['weight_kg'], 2) ?> kg</div>
                                    <div class="text-emerald-700 font-bold"><?= format_rp($item['total_cost']) ?></div>
                                </td>
                                <td class="py-4 px-6">
                                    <?= get_status_badge($item['status']) ?>
                                </td>
                                <td class="py-4 px-6 text-right space-x-2">
                                    <a href="<?= url('/shipments/detail?id=' . $item['id']) ?>" class="inline-flex items-center text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-lg border border-emerald-200">
                                        Detail
                                    </a>
                                    <a href="<?= url('/shipments/label?id=' . $item['id']) ?>" target="_blank" class="inline-flex items-center text-xs font-bold text-slate-800 bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg border border-gray-200">
                                        <i class="ri-printer-line mr-1"></i> Cetak Nota
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($total > 0): ?>
        <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-sm text-gray-500">
                Menampilkan <span class="font-bold text-slate-900"><?= count($shipments) ?></span> dari <span class="font-bold text-slate-900"><?= number_format($total) ?></span> resi
            </div>
            
            <?php if ($last_page > 1): ?>
                <?php
                // Build base URL for pagination
                $queryParams = $_GET;
                unset($queryParams['page']);
                $baseUrl = url('/shipments') . '?' . http_build_query($queryParams);
                $baseUrl .= (!empty($queryParams) ? '&' : '') . 'page=';
                ?>
                <div class="flex items-center gap-1">
                    <?php if ($current_page > 1): ?>
                        <a href="<?= $baseUrl . ($current_page - 1) ?>" class="px-3 py-1.5 rounded-lg border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">Prev</a>
                    <?php endif; ?>
                    
                    <?php 
                    $start = max(1, $current_page - 2);
                    $end = min($last_page, $current_page + 2);
                    for ($i = $start; $i <= $end; $i++): 
                    ?>
                        <a href="<?= $baseUrl . $i ?>" class="px-3 py-1.5 rounded-lg border text-sm font-medium transition-colors <?= $i == $current_page ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-gray-200 text-gray-600 hover:bg-gray-50' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($current_page < $last_page): ?>
                        <a href="<?= $baseUrl . ($current_page + 1) ?>" class="px-3 py-1.5 rounded-lg border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php renderSidebarFooter(); ?>
