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
        
        try {
            $result = $shipmentModel->getPaginated($page, $perPage, $search, $status, $startDate, $endDate);
        } catch (\Throwable $e) {}

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
}
