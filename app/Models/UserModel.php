<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class UserModel extends Model {
    protected $table = 'users';

    public function findByUsername($username) {
        $stmt = $this->db->prepare("SELECT u.*, h.name as hub_name FROM users u LEFT JOIN hubs h ON u.hub_id = h.id WHERE u.username = ? LIMIT 1");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllWithHub() {
        $sql = "SELECT u.*, h.name as hub_name FROM users u LEFT JOIN hubs h ON u.hub_id = h.id ORDER BY u.id DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
