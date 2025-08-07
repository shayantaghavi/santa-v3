<?php
// app/Managers/ConfigManager.php

namespace App\Managers;

/**
 * ConfigManager
 * A utility class to handle reading from and writing to the main config_data.json file.
 */
class ConfigManager
{
    /**
     * @var string The full path to the config_data.json file.
     */
    private static string $filePath;

    /**
     * A private helper to initialize the file path.
     */
    private static function initialize(): void
    {
        if (empty(self::$filePath)) {
            self::$filePath = CONFIG_PATH . '/config_data.json';
        }
    }

    /**
     * Reads and decodes the config_data.json file.
     *
     * @return array The configuration data as an associative array.
     */
    public static function read(): array
    {
        self::initialize();

        if (!file_exists(self::$filePath)) {
            // If the file doesn't exist, return a default structure to avoid errors.
            return [
                'templateLinks' => [],
                'userConfigs' => [],
                'excludedConfigs' => [],
                'connectionBasedConfigs' => [],
                'trojBasedConfigs' => [],
                'globallyDisabledTemplates' => [],
            ];
        }

        $jsonContent = file_get_contents(self::$filePath);
        return json_decode($jsonContent, true) ?: []; // Return empty array on JSON decode failure.
    }

    /**
     * Encodes an array into JSON and writes it to the config_data.json file.
     *
     * @param array $data The configuration data array to be saved.
     * @return bool True on success, false on failure.
     */
    public static function write(array $data): bool
    {
        self::initialize();

        // Encode the data with pretty printing for human readability.
        $jsonContent = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($jsonContent === false) {
            error_log('Failed to encode data for config_data.json.');
            return false;
        }

        // file_put_contents returns the number of bytes written, or false on failure.
        return file_put_contents(self::$filePath, $jsonContent) !== false;
    }
}