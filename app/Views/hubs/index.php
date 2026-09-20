<?php
renderSidebarHeader("Data Gudang & Hub - PT. Barongko Darma Logistik");
?>

<div class="space-y-6">

    <!-- ─── Page Header ─────────────────────────────────── -->
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <h1 class="page-title dark:text-white">Gudang & Hub Transit</h1>
            <p class="page-subtitle dark:text-gray-400">Kelola lokasi gudang pusat, cabang & pusat sortir BDL.</p>
        </div>
        <button onclick="document.getElementById('modal-add-hub').classList.remove('hidden')" class="btn-primary w-max">
            <i class="ri-add-line"></i> Tambah Hub
        </button>
    </div>

    <!-- ─── Cards Grid ───────────────────────────────────── -->
    <?php if (empty($hubs)): ?>
        <div class="card dark:bg-slate-800 dark:border-slate-700 p-14 text-center">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4 bg-gray-50 dark:bg-slate-700">
                <i class="ri-building-4-line text-2xl text-gray-400 dark:text-gray-500"></i>
            </div>
            <p class="font-bold text-gray-600 dark:text-gray-300">Belum ada data Hub</p>
            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Klik "Tambah Hub" untuk menambahkan.</p>
        </div>
    <?php else: 
        $totalAll = count($hubs);
        $totalCentral = count(array_filter($hubs, fn($h) => $h['type'] === 'CENTRAL_HUB'));
        $totalSorting = count(array_filter($hubs, fn($h) => $h['type'] === 'SORTING_CENTER'));
        $totalBranch = count(array_filter($hubs, fn($h) => $h['type'] === 'BRANCH_HUB'));
    ?>
    
    <!-- ─── Filter Tabs ───────────────────────────────── -->
    <div class="flex items-center gap-1 flex-wrap border-b-2 border-gray-100 dark:border-slate-700 pb-0.5" id="type-filter-tabs">

        <button onclick="filterType('all',this)" data-tab="all" class="type-tab px-4 py-2 rounded-t-lg text-[13px] font-bold border-b-2 border-slate-900 text-slate-900 dark:border-white dark:text-white transition-all -mb-1">
            Semua <span class="font-medium text-gray-500 dark:text-gray-400">(<?= $totalAll ?>)</span>
        </button>

        <?php if ($totalCentral > 0): ?>
        <button onclick="filterType('CENTRAL_HUB',this)" data-tab="CENTRAL_HUB" class="type-tab px-4 py-2 rounded-t-lg text-[13px] font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition-all -mb-1">
            Central Hub <span class="font-medium opacity-70">(<?= $totalCentral ?>)</span>
        </button>
        <?php endif; ?>

        <?php if ($totalSorting > 0): ?>
        <button onclick="filterType('SORTING_CENTER',this)" data-tab="SORTING_CENTER" class="type-tab px-4 py-2 rounded-t-lg text-[13px] font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition-all -mb-1">
            Sorting Center <span class="font-medium opacity-70">(<?= $totalSorting ?>)</span>
        </button>
        <?php endif; ?>

        <?php if ($totalBranch > 0): ?>
        <button onclick="filterType('BRANCH_HUB',this)" data-tab="BRANCH_HUB" class="type-tab px-4 py-2 rounded-t-lg text-[13px] font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition-all -mb-1">
            Branch Hub <span class="font-medium opacity-70">(<?= $totalBranch ?>)</span>
        </button>
        <?php endif; ?>

    </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <?php foreach ($hubs as $h): 
                $hasPhoto = !empty($h['photo']);
                $photoUrl = $hasPhoto ? url('/' . $h['photo']) : '';
                $initials = strtoupper(substr($h['name'], 0, 2));
                
                $typeColor = 'text-gray-600 dark:text-gray-300';
                $typeBg = 'bg-gray-100 dark:bg-slate-700';
                if ($h['type'] === 'CENTRAL_HUB') { $typeColor = 'text-red-600 dark:text-red-400'; $typeBg = 'bg-red-50 dark:bg-red-900/30'; }
                if ($h['type'] === 'SORTING_CENTER') { $typeColor = 'text-amber-600 dark:text-amber-400'; $typeBg = 'bg-amber-50 dark:bg-amber-900/30'; }
                if ($h['type'] === 'BRANCH_HUB') { $typeColor = 'text-emerald-600 dark:text-emerald-400'; $typeBg = 'bg-emerald-50 dark:bg-emerald-900/30'; }
            ?>
            <div class="card card-hover dark:bg-slate-800 dark:border-slate-700 overflow-hidden flex flex-row relative min-h-[12rem] hub-card" data-type="<?= $h['type'] ?>">
                
                <!-- Kebab Menu -->
                <button class="absolute top-3 right-3 z-10 w-7 h-7 rounded-full bg-white/90 dark:bg-slate-800/90 backdrop-blur shadow flex items-center justify-center text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors" 
                        onclick="toggleDropdown('dd-hub-<?= $h['id'] ?>', event)" title="Opsi">
                    <i class="ri-more-2-fill"></i>
                </button>

                <!-- Dropdown -->
                <div id="dd-hub-<?= $h['id'] ?>" class="uc-dropdown hidden absolute top-11 right-3 z-20 w-36 bg-white dark:bg-slate-700 rounded-xl shadow-lg border border-gray-100 dark:border-slate-600 py-1">
                    <button class="uc-drop-item w-full text-left px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-slate-600 dark:text-gray-200 flex items-center gap-2" 
                            onclick="openEditHubModal(<?= htmlspecialchars(json_encode($h), ENT_QUOTES) ?>); closeAllDropdowns();">
                        <i class="ri-edit-line text-blue-500 dark:text-blue-400"></i> Edit Hub
                    </button>
                    <div class="h-px bg-gray-100 dark:bg-slate-600 my-1"></div>
                    <button type="button" class="uc-drop-item danger w-full text-left px-4 py-2 text-sm hover:bg-red-50 dark:hover:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center gap-2"
                        onclick="openDeleteHubModal(<?= $h['id'] ?>, '<?= e($h['name']) ?>')">
                        <i class="ri-delete-bin-line"></i> Hapus
                    </button>
                </div>

                <!-- Left: Info -->
                <div class="flex-1 p-5 flex flex-col justify-center min-w-0 pr-6">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="font-mono text-xs font-bold text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-slate-700 px-2 py-0.5 rounded"><?= e($h['code']) ?></span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full <?= $typeColor ?> <?= $typeBg ?>"><?= str_replace('_', ' ', $h['type']) ?></span>
                    </div>
                    <h3 class="font-bold text-gray-900 dark:text-white text-lg leading-tight mb-2 truncate" title="<?= e($h['name']) ?>"><?= e($h['name']) ?></h3>
                    <div class="space-y-1 mt-auto">
                        <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1.5 truncate">
                            <i class="ri-map-pin-2-line text-gray-400 dark:text-gray-500"></i> <?= e($h['city']) ?> <?= !empty($h['address']) ? '- ' . e($h['address']) : '' ?>
                        </p>
                        <?php if (!empty($h['phone'])): ?>
                        <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1.5 truncate">
                            <i class="ri-phone-line text-gray-400 dark:text-gray-500"></i> <?= e($h['phone']) ?>
                        </p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Avatar Stack -->
                    <?php if (!empty($hubUsers[$h['id']])): 
                        $hUsers = $hubUsers[$h['id']];
                        $displayUsers = array_slice($hUsers, 0, 4);
                        $rem = count($hUsers) - 4;
                    ?>
                    <div class="flex items-center mt-3 pt-3 border-t border-gray-100 dark:border-slate-700">
                        <div class="flex -space-x-1.5">
                            <?php foreach($displayUsers as $hu): 
                                $ava = !empty($hu['avatar']) ? url('/' . $hu['avatar']) : 'https://placehold.co/100x100/f0f0ee/9a9a90?text='.urlencode(strtoupper(substr($hu['name'], 0, 1)));
                            ?>
                            <img class="w-6 h-6 rounded-full border-2 border-white dark:border-slate-800 bg-gray-100 dark:bg-slate-700 object-cover" src="<?= $ava ?>" title="<?= e($hu['name']) ?> (<?= e($hu['role']) ?>)" alt="<?= e($hu['name']) ?>">
                            <?php endforeach; ?>
                            <?php if($rem > 0): ?>
                            <div class="w-6 h-6 rounded-full border-2 border-white dark:border-slate-800 bg-gray-50 dark:bg-slate-700 text-gray-600 dark:text-gray-400 flex items-center justify-center text-[9px] font-bold z-10 relative">+<?= $rem ?></div>
                            <?php endif; ?>
                        </div>
                        <span class="text-[11px] text-gray-400 dark:text-gray-500 ml-2 font-medium"><?= count($hUsers) ?> Petugas</span>
                    </div>
                    <?php endif; ?>
                    
                </div>

                <!-- Right: Image (Full height) -->
                <div class="w-1/3 sm:w-2/5 flex-shrink-0 bg-gray-100 dark:bg-slate-700/50 relative border-l border-gray-100 dark:border-slate-700 overflow-hidden flex items-center justify-center">
                    <?php if ($hasPhoto): ?>
                        <img src="<?= $photoUrl ?>" class="w-full h-full object-cover absolute inset-0" alt="<?= e($h['name']) ?>">
                    <?php else: ?>
                        <div class="text-[80px] font-black text-gray-200 dark:text-slate-600 tracking-tighter mix-blend-multiply dark:mix-blend-normal leading-none" style="transform: scale(1.5) rotate(-10deg) translateY(10%)">
                            <?= $initials ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- ═══ Modal: Add Hub ═══════════════════════════════════ -->
