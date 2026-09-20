<?php

/**
 * Get dynamic settings from database
 */
function get_setting($key, $default = null) {
    static $settings = null;
    if ($settings === null) {
        try {
            $db = \App\Core\Database::getInstance()->getConnection();
            $stmt = $db->query("SELECT key_name, key_value FROM settings");
            if ($stmt) {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $settings = [];
                foreach ($rows as $r) {
                    $settings[$r['key_name']] = $r['key_value'];
                }
            } else {
                $settings = [];
            }
        } catch (\Throwable $e) {
            $settings = [];
        }
    }
    return $settings[$key] ?? $default;
}

define('COMPANY_NAME', get_setting('company_name', 'Barongko Logistik'));
define('COMPANY_ADDRESS', get_setting('company_address', 'Jl. Kalianget No. 96 Surabaya'));
define('COMPANY_PHONE', get_setting('company_phone', '0811 5877 234'));
define('COMPANY_EMAIL', getenv('COMPANY_EMAIL') ?: 'ptbarongkodarmalogistik@gmail.com');
define('COMPANY_LOGO', getenv('COMPANY_LOGO') ?: 'img/logo.png');

/**
 * Generate full or relative URL
 */
function url($path = '') {
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $baseUrl = rtrim(dirname($scriptName), '/\\');
    
    if ($baseUrl === '/' || $baseUrl === '\\') {
        $baseUrl = '';
    }

    $path = '/' . ltrim($path, '/');
    return $baseUrl . $path;
}

/**
 * Escape string for HTML safe output
 */
function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Format currency IDR
 */
function format_rp($amount) {
    return 'Rp ' . number_format($amount ?? 0, 0, ',', '.');
}

/**
 * Format date time to Indonesian standard
 */
function format_datetime($datetime) {
    if (empty($datetime)) return '-';
    $time = strtotime($datetime);
    return date('d M Y H:i', $time);
}

/**
 * Generate unique Waybill/Resi Number for BDL
 * Format: SJ-YYYYMMDD-XXXX (e.g. SJ-20260918-1340)
 */
function generate_resi_number() {
    $dateStr = date('Ymd');
    $randomStr = rand(1000, 9999);
    return "SJ-{$dateStr}-{$randomStr}";
}

/**
 * Smart filter for Indonesian phone numbers
 * Cleans non-numeric, converts +62, 62 to 08
 */
function standardize_phone($phone) {
    if (empty($phone)) return '';
    // Remove all non-numeric chars
    $clean = preg_replace('/[^0-9]/', '', $phone);
    // Replace leading 62 with 0
    if (strpos($clean, '62') === 0) {
        $clean = '0' . substr($clean, 2);
    }
    return $clean;
}

/**
 * Human friendly status label badge class
 */
function get_status_badge($status) {
    switch ($status) {
        case 'DRAFT':
            return '<span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-300">Draft</span>';
        case 'RECEIVED_AT_HUB':
            return '<span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400">Tiba di Hub</span>';
        case 'SORTED':
            return '<span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-400">Disortir</span>';
        case 'IN_TRANSIT':
            return '<span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-400">Dalam Perjalanan</span>';
        case 'OUT_FOR_DELIVERY':
            return '<span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-400">Dalam Pengantaran</span>';
        case 'DELIVERED':
            return '<span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-400">Diterima</span>';
        case 'CANCELLED':
            return '<span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-rose-100 dark:bg-rose-900/30 text-rose-800 dark:text-rose-400">Dibatalkan</span>';
        case 'PROBLEM':
            return '<span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-600 dark:bg-red-500/20 text-white dark:text-red-400 shadow-sm border border-red-700 dark:border-red-500/50 animate-pulse">Bermasalah</span>';
        default:
            return '<span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-400">' . e($status) . '</span>';
    }
}
