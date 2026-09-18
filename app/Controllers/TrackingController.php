<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ShipmentModel;
use App\Models\CheckpointModel;

class TrackingController extends Controller {

    public function index() {
        $resi = trim($_GET['resi'] ?? '');
        $shipment = null;
        $checkpoints = [];

        if (!empty($resi)) {
            $shipmentModel = new ShipmentModel();
            $checkpointModel = new CheckpointModel();

            try {
                $shipment = $shipmentModel->findByResi($resi);
                if ($shipment) {
                    $checkpoints = $checkpointModel->getByShipmentId($shipment['id']);
                }
            } catch (\Throwable $e) {
                // Silently handle
            }
        }

        $this->view('tracking/public', [
            'resi' => $resi,
            'shipment' => $shipment,
            'checkpoints' => $checkpoints
        ]);
    }

    public function search() {
        return $this->index();
    }

    public function apiTrack() {
        $resi = trim($_GET['resi'] ?? '');
        if (empty($resi)) {
            return $this->json(['status' => 'error', 'message' => 'Nomor resi wajib diisi.'], 400);
        }

        $shipmentModel = new ShipmentModel();
        $checkpointModel = new CheckpointModel();

        try {
            $shipment = $shipmentModel->findByResi($resi);
            if (!$shipment) {
                return $this->json(['status' => 'error', 'message' => 'Nomor resi tidak ditemukan.'], 444);
            }
            $checkpoints = $checkpointModel->getByShipmentId($shipment['id']);
            return $this->json([
                'status' => 'success',
                'shipment' => $shipment,
                'checkpoints' => $checkpoints
            ]);
        } catch (\Throwable $e) {
            return $this->json(['status' => 'error', 'message' => 'Gagal memproses tracking.'], 500);
        }
    }
}
