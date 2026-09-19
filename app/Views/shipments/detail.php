<?php
renderSidebarHeader("Detail Resi - " . ($shipment['resi_number'] ?? ''));
?>

<div class="max-w-4xl mx-auto space-y-6">

    <?php if (!empty($_SESSION['success_flash'])): ?>
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-md">
            <p class="text-sm text-emerald-700 font-medium"><?= e($_SESSION['success_flash']) ?></p>
        </div>
        <?php unset($_SESSION['success_flash']); ?>
    <?php endif; ?>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="<?= url('/shipments') ?>"
                class="text-xs text-emerald-700 font-bold hover:underline mb-1 inline-block">
                <i class="ri-arrow-left-line"></i> Kembali ke Data Pengiriman
            </a>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-3">
                Resi: <?= e($shipment['resi_number']) ?>
                <?= get_status_badge($shipment['status']) ?>
            </h1>
        </div>
        <div class="flex gap-3">
            <a href="<?= url('/shipments/label?id=' . $shipment['id']) ?>" target="_blank"
                class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl text-sm shadow-md flex items-center gap-2">
                <i class="ri-printer-line"></i> Cetak Tanda Terima Barang
            </a>
        </div>
    </div>

    <!-- Overview Box -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-3">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Informasi Pengirim</h3>
            <div>
                <p class="font-extrabold text-slate-900 text-base"><?= e($shipment['sender_name']) ?></p>
                <p class="text-xs text-emerald-700 font-bold mt-0.5"><i class="ri-phone-line"></i>
                    <?= e($shipment['sender_phone']) ?></p>
                <p class="text-xs text-slate-600 mt-1"><?= e($shipment['sender_address']) ?></p>
            </div>
        </div>

        <div class="space-y-3">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Informasi Penerima</h3>
            <div>
                <p class="font-extrabold text-slate-900 text-base"><?= e($shipment['receiver_name']) ?></p>
                <p class="text-xs text-emerald-700 font-bold mt-0.5"><i class="ri-phone-line"></i>
                    <?= e($shipment['receiver_phone']) ?></p>
                <p class="text-xs text-slate-600 mt-1"><?= e($shipment['receiver_address']) ?></p>
            </div>
        </div>
    </div>

    <!-- Barcode & Tracking Timeline -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 space-y-6">
        <div class="flex flex-col sm:flex-row items-center justify-between border-b border-gray-100 pb-6">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Barcode Resi</h3>
                <p class="text-xs text-gray-400">Barcode ini digunakan untuk scan kamera HP oleh petugas / kurir BDL.
                </p>
            </div>
            <div class="mt-4 sm:mt-0 text-center bg-gray-50 p-3 rounded-xl border border-gray-200">
                <?php $barcodeType = get_setting('barcode_type', '1d'); ?>
                <?php if ($barcodeType === 'qr'): ?>
                    <div id="barcode-qr" class="mx-auto flex flex-col items-center"></div>
                    <div class="text-xs font-mono font-bold mt-2 text-slate-800"><?= e($shipment['resi_number']) ?></div>
                <?php else: ?>
                    <svg id="barcode"></svg>
                <?php endif; ?>
            </div>
        </div>

        <!-- Checkpoints Timeline -->
        <div>
            <h3 class="font-bold text-slate-900 text-base mb-6 flex items-center gap-2">
                <i class="ri-history-line text-emerald-600"></i> Checkpoint Audit & Tracking
            </h3>
            <?php if (empty($checkpoints)): ?>
                <p class="text-sm text-gray-400">Belum ada riwayat scan.</p>
            <?php else: ?>
                <div class="relative border-l-2 border-emerald-100 ml-4 space-y-6">
                    <?php foreach (array_reverse($checkpoints) as $index => $cp): ?>
                        <div class="relative pl-8">
                            <div
                                class="absolute -left-[17px] top-0 w-8 h-8 rounded-full flex items-center justify-center text-white <?= $index === 0 ? 'bg-emerald-600 ring-4 ring-emerald-100 shadow-md' : 'bg-gray-300' ?>">
                                <i class="ri-check-line text-sm"></i>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <div class="flex items-center justify-between text-xs text-gray-400 mb-1">
                                    <span class="font-bold text-emerald-700"><?= format_datetime($cp['scanned_at']) ?></span>
                                    <div title="Petugas: <?= e($cp['user_name'] ?? 'System') ?>">
                                        <?php if (!empty($cp['user_avatar'])): ?>
                                            <img src="<?= url('/' . $cp['user_avatar']) ?>" alt="Petugas"
                                                class="w-6 h-6 rounded-full shadow-sm object-cover">
                                        <?php else: ?>
                                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($cp['user_name'] ?? 'System') ?>&background=random&color=fff&size=28"
                                                alt="Petugas" class="w-6 h-6 rounded-full shadow-sm">
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <h4 class="font-bold text-slate-900 text-sm"><?= e($cp['location_name']) ?></h4>
                                <p class="text-xs text-slate-600 mt-1"><?= e($cp['notes']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        <?php if ($barcodeType === 'qr'): ?>
            new QRCode(document.getElementById("barcode-qr"), {
                text: "<?= e($shipment['resi_number']) ?>",
                width: 120,
                height: 120,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.M
            });
        <?php else: ?>
            JsBarcode("#barcode", "<?= e($shipment['resi_number']) ?>", {
                format: "CODE128",
                width: 2,
                height: 50,
                displayValue: true
            });
        <?php endif; ?>
    });
</script>

<?php renderSidebarFooter(); ?>