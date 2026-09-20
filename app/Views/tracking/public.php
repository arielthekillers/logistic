<?php
renderPublicHeader("Tracking Barang - Logistic Express");
?>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #tracking-map { height: 350px; z-index: 10; border-radius: 1rem; }
    .step-active .step-icon { background: #4f46e5; border-color: #4f46e5; color: white; }
    .step-active .step-text { color: #4f46e5; font-weight: bold; }
    .step-completed .step-icon { background: #10b981; border-color: #10b981; color: white; }
    .step-completed .step-line { background: #10b981; }
    .step-completed .step-text { color: #10b981; font-weight: bold; }
</style>

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

                <!-- Tracking Progress Bar -->
                <?php
                $statusOrder = ['RECEIVED_AT_HUB', 'SORTED', 'IN_TRANSIT', 'OUT_FOR_DELIVERY', 'DELIVERED'];
                $currentStatusIndex = array_search($shipment['status'], $statusOrder);
                if ($currentStatusIndex === false) {
                    // Jika Draft / Problem / Cancelled
                    $currentStatusIndex = -1;
                }
                
                $stepLabels = [
                    'RECEIVED_AT_HUB' => ['ikon' => 'ri-inbox-archive-line', 'teks' => 'Drop Hub'],
                    'SORTED' => ['ikon' => 'ri-folders-line', 'teks' => 'Disortir'],
                    'IN_TRANSIT' => ['ikon' => 'ri-truck-line', 'teks' => 'Transit'],
                    'OUT_FOR_DELIVERY' => ['ikon' => 'ri-e-bike-2-line', 'teks' => 'Kurir'],
                    'DELIVERED' => ['ikon' => 'ri-checkbox-circle-line', 'teks' => 'Diterima']
                ];
                ?>
                <div class="pt-8 pb-4">
                    <div class="flex items-center justify-between relative">
                        <!-- Connecting Line Background -->
                        <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1.5 bg-gray-100 rounded-full z-0"></div>
                        
                        <?php foreach ($statusOrder as $idx => $s): 
                            $isCompleted = ($currentStatusIndex !== -1 && $idx < $currentStatusIndex);
                            $isActive = ($currentStatusIndex !== -1 && $idx === $currentStatusIndex);
                            
                            $stepClass = '';
                            if ($isCompleted) $stepClass = 'step-completed';
                            elseif ($isActive) $stepClass = 'step-active';
                        ?>
                        <div class="relative z-10 flex flex-col items-center <?= $stepClass ?>">
                            <div class="step-icon w-10 h-10 sm:w-12 sm:h-12 bg-white border-2 border-gray-200 rounded-full flex items-center justify-center text-gray-400 shadow-sm transition-colors duration-300">
                                <i class="<?= $stepLabels[$s]['ikon'] ?> sm:text-lg"></i>
                            </div>
                            <div class="step-text text-[10px] sm:text-xs text-gray-400 mt-2 text-center absolute top-full w-24 -ml-6 sm:-ml-6">
                                <?= $stepLabels[$s]['teks'] ?>
                            </div>
                            
                            <?php if ($idx < count($statusOrder) - 1): ?>
                            <!-- Active Line Segment -->
                            <div class="step-line absolute top-1/2 left-[calc(100%+0px)] -translate-y-1/2 h-1.5 transition-all duration-700" style="width: calc(100vw / 5); max-width: 140px; z-index: -1;"></div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Sender & Receiver Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-12">
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

            <!-- Leaflet Interactive Map Card -->
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200">
                <div class="mb-3 px-2 flex justify-between items-center">
                    <h3 class="font-bold text-gray-900"><i class="ri-map-2-line text-indigo-600 mr-1"></i> Live Map Tracking</h3>
                    <span class="text-xs bg-indigo-50 text-indigo-600 px-2 py-1 rounded-md font-semibold">Simulasi Jalur</span>
                </div>
                <div id="tracking-map" class="shadow-inner border border-gray-100"></div>
            </div>
        </div>
    <?php endif; ?>

</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (document.getElementById('tracking-map')) {
            // Inisialisasi map dengan default center Indonesia
            const map = L.map('tracking-map').setView([-2.5489, 118.0149], 5);
            
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; OpenStreetMap contributors & CARTO',
                maxZoom: 19
            }).addTo(map);

            // Mockup coordinates based on common cities for visual demo
            const cityCoords = {
                'Jakarta': [-6.2088, 106.8456],
                'Bandung': [-6.9175, 107.6191],
                'Surabaya': [-7.2504, 112.7688],
                'Semarang': [-6.9932, 110.4203],
                'Yogyakarta': [-7.7956, 110.3695],
                'Medan': [3.5952, 98.6722],
                'Makassar': [-5.1477, 119.4327],
                'Bali': [-8.4095, 115.1889]
            };

            // Parse checkpoints location names to estimate path
            <?php 
                $routeCities = [];
                if (!empty($checkpoints)) {
                    foreach (array_reverse($checkpoints) as $cp) {
                        $loc = strtolower($cp['location_name']);
                        $routeCities[] = $loc;
                    }
                }
            ?>
            const routeLog = <?= json_encode($routeCities) ?>;
            
            let coords = [];
            routeLog.forEach(locName => {
                let found = false;
                for (const [city, latlng] of Object.entries(cityCoords)) {
                    if (locName.includes(city.toLowerCase())) {
                        coords.push(latlng);
                        found = true;
                        break;
                    }
                }
                // Fallback default
                if (!found && coords.length === 0) {
                    coords.push(cityCoords['Jakarta']);
                } else if (!found) {
                    // slightly offset previous to show movement
                    let last = coords[coords.length-1];
                    coords.push([last[0] + 0.05, last[1] + 0.05]);
                }
            });

            if (coords.length > 0) {
                // Remove duplicates visually
                const uniqueCoords = [...new Set(coords.map(c => c.join(',')))].map(c => c.split(',').map(Number));
                
                // Draw polyline
                const pathLine = L.polyline(uniqueCoords, {
                    color: '#4f46e5',
                    weight: 4,
                    opacity: 0.7,
                    dashArray: '10, 10',
                    lineJoin: 'round'
                }).addTo(map);
                
                // Fit bounds
                map.fitBounds(pathLine.getBounds(), { padding: [50, 50] });

                // Add marker to current latest location
                const currentLoc = coords[coords.length - 1];
                
                const customIcon = L.divIcon({
                    html: `<div style="background:#ef4444; color:white; width:30px; h:30px; border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); border: 2px solid white; font-size:16px"><i class="ri-map-pin-2-fill"></i></div>`,
                    className: 'custom-leaflet-icon',
                    iconSize: [30, 30],
                    iconAnchor: [15, 30]
                });

                L.marker(currentLoc, {icon: customIcon})
                    .addTo(map)
                    .bindPopup("<b>Posisi Saat Ini</b><br><?= e($shipment['current_hub_name'] ?? '') ?>")
                    .openPopup();
            }
        }
    });
</script>

<?php renderPublicFooter(); ?>
