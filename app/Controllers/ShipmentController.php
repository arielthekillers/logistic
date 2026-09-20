<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ShipmentModel;
use App\Models\HubModel;
use App\Models\CheckpointModel;

class ShipmentController extends Controller {

    public function index() {
        require_auth();
        $shipmentModel = new ShipmentModel();
        
        $search = trim($_GET['search'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $startDate = trim($_GET['start_date'] ?? '');
        $endDate = trim($_GET['end_date'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 15;
        
        $result = ['data' => [], 'total' => 0, 'last_page' => 1, 'current_page' => 1];
        
        $errorMsg = null;
        try {
            $result = $shipmentModel->getPaginated($page, $perPage, $search, $status, $startDate, $endDate);
        } catch (\Throwable $e) {
            $errorMsg = $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
        }

        $this->view('shipments/index', [
            'shipments' => $result['data'],
            'total' => $result['total'],
            'last_page' => $result['last_page'],
            'current_page' => $result['current_page'],
            'search' => $search,
            'status' => $status,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
    }
    public function dataTable() {
        require_auth();
        $shipmentModel = new \App\Models\ShipmentModel();

        $draw = (int)($_GET['draw'] ?? 1);
        $start = (int)($_GET['start'] ?? 0);
        $length = (int)($_GET['length'] ?? 10);
        $search = '';
        if (isset($_GET['search']) && is_array($_GET['search'])) {
            $search = trim($_GET['search']['value'] ?? '');
        } elseif (isset($_GET['search']) && is_string($_GET['search'])) {
            $search = trim($_GET['search']);
        }
        $status = trim($_GET['status'] ?? '');
        $startDate = trim($_GET['start_date'] ?? '');
        $endDate = trim($_GET['end_date'] ?? '');

        $page = ($length > 0) ? ($start / $length) + 1 : 1;
        $perPage = $length > 0 ? $length : 10;

        $result = ['data' => [], 'total' => 0];
        $errorMsg = null;
        try {
            $result = $shipmentModel->getPaginated($page, $perPage, $search, $status, $startDate, $endDate);
        } catch (\Throwable $e) {
            $errorMsg = $e->getMessage();
        }

        $data = [];
        foreach ($result['data'] as $item) {
            $weight = (float)($item['weight_kg'] ?? 0);
            $cost = (float)($item['total_cost'] ?? 0);
            $data[] = [
                'id' => $item['id'],
                'resi_number' => e($item['resi_number'] ?? ''),
                'sender' => '<div class="font-bold text-slate-900 dark:text-white">' . e($item['sender_name'] ?? '') . '</div><div class="text-xs text-gray-400 dark:text-gray-500">' . e($item['sender_phone'] ?? '') . '</div>',
                'receiver' => '<div class="font-bold text-slate-900 dark:text-white">' . e($item['receiver_name'] ?? '') . '</div><div class="text-xs text-gray-400 dark:text-gray-500">' . e($item['receiver_phone'] ?? '') . '</div>',
                'hubs' => '<div class="text-xs text-slate-600 dark:text-gray-300"><div><span class="text-gray-400 dark:text-gray-500">Asal:</span> ' . e($item['origin_hub_name'] ?? '-') . '</div><div><span class="text-gray-400 dark:text-gray-500">Tujuan:</span> ' . e($item['destination_hub_name'] ?? '-') . '</div></div>',
                'weight_cost' => '<div class="text-xs"><div class="font-bold text-slate-900 dark:text-white">' . number_format($weight, 2) . ' kg</div><div class="text-emerald-700 dark:text-emerald-400 font-bold">' . format_rp($cost) . '</div></div>',
                'status' => get_status_badge($item['status'] ?? ''),
                'action' => '<div class="text-right space-x-2"><a href="' . url('/shipments/detail?id=' . $item['id']) . '" class="inline-flex items-center text-xs font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 px-3 py-1.5 rounded-lg border border-emerald-200 dark:border-emerald-800">Detail</a> <a href="' . url('/shipments/label?id=' . $item['id']) . '" target="_blank" class="inline-flex items-center text-xs font-bold text-slate-800 dark:text-gray-200 bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 px-3 py-1.5 rounded-lg border border-gray-200 dark:border-slate-600"><i class="ri-printer-line mr-1"></i> Cetak</a></div>'
            ];
        }

        // Clear any previous output (e.g. notices) to avoid breaking JSON
        while (ob_get_level()) { ob_end_clean(); }

        header('Content-Type: application/json');
        $json = json_encode([
            "draw" => $draw,
            "recordsTotal" => $result['total'] ?? 0,
            "recordsFiltered" => $result['total'] ?? 0,
            "data" => $data,
            "error" => $errorMsg
        ], JSON_INVALID_UTF8_SUBSTITUTE);

        if ($json === false) {
            echo json_encode(["error" => "JSON Encoding Error: " . json_last_error_msg()]);
        } else {
            echo $json;
        }
        exit;
    }

    public function create() {
        require_auth();
        $hubModel = new HubModel();
        
        $hubs = [];
        try {
            $hubs = $hubModel->findAll('name', 'ASC');
        } catch (\Throwable $e) {}

        $autoResi = generate_resi_number();

        $this->view('shipments/create', [
            'hubs' => $hubs,
            'autoResi' => $autoResi
        ]);
    }

    public function store() {
        require_auth();

        $resiNumber = trim($_POST['resi_number'] ?? generate_resi_number());
        $senderName = trim($_POST['sender_name'] ?? '');
        $senderPhone = standardize_phone($_POST['sender_phone'] ?? '');
        $senderAddress = trim($_POST['sender_address'] ?? '');
        $receiverName = trim($_POST['receiver_name'] ?? '');
        $receiverPhone = standardize_phone($_POST['receiver_phone'] ?? '');
        $receiverAddress = trim($_POST['receiver_address'] ?? '');
        $originHubId = (int)($_POST['origin_hub_id'] ?? 1);
        $destinationHubId = (int)($_POST['destination_hub_id'] ?? 1);
        
        // Process package items
        $kemasanArr = $_POST['kemasan'] ?? [];
        $koliItemArr = $_POST['koli_item'] ?? [];
        $packageItems = [];
        $totalKoli = 0;
        
        for ($i = 0; $i < count($kemasanArr); $i++) {
            $kem = trim($kemasanArr[$i]);
            $kol = (int)($koliItemArr[$i] ?? 1);
            if (!empty($kem)) {
                $packageItems[] = [
                    'kemasan' => $kem,
                    'koli' => $kol
                ];
                $totalKoli += $kol;
            }
        }
        
        $packageItemsJson = !empty($packageItems) ? json_encode($packageItems) : null;
        if ($totalKoli === 0) $totalKoli = 1;

        $weightKg = (float)($_POST['weight_kg'] ?? 1.0);
        $totalCost = (float)($_POST['total_cost'] ?? 0.0);
        $notes = trim($_POST['notes'] ?? '');

        if (empty($senderName) || empty($receiverName) || empty($senderAddress) || empty($receiverAddress)) {
            $_SESSION['error_flash'] = "Semua data pengirim & penerima wajib diisi.";
            $this->redirect('/shipments/create');
            return;
        }

        $shipmentModel = new ShipmentModel();
        $checkpointModel = new CheckpointModel();

        try {
            $shipmentId = $shipmentModel->create([
                'resi_number' => $resiNumber,
                'sender_name' => $senderName,
                'sender_phone' => $senderPhone,
                'sender_address' => $senderAddress,
                'receiver_name' => $receiverName,
                'receiver_phone' => $receiverPhone,
                'receiver_address' => $receiverAddress,
                'origin_hub_id' => $originHubId,
                'current_hub_id' => $originHubId,
                'destination_hub_id' => $destinationHubId,
                'status' => 'RECEIVED_AT_HUB',
                'koli' => $totalKoli,
                'package_items' => $packageItemsJson,
                'weight_kg' => $weightKg,
                'total_cost' => $totalCost,
                'notes' => $notes,
                'created_by' => auth_user()['id'] ?? 1
            ]);

            // Add initial checkpoint
            $hubModel = new HubModel();
            $originHub = $hubModel->find($originHubId);
            $hubName = $originHub['name'] ?? 'Hub asal';

            $checkpointModel->create([
                'shipment_id' => $shipmentId,
                'hub_id' => $originHubId,
                'user_id' => auth_user()['id'] ?? 1,
                'status' => 'RECEIVED_AT_HUB',
                'location_name' => $hubName,
                'notes' => 'Resi diterbitkan & barang diterima di ' . $hubName
            ]);

            $_SESSION['success_flash'] = "Pengiriman dengan nomor Resi {$resiNumber} berhasil disimpan.";
            $this->redirect('/shipments/detail?id=' . $shipmentId);
        } catch (\Throwable $e) {
            $_SESSION['error_flash'] = "Gagal menyimpan pengiriman: " . $e->getMessage();
            $this->redirect('/shipments/create');
        }
    }

    public function detail() {
        require_auth();
        $id = (int)($_GET['id'] ?? 0);
        $shipmentModel = new ShipmentModel();
        $checkpointModel = new CheckpointModel();

        $shipment = $shipmentModel->find($id);
        if (!$shipment) {
            $this->redirect('/shipments');
            return;
        }

        $fullShipment = $shipmentModel->findByResi($shipment['resi_number']);
        $checkpoints = $checkpointModel->getByShipmentId($id);

        $this->view('shipments/detail', [
            'shipment' => $fullShipment,
            'checkpoints' => $checkpoints
        ]);
    }

    public function printLabel() {
        require_auth();
        $id = (int)($_GET['id'] ?? 0);
        $shipmentModel = new ShipmentModel();

        $shipment = $shipmentModel->find($id);
        if (!$shipment) {
            echo "Shipment not found";
            return;
        }

        $fullShipment = $shipmentModel->findByResi($shipment['resi_number']);

        $this->view('shipments/label', [
            'shipment' => $fullShipment
        ]);
    }
    public function export() {
        require_auth();
        $shipmentModel = new \App\Models\ShipmentModel();
        
        $search = trim($_GET['search'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $startDate = trim($_GET['start_date'] ?? '');
        $endDate = trim($_GET['end_date'] ?? '');
        
        // Fetch up to 10000 rows for export to avoid huge memory spike
        $result = $shipmentModel->getPaginated(1, 10000, $search, $status, $startDate, $endDate);
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=Data_Pengiriman_' . date('Ymd_His') . '.csv');
        $output = fopen('php://output', 'w');
        
        fputcsv($output, ['ID', 'No Resi', 'Pengirim', 'No HP Pengirim', 'Penerima', 'No HP Penerima', 'Hub Asal', 'Hub Tujuan', 'Berat (Kg)', 'Total Biaya', 'Status', 'Tanggal Dibuat']);
        
        foreach ($result['data'] as $row) {
            fputcsv($output, [
                $row['id'],
                $row['resi_number'],
                $row['sender_name'],
                $row['sender_phone'],
                $row['receiver_name'],
                $row['receiver_phone'],
                $row['origin_hub_name'] ?? '-',
                $row['destination_hub_name'] ?? '-',
                $row['weight_kg'],
                $row['total_cost'],
                $row['status'],
                $row['created_at']
            ]);
        }
        fclose($output);
        exit;
    }

    public function bulkDelete() {
        require_auth();
        header('Content-Type: application/json');
        
        $data = json_decode(file_get_contents('php://input'), true);
        $ids = $data['ids'] ?? [];
        
        if (empty($ids) || !is_array($ids)) {
            echo json_encode(['status' => 'error', 'message' => 'Tidak ada data yang dipilih']);
            exit;
        }
        
        $shipmentModel = new \App\Models\ShipmentModel();
        $successCount = 0;
        
        // Delete each item
        foreach ($ids as $id) {
            $id = (int)$id;
            if ($id > 0) {
                $shipmentModel->delete($id);
                $successCount++;
            }
        }
        
        echo json_encode(['status' => 'success', 'message' => "$successCount resi berhasil dihapus"]);
        exit;
    }

    public function importCSV() {
        require_auth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['file']['tmp_name'])) {
            $_SESSION['error_flash'] = "Silakan pilih file CSV terlebih dahulu.";
            $this->redirect('/shipments');
            return;
        }

        $file = $_FILES['file']['tmp_name'];
        $handle = fopen($file, "r");
        if ($handle !== FALSE) {
            $header = fgetcsv($handle, 1000, ",");
            $shipmentModel = new \App\Models\ShipmentModel();
            $success = 0;
            
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // Expected basic CSV: Resi, Sender, SenderPhone, Receiver, ReceiverPhone, OriginHubId, DestHubId, Weight, Cost
                if (count($data) >= 9) {
                    try {
                        $shipmentModel->create([
                            'resi_number' => trim($data[0]),
                            'sender_name' => trim($data[1]),
                            'sender_phone' => trim($data[2]),
                            'receiver_name' => trim($data[3]),
                            'receiver_phone' => trim($data[4]),
                            'origin_hub_id' => (int)$data[5],
                            'current_hub_id' => (int)$data[5],
                            'destination_hub_id' => (int)$data[6],
                            'weight_kg' => (float)$data[7],
                            'total_cost' => (float)$data[8],
                            'status' => 'DRAFT',
                            'created_by' => auth_user()['id'] ?? 1
                        ]);
                        $success++;
                    } catch (\Throwable $e) {} // Skip on duplicate or error
                }
            }
            fclose($handle);
            $_SESSION['success_flash'] = "$success resi berhasil diimpor.";
        } else {
            $_SESSION['error_flash'] = "Gagal membaca file CSV.";
        }
        $this->redirect('/shipments');
    }
}
