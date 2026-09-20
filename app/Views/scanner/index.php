<?php
renderSidebarHeader("Scan Barcode / QR Kamera - PT. Barongko Darma Logistik");
$user = auth_user();
$userHubId = $user['hub_id'] ?? null;
?>

<div class="space-y-6 max-w-6xl mx-auto">
    <!-- ─── Page Header ─────────────────────────────────── -->
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <h1 class="page-title">Scanner Operasional</h1>
            <p class="page-subtitle">Pindai barcode resi pengiriman untuk memperbarui status *checkpoint* secara instan.</p>
        </div>
    </div>

    <!-- ─── Centered Layout ────────────────────────── -->
    <div class="max-w-3xl mx-auto space-y-6">

    <!-- ─── Hub Selector (Global) ────────────────────────── -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h3 class="font-bold text-gray-900 text-sm">Lokasi Saat Ini</h3>
            <p class="text-xs text-gray-500">Pilih gudang/cabang tempat Anda memindai resi.</p>
        </div>
        <select id="scan-hub" class="form-input min-w-[250px] !py-2 !text-sm">
            <?php foreach ($hubs as $h): ?>
                <?php $isSelected = ($userHubId && $h['id'] == $userHubId) ? 'selected' : ''; ?>
                <option value="<?= $h['id'] ?>" <?= $isSelected ?>><?= e($h['name']) ?> (<?= e($h['city']) ?>)</option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- ─── App-Style Icon Grid ────────────────────────── -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 sm:gap-6">
        
        <!-- RECEIVED_AT_HUB -->
        <button type="button" onclick="openScannerModal('RECEIVED_AT_HUB', 'Scan Masuk Hub')" class="group flex flex-col items-center gap-3 transition-transform hover:scale-105 active:scale-95 focus:outline-none">
            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-[1.5rem] bg-gradient-to-br from-blue-400 to-blue-600 shadow-lg shadow-blue-500/30 flex items-center justify-center text-white relative overflow-hidden">
                <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <i class="ri-inbox-archive-line text-4xl sm:text-5xl"></i>
            </div>
            <span class="text-sm font-bold text-gray-700 text-center leading-tight">Tiba di<br>Hub</span>
        </button>

        <!-- SORTED -->
        <button type="button" onclick="openScannerModal('SORTED', 'Scan Sortir')" class="group flex flex-col items-center gap-3 transition-transform hover:scale-105 active:scale-95 focus:outline-none">
            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-[1.5rem] bg-gradient-to-br from-purple-400 to-purple-600 shadow-lg shadow-purple-500/30 flex items-center justify-center text-white relative overflow-hidden">
                <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <i class="ri-folders-line text-4xl sm:text-5xl"></i>
            </div>
            <span class="text-sm font-bold text-gray-700 text-center leading-tight">Proses<br>Sortir</span>
        </button>

        <!-- IN_TRANSIT -->
        <button type="button" onclick="openScannerModal('IN_TRANSIT', 'Scan Transit')" class="group flex flex-col items-center gap-3 transition-transform hover:scale-105 active:scale-95 focus:outline-none">
            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-[1.5rem] bg-gradient-to-br from-amber-400 to-amber-600 shadow-lg shadow-amber-500/30 flex items-center justify-center text-white relative overflow-hidden">
                <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <i class="ri-truck-line text-4xl sm:text-5xl"></i>
            </div>
            <span class="text-sm font-bold text-gray-700 text-center leading-tight">Dalam<br>Transit</span>
        </button>

        <!-- OUT_FOR_DELIVERY -->
        <button type="button" onclick="openScannerModal('OUT_FOR_DELIVERY', 'Scan Pengantaran')" class="group flex flex-col items-center gap-3 transition-transform hover:scale-105 active:scale-95 focus:outline-none">
            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-[1.5rem] bg-gradient-to-br from-indigo-400 to-indigo-600 shadow-lg shadow-indigo-500/30 flex items-center justify-center text-white relative overflow-hidden">
                <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <i class="ri-e-bike-2-line text-4xl sm:text-5xl"></i>
            </div>
            <span class="text-sm font-bold text-gray-700 text-center leading-tight">Dibawa<br>Kurir</span>
        </button>

        <!-- DELIVERED -->
        <button type="button" onclick="openScannerModal('DELIVERED', 'Scan Paket Diterima')" class="group flex flex-col items-center gap-3 transition-transform hover:scale-105 active:scale-95 focus:outline-none">
            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-[1.5rem] bg-gradient-to-br from-emerald-400 to-emerald-600 shadow-lg shadow-emerald-500/30 flex items-center justify-center text-white relative overflow-hidden">
                <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <i class="ri-checkbox-circle-line text-4xl sm:text-5xl"></i>
            </div>
            <span class="text-sm font-bold text-gray-700 text-center leading-tight">Paket<br>Diterima</span>
        </button>

        <?php if (has_role('admin') || has_role('operator_hub')): ?>
        <!-- PROBLEM -->
        <button type="button" onclick="openScannerModal('PROBLEM', 'Scan Paket Bermasalah')" class="group flex flex-col items-center gap-3 transition-transform hover:scale-105 active:scale-95 focus:outline-none">
            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-[1.5rem] bg-gradient-to-br from-rose-400 to-rose-600 shadow-lg shadow-rose-500/30 flex items-center justify-center text-white relative overflow-hidden">
                <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <i class="ri-alarm-warning-line text-4xl sm:text-5xl"></i>
            </div>
            <span class="text-sm font-bold text-gray-700 text-center leading-tight">Paket<br>Bermasalah</span>
        </button>
        <?php endif; ?>

    </div>
