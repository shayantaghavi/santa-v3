<?php
// app/Controllers/UserController.php

namespace App\Controllers;

use App\Managers\DatabaseManager;
use App\Managers\XuiApiManager;
use PDOException;

/**
 * UserController
 * Handles all logic related to user management pages.
 * It now extends AuthenticatedController to protect all its methods.
 */
class UserController extends AuthenticatedController
{
    /**
     * Displays the main user management page with a list of all users,
     * enriched with live data from X-UI panels.
     */
    public function index(): void
    {
        $dbUsers = DatabaseManager::select('SELECT id, remark, uuid, status, created_at FROM users ORDER BY remark ASC');
        $xuiApiManager = new XuiApiManager();
        $xuiData = $xuiApiManager->getXuiAllClientsTrafficAggregatedByUuid();

        $users = [];
        foreach ($dbUsers as $dbUser) {
            $uuid = $dbUser['uuid'];
            $liveData = $xuiData[$uuid] ?? null;

            $dbUser['up'] = $liveData['up'] ?? 0;
            $dbUser['down'] = $liveData['down'] ?? 0;
            $dbUser['total'] = $liveData['total'] ?? 0;
            $dbUser['expiryTime'] = $liveData['expiryTime'] ?? 0;
            $dbUser['xui_status'] = $liveData['enable'] ?? false;
            $dbUser['is_online'] = isset($liveData);

            $users[] = $dbUser;
        }
        
        $data = [
            'pageTitle' => 'مدیریت کاربران',
            'users' => $users,
        ];
        $this->view('pages.users', $data);
    }
    
    /**
     * Finds users that exist on X-UI panels but not in the local database,
     * and adds them to the local database.
     */
    public function syncFromXui(): void
    {
        try {
            $localUsersResult = DatabaseManager::select('SELECT uuid FROM users');
            $localUuids = array_column($localUsersResult, 'uuid');

            $xuiApiManager = new XuiApiManager();
            $xuiData = $xuiApiManager->getXuiAllClientsTrafficAggregatedByUuid();
            $xuiUuids = array_keys($xuiData);

            $newUuids = array_diff($xuiUuids, $localUuids);

            $newUsersCount = 0;
            if (!empty($newUuids)) {
                $sql = "INSERT INTO users (remark, uuid, status, created_at) VALUES (:remark, :uuid, 'active', NOW())";
                
                foreach ($newUuids as $uuid) {
                    $remark = 'xui_synced_' . substr(md5($uuid), 0, 8);
                    DatabaseManager::execute($sql, [
                        ':remark' => $remark,
                        ':uuid' => $uuid,
                    ]);
                    $newUsersCount++;
                }
            }

            if ($newUsersCount > 0) {
                $_SESSION['flash_message'] = ['type' => 'success', 'text' => "همگام‌سازی کامل شد. {$newUsersCount} کاربر جدید اضافه شد."];
            } else {
                $_SESSION['flash_message'] = ['type' => 'info', 'text' => 'همگام‌سازی انجام شد. هیچ کاربر جدیدی یافت نشد.'];
            }
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'خطا در پایگاه داده هنگام همگام‌سازی.'];
            error_log($e->getMessage());
        }

        header('Location: /users');
        exit;
    }

    /**
     * Shows the form for creating a new user.
     */
    public function create(): void
    {
        $data = [
            'pageTitle' => 'افزودن کاربر جدید',
        ];
        $this->view('pages.users_create', $data);
    }

    /**
     * Stores a new user in the database.
     */
    public function store(): void
    {
        $remark = trim($_POST['remark'] ?? '');
        $uuid = trim($_POST['uuid'] ?? '');
        if (empty($remark) || empty($uuid)) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'نام کاربری و UUID نمی‌توانند خالی باشند.'];
            header('Location: /users/create');
            exit;
        }
        try {
            $sql = "INSERT INTO users (remark, uuid, status, created_at) VALUES (:remark, :uuid, 'active', NOW())";
            $params = [':remark' => $remark, ':uuid' => $uuid];
            DatabaseManager::execute($sql, $params);
            $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'کاربر با موفقیت ایجاد شد.'];
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'خطا: نام کاربری یا UUID تکراری است.'];
            } else {
                $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'خطا در پایگاه داده رخ داد.'];
                error_log($e->getMessage());
            }
        }
        header('Location: /users');
        exit;
    }

    /**
     * Shows the form for editing an existing user.
     */
    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'شناسه کاربر نامعتبر است.'];
            header('Location: /users');
            exit;
        }
        $sql = "SELECT id, remark, uuid, status FROM users WHERE id = :id";
        $result = DatabaseManager::select($sql, [':id' => $id]);
        $user = $result[0] ?? null;
        if (!$user) {
            $_SESSION['flash_message'] = ['type' => 'warning', 'text' => 'کاربری با این شناسه یافت نشد.'];
            header('Location: /users');
            exit;
        }
        $data = ['pageTitle' => 'ویرایش کاربر: ' . htmlspecialchars($user['remark']), 'user' => $user];
        $this->view('pages.users_edit', $data);
    }

    /**
     * Updates an existing user in the database.
     */
    public function update(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        $remark = trim($_POST['remark'] ?? '');
        $status = trim($_POST['status'] ?? '');
        if ($id <= 0 || empty($remark) || !in_array($status, ['active', 'inactive'])) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'داده‌های ارسالی نامعتبر است.'];
            header('Location: /users');
            exit;
        }
        try {
            $sql = "UPDATE users SET remark = :remark, status = :status WHERE id = :id";
            $params = [':remark' => $remark, ':status' => $status, ':id' => $id];
            DatabaseManager::execute($sql, $params);
            $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'تغییرات با موفقیت ذخیره شد.'];
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'خطا: نام کاربری تکراری است.'];
            } else {
                $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'خطا در پایگاه داده هنگام به‌روزرسانی.'];
                error_log($e->getMessage());
            }
        }
        header('Location: /users');
        exit;
    }

    /**
     * Deletes a user from the database.
     */
    public function delete(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'شناسه کاربر نامعتبر است.'];
            header('Location: /users');
            exit;
        }
        try {
            $sql = "DELETE FROM users WHERE id = :id";
            $params = [':id' => $id];
            $rowCount = DatabaseManager::execute($sql, $params);
            if ($rowCount > 0) {
                $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'کاربر با موفقیت حذف شد.'];
            } else {
                $_SESSION['flash_message'] = ['type' => 'warning', 'text' => 'کاربری با این شناسه یافت نشد.'];
            }
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'خطا در پایگاه داده هنگام حذف کاربر.'];
            error_log($e->getMessage());
        }
        header('Location: /users');
        exit;
    }
}