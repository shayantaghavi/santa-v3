<?php
// app/Controllers/DashboardController.php

namespace App\Controllers;

use App\Managers\DatabaseManager;
use App\Managers\XuiApiManager;

/**
 * DashboardController
 * Handles the logic for the main dashboard page.
 */
class DashboardController extends AuthenticatedController
{
    /**
     * Shows the main dashboard page with system statistics.
     *
     * @return void
     */
    public function index(): void
    {
        // --- 1. Fetch X-UI Panel Stats ---
        $xuiManager = new XuiApiManager();
        $xuiData = $xuiManager->getXuiAllClientsTrafficAggregatedByUuid();
        $onlineUsers = count($xuiData);
        
        // Fetch panel count dynamically
        $panels = DatabaseManager::select("SELECT COUNT(id) as panel_count FROM xui_panels WHERE is_active = 1");
        $connectedPanels = $panels[0]['panel_count'] ?? 0;

        // --- 2. Fetch Database Status & Last Update ---
        $dbStatus = 'Connection Error';
        $lastTrafficUpdate = 'N/A';
        try {
            DatabaseManager::getInstance(); // Attempt to connect
            $dbStatus = 'Successful';

            $result = DatabaseManager::select("SELECT MAX(executed_at) as last_update FROM cron_logs WHERE job_name = 'traffic_sync' AND status = 'success'");
            if (!empty($result[0]['last_update'])) {
                $lastTrafficUpdate = date('Y-m-d H:i:s', strtotime($result[0]['last_update']));
            }
        } catch (\PDOException $e) {
            $dbStatus = 'Connection Error';
        }

        // --- 3. Fetch Recent Cron Job Logs ---
        $cronLogs = DatabaseManager::select("SELECT job_name, status, message, executed_at FROM cron_logs ORDER BY executed_at DESC LIMIT 5");

        // --- 4. Prepare Data and Render View ---
        $data = [
            'pageTitle' => 'داشبورد اصلی',
            'connectedPanels' => $connectedPanels,
            'onlineUsers' => $onlineUsers,
            'dbStatus' => $dbStatus, // This variable is now correctly defined and passed
            'lastTrafficUpdate' => $lastTrafficUpdate,
            'cronLogs' => $cronLogs,
        ];

        $this->view('pages.dashboard', $data);
    }
}