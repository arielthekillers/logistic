<?php
renderSidebarHeader("Pengaturan Profil - PT. Barongko Darma Logistik");
$avatarUrl = !empty($user['avatar']) ? url('/' . $user['avatar']) : null;
?>

<div class="space-y-6 max-w-4xl">

    <!-- ─── Page Header ─────────────────────────────────── -->
    <div>
        <h1 class="page-title">Profil Saya</h1>
        <p class="page-subtitle">Kelola foto, data diri, dan keamanan akun Anda.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- ─── Left: Avatar Card ────────────────────────── -->
        <div class="card p-7 flex flex-col items-center text-center space-y-4">
            <!-- Avatar -->
            <div id="avatar-preview-box"
                 class="w-28 h-28 rounded-full overflow-hidden shadow-lg flex items-center justify-center text-white text-4xl font-black"
                 style="background:linear-gradient(135deg,#40bf4e,#2b2c1e)">
                <?php if ($avatarUrl): ?>
                    <img id="avatar-preview-img" src="<?= $avatarUrl ?>" alt="Avatar" class="w-full h-full object-cover">
                <?php else: ?>
                    <span id="avatar-initials"><?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?></span>
                    <img id="avatar-preview-img" src="" alt="" class="w-full h-full object-cover hidden">
                <?php endif; ?>
            </div>

            <!-- Info -->
            <div>
                <h2 class="font-extrabold text-gray-900 text-lg"><?= e($user['name']) ?></h2>
                <span class="badge mt-1 pill-<?= $user['role'] === 'admin' ? 'admin' : ($user['role'] === 'courier' ? 'courier' : 'operator') ?>"
                      style="font-size:11.5px;padding:4px 12px">
                    <?= e(ucwords(str_replace('_', ' ', $user['role']))) ?>
                </span>
                <p class="text-xs text-gray-400 mt-2">
                    <i class="ri-building-4-line mr-0.5"></i> <?= e($user['hub_name'] ?? 'Semua Hub') ?>
                </p>
            </div>

            <div class="w-full border-t border-gray-50 pt-4 text-xs text-gray-400">
                Bergabung: <?= format_datetime($user['created_at']) ?>
            </div>
        </div>

        <!-- ─── Right: Forms ─────────────────────────────── -->
        <div class="lg:col-span-2 space-y-5">

            <!-- Edit Profile Form -->
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50 flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-sm" style="background:#f0fdf4;color:#40bf4e">
                        <i class="ri-user-settings-line"></i>
                    </div>
                    <h3 class="font-bold text-gray-900" style="font-size:14.5px">Edit Data Diri & Avatar</h3>
                </div>
                <form action="<?= url('/profile/update') ?>" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                    <div>
                        <label class="form-label">Ganti Foto Avatar</label>
                        <input type="file" name="avatar" id="avatar-input"
                               accept="image/jpeg,image/png,image/webp"
                               onchange="previewAvatar(this)"
                               class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
                        <p class="text-xs text-gray-400 mt-1.5">JPG, PNG, atau WEBP — maks. 3MB.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Nama Lengkap *</label>
                            <input type="text" name="name" value="<?= e($user['name']) ?>" required class="form-input">
                        </div>
                        <div>
                            <label class="form-label">No. HP / WhatsApp</label>
                            <input type="text" name="phone" value="<?= e($user['phone'] ?? '') ?>" placeholder="08xxxxxxxxxx" class="form-input">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-xl" style="background:#f8f8f6">
                        <div>
                            <label class="form-label">Username</label>
                            <input type="text" value="<?= e($user['username']) ?>" readonly class="form-input" style="font-family:monospace">
                        </div>
                        <div>
                            <label class="form-label">Hub / Gudang</label>
                            <input type="text" value="<?= e($user['hub_name'] ?? 'Global') ?>" readonly class="form-input">
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="btn-green">
                            <i class="ri-save-line"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password Form -->
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50 flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-sm" style="background:#fff5f5;color:#dd2c24">
                        <i class="ri-lock-password-line"></i>
                    </div>
                    <h3 class="font-bold text-gray-900" style="font-size:14.5px">Ubah Password</h3>
                </div>
                <form action="<?= url('/profile/change-password') ?>" method="POST" class="p-6 space-y-4">
                    <div>
                        <label class="form-label">Password Saat Ini *</label>
                        <input type="password" name="old_password" required placeholder="••••••••" class="form-input">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Password Baru *</label>
                            <input type="password" name="new_password" required placeholder="Min. 6 karakter" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Konfirmasi Password *</label>
                            <input type="password" name="confirm_password" required placeholder="Ulangi password" class="form-input">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="btn-accent">
                            <i class="ri-key-line"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<style>
.pill-admin    { background:#fff5f5;color:#dd2c24;border:1px solid #fecaca; }
.pill-operator { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
.pill-courier  { background:#fffbeb;color:#b45309;border:1px solid #fde68a; }
</style>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('avatar-preview-img');
            const initials = document.getElementById('avatar-initials');
            img.src = e.target.result;
            img.classList.remove('hidden');
            if (initials) initials.classList.add('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php renderSidebarFooter(); ?>
