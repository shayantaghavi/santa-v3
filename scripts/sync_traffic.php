#!/usr/bin/php
<?php
// scripts/sync_traffic.php

// --- SECURITY CHECK ---
// This script is designed to be run from the command line (cron job) only.
// This check prevents it from being executed via a web browser.
if (php_sapi_name() !== 'cli') {
    die('This script can only be run from the command line.');
}

// Set the working directory to the project root.
chdir(dirname(__DIR__)); // This will now point to public_html

// Bootstrap the application. The path is now relative to public_html.
require_once __DIR__ . '/../config/bootstrap.php';

use App\Managers\XuiApiManager;
use App\Managers\DatabaseManager;
use PDOException;

// --- Lock Mechanism to prevent overlapping runs ---
$lockFile = __DIR__ . '/sync_traffic.lock';
if (file_exists($lockFile)) {
    if (time() - filemtime($lockFile) > 1800) { // 30-minute timeout
        unlink($lockFile);
    } else {
        echo "Script is already running. Exiting.\n";
        exit;
    }
}
touch($lockFile);
// --- End Lock Mechanism ---

$startTime = microtime(true);
$jobName = 'traffic_sync';
echo "[$jobName] Script started at " . date('Y-m-d H:i:s') . "\n";

try {
    $xuiManager = new XuiApiManager();
    $xuiData = $xuiManager->getXuiAllClientsTrafficAggregatedByUuid();
    
    if (empty($xuiData)) {
        throw new Exception('No data received from X-UI panels. Login might have failed.');
    }

    $updatedCount = 0;
    
    foreach ($xuiData as $uuid => $data) {
        DatabaseManager::updateUserTraffic($uuid, $data['up'] ?? 0, $data['down'] ?? 0);
        $updatedCount++;
    }

    $duration = round(microtime(true) - $startTime, 2);
    $message = "Successfully updated {$updatedCount} users in {$duration} seconds.";
    DatabaseManager::logCronJob($jobName, 'success', $message);
    echo "[$jobName] $message\n";

} catch (Exception $e) {
    $message = "Script failed: " . $e->getMessage();
    DatabaseManager::logCronJob($jobName, 'failed', $message);
    echo "[$jobName] ERROR: $message\n";
} finally {
    if (file_exists($lockFile)) {
        unlink($lockFile);
    }
    echo "[$jobName] Script finished at " . date('Y-m-d H:i:s') . "\n";
}