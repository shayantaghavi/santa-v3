<?php
// app/Controllers/SubscriptionController.php

namespace App\Controllers;

use App\Managers\ConfigManager;
use App\Managers\DatabaseManager;

/**
 * SubscriptionController
 * Handles the generation of the subscription link for end-users and clients.
 */
class SubscriptionController extends BaseController
{
    /**
     * Generates the subscription output based on the user's remark.
     *
     * @return void
     */
    public function generate(): void
    {
        // 1. Get user remark from the query string (?subs=...).
        $remark = $_GET['subs'] ?? null;
        if (!$remark) {
            $this->notFound();
            return;
        }

        // 2. Find the user in our database.
        $userSql = "SELECT uuid, remark FROM users WHERE remark = :remark AND status = 'active'";
        $userResult = DatabaseManager::select($userSql, [':remark' => $remark]);
        $user = $userResult[0] ?? null;

        if (!$user) {
            $this->notFound();
            return;
        }

        // 3. Gather all necessary data.
        // Traffic data from the traffic table
        $trafficSql = "SELECT up, down FROM user_traffic WHERE user_uuid = :uuid";
        $trafficResult = DatabaseManager::select($trafficSql, [':uuid' => $user['uuid']]);
        $traffic = $trafficResult[0] ?? ['up' => 0, 'down' => 0];

        // Template data from the config file
        $config = ConfigManager::read();
        $templateLinks = $config['templateLinks'] ?? [];
        $disabledTemplates = $config['globallyDisabledTemplates'] ?? [];

        // Global settings
        $expiryTimestamp = setting('fixed_expiry_timestamp', 0);
        $totalGB = setting('default_total_traffic_gb', 0);
        $totalBytes = $totalGB * 1024 * 1024 * 1024;

        // 4. Generate the final subscription links.
        $finalLinks = [];
        foreach ($templateLinks as $name => $link) {
            // Skip templates that are globally disabled.
            if (in_array($name, $disabledTemplates)) {
                continue;
            }

            $finalLinks[] = str_replace(
                ['UUID', '#USER'],
                [$user['uuid'], '#' . $user['remark']],
                $link
            );
        }

        // 5. Determine the output type based on User-Agent.
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $isBrowser = preg_match('/Mozilla|Chrome|Safari|Firefox|Edge|Opera/i', $userAgent);

        if ($isBrowser) {
            // For browsers, show a user-friendly HTML page.
            $data = [
                'pageTitle' => 'اطلاعات اشتراک',
                'user' => $user,
                'traffic' => $traffic,
                'totalGB' => $totalGB,
                'expiryTimestamp' => $expiryTimestamp,
                'subscriptionLink' => "{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['HTTP_HOST']}/subscription?subs={$user['remark']}",
            ];
            $this->view('pages.subscription_info', $data);
        } else {
            // For clients, provide the raw subscription data.
            $this->outputForClient($finalLinks, $traffic, $totalBytes, $expiryTimestamp);
        }
    }

    /**
     * Outputs the subscription data for a client application (e.g., V2Ray).
     */
    private function outputForClient(array $links, array $traffic, int $totalBytes, int $expiryTimestamp): void
    {
        header('Content-Type: text/plain; charset=utf-8');

        // Create the Subscription-Userinfo header
        $userInfo = "upload={$traffic['up']}; download={$traffic['down']}; total={$totalBytes}; expire={$expiryTimestamp}";
        header('Subscription-Userinfo: ' . $userInfo);

        // Output the base64 encoded list of links.
        echo base64_encode(implode("\n", $links));
    }

    /**
     * Outputs a simple "Not Found" message for browsers.
     */
    private function notFound(): void
    {
        header("HTTP/1.0 404 Not Found");
        $this->view('pages.errors.404', ['pageTitle' => 'یافت نشد']);
    }
}