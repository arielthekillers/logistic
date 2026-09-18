<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>TANDA TERIMA BARANG - <?= e($shipment['resi_number']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <style>
        @media print {
            @page { margin: 10mm; }
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; margin: 0 !important; justify-content: flex-start !important; min-height: 0 !important; }
            .receipt-box { border: 2px solid black !important; box-shadow: none !important; width: 900px !important; max-width: none !important; margin: 0 auto; transform-origin: top center; }
        }
    </style>
</head>
<body class="bg-gray-100 p-4 sm:p-8 flex flex-col items-center justify-center min-h-screen font-sans">

    <!-- Print Action Header -->
    <div class="no-print mb-6 flex gap-3">
        <button onclick="window.print()" class="px-6 py-2.5 bg-rose-600 text-white font-bold rounded-xl shadow-lg hover:bg-rose-700 transition-all flex items-center gap-2">
            🖨️ Cetak Tanda Terima Barang (Print)
        </button>
        <button onclick="window.close()" class="px-5 py-2.5 bg-gray-200 text-gray-800 font-semibold rounded-xl hover:bg-gray-300">
            Tutup
        </button>
    </div>

    <!-- Official Tanda Terima Barang Document -->
    <div class="receipt-box w-full max-w-4xl bg-white border-2 border-black p-5 text-black text-xs leading-normal shadow-2xl relative">
        
        <!-- Header Company Logo & Title -->
        <div class="flex items-start justify-between border-b-2 border-black pb-3 mb-3 gap-2">
            <div class="flex items-center space-x-3">
                <!-- Logo Image -->
                <img src="<?= url('/' . COMPANY_LOGO) ?>" alt="Logo BDL" class="h-14 w-auto object-contain">
                <div>
                    <h1 class="text-xl sm:text-2xl font-black tracking-tight text-red-600 uppercase"><?= COMPANY_NAME ?></h1>
                    <p class="font-bold text-[11px] text-gray-800"><?= COMPANY_ADDRESS ?></p>
                    <p class="text-[10px] text-gray-700">Telp. <?= COMPANY_PHONE ?> | Email: <?= COMPANY_EMAIL ?></p>
                </div>
            </div>
            
            <div class="text-right border-t-0 pt-0 w-auto flex flex-col justify-between items-end">
                <span class="text-xs font-bold uppercase text-gray-500">NO. RESI / WAYBILL:</span>
                <span class="text-lg font-black font-mono text-black">No. <?= e($shipment['resi_number']) ?></span>
            </div>
        </div>

        <!-- Document Title Banner -->
        <div class="bg-black text-white text-center py-1.5 font-black uppercase text-sm tracking-wider mb-3">
            TANDA TERIMA BARANG
        </div>

        <!-- Shipper & Consignee Grid -->
        <div class="grid grid-cols-2 border-2 border-black mb-3">
            <!-- Shipper (DARI) -->
            <div class="p-3 border-r-2 border-black space-y-1">
                <span class="font-black text-gray-500 uppercase text-[10px] block border-b border-gray-300 pb-1">DARI / SHIPPER:</span>
                <p class="font-black text-sm uppercase text-gray-900"><?= e($shipment['sender_name']) ?></p>
                <p class="font-bold text-xs text-gray-800"><?= e($shipment['sender_phone']) ?></p>
                <p class="text-xs text-gray-700 uppercase font-medium leading-tight"><?= e($shipment['sender_address']) ?></p>
                <p class="text-xs font-bold text-indigo-900 uppercase">KOTA ASAL: <?= e($shipment['origin_city'] ?? 'SURABAYA') ?></p>
            </div>

            <!-- Consignee (KEPADA) -->
            <div class="p-3 space-y-1">
                <span class="font-black text-gray-500 uppercase text-[10px] block border-b border-gray-300 pb-1">KEPADA / CONSIGNEE:</span>
                <p class="font-black text-sm uppercase text-gray-900"><?= e($shipment['receiver_name']) ?></p>
                <p class="font-bold text-xs text-gray-800"><?= e($shipment['receiver_phone']) ?></p>
                <p class="text-xs text-gray-700 uppercase font-medium leading-tight"><?= e($shipment['receiver_address']) ?></p>
                <p class="text-xs font-bold text-indigo-900 uppercase">KOTA TUJUAN: <?= e($shipment['destination_city'] ?? 'SAMARINDA') ?></p>
            </div>
        </div>

        <!-- Goods Table -->
        <table class="w-full text-left border-collapse border-2 border-black text-xs mb-3">
            <thead>
                <tr class="bg-gray-200 text-black uppercase text-[11px] font-black border-b-2 border-black">
                    <th class="border-r border-black p-2">Isi Barang Menurut Pengakuan</th>
                    <th class="border-r border-black p-2">Kemasan & Dimensi</th>
                    <th class="border-r border-black p-2 text-center w-16">Koli</th>
                    <th class="border-r border-black p-2 text-center w-20">Berat (Kg)</th>
                    <th class="p-2">Keterangan / No. SJ</th>
                </tr>
            </thead>
            <tbody class="font-semibold text-gray-900">
                <?php
                $packages = [];
                if (!empty($shipment['package_items'])) {
                    $packages = json_decode($shipment['package_items'], true) ?: [];
                }
                if (empty($packages)) {
                    $packages = [['kemasan' => 'Dos / Karung / Pack', 'koli' => $shipment['koli'] ?? 1]];
                }
                $rowCount = count($packages);
                foreach ($packages as $idx => $pkg): 
                ?>
                <tr class="<?= $idx === $rowCount - 1 ? 'border-b border-black' : 'border-b border-gray-300' ?>">
                    <?php if ($idx === 0): ?>
                    <td class="border-r border-black p-2.5 font-bold uppercase" rowspan="<?= $rowCount ?>">
                        <?= e($shipment['notes'] ?: 'SPAREPART / BARANG GENERAL') ?>
                    </td>
                    <?php endif; ?>
                    
                    <td class="border-r border-black p-2.5">
                        <?= e($pkg['kemasan']) ?>
                    </td>
                    <td class="border-r border-black p-2.5 text-center font-bold text-sm">
                        <?= e($pkg['koli']) ?>
                    </td>
                    
                    <?php if ($idx === 0): ?>
                    <td class="border-r border-black p-2.5 text-center font-bold text-sm" rowspan="<?= $rowCount ?>">
                        <?= number_format($shipment['weight_kg'], 1) ?>
                    </td>
                    <td class="p-2.5 font-mono" rowspan="<?= $rowCount ?>">
                        <?= e($shipment['resi_number']) ?>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
                
                <?php if ($rowCount > 1): ?>
                <tr class="border-b border-black bg-gray-100">
                    <td class="border-r border-black p-2 text-right font-bold text-xs" colspan="2">TOTAL KOLI:</td>
                    <td class="border-r border-black p-2 text-center font-bold text-sm"><?= e($shipment['koli']) ?></td>
                    <td colspan="2"></td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Cost & Payment Details Grid -->
        <div class="grid grid-cols-2 gap-3 border-2 border-black p-3 mb-4">
            <!-- Payment Type Checks -->
            <div>
                <span class="font-black text-gray-500 uppercase text-[10px] block mb-2">METODE PEMBAYARAN / PAYMENT:</span>
                <div class="grid grid-cols-2 gap-2 text-xs font-bold">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" checked class="w-4 h-4 text-black border-black">
                        <span>[✓] Cash</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" class="w-4 h-4">
                        <span>[ ] Invoice</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" class="w-4 h-4">
                        <span>[ ] Collect</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" class="w-4 h-4">
                        <span>[ ] Credit Card</span>
                    </label>
                </div>
            </div>

            <!-- Cost Summary -->
            <div class="space-y-1 text-right border-l border-black pl-4">
                <div class="flex justify-between">
                    <span class="text-gray-600 font-bold">Biaya Kirim:</span>
                    <span class="font-bold text-gray-900"><?= format_rp($shipment['total_cost']) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 font-bold">Asuransi:</span>
                    <span class="font-bold text-gray-900">Rp 0</span>
                </div>
                <div class="flex justify-between border-t border-gray-300 pt-1 text-sm font-black text-black">
                    <span>JUMLAH TOTAL:</span>
                    <span><?= format_rp($shipment['total_cost']) ?></span>
                </div>
            </div>
        </div>

        <!-- Barcode & Signatures Section -->
        <div class="grid grid-cols-4 gap-3 items-end pt-2">
            <!-- Barcode Rendering -->
            <div class="text-center col-span-1 flex flex-col items-center justify-center">
                <?php $barcodeType = get_setting('barcode_type', '1d'); ?>
                <?php if ($barcodeType === 'qr'): ?>
                    <div id="tanda-terima-qr" class="mx-auto bg-white p-2 border border-gray-200 rounded-lg"></div>
                    <span class="font-bold font-mono text-[11px] mt-1 text-gray-900"><?= e($shipment['resi_number']) ?></span>
                <?php else: ?>
                    <svg id="tanda-terima-barcode" class="mx-auto"></svg>
                <?php endif; ?>
            </div>

            <!-- Signatures -->
            <div class="col-span-3 grid grid-cols-3 text-center text-xs gap-2 pt-4">
                <div class="border-t border-dashed border-black pt-2">
                    <p class="font-bold uppercase text-[10px] text-gray-600 mb-8">PETUGAS BDL</p>
                    <p class="font-bold text-gray-900">( ......................... )</p>
                </div>
                <div class="border-t border-dashed border-black pt-2">
                    <p class="font-bold uppercase text-[10px] text-gray-600 mb-8">PENGIRIM</p>
                    <p class="font-bold text-gray-900">( <?= e($shipment['sender_name']) ?> )</p>
                </div>
                <div class="border-t border-dashed border-black pt-2">
                    <p class="font-bold uppercase text-[10px] text-gray-600 mb-8">PENERIMA</p>
                    <p class="font-bold text-gray-900">( <?= e($shipment['receiver_name']) ?> )</p>
                </div>
            </div>
        </div>

        <!-- Footer Date -->
        <div class="text-right text-[10px] font-bold text-gray-500 mt-3 pt-1 border-t border-gray-200">
            Surabaya, <?= date('d M Y') ?> | Tanda Terima Resmi <?= e(COMPANY_NAME) ?>
        </div>
    </div>
    <!-- Load QRCode.js for printing -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <script>
        <?php if ($barcodeType === 'qr'): ?>
            new QRCode(document.getElementById("tanda-terima-qr"), {
                text: "<?= e($shipment['resi_number']) ?>",
                width: 90,
                height: 90,
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.M
            });
        <?php else: ?>
            JsBarcode("#tanda-terima-barcode", "<?= e($shipment['resi_number']) ?>", {
                format: "CODE128",
                width: 1.8,
                height: 45,
                fontSize: 12,
                displayValue: true
            });
        <?php endif; ?>
    </script>
</body>
</html>
