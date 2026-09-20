<?php
renderSidebarHeader("Data Pengiriman - PT. Barongko Darma Logistik");
?>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.25rem 0.5rem;
        margin-left: 0.25rem;
        border-radius: 0.375rem;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        color: #374151 !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #10b981;
        color: white !important;
        border-color: #10b981;
    }
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
        padding: 0.25rem 0.5rem;
        margin-left: 0.5rem;
    }
    .dataTables_wrapper {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        font-size: 13.5px !important;
        color: #64748b;
    }
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
    }
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
        padding: 0.25rem 2rem 0.25rem 0.5rem;
    }
    /* Dark Mode Overrides */
    .dark .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-color: #334155;
        background: #1e293b;
        color: #e2e8f0 !important;
    }
    .dark .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #10b981;
        color: white !important;
        border-color: #10b981;
    }
    .dark .dataTables_wrapper .dataTables_filter input,
    .dark .dataTables_wrapper .dataTables_length select {
        border-color: #334155;
        background: #0f172a;
        color: #e2e8f0;
    }
    .dark .dataTables_wrapper .dataTables_info,
    .dark .dataTables_wrapper .dataTables_length,
    .dark .dataTables_wrapper .dataTables_filter {
        color: #94a3b8 !important;
    }
</style>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Data Pengiriman Barang (Resi)</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Kelola dan lihat seluruh manifes resi pengiriman BDL.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <form action="<?= url('/shipments/importCSV') ?>" method="POST" enctype="multipart/form-data" class="inline" id="formImport">
                <input type="file" name="file" id="csvFile" accept=".csv" class="hidden" onchange="document.getElementById('formImport').submit()">
                <button type="button" onclick="document.getElementById('csvFile').click()" class="btn-outline">
                    <i class="ri-file-upload-line text-amber-500"></i> Import CSV
                </button>
            </form>
            <button onclick="exportExcel()" class="btn-outline">
                <i class="ri-file-excel-2-line text-emerald-500"></i> Export CSV
            </button>
            <a href="<?= url('/shipments/create') ?>" class="btn-green">
                <i class="ri-add-line"></i> Buat Resi
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700">
        <form id="filterForm" class="flex flex-col xl:flex-row gap-3" onsubmit="event.preventDefault(); reloadTable();">
            <!-- Date Filters -->
            <div class="w-full xl:w-auto flex items-center gap-2">
                <input type="date" name="start_date" id="start_date" class="bg-gray-50 dark:bg-slate-900 text-slate-800 dark:text-gray-200 border border-gray-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-sm focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-emerald-500 outline-none transition-all" title="Tanggal Mulai">
                <span class="text-gray-400 dark:text-gray-500 text-xs">s/d</span>
                <input type="date" name="end_date" id="end_date" class="bg-gray-50 dark:bg-slate-900 text-slate-800 dark:text-gray-200 border border-gray-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-sm focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-emerald-500 outline-none transition-all" title="Tanggal Akhir">
            </div>
            <!-- Status -->
            <div class="w-full xl:w-48">
                <select name="status" id="filter_status" class="w-full bg-gray-50 dark:bg-slate-900 text-slate-800 dark:text-gray-200 border border-gray-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-emerald-500 outline-none transition-all">
                    <option value="">-- Semua Status --</option>
                    <option value="DRAFT">Draft</option>
                    <option value="RECEIVED_AT_HUB">Tiba di Hub</option>
                    <option value="SORTED">Disortir</option>
                    <option value="IN_TRANSIT">Dalam Perjalanan</option>
                    <option value="OUT_FOR_DELIVERY">Dalam Pengantaran</option>
                    <option value="DELIVERED">Diterima</option>
                    <option value="PROBLEM">Bermasalah</option>
                    <option value="CANCELLED">Dibatalkan</option>
                </select>
            </div>
            <!-- Actions -->
            <div class="flex items-center gap-2">
                <button type="button" onclick="reloadTable()" class="px-5 py-2.5 bg-slate-800 dark:bg-slate-700 hover:bg-slate-900 dark:hover:bg-slate-600 text-white font-bold rounded-xl shadow-sm transition-colors text-sm">
                    Filter
                </button>
                <button type="button" onclick="resetFilters()" class="px-5 py-2.5 bg-gray-100 dark:bg-slate-900 hover:bg-gray-200 dark:hover:bg-slate-950 text-slate-700 dark:text-gray-300 border dark:border-slate-700 font-bold rounded-xl transition-colors text-sm">
                    Reset
                </button>
            </div>
        </form>
    </div>

    <!-- Bulk Actions -->
    <div id="bulkActions" class="hidden bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/50 p-3 rounded-xl flex items-center justify-between">
        <span class="text-amber-800 dark:text-amber-400 text-sm font-bold"><span id="selectedCount">0</span> resi terpilih</span>
        <button onclick="bulkDelete()" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg transition-colors">
            Hapus Terpilih
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-4">
        <div class="overflow-x-auto">
            <table id="shipmentsTable" class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-slate-900/50 text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wider border-b border-gray-100 dark:border-slate-700">
                        <th class="py-3 px-4 w-10 text-center"><input type="checkbox" id="selectAll" class="rounded border-gray-300 dark:border-slate-600 text-emerald-600 dark:bg-slate-700"></th>
                        <th class="py-3 px-4 font-semibold">Resi</th>
                        <th class="py-3 px-4 font-semibold">Pengirim</th>
                        <th class="py-3 px-4 font-semibold">Penerima</th>
                        <th class="py-3 px-4 font-semibold">Hub Asal & Tujuan</th>
                        <th class="py-3 px-4 font-semibold">Berat / Biaya</th>
                        <th class="py-3 px-4 font-semibold">Status</th>
                        <th class="py-3 px-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700 text-slate-700 dark:text-gray-300">
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- jQuery and DataTables -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
    let dtTable;
    
    $(document).ready(function() {
        dtTable = $('#shipmentsTable').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: "<?= url('/shipments/dataTable') ?>",
                type: "GET",
                data: function(d) {
                    d.status = $('#filter_status').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                }
            },
            columns: [
                { 
                    data: 'id', 
                    orderable: false, 
                    searchable: false,
                    className: 'text-center',
                    render: function(data) {
                        return `<input type="checkbox" class="row-checkbox rounded border-gray-300 text-emerald-600" value="${data}">`;
                    }
                },
                { data: 'resi_number', className: 'font-bold text-emerald-700 font-mono' },
                { data: 'sender' },
                { data: 'receiver' },
                { data: 'hubs' },
                { data: 'weight_cost' },
                { data: 'status' },
                { data: 'action', orderable: false, searchable: false }
            ],
            language: {
                search: "Cari Resi:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ data",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "Lanjut",
                    previous: "Kembali"
                }
            },
            order: [[1, 'desc']], // order by resi desc initially
            drawCallback: function() {
                updateBulkActions();
            }
        });

        $('#selectAll').on('change', function() {
            $('.row-checkbox').prop('checked', this.checked);
            updateBulkActions();
        });

        $('#shipmentsTable tbody').on('change', '.row-checkbox', function() {
            updateBulkActions();
        });
    });

    function reloadTable() {
        dtTable.ajax.reload();
    }

    function resetFilters() {
        $('#filterForm')[0].reset();
        reloadTable();
    }

    function updateBulkActions() {
        let count = $('.row-checkbox:checked').length;
        $('#selectedCount').text(count);
        if (count > 0) {
            $('#bulkActions').removeClass('hidden');
        } else {
            $('#bulkActions').addClass('hidden');
        }
    }

    function bulkDelete() {
        let ids = [];
        $('.row-checkbox:checked').each(function() {
            ids.push($(this).val());
        });
        
        if (ids.length === 0) return;
        
        if (confirm(`Yakin ingin menghapus ${ids.length} resi terpilih?`)) {
            fetch("<?= url('/shipments/bulkDelete') ?>", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ids: ids })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    SwalToast.fire({icon: 'success', title: data.message});
                    $('#selectAll').prop('checked', false);
                    reloadTable();
                } else {
                    SwalToast.fire({icon: 'error', title: data.message});
                }
            })
            .catch(err => {
                SwalToast.fire({icon: 'error', title: 'Terjadi kesalahan pada server'});
            });
        }
    }

    function exportExcel() {
        // Build export URL with current filters
        let params = new URLSearchParams({
            status: $('#filter_status').val(),
            start_date: $('#start_date').val(),
            end_date: $('#end_date').val(),
            search: dtTable.search()
        });
        
        window.location.href = "<?= url('/shipments/export') ?>?" + params.toString();
    }
</script>

<?php renderSidebarFooter(); ?>
