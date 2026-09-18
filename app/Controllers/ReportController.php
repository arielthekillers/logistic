<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ShipmentModel;
use App\Models\HubModel;
use PDO;

class ReportController extends Controller {

    public function index() {
        require_roles(['admin']);

        $hubModel = new HubModel();
        $hubs = $hubModel->findAll('name', 'ASC');

        $this->view('reports/index', [
            'hubs' => $hubs
        ]);
    }

    public function exportCsv() {
        require_roles(['admin']);

        $startDate = $_GET['start_date'] ?? date('Y-m-d');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        $status = $_GET['status'] ?? '';
        $hubId = $_GET['hub_id'] ?? '';

        $db = \App\Core\Database::getInstance()->getConnection();

        // Build query
        $sql = "SELECT s.resi_number, s.sender_name, s.sender_phone, 
                       s.receiver_name, s.receiver_phone, s.receiver_address,
                       s.weight_kg, s.koli, s.package_items, s.status, s.total_cost,
                       ho.name as origin_hub, hd.name as destination_hub, hc.name as current_hub,
                       s.created_at
                FROM shipments s
                LEFT JOIN hubs ho ON s.origin_hub_id = ho.id
                LEFT JOIN hubs hd ON s.destination_hub_id = hd.id
                LEFT JOIN hubs hc ON s.current_hub_id = hc.id
                WHERE DATE(s.created_at) >= ? AND DATE(s.created_at) <= ?";
        
        $params = [$startDate, $endDate];

        if ($status !== '') {
            $sql .= " AND s.status = ?";
            $params[] = $status;
        }

        if ($hubId !== '') {
            $sql .= " AND s.current_hub_id = ?";
            $params[] = $hubId;
        }

        $sql .= " ORDER BY s.created_at DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Prepare CSV
        $filename = "Export_Laporan_BDL_{$startDate}_to_{$endDate}.csv";
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        
        $output = fopen('php://output', 'w');
        
        // Add BOM for Excel UTF-8 reading
        fputs($output, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF)));

        // CSV Header
        fputcsv($output, [
            'No Resi', 'Tgl Dibuat', 'Status', 
            'Pengirim', 'Telp Pengirim', 
            'Penerima', 'Telp Penerima', 'Alamat Tujuan',
            'Berat (kg)', 'Jml Koli', 'Detail Barang', 'Biaya (Rp)',
            'Hub Asal', 'Hub Tujuan', 'Posisi Saat Ini'
        ]);

        // Output rows
        foreach ($records as $row) {
            fputcsv($output, [
                $row['resi_number'],
                $row['created_at'],
                $row['status'],
                $row['sender_name'],
                $row['sender_phone'],
                $row['receiver_name'],
                $row['receiver_phone'],
                $row['receiver_address'],
                $row['weight_kg'],
                $row['koli'],
                $row['package_items'],
                $row['total_cost'],
                $row['origin_hub'],
                $row['destination_hub'],
                $row['current_hub']
            ]);
        }

        fclose($output);
        exit;
    }
}
