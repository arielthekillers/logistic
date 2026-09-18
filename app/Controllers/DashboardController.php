<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ShipmentModel;
use App\Models\HubModel;
use App\Models\UserModel;

class DashboardController extends Controller {

    public function index() {
        require_auth();

        $shipmentModel = new ShipmentModel();
        $hubModel = new HubModel();
        $userModel = new UserModel();

        $stats = [
            'counts' => [
                'TOTAL' => 0,
                'RECEIVED_AT_HUB' => 0,
                'IN_TRANSIT' => 0,
                'DELIVERED' => 0,
                'PROBLEM' => 0,
            ],
            'recent_shipments' => [],
            'total_hubs' => 0,
            'total_users' => 0,
            'today_count' => 0,
            'active_by_hub' => [],
        ];

        try {
            $stats['counts'] = $shipmentModel->getCountsByStatus();
            $stats['recent_shipments'] = $shipmentModel->getAllWithHubs(10);
            $stats['total_hubs'] = count($hubModel->findAll());
            $stats['total_users'] = count($userModel->findAll());
            $stats['today_count'] = $shipmentModel->getTodayCount();
            $stats['active_by_hub'] = $shipmentModel->getActiveShipmentsByHub();
        } catch (\Throwable $e) {
            // Silence DB exception on initial unpopulated DB state
        }

        $this->view('dashboard/index', [
            'stats' => $stats
        ]);
    }
}
