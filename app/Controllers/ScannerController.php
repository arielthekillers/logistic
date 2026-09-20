<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ShipmentModel;
use App\Models\CheckpointModel;
use App\Models\HubModel;

class ScannerController extends Controller {

    public function index() {
        require_auth();
        
        $hubModel = new HubModel();
        $hubs = $hubModel->findAll('name', 'ASC');

        $this->view('scanner/index', [
            'hubs' => $hubs
        ]);
    }

    public function autocomplete() {
        require_auth();
        $q = trim($_GET['q'] ?? '');
        
        if (strlen($q) < 3) {
            return $this->json(['suggestions' => []]);
        }

        $db = \App\Core\Database::getInstance()->getConnection();
        $sql = "SELECT resi_number, sender_name, receiver_name FROM shipments 
                WHERE resi_number LIKE ? 
                AND status NOT IN ('DELIVERED', 'CANCELLED') 
                ORDER BY created_at DESC LIMIT 10";
        $stmt = $db->prepare($sql);
        $stmt->execute(['%' . $q . '%']);
        
        $suggestions = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $this->json(['suggestions' => $suggestions]);
    }

    public function processScan() {
        require_auth();

        $resi = trim($_POST['resi_number'] ?? '');
        $status = trim($_POST['status'] ?? 'SORTED');
        $notes = trim($_POST['notes'] ?? '');
        $hubId = (int)($_POST['hub_id'] ?? (auth_user()['hub_id'] ?? 1));

        if (empty($resi)) {
            return $this->json(['status' => 'error', 'message' => 'Nomor resi tidak terdeteksi.'], 400);
        }

        // Restrict 'PROBLEM' status to admin and operator_hub only
        if ($status === 'PROBLEM' && !has_role('admin') && !has_role('operator_hub')) {
            return $this->json(['status' => 'error', 'message' => 'Anda tidak memiliki akses untuk mengecap paket sebagai bermasalah.'], 403);
        }

        $shipmentModel = new ShipmentModel();
        $checkpointModel = new CheckpointModel();
        $hubModel = new HubModel();

        $shipment = $shipmentModel->findByResi($resi);
        if (!$shipment) {
            return $this->json(['status' => 'error', 'message' => "Resi {$resi} tidak ditemukan!"], 404);
        }

        $hub = $hubModel->find($hubId);
        $hubName = $hub['name'] ?? 'Hub Transit';

        try {
            // Update Shipment status & current location
            $shipmentModel->update($shipment['id'], [
                'status' => $status,
                'current_hub_id' => $hubId
            ]);

            // Insert new Checkpoint
            $statusLabels = [
                'RECEIVED_AT_HUB' => 'Scan Masuk Hub',
                'SORTED' => 'Scan Sortir',
                'IN_TRANSIT' => 'Scan Keberangkatan (In Transit)',
                'OUT_FOR_DELIVERY' => 'Scan Pengantaran (Kurir)',
                'DELIVERED' => 'Scan Paket Diterima',
                'PROBLEM' => 'Scan Paket Bermasalah / Tertahan',
            ];
            $actionLabel = $statusLabels[$status] ?? $status;

            // Check if latest checkpoint is identical to prevent spam scanning
            $db = \App\Core\Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT id, status, hub_id FROM checkpoints WHERE shipment_id = ? ORDER BY scanned_at DESC LIMIT 1");
            $stmt->execute([$shipment['id']]);
            $latest = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($latest && $latest['status'] === $status && (int)$latest['hub_id'] === $hubId) {
                return $this->json([
                    'status' => 'success',
                    'message' => "Resi {$resi} sudah dalam status [{$status}] di {$hubName}.",
                    'shipment' => $shipmentModel->findByResi($resi)
                ]);
            }

            // Handle photo_proof
            $photoPath = null;
            if (!empty($_POST['photo_proof'])) {
                $base64 = $_POST['photo_proof'];
                if (preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
                    $base64 = substr($base64, strpos($base64, ',') + 1);
                    $type = strtolower($type[1]);
                    if (in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) {
                        $base64 = base64_decode($base64);
                        if ($base64 !== false) {
                            $fileName = 'proof_' . $shipment['id'] . '_' . time() . '.' . $type;
                            $uploadDir = __DIR__ . '/../../../public/uploads/';
                            if (!is_dir($uploadDir)) {
                                mkdir($uploadDir, 0777, true);
                            }
                            if (file_put_contents($uploadDir . $fileName, $base64)) {
                                $photoPath = 'uploads/' . $fileName;
                            }
                        }
                    }
                }
            }

            $checkpointModel->create([
                'shipment_id' => $shipment['id'],
                'hub_id' => $hubId,
                'user_id' => auth_user()['id'] ?? 1,
                'status' => $status,
                'location_name' => $hubName,
                'photo_proof' => $photoPath,
                'notes' => $actionLabel . ($notes ? ": {$notes}" : '')
            ]);

            return $this->json([
                'status' => 'success',
                'message' => "Resi {$resi} berhasil di-scan status [{$status}] di {$hubName}.",
                'shipment' => $shipmentModel->findByResi($resi)
            ]);
        } catch (\Throwable $e) {
            return $this->json(['status' => 'error', 'message' => 'Gagal memperbarui status: ' . $e->getMessage()], 500);
        }
    }
}
