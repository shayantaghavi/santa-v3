<?php
// app/Controllers/SettingsController.php

namespace App\Controllers;

use App\Managers\DatabaseManager;
use PDOException;

/**
 * SettingsController
 * Handles the logic for the application settings page.
 */
class SettingsController extends AuthenticatedController
{
    private string $settingsFilePath;

    public function __construct()
    {
        parent::__construct();
        $this->settingsFilePath = CONFIG_PATH . '/settings_data.json';
    }

    /**
     * Displays the main settings page.
     */
    public function index(): void
    {
        $this->showSettingsPage();
    }
    
    /**
     * Shows the form for editing an existing X-UI panel.
     */
    public function editPanel(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'شناسه پنل نامعتبر است.'];
            header('Location: /settings');
            exit;
        }

        $sql = "SELECT id, name, api_url, username, is_active FROM xui_panels WHERE id = :id";
        $result = DatabaseManager::select($sql, [':id' => $id]);
        $panelToEdit = $result[0] ?? null;

        if (!$panelToEdit) {
            $_SESSION['flash_message'] = ['type' => 'warning', 'text' => 'پنلی با این شناسه یافت نشد.'];
            header('Location: /settings');
            exit;
        }
        
        $this->showSettingsPage(['panelToEdit' => $panelToEdit]);
    }

    /**
     * Updates an existing X-UI panel in the database.
     */
    public function updatePanel(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $apiUrl = filter_input(INPUT_POST, 'api_url', FILTER_VALIDATE_URL);
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($id <= 0 || empty($name) || !$apiUrl || empty($username)) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'داده‌های ارسالی نامعتبر است.'];
            header('Location: /settings');
            exit;
        }

        $sql = "UPDATE xui_panels SET name = :name, api_url = :api_url, username = :username";
        $params = [
            ':name' => $name,
            ':api_url' => $apiUrl,
            ':username' => $username,
        ];

        if (!empty($password)) {
            $sql .= ", password = :password";
            $params[':password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $sql .= " WHERE id = :id";
        $params[':id'] = $id;

        try {
            DatabaseManager::execute($sql, $params);
            $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'پنل با موفقیت به‌روزرسانی شد.'];
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'خطا: پنلی با این نام از قبل وجود دارد.'];
            } else {
                $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'خطا در پایگاه داده هنگام به‌روزرسانی پنل.'];
                error_log($e->getMessage());
            }
        }

        header('Location: /settings');
        exit;
    }
    
    /**
     * A private helper method to render the settings page with all necessary data.
     */
    private function showSettingsPage(array $extraData = []): void
    {
        $panels = DatabaseManager::select('SELECT id, name, api_url, username, is_active FROM xui_panels ORDER BY name ASC');
        $data = ['pageTitle' => 'تنظیمات پنل', 'settings' => SETTINGS, 'panels' => $panels];
        $data = array_merge($data, $extraData);
        $this->view('pages.settings', $data);
    }
    
    /**
     * Updates the general settings in the settings_data.json file.
     */
    public function updateGeneral(): void
    {
        $url = filter_input(INPUT_POST, 'telegram_support_url', FILTER_SANITIZE_URL);
        $traffic = filter_input(INPUT_POST, 'default_total_traffic_gb', FILTER_VALIDATE_INT);
        $date = $_POST['fixed_expiry_date'] ?? '';
        $timestamp = null;
        if (!empty($date)) { $timestamp = strtotime($date); }
        $settings = json_decode(file_get_contents($this->settingsFilePath), true);
        $settings['telegram_support_url'] = $url;
        $settings['default_total_traffic_gb'] = ($traffic !== false && $traffic >= 0) ? $traffic : 0;
        $settings['fixed_expiry_timestamp'] = $timestamp;
        $result = file_put_contents($this->settingsFilePath, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        if ($result !== false) { $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'تنظیمات عمومی با موفقیت به‌روزرسانی شد.']; } else { $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'خطا در ذخیره فایل تنظیمات.']; }
        header('Location: /settings');
        exit;
    }

    /**
     * Updates the database connection settings in the settings_data.json file.
     */
    public function updateDb(): void
    {
        $host = trim($_POST['db_host'] ?? '');
        $name = trim($_POST['db_name'] ?? '');
        $user = trim($_POST['db_user'] ?? '');
        $pass = $_POST['db_pass'] ?? '';
        if (empty($host) || empty($name) || empty($user)) { $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'هاست، نام دیتابیس و نام کاربری نمی‌توانند خالی باشند.']; header('Location: /settings'); exit; }
        $settings = json_decode(file_get_contents($this->settingsFilePath), true);
        $settings['db_settings']['db_host'] = $host;
        $settings['db_settings']['db_name'] = $name;
        $settings['db_settings']['db_user'] = $user;
        if (!empty($pass)) { $settings['db_settings']['db_pass'] = $pass; }
        $result = file_put_contents($this->settingsFilePath, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        if ($result !== false) { $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'تنظیمات دیتابیس با موفقیت به‌روزرسانی شد.']; } else { $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'خطا در ذخیره فایل تنظیمات.']; }
        header('Location: /settings');
        exit;
    }

    /**
     * Stores a new X-UI panel in the database.
     */
    public function storePanel(): void
    {
        $name = trim($_POST['name'] ?? '');
        $apiUrl = filter_input(INPUT_POST, 'api_url', FILTER_VALIDATE_URL);
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        if (empty($name) || !$apiUrl || empty($username) || empty($password)) { $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'تمام فیلدها اجباری هستند و آدرس API باید معتبر باشد.']; header('Location: /settings'); exit; }
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        try {
            $sql = "INSERT INTO xui_panels (name, api_url, username, password, is_active) VALUES (:name, :api_url, :username, :password, 1)";
            $params = [':name' => $name, ':api_url' => $apiUrl, ':username' => $username, ':password' => $hashedPassword];
            DatabaseManager::execute($sql, $params);
            $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'پنل جدید با موفقیت اضافه شد.'];
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') { $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'خطا: پنلی با این نام از قبل وجود دارد.']; } else { $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'خطا در پایگاه داده هنگام افزودن پنل.']; error_log($e->getMessage()); }
        }
        header('Location: /settings');
        exit;
    }
    
    /**
     * Deletes an X-UI panel from the database.
     */
    public function deletePanel(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) { $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'شناسه پنل نامعتبر است.']; header('Location: /settings'); exit; }
        try {
            $sql = "DELETE FROM xui_panels WHERE id = :id";
            $rowCount = DatabaseManager::execute($sql, [':id' => $id]);
            if ($rowCount > 0) { $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'پنل با موفقیت حذف شد.']; } else { $_SESSION['flash_message'] = ['type' => 'warning', 'text' => 'پنلی با این شناسه یافت نشد.']; }
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'خطا در حذف پنل.'];
            error_log($e->getMessage());
        }
        header('Location: /settings');
        exit;
    }
}