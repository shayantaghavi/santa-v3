<?php
// migrate.php (Debugging Version)

// Bootstrap the application to get access to our managers and settings.
require_once __DIR__ . '/config/bootstrap.php';

use App\Managers\DatabaseManager;
use PDOException;

echo "<!DOCTYPE html><html lang='en' dir='ltr'><head><title>Data Migration</title>";
echo "<style>body { font-family: sans-serif; padding: 20px; line-height: 1.8; } .success { color: green; } .error { color: red; } .skip { color: orange; } .info { color: blue; }</style>";
echo "</head><body>";
echo "<h1>Starting User Migration (Debug Mode)...</h1><hr>";

// Step 1: Check Database Connection
echo "<p class='info'>Step 1: Checking database connection...</p>";
try {
    DatabaseManager::getInstance(); // Try to get a connection instance
    echo "<p class='success'>Database connection successful!</p><hr>";
} catch (PDOException $e) {
    die("<p class='error'><strong>DATABASE CONNECTION FAILED!</strong><br>Please check your `db_settings` in the `settings_data.json` file.<br>Error details: " . $e->getMessage() . "</p></body></html>");
}

// Step 2: Check for JSON file
echo "<p class='info'>Step 2: Looking for `config_data_old.json`...</p>";
$jsonFilePath = __DIR__ . '/config_data_old.json';
if (!file_exists($jsonFilePath)) {
    die("<p class='error'>Error: `config_data_old.json` not found in the project root. Please upload the file and refresh the page.</p></body></html>");
}
echo "<p class='success'>File `config_data_old.json` found!</p><hr>";

// Step 3: Read and process users from JSON
echo "<p class='info'>Step 3: Reading users and inserting into the database...</p>";
$jsonContent = file_get_contents($jsonFilePath);
$configData = json_decode($jsonContent, true);
$userConfigs = $configData['userConfigs'] ?? [];

if (empty($userConfigs)) {
    die("<p class='error'>Error: No users found in the `userConfigs` section of the JSON file.</p></body></html>");
}

$insertedCount = 0;
$skippedCount = 0;

$sql = "INSERT INTO users (remark, uuid, status, created_at) VALUES (:remark, :uuid, 'active', NOW())";

foreach ($userConfigs as $remark => $details) {
    $uuid = $details['UUIDs']['Connection'] ?? null;

    if (!$uuid) {
        echo "<p class='skip'>- User '{$remark}' skipped (missing Connection UUID).</p>";
        $skippedCount++;
        continue;
    }

    try {
        $params = [':remark' => $remark, ':uuid' => $uuid];
        DatabaseManager::execute($sql, $params);
        echo "<p class='success'>+ User '{$remark}' successfully added to the database.</p>";
        $insertedCount++;
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            echo "<p class='skip'>- User '{$remark}' already exists in the database, skipped.</p>";
            $skippedCount++;
        } else {
            echo "<p class='error'>! Error adding user '{$remark}': " . $e->getMessage() . "</p>";
            $skippedCount++;
        }
    }
}

echo "<hr><h2>Migration Process Complete!</h2>";
echo "<p><strong>Users Added:</strong> <span class='success'>{$insertedCount}</span></p>";
echo "<p><strong>Users Skipped (duplicates or incomplete):</strong> <span class='skip'>{$skippedCount}</span></p>";
echo "<p style='font-weight: bold; color: red;'>Important: After verifying the data, delete this file (migrate.php) and config_data_old.json from your host.</p>";
echo "</body></html>";