<div id="modal-add-hub" class="modal-overlay hidden">
    <div class="modal-box max-w-lg">
        <div class="modal-head">
            <h3><i class="ri-building-4-line mr-2" style="color:#40bf4e"></i>Tambah Gudang / Hub</h3>
            <button onclick="document.getElementById('modal-add-hub').classList.add('hidden')" class="close-btn">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <form action="<?= url('/hubs/store') ?>" method="POST" enctype="multipart/form-data">
            <div class="modal-body space-y-4">
                
                <!-- Upload Foto -->
                <div class="flex items-center gap-4 p-4 border border-dashed border-gray-300 rounded-2xl bg-gray-50/50">
                    <div class="w-12 h-12 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-400 shadow-sm">
                        <i class="ri-image-add-line text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Foto Gudang (Opsional)</label>
                        <input type="file" name="photo" accept="image/*" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label">Kode Hub *</label>
                        <input type="text" name="code" required placeholder="HUB-XXX" class="form-input" style="text-transform:uppercase;font-family:monospace">
                    </div>
                    <div>
                        <label class="form-label">Tipe Hub *</label>
                        <select name="type" class="form-input">
                            <option value="CENTRAL_HUB">Central Hub</option>
                            <option value="SORTING_CENTER">Sorting Center</option>
                            <option value="BRANCH_HUB">Branch Hub</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="form-label">Nama Gudang *</label>
                    <input type="text" name="name" required placeholder="Gudang Surabaya Pusat" class="form-input">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label">Kota *</label>
                        <input type="text" name="city" required placeholder="Surabaya" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="phone" placeholder="031-xxxxxxx" class="form-input">
                    </div>
                </div>

                <div>
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea name="address" rows="2" class="form-input" placeholder="Jl. Raya Gudang No 123..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="document.getElementById('modal-add-hub').classList.add('hidden')" class="btn-outline" style="padding:8px 18px;font-size:13px">Batal</button>
                <button type="submit" class="btn-primary" style="padding:8px 20px;font-size:13px">
                    <i class="ri-save-line"></i> Simpan Hub
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ═══ Modal: Edit Hub ══════════════════════════════════ -->
<div id="modal-edit-hub" class="modal-overlay hidden">
    <div class="modal-box max-w-lg">
        <div class="modal-head">
            <h3><i class="ri-edit-line mr-2" style="color:#dd2c24"></i>Edit Gudang / Hub</h3>
            <button onclick="document.getElementById('modal-edit-hub').classList.add('hidden')" class="close-btn">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <form action="<?= url('/hubs/update') ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="hub_id" id="edit-hub-id">
            <input type="hidden" name="delete_photo" id="edit-hub-delete-photo" value="0">
            <div class="modal-body space-y-4">
                
                <!-- Upload Foto -->
                <div class="flex items-center gap-4 p-4 border border-dashed border-gray-300 rounded-2xl bg-gray-50/50 relative">
                    <img id="edit-hub-preview" src="" class="w-12 h-12 rounded-xl object-cover bg-gray-100 border border-gray-200 shadow-sm hidden">
                    <div id="edit-hub-icon" class="w-12 h-12 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-400 shadow-sm">
                        <i class="ri-image-edit-line text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Ganti Foto (Opsional)</label>
                        <div class="flex items-center gap-2">
                            <input type="file" name="photo" id="edit-hub-file" accept="image/*" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
                            <button type="button" id="btn-remove-photo" onclick="removeEditHubPhoto()" class="hidden text-red-500 hover:bg-red-50 p-1.5 rounded-lg text-lg transition-colors" title="Hapus Foto">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label">Kode Hub *</label>
                        <input type="text" name="code" id="edit-hub-code" required class="form-input" style="text-transform:uppercase;font-family:monospace">
                    </div>
                    <div>
                        <label class="form-label">Tipe Hub *</label>
                        <select name="type" id="edit-hub-type" class="form-input">
                            <option value="CENTRAL_HUB">Central Hub</option>
                            <option value="SORTING_CENTER">Sorting Center</option>
                            <option value="BRANCH_HUB">Branch Hub</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="form-label">Nama Gudang *</label>
                    <input type="text" name="name" id="edit-hub-name" required class="form-input">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label">Kota *</label>
                        <input type="text" name="city" id="edit-hub-city" required class="form-input">
                    </div>
                    <div>
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="phone" id="edit-hub-phone" class="form-input">
                    </div>
                </div>

                <div>
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea name="address" id="edit-hub-address" rows="2" class="form-input"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="document.getElementById('modal-edit-hub').classList.add('hidden')" class="btn-outline" style="padding:8px 18px;font-size:13px">Batal</button>
                <button type="submit" class="btn-primary" style="padding:8px 20px;font-size:13px">
                    <i class="ri-save-line"></i> Update Hub
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="form-delete-hub" method="POST" action="<?= url('/hubs/delete') ?>" style="display:none">
    <input type="hidden" name="hub_id" id="delete-hub-id">
