<?php
// app/Controllers/TemplateController.php

namespace App\Controllers;

use App\Managers\ConfigManager;

/**
 * TemplateController
 * Handles all logic for the subscription template management page.
 */
class TemplateController extends AuthenticatedController
{
    /**
     * Displays the main template management page.
     */
    public function index(): void
    {
        $config = ConfigManager::read();
        $data = [
            'pageTitle' => 'مدیریت قالب‌ها',
            'templateLinks' => $config['templateLinks'] ?? [],
            'globallyDisabledTemplates' => $config['globallyDisabledTemplates'] ?? [],
        ];
        $this->view('pages.templates', $data);
    }

    /**
     * Stores a new template in the config_data.json file.
     */
    public function store(): void
    {
        $name = trim($_POST['name'] ?? '');
        $link = trim($_POST['link'] ?? '');

        if (empty($name) || empty($link)) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'نام و لینک قالب نمی‌توانند خالی باشند.'];
            header('Location: /templates');
            exit;
        }

        $config = ConfigManager::read();
        if (isset($config['templateLinks'][$name])) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'قالبی با این نام از قبل وجود دارد.'];
            header('Location: /templates');
            exit;
        }

        $config['templateLinks'][$name] = $link;
        if (ConfigManager::write($config)) {
            $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'قالب جدید با موفقیت اضافه شد.'];
        } else {
            $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'خطا در ذخیره فایل پیکربندی.'];
        }
        header('Location: /templates');
        exit;
    }
    
    /**
     * Updates the global active/inactive status of all templates.
     *
     * @return void
     */
    public function updateStatuses(): void
    {
        // 1. Get the list of templates that were checked as 'active' from the form.
        $activeTemplates = $_POST['active_templates'] ?? [];

        // 2. Read the current config.
        $config = ConfigManager::read();

        // 3. Get a list of ALL available template names.
        $allTemplateNames = array_keys($config['templateLinks'] ?? []);

        // 4. Determine which templates are disabled by finding the difference.
        // These are the templates that exist but were NOT in the submitted 'active' list.
        $disabledTemplates = array_diff($allTemplateNames, $activeTemplates);

        // 5. Update the config array with the new list of disabled templates.
        // array_values() is used to reset the array keys.
        $config['globallyDisabledTemplates'] = array_values($disabledTemplates);

        // 6. Write the updated config back to the file.
        if (ConfigManager::write($config)) {
            $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'وضعیت قالب‌ها با موفقیت به‌روزرسانی شد.'];
        } else {
            $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'خطا در ذخیره فایل پیکربندی.'];
        }

        // 7. Redirect back.
        header('Location: /templates');
        exit;
    }

    /**
     * Deletes a template from the config_data.json file.
     */
    public function delete(): void
    {
        $nameToDelete = $_GET['name'] ?? '';
        if (empty($nameToDelete)) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'نام قالب مشخص نشده است.'];
            header('Location: /templates');
            exit;
        }

        $config = ConfigManager::read();
        if (!isset($config['templateLinks'][$nameToDelete])) {
            $_SESSION['flash_message'] = ['type' => 'warning', 'text' => 'قالبی با این نام یافت نشد.'];
            header('Location: /templates');
            exit;
        }

        unset($config['templateLinks'][$nameToDelete]);
        $config['globallyDisabledTemplates'] = array_values(array_diff($config['globallyDisabledTemplates'] ?? [], [$nameToDelete]));
        $config['connectionBasedConfigs'] = array_values(array_diff($config['connectionBasedConfigs'] ?? [], [$nameToDelete]));
        $config['trojBasedConfigs'] = array_values(array_diff($config['trojBasedConfigs'] ?? [], [$nameToDelete]));
        
        if (ConfigManager::write($config)) {
            $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'قالب با موفقیت حذف شد.'];
        } else {
            $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'خطا در ذخیره فایل پیکربندی.'];
        }
        header('Location: /templates');
        exit;
    }
}