</div>

<!-- QR Scanner Modal -->
<div id="scanner-modal" class="fixed inset-0 z-[100] hidden">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
    
    <!-- Modal Content -->
    <div class="absolute inset-0 sm:inset-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 w-full sm:w-[500px] h-full sm:h-[650px] bg-white sm:rounded-3xl shadow-2xl flex flex-col overflow-hidden ring-1 ring-gray-900/5">
        
        <div id="modal-header" class="px-6 py-5 flex items-start justify-between bg-white z-10 relative transition-colors duration-300">
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-1" id="modal-title">Scan Barcode / QR</h3>
                <p class="text-sm text-gray-500 flex items-center gap-1" id="modal-hub-container">
                    <i class="ri-map-pin-line opacity-70"></i>
                    <span id="modal-hub-info">Loading lokasi...</span>
                </p>
            </div>
            <button id="modal-close-btn" onclick="closeScannerModal()" class="w-9 h-9 flex items-center justify-center rounded-full bg-gray-50 text-gray-500 hover:bg-gray-100 hover:text-gray-900 transition-colors shadow-sm border border-gray-100">
                <i class="ri-close-line text-lg"></i>
            </button>
        </div>

        <!-- Camera Area -->
        <div class="flex-1 bg-black relative" id="camera-wrapper">
            <div id="reader" class="w-full h-full object-cover flex-1"></div>
        </div>

        <!-- Manual Input Area (Unified inside modal) -->
        <div class="px-6 py-6 bg-white border-t border-gray-100 z-10 relative text-center">
            <div class="flex items-center justify-center gap-3 mb-4">
                <div class="h-px bg-gray-200 flex-1"></div>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Atau Ketik Resi Manual</p>
                <div class="h-px bg-gray-200 flex-1"></div>
            </div>
            <form onsubmit="event.preventDefault();" class="flex gap-2 relative justify-center">
                <input type="text" id="manual-resi" placeholder="SJ-2026..." class="form-input flex-1 text-center font-mono uppercase tracking-widest text-base shadow-sm !py-3" autocomplete="off" oninput="handleScannerInput(this.value)" onblur="setTimeout(()=>document.getElementById('autocomplete-dropdown').classList.add('hidden'), 200)">
                
                <!-- Custom Autocomplete Dropdown -->
                <div id="autocomplete-dropdown" class="hidden absolute bottom-full left-0 mb-2 w-full bg-white border border-gray-200 rounded-xl shadow-xl z-50 max-h-48 overflow-y-auto text-left">
                </div>
            </form>
            <input type="file" id="photo-proof-input" accept="image/*" capture="environment" class="hidden">
        </div>
    </div>
</div>



<style>
    /* Clean up the HTML5 QrCode default styles */
    #reader { border: none !important; width: 100% !important; height: 100% !important; display: flex; flex-direction: column; margin: 0; padding: 0; }
    #reader > div { margin: 0 !important; padding: 0 !important; border: none !important; }
    #reader video { object-fit: cover !important; border-radius: 0 !important; width: 100% !important; height: 100% !important; flex: 1; margin: 0 !important; padding: 0 !important; }
    #reader__dashboard_section_csr span { color: white !important; font-family: 'Plus Jakarta Sans', sans-serif !important; }
    #reader__dashboard_section_swaplink { display: none !important; }
</style>

