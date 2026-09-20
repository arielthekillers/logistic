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
            <h1 class="page-title dark:text-white"><?= e($user['name'] ?? 'Operator') ?></h1>
            <p class="page-subtitle dark:text-gray-300">Ringkasan operasional & pengiriman terkini BDL.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <?php if (has_role('admin')): ?>
            <div class="relative">
                <select onchange="window.location.href='<?= url('/dashboard') ?>?hub_id=' + this.value" class="appearance-none bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-gray-200 py-2.5 pl-4 pr-10 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors shadow-sm cursor-pointer">
                    <option value="all">🌍 Semua Hub (Global)</option>
                    <?php foreach ($hubs ?? [] as $hub): ?>
                        <option value="<?= $hub['id'] ?>" <?= ($selected_hub_id == $hub['id']) ? 'selected' : '' ?>>
                            🏢 <?= e($hub['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                    <i class="ri-arrow-down-s-line"></i>
                </div>
            </div>
            <?php endif; ?>

            <a href="<?= url('/scanner') ?>" class="btn-outline">
                <i class="ri-qr-scan-2-line text-emerald-500"></i> Scan HP
            </a>
            <a href="<?= url('/shipments/create') ?>" class="btn-primary">
                <i class="ri-add-line"></i> Buat Resi
            </a>
        </div>
    </div>

    <!-- ─── Stat Cards ───────────────────────────────────── -->
    <?php if (!has_role('courier')): ?>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="card card-hover p-5 flex items-center gap-4 dark:bg-slate-800 dark:border-slate-700">
            <div class="stat-icon bg-gray-100 dark:bg-slate-700">
                <i class="ri-box-3-line text-slate-800 dark:text-gray-200 text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Resi Hari Ini</p>
                <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white mt-0.5"><?= number_format($stats['today_count'] ?? 0) ?></h3>
            </div>
        </div>

        <div class="card card-hover p-5 flex items-center gap-4 dark:bg-slate-800 dark:border-slate-700">
            <div class="stat-icon bg-blue-50 dark:bg-blue-900/30">
                <i class="ri-building-4-line text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tiba di Hub</p>
                <h3 class="text-2xl font-extrabold text-blue-600 dark:text-blue-400 mt-0.5"><?= number_format($counts['RECEIVED_AT_HUB'] ?? 0) ?></h3>
            </div>
        </div>

        <div class="card card-hover p-5 flex items-center gap-4 dark:bg-slate-800 dark:border-slate-700">
            <div class="stat-icon bg-yellow-50 dark:bg-yellow-900/30">
                <i class="ri-truck-line text-yellow-600 dark:text-yellow-400 text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Dalam Perjalanan</p>
                <h3 class="text-2xl font-extrabold text-yellow-600 dark:text-yellow-400 mt-0.5"><?= number_format($counts['IN_TRANSIT'] ?? 0) ?></h3>
            </div>
        </div>

        <div class="card card-hover p-5 flex items-center gap-4 dark:bg-slate-800 dark:border-slate-700">
            <div class="stat-icon bg-green-50 dark:bg-green-900/30">
                <i class="ri-checkbox-circle-line text-green-600 dark:text-green-400 text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Diterima</p>
                <h3 class="text-2xl font-extrabold text-green-600 dark:text-green-400 mt-0.5"><?= number_format($counts['DELIVERED'] ?? 0) ?></h3>
            </div>
        </div>
    </div>
    <?php else: ?>
    <!-- Courier Dashboard Simplified -->
    <div class="bg-gradient-to-r from-emerald-500 to-teal-500 rounded-2xl p-6 text-white shadow-lg flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold">Siap Mengantar Paket Hari Ini?</h2>
            <p class="text-emerald-100 mt-1 text-sm">Gunakan fitur scanner untuk memperbarui status pengiriman secara instan ke pelanggan.</p>
        </div>
        <a href="<?= url('/scanner') ?>" class="bg-white text-emerald-600 hover:bg-emerald-50 font-bold py-3 px-6 rounded-xl shadow-sm transition-colors flex items-center gap-2">
            <i class="ri-qr-scan-2-line text-xl"></i> Buka Scanner
        </a>
    </div>
    <?php endif; ?>

    <!-- ─── Charts Row ───────────────────────── -->
    <?php if (!has_role('courier')): ?>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mt-6 mb-6">
        <!-- Line Chart: Weekly Trend -->
        <div class="lg:col-span-8 card p-6 dark:bg-slate-800 dark:border-slate-700">
            <h2 class="font-bold text-gray-900 dark:text-white text-[15px] mb-4">Tren Pengiriman 7 Hari Terakhir</h2>
            <div class="w-full h-64">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- Doughnut Chart: Status Proportion -->
        <div class="lg:col-span-4 card p-6 dark:bg-slate-800 dark:border-slate-700">
            <h2 class="font-bold text-gray-900 dark:text-white text-[15px] mb-4">Proporsi Status Pengiriman</h2>
            <div class="w-full h-64 flex justify-center">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ─── 2 Column Layout: Penumpukan vs Recent ───────────────────────── -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Active Hubs / Bottlenecks -->
        <?php if (!has_role('courier')): ?>
        <div class="lg:col-span-4 space-y-6">
            <div class="card p-6 h-full border-t-4 border-t-amber-500 dark:bg-slate-800 dark:border-slate-700">
                <div class="mb-4">
                    <h2 class="font-bold text-gray-900 dark:text-white text-[15px] flex items-center gap-2">
                        <i class="ri-alert-line text-amber-500 mr-1"></i> Penumpukan per Checkpoint
                    </h2>
                    <p class="text-xs text-gray-400 mt-1">Paket aktif yang sedang berada di Hub (belum terkirim)</p>
                </div>
                
                <div class="space-y-4">
                    <?php if (empty($stats['active_by_hub'])): ?>
                        <div class="text-center text-sm text-gray-400 py-4">Semua hub kosong.</div>
                    <?php else: ?>
                        <?php foreach ($stats['active_by_hub'] as $hub): 
                            $isMyHub = (($user['hub_id'] ?? null) == ($hub['hub_id'] ?? null));
                        ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-slate-700/50 rounded-xl border border-gray-100 dark:border-slate-600 <?php echo $isMyHub ? 'border-blue-300 dark:border-blue-500 bg-blue-50 dark:bg-blue-900/20' : ''; ?>">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-white dark:bg-slate-600 shadow-sm flex items-center justify-center text-gray-500 dark:text-gray-300 border border-gray-200 dark:border-slate-500">
                                        <i class="ri-building-line text-sm"></i>
                                    </div>
                                    <span class="font-bold text-sm text-slate-800 dark:text-gray-100">
                                        <?= e($hub['hub_name'] ?? 'Unknown Hub') ?>
                                        <?php if ($isMyHub): ?>
                                            <span class="text-[10px] text-blue-600 dark:text-blue-400 block">(Lokasi Anda)</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <span class="bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-300 text-xs font-bold px-2 py-1 rounded-md">
                                    <?= number_format($hub['active_count']) ?> paket
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Recent Shipments -->
        <div class="lg:col-span-8 card overflow-hidden dark:bg-slate-800 dark:border-slate-700 <?php echo has_role('courier') ? 'lg:col-span-12' : ''; ?>">
        <div class="px-6 py-5 flex items-center justify-between border-b border-gray-50 dark:border-slate-700">
            <div>
                <h2 class="font-bold text-gray-900 dark:text-white text-[15px]">Pengiriman Terbaru</h2>
                <p class="text-xs text-gray-400 mt-0.5">10 transaksi resi paling terkini</p>
            </div>
            <a href="<?= url('/shipments') ?>" class="text-xs font-bold flex items-center gap-1 hover:underline text-emerald-600 dark:text-emerald-400">
                Lihat Semua <i class="ri-arrow-right-s-line"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="data-table w-full text-left border-collapse">
                <thead class="bg-gray-50 dark:bg-slate-800 text-gray-500 dark:text-gray-400 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3">Nomor Resi</th>
                        <th class="px-4 py-3">Pengirim</th>
                        <th class="px-4 py-3">Penerima</th>
                        <th class="px-4 py-3">Lokasi</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    <?php if (empty($recent)): ?>
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-400">
                                <i class="ri-inbox-line text-4xl block mb-2 text-gray-300 dark:text-gray-600"></i>
                                Belum ada data pengiriman.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recent as $item): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50">
                                <td class="px-4 py-3">
                                    <a href="<?= url('/shipments/detail?id=' . $item['id']) ?>"
                                       class="font-mono font-bold text-sm hover:underline flex items-center gap-1.5 text-slate-800 dark:text-gray-200">
                                        <i class="ri-barcode-line text-gray-400 text-base"></i>
                                        <?= e($item['resi_number']) ?>
                                    </a>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-gray-900 dark:text-gray-100"><?= e($item['sender_name']) ?></div>
                                    <div class="text-xs text-gray-400"><?= e($item['sender_phone']) ?></div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-gray-900 dark:text-gray-100"><?= e($item['receiver_name']) ?></div>
                                    <div class="text-xs text-gray-400"><?= e($item['receiver_phone']) ?></div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300 text-[11.5px] px-2 py-1 rounded flex w-fit items-center">
                                        <i class="ri-map-pin-2-line mr-1 text-red-500"></i>
                                        <?= e($item['current_hub_name'] ?? 'Hub Asal') ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3"><?= get_status_badge($item['status']) ?></td>
                                <td class="px-4 py-3 text-right">
                                    <a href="<?= url('/shipments/detail?id=' . $item['id']) ?>"
                                       class="badge bg-gray-100 dark:bg-slate-700 text-slate-800 dark:text-gray-200 px-3 py-1.5 rounded-md hover:bg-gray-200 dark:hover:bg-slate-600">
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

<?php if (!has_role('courier')): ?>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isDarkMode = document.documentElement.classList.contains('dark');
    const textColor = isDarkMode ? '#cbd5e1' : '#475569';
    const gridColor = isDarkMode ? '#334155' : '#f1f5f9';
    
    const weeklyData = <?= json_encode($stats['weekly_trend'] ?? []) ?>;
    const trendLabels = Object.keys(weeklyData).map(date => {
        let d = new Date(date);
        return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
    });
    const trendValues = Object.values(weeklyData);

    const trendCtx = document.getElementById('trendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'Jumlah Resi',
                data: trendValues,
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3,
                pointBackgroundColor: '#10b981',
                pointBorderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { ticks: { color: textColor }, grid: { display: false } },
                y: { ticks: { color: textColor, precision: 0 }, grid: { color: gridColor } }
            }
        }
    });

    const statusCounts = <?= json_encode($stats['counts'] ?? []) ?>;
    // Exclude TOTAL
    const statusLabelsRaw = Object.keys(statusCounts).filter(k => k !== 'TOTAL' && statusCounts[k] > 0);
    const statusLabelsMap = {
        'DRAFT': 'Draft',
        'RECEIVED_AT_HUB': 'Tiba di Hub',
        'SORTED': 'Disortir',
        'IN_TRANSIT': 'Dalam Perjalanan',
        'OUT_FOR_DELIVERY': 'Diantar',
        'DELIVERED': 'Diterima',
        'CANCELLED': 'Dibatalkan',
        'PROBLEM': 'Bermasalah'
    };
    const statusLabels = statusLabelsRaw.map(k => statusLabelsMap[k] || k);
    const statusValues = statusLabelsRaw.map(k => statusCounts[k]);
    const statusColors = ['#f59e0b', '#3b82f6', '#8b5cf6', '#6366f1', '#10b981', '#ef4444', '#64748b'];

    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusValues,
                backgroundColor: statusColors,
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { color: textColor } }
            },
            cutout: '65%'
        }
    });
});
</script>
<?php endif; ?>

<?php renderSidebarFooter(); ?>