</form>

<script>
function closeAllDropdowns() {
    document.querySelectorAll('.uc-dropdown').forEach(d => d.classList.add('hidden'));
}
function toggleDropdown(id, e) {
    e.stopPropagation();
    const target = document.getElementById(id);
    const isHidden = target.classList.contains('hidden');
    closeAllDropdowns();
    if (isHidden) target.classList.remove('hidden');
}
document.addEventListener('click', closeAllDropdowns);

function openEditHubModal(hub) {
    document.getElementById('edit-hub-id').value = hub.id;
    document.getElementById('edit-hub-code').value = hub.code;
    document.getElementById('edit-hub-type').value = hub.type;
    document.getElementById('edit-hub-name').value = hub.name;
    document.getElementById('edit-hub-city').value = hub.city;
    document.getElementById('edit-hub-phone').value = hub.phone ?? '';
    document.getElementById('edit-hub-address').value = hub.address ?? '';
    
    document.getElementById('edit-hub-delete-photo').value = '0';
    document.getElementById('edit-hub-file').value = '';

    if (hub.photo) {
        document.getElementById('edit-hub-preview').src = '<?= url("/") ?>' + hub.photo;
        document.getElementById('edit-hub-preview').classList.remove('hidden');
        document.getElementById('edit-hub-icon').classList.add('hidden');
        document.getElementById('btn-remove-photo').classList.remove('hidden');
    } else {
        document.getElementById('edit-hub-preview').classList.add('hidden');
        document.getElementById('edit-hub-icon').classList.remove('hidden');
        document.getElementById('btn-remove-photo').classList.add('hidden');
    }

    document.getElementById('modal-edit-hub').classList.remove('hidden');
}

