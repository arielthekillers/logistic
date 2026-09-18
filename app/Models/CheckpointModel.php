<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class CheckpointModel extends Model {
    protected $table = 'checkpoints';

    public function getByShipmentId($shipmentId) {
        $sql = "SELECT c.*, h.name as hub_name, u.name as user_name, u.avatar as user_avatar 
                FROM checkpoints c
                LEFT JOIN hubs h ON c.hub_id = h.id
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.shipment_id = ?
                ORDER BY c.scanned_at ASC, c.id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$shipmentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
