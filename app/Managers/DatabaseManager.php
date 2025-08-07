<?php
// app/Managers/DatabaseManager.php

namespace App\Managers;

use PDO;
use PDOException;

/**
 * DatabaseManager handles all database interactions for the application.
 */
class DatabaseManager
{
    /** @var PDO|null The single instance of the PDO connection. */
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}

    /**
     * Gets the single instance of the PDO database connection.
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $host = setting('db_settings.db_host', 'localhost');
            $name = setting('db_settings.db_name');
            $user = setting('db_settings.db_user');
            $pass = setting('db_settings.db_pass');
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;dbname=$name;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                error_log("Database Connection Failed: " . $e->getMessage());
                throw new PDOException($e->getMessage(), (int)$e->getCode());
            }
        }

        return self::$instance;
    }

    /**
     * A helper method to easily run SELECT queries.
     */
    public static function select(string $sql, array $params = []): array
    {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * A helper method to easily run INSERT, UPDATE, or DELETE queries.
     */
    public static function execute(string $sql, array $params = []): int
    {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }
    
    // --- NEW METHODS ---

    /**
     * Inserts or updates a user's traffic data in the user_traffic table.
     *
     * @param string $uuid The user's UUID.
     * @param int $up The total upload bytes.
     * @param int $down The total download bytes.
     * @return void
     */
    public static function updateUserTraffic(string $uuid, int $up, int $down): void
    {
        $sql = "
            INSERT INTO user_traffic (user_uuid, up, down)
            VALUES (:uuid, :up, :down)
            ON DUPLICATE KEY UPDATE
                up = VALUES(up),
                down = VALUES(down)
        ";
        $params = [
            ':uuid' => $uuid,
            ':up' => $up,
            ':down' => $down,
        ];
        self::execute($sql, $params);
    }

    /**
     * Logs the result of a cron job execution into the cron_logs table.
     *
     * @param string $jobName The name of the job (e.g., 'traffic_sync').
     * @param string $status The result ('success' or 'failed').
     * @param string $message A descriptive message.
     * @return void
     */
    public static function logCronJob(string $jobName, string $status, string $message): void
    {
        $sql = "INSERT INTO cron_logs (job_name, status, message) VALUES (:job_name, :status, :message)";
        $params = [
            ':job_name' => $jobName,
            ':status' => $status,
            ':message' => $message,
        ];
        self::execute($sql, $params);
    }
}