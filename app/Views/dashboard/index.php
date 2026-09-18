<?php
renderSidebarHeader("Dashboard - PT. Barongko Darma Logistik");
$counts = $stats['counts'] ?? [];
$recent = $stats['recent_shipments'] ?? [];
$user   = auth_user();
$hour   = (int)date('H');
$greet  = $hour < 11 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));
?>

<div class="space-y-7">

    <!-- ─── Greeting Bar ─────────────────────────────────── -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm text-gray-400 font-medium"><?= $greet ?>, 👋</p>
            <h1 class="page-title"><?= e($user['name'] ?? 'Operator') ?></h1>
            <p class="page-subtitle">Ringkasan operasional & pengiriman terkini BDL.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="<?= url('/scanner') ?>" class="btn-outline">
                <i class="ri-qr-scan-2-line" style="color:#40bf4e"></i> Scan HP
            </a>
            <a href="<?= url('/shipments/create') ?>" class="btn-primary">
                <i class="ri-add-line"></i> Buat Resi
            </a>
        </div>
    </div>

    <!-- ─── Stat Cards ───────────────────────────────────── -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="card card-hover p-5 flex items-center gap-4">
            <div class="stat-icon" style="background:#f5f5f0">
                <i class="ri-box-3-line" style="color:#2b2c1e; font-size:20px"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Resi Hari Ini</p>
                <h3 class="text-2xl font-extrabold text-gray-900 mt-0.5"><?= number_format($stats['today_count'] ?? 0) ?></h3>
            </div>
        </div>

        <div class="card card-hover p-5 flex items-center gap-4">
            <div class="stat-icon" style="background:#eff6ff">
                <i class="ri-building-4-line" style="color:#2563eb; font-size:20px"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tiba di Hub</p>
                <h3 class="text-2xl font-extrabold" style="color:#2563eb; margin-top:2px"><?= number_format($counts['RECEIVED_AT_HUB'] ?? 0) ?></h3>
            </div>
        </div>

        <div class="card card-hover p-5 flex items-center gap-4">
            <div class="stat-icon" style="background:#fefce8">
                <i class="ri-truck-line" style="color:#ca8a04; font-size:20px"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Dalam Perjalanan</p>
                <h3 class="text-2xl font-extrabold" style="color:#ca8a04; margin-top:2px"><?= number_format($counts['IN_TRANSIT'] ?? 0) ?></h3>
            </div>
        </div>

        <div class="card card-hover p-5 flex items-center gap-4">
            <div class="stat-icon" style="background:#f0fdf4">
                <i class="ri-checkbox-circle-line" style="color:#16a34a; font-size:20px"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Diterima</p>
                <h3 class="text-2xl font-extrabold" style="color:#16a34a; margin-top:2px"><?= number_format($counts['DELIVERED'] ?? 0) ?></h3>
            </div>
        </div>
    </div>

    <!-- ─── 2 Column Layout: Penumpukan vs Recent ───────────────────────── -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Active Hubs / Bottlenecks -->
        <div class="lg:col-span-4 space-y-6">
            <div class="card p-6 h-full border-t-4 border-t-amber-500">
                <div class="mb-4">
                    <h2 class="font-bold text-gray-900" style="font-size:15px flex items-center gap-2">
                        <i class="ri-alert-line text-amber-500 mr-1"></i> Penumpukan per Checkpoint
                    </h2>
                    <p class="text-xs text-gray-400 mt-1">Paket aktif yang sedang berada di Hub (belum terkirim)</p>
                </div>
                
                <div class="space-y-4">
                    <?php if (empty($stats['active_by_hub'])): ?>
                        <div class="text-center text-sm text-gray-400 py-4">Semua hub kosong.</div>
                    <?php else: ?>
                        <?php foreach ($stats['active_by_hub'] as $hub): ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-white shadow-sm flex items-center justify-center text-gray-500 border border-gray-200">
                                        <i class="ri-building-line text-sm"></i>
                                    </div>
                                    <span class="font-bold text-sm text-slate-800"><?= e($hub['hub_name'] ?? 'Unknown Hub') ?></span>
                                </div>
                                <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2 py-1 rounded-md">
                                    <?= number_format($hub['active_count']) ?> paket
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Shipments -->
        <div class="lg:col-span-8 card overflow-hidden">
        <div class="px-6 py-5 flex items-center justify-between border-b border-gray-50">
            <div>
                <h2 class="font-bold text-gray-900" style="font-size:15px">Pengiriman Terbaru</h2>
                <p class="text-xs text-gray-400 mt-0.5">10 transaksi resi paling terkini</p>
            </div>
            <a href="<?= url('/shipments') ?>" class="text-xs font-bold flex items-center gap-1 hover:underline" style="color:#40bf4e">
                Lihat Semua <i class="ri-arrow-right-s-line"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nomor Resi</th>
                        <th>Pengirim</th>
                        <th>Penerima</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recent)): ?>
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-400">
                                <i class="ri-inbox-line text-4xl block mb-2 text-gray-300"></i>
                                Belum ada data pengiriman.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recent as $item): ?>
                            <tr>
                                <td>
                                    <a href="<?= url('/shipments/detail?id=' . $item['id']) ?>"
                                       class="font-mono font-bold text-sm hover:underline flex items-center gap-1.5"
                                       style="color:#2b2c1e">
                                        <i class="ri-barcode-line text-gray-400 text-base"></i>
                                        <?= e($item['resi_number']) ?>
                                    </a>
                                </td>
                                <td>
                                    <div class="font-semibold text-gray-900"><?= e($item['sender_name']) ?></div>
                                    <div class="text-xs text-gray-400"><?= e($item['sender_phone']) ?></div>
                                </td>
                                <td>
                                    <div class="font-semibold text-gray-900"><?= e($item['receiver_name']) ?></div>
                                    <div class="text-xs text-gray-400"><?= e($item['receiver_phone']) ?></div>
                                </td>
                                <td>
                                    <span class="badge" style="background:#f5f5f0;color:#6b6b65;font-size:11.5px">
                                        <i class="ri-map-pin-2-line mr-1" style="color:#dd2c24"></i>
                                        <?= e($item['current_hub_name'] ?? 'Hub Asal') ?>
                                    </span>
                                </td>
                                <td><?= get_status_badge($item['status']) ?></td>
                                <td class="text-right">
                                    <a href="<?= url('/shipments/detail?id=' . $item['id']) ?>"
                                       class="badge" style="background:#f5f5f0;color:#2b2c1e;padding:5px 12px;cursor:pointer">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php renderSidebarFooter(); ?>
