<?php
// app/Managers/XuiApiManager.php

namespace App\Managers;

use App\Managers\DatabaseManager;
use PDO;

/**
 * XuiApiManager handles all API communications with 3X-UI panels.
 * It's now database-driven, fetching panel configurations from the database.
 */
class XuiApiManager
{
    private array $panels = [];
    private array $sessions = [];
    private string $cacheFile;
    private int $cacheLifetime = 300; // 5 minutes

    /**
     * The constructor fetches active X-UI panels from the database.
     */
    public function __construct()
    {
        // Define cache file path
        $this->cacheFile = APP_PATH . '/logs/xui_api_cache.json';

        // Fetch active panels from the database
        $this->panels = DatabaseManager::select("SELECT id, name, api_url, username, password FROM xui_panels WHERE is_active = 1");
    }

    /**
     * Executes a cURL request to a specific X-UI API instance.
     *
     * @param string $baseUrl The base URL of the panel API.
     * @param string $endpoint The API endpoint to call.
     * @param array $postData Data for POST requests.
     * @param string $method HTTP method (GET or POST).
     * @param bool $isLogin Whether this is a login request (to handle cookies).
     * @return array|null The decoded JSON response or null on failure.
     */
    private function callXuiApi(string $baseUrl, string $endpoint, array $postData = [], string $method = 'GET', bool $isLogin = false): ?array
    {
        $url = rtrim($baseUrl, '/') . $endpoint;
        $ch = curl_init();
        // cURL options setup...
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false, // Set to true in production with valid certs
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_FOLLOWLOCATION => true,
        ]);

        $headers = [];
        $panelKey = md5($baseUrl);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($isLogin) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
                $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
                $headers[] = 'Content-Type: application/json';
            }
        }

        if ($isLogin) {
            curl_setopt($ch, CURLOPT_HEADER, true);
        } elseif (isset($this->sessions[$panelKey])) {
            $headers[] = 'Cookie: ' . $this->sessions[$panelKey];
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            error_log("cURL Error for {$url}: " . curl_error($ch));
            curl_close($ch);
            return null;
        }

        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headerContent = substr($response, 0, $headerSize);
        $bodyContent = substr($response, $headerSize);
        
        if ($isLogin) {
            if (preg_match('/^Set-Cookie:\s*([^;]*session=.*?)[\r\n]/mi', $headerContent, $matches)) {
                 $this->sessions[$panelKey] = trim($matches[1]);
            }
            $decodedResponse = json_decode($bodyContent, true);
        } else {
            $decodedResponse = json_decode($response, true);
        }
        
        curl_close($ch);

        if ($httpCode >= 400 || !is_array($decodedResponse)) {
            error_log("API call failed for {$url} with status {$httpCode}");
            return null;
        }

        return $decodedResponse;
    }

    /**
     * Attempts to log in to all configured X-UI panels.
     *
     * @return array An array of login statuses, with panel name as key.
     */
    public function loginAllPanels(): array
    {
        $loginResults = [];
        foreach ($this->panels as $panel) {
            $response = $this->callXuiApi(
                $panel['api_url'],
                '/login',
                ['username' => $panel['username'], 'password' => $panel['password']],
                'POST',
                true
            );
            $loginResults[$panel['name']] = ($response['success'] ?? false);
        }
        return $loginResults;
    }

    /**
     * Gets aggregated traffic data for all clients from ALL configured panels.
     * Uses caching to reduce API calls.
     *
     * @return array Aggregated client data keyed by UUID.
     */
    public function getXuiAllClientsTrafficAggregatedByUuid(): array
    {
        // Check cache first
        if (file_exists($this->cacheFile) && (time() - filemtime($this->cacheFile) < $this->cacheLifetime)) {
            return json_decode(file_get_contents($this->cacheFile), true) ?: [];
        }

        $this->loginAllPanels();
        $aggregatedData = [];

        foreach ($this->panels as $panel) {
            $panelKey = md5($panel['api_url']);
            if (!isset($this->sessions[$panelKey])) {
                continue; // Skip panels we couldn't log in to
            }

            $inbounds = $this->callXuiApi($panel['api_url'], '/panel/api/inbounds/list');
            
            if (!($inbounds['success'] ?? false) || empty($inbounds['obj'])) {
                continue;
            }

            foreach ($inbounds['obj'] as $inbound) {
                if (empty($inbound['clientStats'])) continue;

                $settings = json_decode($inbound['settings'], true);
                $clientsInSettings = array_column($settings['clients'] ?? [], null, 'email');

                foreach ($inbound['clientStats'] as $stat) {
                    $email = $stat['email'];
                    // Find the client's UUID from the settings using their email
                    $uuid = $clientsInSettings[$email]['id'] ?? null;
                    if (!$uuid) continue;

                    if (!isset($aggregatedData[$uuid])) {
                        $aggregatedData[$uuid] = ['up' => 0, 'down' => 0, 'total' => 0, 'expiryTime' => 0, 'enable' => true, 'remark' => $email];
                    }

                    $aggregatedData[$uuid]['up'] += $stat['up'];
                    $aggregatedData[$uuid]['down'] += $stat['down'];
                    $aggregatedData[$uuid]['total'] = max($aggregatedData[$uuid]['total'], $stat['total']); // Get the highest limit
                    // Find the earliest expiry date among all configs
                    if ($stat['expiryTime'] > 0 && ($aggregatedData[$uuid]['expiryTime'] == 0 || $stat['expiryTime'] < $aggregatedData[$uuid]['expiryTime'])) {
                        $aggregatedData[$uuid]['expiryTime'] = $stat['expiryTime'];
                    }
                    if (!$stat['enable']) {
                        $aggregatedData[$uuid]['enable'] = false;
                    }
                }
            }
        }
        
        // Write fresh data to cache
        file_put_contents($this->cacheFile, json_encode($aggregatedData, JSON_PRETTY_PRINT));
        
        return $aggregatedData;
    }
}