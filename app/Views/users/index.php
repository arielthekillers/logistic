<?php
renderSidebarHeader("User & Operator - PT. Barongko Darma Logistik");
?>

<style>
/* ─── User Card ─────────────────────────────────── */
.user-card {
    background: #fff;
    border-radius: 18px;
    border: 1.5px solid #efefed;
    padding: 18px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    position: relative;
    transition: box-shadow 0.22s, border-color 0.22s, transform 0.2s;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
}
.user-card:hover {
    box-shadow: 0 8px 28px rgba(0,0,0,0.08);
    border-color: #d0d0cc;
    transform: translateY(-2px);
}

/* Avatar + Info row */
.user-card-top {
    display: flex;
    align-items: center;
    gap: 14px;
    padding-right: 28px; /* space for kebab btn */
}

.uc-avatar {
    width: 52px; height: 52px;
    border-radius: 50%;
    flex-shrink: 0;
    overflow: hidden;
    display: flex; align-items: center; justify-content: center;
    font-weight: 900;
    font-size: 1.15rem;
    color: #fff;
}
.uc-avatar img { width: 100%; height: 100%; object-fit: cover; }

.uc-info { flex: 1; min-width: 0; }
.uc-name {
    font-size: 14.5px;
    font-weight: 800;
    color: #1a1a1a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    letter-spacing: -0.3px;
    line-height: 1.2;
}
.uc-sub {
    font-size: 12px;
    color: #9a9a90;
    font-weight: 500;
    margin-top: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Tags */
.uc-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}
.uc-tag {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    padding: 3px 10px;
    border-radius: 100px;
    font-size: 11px;
    font-weight: 600;
    border: 1px solid;
}
.tag-admin    { background:#fff5f5; color:#dd2c24; border-color:#fecaca; }
.tag-operator { background:#f0fdf4; color:#16a34a; border-color:#bbf7d0; }
.tag-courier  { background:#fffbeb; color:#b45309; border-color:#fde68a; }
.tag-hub      { background:#f8f8f6; color:#6b6b65; border-color:#e0e0dc; }
.tag-phone    { background:#f8f8f6; color:#6b6b65; border-color:#e0e0dc; }

/* ─── Kebab 3-dot Menu ───────────────────────────── */
.uc-kebab-btn {
    position: absolute;
    top: 14px; right: 14px;
    width: 28px; height: 28px;
    border-radius: 8px;
    border: none;
    background: transparent;
    color: #b0b0a8;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    font-size: 16px;
    transition: background 0.15s, color 0.15s;
    z-index: 10;
}
.uc-kebab-btn:hover { background: #f5f5f0; color: #1a1a1a; }

.uc-dropdown {
    position: absolute;
    top: 44px; right: 10px;
    background: #fff;
    border: 1.5px solid #ebebea;
    border-radius: 14px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.12);
    min-width: 150px;
    z-index: 100;
    overflow: hidden;
    animation: dropIn 0.18s cubic-bezier(0.16,1,0.3,1);
}
@keyframes dropIn {
    from { opacity:0; transform: translateY(-6px) scale(0.97); }
    to   { opacity:1; transform: translateY(0) scale(1); }
}
.uc-dropdown.hidden { display: none; }

.uc-drop-item {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 10px 14px;
    font-size: 13px;
    font-weight: 600;
    color: #3a3a35;
    cursor: pointer;
    border: none;
    background: transparent;
    width: 100%;
    text-align: left;
    transition: background 0.15s;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.uc-drop-item:hover { background: #f5f5f0; }
.uc-drop-item.danger { color: #dd2c24; }
.uc-drop-item.danger:hover { background: #fff5f5; }
.uc-drop-divider { height: 1px; background: #f0f0ee; margin: 2px 0; }

/* Dark Mode Overrides */
.dark .user-card { background: #1e293b; border-color: #334155; }
.dark .user-card:hover { border-color: #475569; }
.dark .uc-name { color: #f8fafc; }
.dark .uc-kebab-btn { color: #94a3b8; }
.dark .uc-kebab-btn:hover { background: #334155; color: #f8fafc; }
.dark .uc-dropdown { background: #334155; border-color: #475569; }
.dark .uc-drop-item { color: #f8fafc; }
.dark .uc-drop-item:hover { background: #475569; }
.dark .uc-drop-item.danger:hover { background: rgba(220, 38, 38, 0.2); }
.dark .uc-drop-divider { background: #475569; }
.dark .tag-hub, .dark .tag-phone { background: #334155; color: #e2e8f0; border-color: #475569; }
.dark .tag-admin { background: rgba(220, 38, 38, 0.1); color: #fca5a5; border-color: rgba(220, 38, 38, 0.3); }
.dark .tag-operator { background: rgba(22, 163, 74, 0.1); color: #86efac; border-color: rgba(22, 163, 74, 0.3); }
.dark .tag-courier { background: rgba(180, 83, 9, 0.1); color: #fcd34d; border-color: rgba(180, 83, 9, 0.3); }
</style>

<div class="space-y-6">

    <!-- ─── Page Header ─────────────────────────────────── -->
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <h1 class="page-title dark:text-white">User & Operator</h1>
            <p class="page-subtitle dark:text-gray-400">Kelola akun Admin, Operator Hub, dan Kurir BDL.</p>
        </div>
        <button onclick="document.getElementById('modal-add-user').classList.remove('hidden')" class="btn-primary w-max">
            <i class="ri-user-add-line"></i> Tambah User
        </button>
    </div>



    <!-- ─── Filter Tabs ───────────────────────────────── -->
    <?php if (!empty($users)):
        $totalAll    = count($users);
        $totalAdmins = count(array_filter($users, fn($u) => $u['role'] === 'admin'));
        $totalOps    = count(array_filter($users, fn($u) => $u['role'] === 'operator_hub'));
        $totalKurir  = count(array_filter($users, fn($u) => $u['role'] === 'courier'));
    ?>
    <div class="flex items-center gap-1 flex-wrap border-b-2 border-gray-100 dark:border-slate-700 pb-0.5" id="role-filter-tabs">

        <button onclick="filterRole('all',this)" data-tab="all" class="role-tab px-4 py-2 rounded-t-lg text-[13px] font-bold border-b-2 border-slate-900 text-slate-900 dark:border-white dark:text-white transition-all -mb-1">
            Semua <span class="font-medium text-gray-500 dark:text-gray-400">(<?= $totalAll ?>)</span>
        </button>

        <?php if ($totalAdmins > 0): ?>
        <button onclick="filterRole('admin',this)" data-tab="admin" class="role-tab px-4 py-2 rounded-t-lg text-[13px] font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition-all -mb-1">
            Admin <span class="font-medium opacity-70">(<?= $totalAdmins ?>)</span>
        </button>
        <?php endif; ?>

        <?php if ($totalOps > 0): ?>
        <button onclick="filterRole('operator_hub',this)" data-tab="operator_hub" class="role-tab px-4 py-2 rounded-t-lg text-[13px] font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition-all -mb-1">
            Operator Hub <span class="font-medium opacity-70">(<?= $totalOps ?>)</span>
        </button>
        <?php endif; ?>

        <?php if ($totalKurir > 0): ?>
        <button onclick="filterRole('courier',this)" data-tab="courier" class="role-tab px-4 py-2 rounded-t-lg text-[13px] font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition-all -mb-1">
            Kurir <span class="font-medium opacity-70">(<?= $totalKurir ?>)</span>
        </button>
        <?php endif; ?>

    </div>
    <?php endif; ?>

    <!-- ─── Cards Grid ───────────────────────────────────── -->
    <?php if (empty($users)): ?>
        <div class="card p-14 text-center dark:bg-slate-800 dark:border-slate-700">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4 bg-gray-50 dark:bg-slate-700">
                <i class="ri-user-unfollow-line text-2xl text-gray-400 dark:text-gray-500"></i>
            </div>
            <p class="font-bold text-gray-600 dark:text-gray-300">Belum ada user terdaftar</p>
            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Klik "Tambah User" untuk menambahkan.</p>
        </div>
    <?php else: ?>
        <?php
        $avatarGrads = [
            'linear-gradient(135deg,#11998e,#38ef7d)',
            'linear-gradient(135deg,#dd2c24,#ff6b35)',
            'linear-gradient(135deg,#40bf4e,#0ea31a)',
            'linear-gradient(135deg,#7c3aed,#3b82f6)',
            'linear-gradient(135deg,#f59e0b,#ef4444)',
            'linear-gradient(135deg,#06b6d4,#6366f1)',
        ];
        ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <?php foreach ($users as $i => $u):
                $avatarUrl = !empty($u['avatar']) ? url('/' . $u['avatar']) : null;
                $avGrad    = $avatarGrads[$i % count($avatarGrads)];
                $roleLabel = 'Operator Hub'; $roleIcon = 'ri-building-4-line'; $roleTag = 'tag-operator';
                if ($u['role'] === 'admin')   { $roleLabel = 'Administrator'; $roleIcon = 'ri-shield-star-line'; $roleTag = 'tag-admin'; }
                if ($u['role'] === 'courier') { $roleLabel = 'Kurir Lapangan'; $roleIcon = 'ri-e-bike-line'; $roleTag = 'tag-courier'; }
            ?>
            <div class="user-card" data-role="<?= $u['role'] ?>">

                <!-- ⋮ Kebab Menu Button -->
                <button class="uc-kebab-btn" onclick="toggleDropdown('dd-<?= $u['id'] ?>', event)" title="Opsi">
                    <i class="ri-more-2-fill"></i>
                </button>

                <!-- Dropdown -->
                <div id="dd-<?= $u['id'] ?>" class="uc-dropdown hidden">
                    <button class="uc-drop-item" onclick="openEditModal(<?= htmlspecialchars(json_encode($u), ENT_QUOTES) ?>); closeAllDropdowns();">
                        <i class="ri-edit-line" style="color:#40bf4e"></i> Edit User
                    </button>
                    <div class="uc-drop-divider"></div>
                    <button type="button" class="uc-drop-item danger"
                        onclick="openDeleteModal(<?= $u['id'] ?>, '<?= e($u['name']) ?>')">
                        <i class="ri-delete-bin-line"></i> Hapus User
                    </button>
                </div>

                <!-- Top: Avatar + Info -->
                <div class="user-card-top">
                    <div class="uc-avatar" style="background:<?= $avGrad ?>">
                        <?php if ($avatarUrl): ?>
                            <img src="<?= $avatarUrl ?>" alt="<?= e($u['name']) ?>">
                        <?php else: ?>
                            <?= strtoupper(substr($u['name'], 0, 1)) ?>
                        <?php endif; ?>
                    </div>
                    <div class="uc-info">
                        <p class="uc-name"><?= e($u['name']) ?></p>
                        <p class="uc-sub">
                            <?php if (!empty($u['hub_name'])): ?>
                                <i class="ri-map-pin-2-line" style="color:#dd2c24;margin-right:2px"></i><?= e($u['hub_name']) ?>
                            <?php else: ?>
                                <i class="ri-global-line" style="color:#9a9a90;margin-right:2px"></i>Semua Hub
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <!-- Tags -->
                <div class="uc-tags">
                    <span class="uc-tag <?= $roleTag ?>">
                        <i class="<?= $roleIcon ?>" style="font-size:10px"></i> <?= $roleLabel ?>
                    </span>
                    <span class="uc-tag tag-hub" style="font-family:monospace">@<?= e($u['username']) ?></span>
                    <?php if (!empty($u['phone'])): ?>
                    <span class="uc-tag tag-phone">
                        <i class="ri-phone-line" style="font-size:10px"></i> <?= e($u['phone']) ?>
                    </span>
                    <?php endif; ?>
                </div>

            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- ═══ Modal: Add User ══════════════════════════════════ -->
<div id="modal-add-user" class="modal-overlay hidden">
    <div class="modal-box">
        <div class="modal-head">
            <h3><i class="ri-user-add-line mr-2" style="color:#40bf4e"></i>Tambah User Baru</h3>
            <button onclick="document.getElementById('modal-add-user').classList.add('hidden')" class="close-btn">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <form action="<?= url('/users/store') ?>" method="POST">
            <div class="modal-body space-y-4">
                <div>
                    <label class="form-label">Nama Lengkap *</label>
                    <input type="text" name="name" required placeholder="Nama lengkap user" class="form-input">
                </div>
                <div>
                    <label class="form-label">Username *</label>
                    <input type="text" name="username" required placeholder="tanpa spasi" class="form-input" style="font-family:monospace">
                </div>
                <div>
                    <label class="form-label">Password *</label>
                    <input type="password" name="password" required placeholder="Minimal 6 karakter" class="form-input">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label">Role *</label>
                        <select name="role" class="form-input">
                            <option value="operator_hub">Operator Hub</option>
                            <option value="courier">Kurir Lapangan</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Hub / Gudang</label>
                        <select name="hub_id" class="form-input">
                            <option value="">-- Global --</option>
                            <?php foreach ($hubs as $h): ?>
                                <option value="<?= $h['id'] ?>"><?= e($h['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="document.getElementById('modal-add-user').classList.add('hidden')" class="btn-outline" style="padding:8px 18px;font-size:13px">Batal</button>
                <button type="submit" class="btn-green" style="padding:8px 20px;font-size:13px">
                    <i class="ri-save-line"></i> Simpan User
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ═══ Modal: Edit User ════════════════════════════════ -->
<div id="modal-edit-user" class="modal-overlay hidden">
    <div class="modal-box">
        <div class="modal-head">
            <h3><i class="ri-edit-line mr-2" style="color:#dd2c24"></i>Edit User</h3>
            <button onclick="document.getElementById('modal-edit-user').classList.add('hidden')" class="close-btn">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <form id="form-edit-user" action="<?= url('/users/update') ?>" method="POST">
            <input type="hidden" name="user_id" id="edit-user-id">
            <div class="modal-body space-y-4">
                <div>
                    <label class="form-label">Nama Lengkap *</label>
                    <input type="text" name="name" id="edit-name" required class="form-input">
                </div>
                <div>
                    <label class="form-label">Username (Read-Only)</label>
                    <input type="text" id="edit-username-display" readonly class="form-input" style="font-family:monospace">
                </div>
                <div>
                    <label class="form-label">Password Baru <span class="normal-case font-normal text-gray-400">(kosongkan jika tidak berubah)</span></label>
                    <input type="password" name="new_password" placeholder="••••••••" class="form-input">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label">Role *</label>
                        <select name="role" id="edit-role" class="form-input">
                            <option value="operator_hub">Operator Hub</option>
                            <option value="courier">Kurir Lapangan</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Hub / Gudang</label>
                        <select name="hub_id" id="edit-hub-id" class="form-input">
                            <option value="">-- Global --</option>
                            <?php foreach ($hubs as $h): ?>
                                <option value="<?= $h['id'] ?>"><?= e($h['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="form-label">No. HP / WhatsApp</label>
                    <input type="text" name="phone" id="edit-phone" placeholder="08xxxxxxxxxx" class="form-input">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="document.getElementById('modal-edit-user').classList.add('hidden')" class="btn-outline" style="padding:8px 18px;font-size:13px">Batal</button>
                <button type="submit" class="btn-accent" style="padding:8px 20px;font-size:13px">
                    <i class="ri-save-line"></i> Update User
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Hidden delete form (submitted by SweetAlert confirm) -->
<form id="form-delete-user" method="POST" action="<?= url('/users/delete') ?>" style="display:none">
    <input type="hidden" name="user_id" id="delete-user-id">
</form>


<script>
// ─── Edit Modal ───────────────────────────────
function openEditModal(user) {
    document.getElementById('edit-user-id').value = user.id ?? '';
    document.getElementById('edit-name').value = user.name ?? '';
    document.getElementById('edit-username-display').value = user.username ?? '';
    document.getElementById('edit-phone').value = user.phone ?? '';
    document.getElementById('edit-role').value = user.role ?? 'operator_hub';
    document.getElementById('edit-hub-id').value = user.hub_id ?? '';
    document.getElementById('modal-edit-user').classList.remove('hidden');
}
document.getElementById('modal-add-user').addEventListener('click', function(e) { if(e.target===this) this.classList.add('hidden'); });
document.getElementById('modal-edit-user').addEventListener('click', function(e) { if(e.target===this) this.classList.add('hidden'); });

// ─── Kebab Dropdown ───────────────────────────
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

// ─── Role Filter Tabs ─────────────────────────
function filterRole(role, btn) {
    // Update tab styles
    document.querySelectorAll('.role-tab').forEach(t => {
        t.classList.remove('border-slate-900', 'text-slate-900', 'dark:border-white', 'dark:text-white');
        t.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
    });
    btn.classList.add('border-slate-900', 'text-slate-900', 'dark:border-white', 'dark:text-white');
    btn.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');

    // Filter cards
    document.querySelectorAll('.user-card').forEach(card => {
        if (role === 'all' || card.dataset.role === role) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

// Set default active tab style on load
document.addEventListener('DOMContentLoaded', () => {
    // Done via server-side/HTML
});

// ─── Delete Confirm — SweetAlert2 ────────────
function openDeleteModal(userId, userName) {
    closeAllDropdowns();
    Swal.fire({
        title: 'Hapus User?',
        html: `User <strong>${userName}</strong> akan dihapus secara permanen dan tidak dapat dikembalikan.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dd2c24',
        cancelButtonColor: '#f0f0ee',
        customClass: {
            cancelButton: 'swal2-cancel-bdl',
            popup: 'swal2-bdl-popup',
            title: 'swal2-bdl-title',
        },
        reverseButtons: true,
        focusCancel: true,
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-user-id').value = userId;
            document.getElementById('form-delete-user').submit();
        }
    });
}
</script>

<?php renderSidebarFooter(); ?>
