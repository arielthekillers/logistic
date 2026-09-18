<?php
renderSidebarHeader("Laporan & Export - PT. Barongko Darma Logistik");
$user = auth_user();
?>

<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <h1 class="page-title">Laporan & Rekap</h1>
            <p class="page-subtitle">Pilih rentang tanggal dan filter untuk mengunduh laporan ke Excel (CSV).</p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card p-6">
        <form action="<?= url('/reports/export') ?>" method="GET" target="_blank" class="space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Start Date -->
                <div>
                    <label class="form-label">Tanggal Mulai *</label>
                    <input type="date" name="start_date" value="<?= date('Y-m-d') ?>" required class="form-input">
                </div>
                
                <!-- End Date -->
                <div>
                    <label class="form-label">Tanggal Akhir *</label>
                    <input type="date" name="end_date" value="<?= date('Y-m-d') ?>" required class="form-input">
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="form-label">Status Paket</label>
                    <select name="status" class="form-input">
                        <option value="">-- Semua Status --</option>
                        <option value="DRAFT">Draft</option>
                        <option value="RECEIVED_AT_HUB">Tiba di Hub</option>
                        <option value="SORTED">Disortir</option>
                        <option value="IN_TRANSIT">Dalam Perjalanan</option>
                        <option value="OUT_FOR_DELIVERY">Dalam Pengantaran</option>
                        <option value="DELIVERED">Diterima</option>
                        <option value="CANCELLED">Dibatalkan</option>
                        <option value="PROBLEM">Bermasalah / Tertahan</option>
                    </select>
                </div>

                <!-- Hub Filter -->
                <div>
                    <label class="form-label">Posisi (Hub) Saat Ini</label>
                    <select name="hub_id" class="form-input">
                        <option value="">-- Semua Hub / Gudang --</option>
                        <?php foreach ($hubs as $h): ?>
                            <option value="<?= $h['id'] ?>"><?= e($h['name']) ?> (<?= e($h['city']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                <button type="submit" class="btn-primary px-8">
                    <i class="ri-file-excel-2-line mr-2"></i> Download Laporan CSV
                </button>
            </div>
            <p class="text-xs text-gray-400 text-right mt-2">File CSV dapat langsung dibuka menggunakan Microsoft Excel.</p>
        </form>
    </div>
</div>

<?php renderSidebarFooter(); ?>
