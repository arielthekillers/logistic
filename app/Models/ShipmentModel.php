<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class ShipmentModel extends Model {
    protected $table = 'shipments';

    public function findByResi($resiNumber) {
        $sql = "SELECT s.*, 
                       ho.name as origin_hub_name, ho.city as origin_city,
                       hc.name as current_hub_name, hc.city as current_city,
                       hd.name as destination_hub_name, hd.city as destination_city
                FROM shipments s
                LEFT JOIN hubs ho ON s.origin_hub_id = ho.id
                LEFT JOIN hubs hc ON s.current_hub_id = hc.id
                LEFT JOIN hubs hd ON s.destination_hub_id = hd.id
                WHERE s.resi_number = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([trim($resiNumber)]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getPaginated($page = 1, $perPage = 15, $search = '', $status = '', $startDate = '', $endDate = '') {
        $offset = ($page - 1) * $perPage;
        
        $whereConditions = [];
        $params = [];
        
        if (!empty($startDate) && empty($endDate)) {
            $endDate = $startDate;
        } elseif (empty($startDate) && !empty($endDate)) {
            $startDate = $endDate;
        }

        if (!empty($startDate) && !empty($endDate)) {
            $whereConditions[] = "DATE(s.created_at) >= ? AND DATE(s.created_at) <= ?";
            $params[] = $startDate;
            $params[] = $endDate;
        }
        
        if (!empty($search)) {
            $whereConditions[] = "(s.resi_number LIKE ? OR s.sender_name LIKE ? OR s.receiver_name LIKE ?)";
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($status)) {
            $whereConditions[] = "s.status = ?";
            $params[] = $status;
        }

        $whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

        // Get total count
        $countSql = "SELECT COUNT(s.id) as total FROM shipments s $whereClause";
        $stmtCount = $this->db->prepare($countSql);
        $stmtCount->execute($params);
        $total = (int)$stmtCount->fetch(PDO::FETCH_ASSOC)['total'];

        // Get paginated data
        $sql = "SELECT s.*, 
                       ho.name as origin_hub_name, 
                       hc.name as current_hub_name, 
                       hd.name as destination_hub_name
                FROM shipments s
                LEFT JOIN hubs ho ON s.origin_hub_id = ho.id
                LEFT JOIN hubs hc ON s.current_hub_id = hc.id
                LEFT JOIN hubs hd ON s.destination_hub_id = hd.id
                $whereClause
                ORDER BY s.id DESC 
                LIMIT " . (int)$perPage . " OFFSET " . (int)$offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'data' => $data,
            'total' => $total,
            'last_page' => ceil($total / $perPage),
            'current_page' => $page,
            'per_page' => $perPage
        ];
    }

    public function getAllWithHubs($limit = 100, $hubId = null) {
        $sql = "SELECT s.*, 
                       ho.name as origin_hub_name, 
                       hc.name as current_hub_name, 
                       hd.name as destination_hub_name
                FROM shipments s
                LEFT JOIN hubs ho ON s.origin_hub_id = ho.id
                LEFT JOIN hubs hc ON s.current_hub_id = hc.id
                LEFT JOIN hubs hd ON s.destination_hub_id = hd.id";
        
        $params = [];
        if ($hubId !== null) {
            $sql .= " WHERE (s.origin_hub_id = ? OR s.current_hub_id = ? OR s.destination_hub_id = ?)";
            $params = [$hubId, $hubId, $hubId];
        }
        
        $sql .= " ORDER BY s.id DESC LIMIT " . (int)$limit;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCountsByStatus($hubId = null) {
        $sql = "SELECT status, COUNT(*) as count FROM shipments";
        $params = [];
        if ($hubId !== null) {
            $sql .= " WHERE (origin_hub_id = ? OR current_hub_id = ? OR destination_hub_id = ?)";
            $params = [$hubId, $hubId, $hubId];
        }
        $sql .= " GROUP BY status";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $counts = [
            'TOTAL' => 0,
            'DRAFT' => 0,
            'RECEIVED_AT_HUB' => 0,
            'SORTED' => 0,
            'IN_TRANSIT' => 0,
            'OUT_FOR_DELIVERY' => 0,
            'DELIVERED' => 0,
            'CANCELLED' => 0,
            'PROBLEM' => 0,
        ];
        foreach ($rows as $row) {
            $counts[$row['status']] = (int)$row['count'];
            $counts['TOTAL'] += (int)$row['count'];
        }
        return $counts;
    }

    public function getTodayCount($hubId = null) {
        // Count shipments created today
        $sql = "SELECT COUNT(*) as count FROM shipments WHERE DATE(created_at) = CURDATE()";
        $params = [];
        if ($hubId !== null) {
            $sql .= " AND (origin_hub_id = ? OR current_hub_id = ? OR destination_hub_id = ?)";
            $params = [$hubId, $hubId, $hubId];
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['count'] ?? 0);
    }

    public function getActiveShipmentsByHub() {
        // Group by current_hub_id for active shipments (not DELIVERED, CANCELLED)
        $sql = "SELECT h.id as hub_id, h.name as hub_name, COUNT(s.id) as active_count 
                FROM hubs h
                LEFT JOIN shipments s ON s.current_hub_id = h.id 
                WHERE s.status NOT IN ('DELIVERED', 'CANCELLED') OR s.status IS NULL
                GROUP BY h.id, h.name
                ORDER BY active_count DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getWeeklyTrend($hubId = null) {
        $sql = "SELECT DATE(created_at) as date, COUNT(id) as count 
                FROM shipments 
                WHERE created_at >= DATE(NOW()) - INTERVAL 6 DAY";
        $params = [];
        if ($hubId !== null) {
            $sql .= " AND (origin_hub_id = ? OR current_hub_id = ? OR destination_hub_id = ?)";
            $params = [$hubId, $hubId, $hubId];
        }
        $sql .= " GROUP BY DATE(created_at) ORDER BY DATE(created_at) ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $trend = [];
        // Initialize last 7 days with 0
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $trend[$date] = 0;
        }
        
        foreach ($rows as $row) {
            if (isset($trend[$row['date']])) {
                $trend[$row['date']] = (int)$row['count'];
            }
        }
        
        return $trend;
    }
}