function removeEditHubPhoto() {
    document.getElementById('edit-hub-delete-photo').value = '1';
    document.getElementById('edit-hub-preview').classList.add('hidden');
    document.getElementById('edit-hub-icon').classList.remove('hidden');
    document.getElementById('btn-remove-photo').classList.add('hidden');
    document.getElementById('edit-hub-file').value = '';
}

function openDeleteHubModal(id, name) {
    closeAllDropdowns();
    Swal.fire({
        title: 'Hapus Hub?',
        html: `Apakah Anda yakin ingin menghapus gudang <b>${name}</b>?<br><span class="text-sm text-red-500">Data ini tidak dapat dikembalikan!</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dd2c24',
        cancelButtonColor: '#9a9a90',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'rounded-xl font-bold px-5 py-2.5',
            cancelButton: 'rounded-xl font-bold px-5 py-2.5'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-hub-id').value = id;
            document.getElementById('form-delete-hub').submit();
        }
    });
}

// Close modals when clicking outside
document.querySelectorAll('.modal-overlay').forEach(modal => {
    modal.addEventListener('click', function(e) { 
        if(e.target === this) this.classList.add('hidden'); 
    });
});

// Filtering function
function filterType(type, btn) {
    // Update active tab styling
    document.querySelectorAll('.type-tab').forEach(tab => {
        tab.style.color = '#6b6b65';
        tab.style.borderBottomColor = 'transparent';
    });
    btn.style.color = '#1a1a1a';
    btn.style.borderBottomColor = '#2b2c1e';

    // Show/hide cards
    document.querySelectorAll('.hub-card').forEach(card => {
        if (type === 'all' || card.getAttribute('data-type') === type) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}
</script>

<?php renderSidebarFooter(); ?>