<script>
    let html5QrcodeScanner = null;
    let selectedStatus = null; // Store the clicked icon's status

    const statusColors = {
        'RECEIVED_AT_HUB': 'bg-blue-600',
        'SORTED': 'bg-purple-600',
        'IN_TRANSIT': 'bg-amber-600',
        'OUT_FOR_DELIVERY': 'bg-indigo-600',
        'DELIVERED': 'bg-emerald-600',
        'PROBLEM': 'bg-rose-600'
    };

    // Move modal to body to prevent z-index and fixed positioning issues from parent containers
    document.addEventListener("DOMContentLoaded", () => {
        const modal = document.getElementById('scanner-modal');
        if (modal) document.body.appendChild(modal);
    });

    function openScannerModal(status, title) {
        selectedStatus = status;
        document.getElementById('modal-title').innerText = title;
        
        // Update modal color theme
        const header = document.getElementById('modal-header');
        const titleEl = document.getElementById('modal-title');
        const hubContainer = document.getElementById('modal-hub-container');
        const closeBtn = document.getElementById('modal-close-btn');
        
        header.className = 'px-6 py-5 flex items-start justify-between z-10 relative transition-colors duration-300 ' + (statusColors[status] || 'bg-slate-800');
        titleEl.className = 'text-lg font-bold text-white mb-1';
        hubContainer.className = 'text-sm text-white/80 flex items-center gap-1';
        closeBtn.className = 'w-9 h-9 flex items-center justify-center rounded-full bg-white/20 text-white hover:bg-white/30 transition-colors border border-white/10';

        // Update Hub Info
        const hubSelect = document.getElementById('scan-hub');
        const selectedHubName = hubSelect.options[hubSelect.selectedIndex]?.text || 'Gudang tidak diketahui';
        document.getElementById('modal-hub-info').innerText = selectedHubName;

        document.getElementById('scanner-modal').classList.remove('hidden');
        document.getElementById('manual-resi').value = ''; // clear input
        
        startScanner();
    }

    function closeScannerModal() {
        document.getElementById('scanner-modal').classList.add('hidden');
        stopScanner();
    }

    function startScanner() {
        <?php $barcodeType = get_setting('barcode_type', '1d'); ?>
        const barcodeType = '<?= e($barcodeType) ?>';
        
        let boxWidth, boxHeight;
        if (barcodeType === 'qr') {
            boxWidth = 250;
            boxHeight = 250;
        } else {
            boxWidth = 300;
            boxHeight = 120;
        }

        html5QrcodeScanner = new Html5Qrcode("reader");
        html5QrcodeScanner.start(
            { facingMode: "environment" },
            { 
                fps: 10, 
                qrbox: function(vw, vh) { 
                    return { 
                        width: Math.min(vw * 0.9, boxWidth), 
                        height: Math.min(vh * 0.9, boxHeight) 
                    }; 
                } 
            },
            (decodedText) => {
                // Kamera berhasil scan, tapi modal tidak tertutup
                processScan(decodedText);
            },
            (errorMessage) => {
                // Ignore
            }
        ).catch(err => {
            SwalToast.fire({
                icon: 'error',
                title: 'Gagal membuka kamera: ' + err
            });
            closeScannerModal();
        });
    }

    function stopScanner() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.stop().then((ignore) => {
                html5QrcodeScanner.clear();
                html5QrcodeScanner = null;
            }).catch((err) => {
                console.log(err);
            });
        }
    }

    let lastScannedResi = "";
    let lastScannedStatus = "";
    let scanTimeout = null;

    function handleScannerInput(val) {
        // Panggil autocomplete jika dirasa perlu
        fetchAutocomplete(val);

        // Auto submit jika ada jeda ketikan (alat scanner biasanya mengirim input dengan sangat cepat)
        clearTimeout(scanTimeout);
        if (val.trim().length >= 5) { // minimal karakter untuk auto-scan
            scanTimeout = setTimeout(() => {
                processScan(val);
            }, 300); // 300ms debounce
        }
    }

    function processScan(scannedText) {
        if (!scannedText || scannedText.trim() === '') return;
        
        const inputField = document.getElementById('manual-resi');
        const hubId = document.getElementById('scan-hub').value;
        const status = selectedStatus; 
        const notes = '';

        if (!hubId) {
            SwalToast.fire({ icon: 'warning', title: 'Pilih lokasi hub terlebih dahulu!' });
            return;
        }

        if (!status) {
            SwalToast.fire({ icon: 'warning', title: 'Pilih status operasional (klik ikon) terlebih dahulu!' });
            return;
        }

        const currentResi = scannedText.trim();
        const currentStatus = status;

        // KOSONGKAN INPUT SEGERA - agar scan berikutnya dari alat fisik tidak menumpuk teks
        inputField.value = '';

        // Ignore if scanning the exact same resi with the exact same status consecutively
        if (currentResi === lastScannedResi && currentStatus === lastScannedStatus) {
            return;
        }

        lastScannedResi = currentResi;
        lastScannedStatus = currentStatus;

        const formData = new FormData();
        formData.append('resi_number', currentResi);
        formData.append('status', currentStatus);
        formData.append('hub_id', hubId);

        // Jika paket DITERIMA atau BERMASALAH, minta foto bukti
        if (currentStatus === 'DELIVERED' || currentStatus === 'PROBLEM') {
            const photoInput = document.getElementById('photo-proof-input');
            photoInput.onchange = function(e) {
                const file = e.target.files[0];
                if (!file) {
                    SwalToast.fire({ icon: 'warning', title: 'Bukti foto wajib dilampirkan!' });
                    lastScannedResi = "";
                    return;
                }
                
                // Client-side Compression with Canvas
                const reader = new FileReader();
                reader.onload = function(event) {
                    const img = new Image();
                    img.onload = function() {
                        const canvas = document.createElement('canvas');
                        const MAX_WIDTH = 800;
                        const MAX_HEIGHT = 800;
                        let width = img.width;
                        let height = img.height;

                        if (width > height) {
                            if (width > MAX_WIDTH) {
                                height *= MAX_WIDTH / width;
                                width = MAX_WIDTH;
                            }
                        } else {
                            if (height > MAX_HEIGHT) {
                                width *= MAX_HEIGHT / height;
                                height = MAX_HEIGHT;
                            }
                        }
                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);
                        const dataUrl = canvas.toDataURL('image/jpeg', 0.6); // 60% quality
                        
                        formData.append('photo_proof', dataUrl);
                        submitScan(formData, currentResi, currentStatus, hubId, inputField);
                    };
                    img.src = event.target.result;
                };
                reader.readAsDataURL(file);
                photoInput.value = '';
            };
            
            // Buka kamera
            photoInput.click();
            return;
        }

        submitScan(formData, currentResi, currentStatus, hubId, inputField);
    }

    function submitScan(formData, currentResi, currentStatus, hubId, inputField) {
        // Check if offline
        if (!navigator.onLine) {
            saveToOfflineQueue(currentResi, currentStatus, hubId, formData.get('photo_proof') || null);
            playBeep();
            SwalToast.fire({
                icon: 'info',
                title: 'Offline: Disimpan di antrean perangkat.'
            });
            inputField.focus();
            setTimeout(() => {
                if (lastScannedResi === currentResi) lastScannedResi = "";
            }, 3000);
            return;
        }

        fetch('<?= url("/scanner/process") ?>', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                playBeep();
                SwalToast.fire({ icon: 'success', title: data.message });
            } else {
                playErrorBeep();
                SwalToast.fire({ icon: 'error', title: data.message });
            }
        })
        .catch(err => {
            playErrorBeep();
            SwalToast.fire({ icon: 'error', title: 'Koneksi ke server gagal.' });
        })
        .finally(() => {
            inputField.focus();
            setTimeout(() => {
                if (lastScannedResi === currentResi) lastScannedResi = "";
            }, 3000);
        });
    }

    // Auto-focus on page load & Setup Offline Sync
    window.onload = () => {
        document.getElementById('manual-resi').focus();
        window.addEventListener('online', syncOfflineQueue);
        if (navigator.onLine) syncOfflineQueue();
    };

    // Offline Queue System
    function saveToOfflineQueue(resi, status, hubId, photoBase64) {
        let queue = JSON.parse(localStorage.getItem('bdl_scan_queue') || '[]');
        queue.push({ resi, status, hubId, photoBase64, time: new Date().getTime() });
        localStorage.setItem('bdl_scan_queue', JSON.stringify(queue));
        updateQueueIndicator();
    }

    function updateQueueIndicator() {
        let queue = JSON.parse(localStorage.getItem('bdl_scan_queue') || '[]');
        let indicator = document.getElementById('offline-indicator');
        
        if (queue.length > 0) {
            if (!indicator) {
                indicator = document.createElement('div');
                indicator.id = 'offline-indicator';
                indicator.className = 'fixed bottom-4 left-1/2 -translate-x-1/2 bg-amber-500 text-white px-4 py-2 rounded-full text-xs font-bold shadow-lg flex items-center gap-2 z-50';
                document.body.appendChild(indicator);
            }
            indicator.innerHTML = `<i class="ri-wifi-off-line"></i> ${queue.length} scan offline menunggu sinkronisasi`;
        } else if (indicator) {
            indicator.remove();
        }
    }

    function syncOfflineQueue() {
        let queue = JSON.parse(localStorage.getItem('bdl_scan_queue') || '[]');
        if (queue.length === 0) return;

        let syncToast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
        syncToast.fire({ icon: 'info', title: `Mensinkronisasi ${queue.length} scan tertunda...` });

        Promise.all(queue.map(item => {
            const fd = new FormData();
            fd.append('resi_number', item.resi);
            fd.append('status', item.status);
            fd.append('hub_id', item.hubId);
            if (item.photoBase64) fd.append('photo_proof', item.photoBase64);
            return fetch('<?= url("/scanner/process") ?>', { method: 'POST', body: fd }).catch(e => null);
        })).then(() => {
            localStorage.removeItem('bdl_scan_queue');
            updateQueueIndicator();
            syncToast.fire({ icon: 'success', title: 'Sinkronisasi offline berhasil!' });
        });
    }

    document.addEventListener("DOMContentLoaded", () => {
        updateQueueIndicator();
    });
    
    // Auto-focus on click anywhere (unless clicking a button/select)
    document.addEventListener('click', (e) => {
        if(e.target.tagName !== 'BUTTON' && e.target.tagName !== 'SELECT' && e.target.tagName !== 'A') {
            document.getElementById('manual-resi').focus();
        }
    });

    // Handle Enter key for manual scanner gun input
    document.getElementById('manual-resi').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(scanTimeout);
            processScan(this.value);
        }
    });
    // Audio API for Scanner Beep (Sukses)
    function playBeep() {
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;
            const ctx = new AudioContext();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.type = 'sine';
            osc.frequency.setValueAtTime(1000, ctx.currentTime); // 1000Hz beep (tinggi)
            gain.gain.setValueAtTime(0.5, ctx.currentTime); // volume
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.1);
            osc.start();
            osc.stop(ctx.currentTime + 0.1);
        } catch (e) {}
    }

    // Audio API for Scanner Error (Gagal)
    function playErrorBeep() {
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;
            const ctx = new AudioContext();
            
            const playLow = (startTime) => {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(250, startTime); // 250Hz beep (kasar & rendah)
                gain.gain.setValueAtTime(0.5, startTime);
                gain.gain.exponentialRampToValueAtTime(0.01, startTime + 0.15);
                osc.start(startTime);
                osc.stop(startTime + 0.15);
            };

            // Bunyi ganda untuk penanda error
            playLow(ctx.currentTime);
            playLow(ctx.currentTime + 0.2);
        } catch (e) {}
    }
    let autocompleteTimeout = null;
    function fetchAutocomplete(val) {
        const dropdown = document.getElementById('autocomplete-dropdown');
        if (val.length < 3) {
            dropdown.classList.add('hidden');
            return;
        }
        
        clearTimeout(autocompleteTimeout);
        autocompleteTimeout = setTimeout(() => {
            fetch('<?= url('/scanner/autocomplete') ?>?q=' + encodeURIComponent(val))
            .then(res => res.json())
            .then(data => {
                dropdown.innerHTML = '';
                if (data.suggestions && data.suggestions.length > 0) {
                    data.suggestions.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'px-4 py-3 border-b border-gray-50 hover:bg-emerald-50 cursor-pointer transition-colors';
                        div.innerHTML = `
                            <div class="font-mono text-sm font-bold text-slate-800">${item.resi_number}</div>
                            <div class="text-xs text-gray-500 mt-0.5 flex items-center gap-1">
                                <span><i class="ri-user-location-line"></i> ${item.sender_name}</span>
                                <i class="ri-arrow-right-line text-emerald-500 mx-1"></i>
                                <span><i class="ri-map-pin-user-line"></i> ${item.receiver_name}</span>
                            </div>
                        `;
                        div.onclick = function() {
                            document.getElementById('manual-resi').value = item.resi_number;
                            dropdown.classList.add('hidden');
                            processScan(item.resi_number); // Auto submit saat klik autocomplete
                        };
                        dropdown.appendChild(div);
                    });
                    dropdown.classList.remove('hidden');
                } else {
                    dropdown.classList.add('hidden');
                }
            })
            .catch(err => console.error(err));
        }, 300);
    }
</script>

<?php renderSidebarFooter(); ?>
