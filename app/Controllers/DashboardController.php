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

        $useRedis = false;
        $redis = null;
        
        if (class_exists('Redis')) {
            try {
                $redis = new \Redis();
                if (@$redis->connect('127.0.0.1', 6379)) {
                    $useRedis = true;
                }
            } catch (\Exception $e) {
                // Fallback if Redis fails
            }
        }

        $user = $_SESSION['user'] ?? null;
        $isAdmin = ($user['role'] ?? '') === 'admin';
        
        $hubs = $hubModel->findAll();
        
        $hubId = null;
        if ($isAdmin) {
            $selectedHubId = $_GET['hub_id'] ?? 'all';
            if ($selectedHubId !== 'all' && is_numeric($selectedHubId)) {
                $hubId = (int)$selectedHubId;
            }
        } else {
            $hubId = $user['hub_id'] ?? null;
        }
        
        $cacheKey = $isAdmin ? ('dashboard_stats_admin_' . ($hubId ?: 'all')) : 'dashboard_stats_hub_' . $hubId;

        if ($useRedis && $redis->exists($cacheKey)) {
            $stats = json_decode($redis->get($cacheKey), true);
        } else {
            try {
                $stats['counts'] = $shipmentModel->getCountsByStatus($hubId);
                $stats['recent_shipments'] = $shipmentModel->getAllWithHubs(10, $hubId);
                $stats['total_hubs'] = count($hubs);
                $stats['total_users'] = count($userModel->findAll());
                $stats['today_count'] = $shipmentModel->getTodayCount($hubId);
                $stats['active_by_hub'] = $shipmentModel->getActiveShipmentsByHub();
                $stats['weekly_trend'] = $shipmentModel->getWeeklyTrend($hubId);

                if ($useRedis) {
                    $redis->setex($cacheKey, 300, json_encode($stats)); // Cache for 5 minutes
                }
            } catch (\Throwable $e) {
                // Silence DB exception on initial unpopulated DB state
            }
        }

        $this->view('dashboard/index', [
            'stats' => $stats,
            'hubs' => $hubs,
            'selected_hub_id' => $hubId
        ]);
    }
}
