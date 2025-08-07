<?php
// routes/web.php

/**
 * This file defines the application's web routes.
 */

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\SettingsController;
use App\Controllers\TemplateController;
use App\Controllers\UserController;
use App\Controllers\SubscriptionController;

return [
    // General Pages
    '/' => [DashboardController::class, 'index'],
    '/dashboard' => [DashboardController::class, 'index'],
    
    // Authentication
    '/login' => [AuthController::class, 'showLoginForm'],
    '/login/handle' => [AuthController::class, 'handleLogin'],
    '/logout' => [AuthController::class, 'logout'],

    // User Management
    '/users' => [UserController::class, 'index'],
    '/users/create' => [UserController::class, 'create'],
    '/users/store' => [UserController::class, 'store'],
    '/users/edit' => [UserController::class, 'edit'],
    '/users/update' => [UserController::class, 'update'],
    '/users/delete' => [UserController::class, 'delete'],
    '/users/sync' => [UserController::class, 'syncFromXui'],

    // Settings
    '/settings' => [SettingsController::class, 'index'],
    '/settings/general' => [SettingsController::class, 'updateGeneral'],
    '/settings/db' => [SettingsController::class, 'updateDb'],
    '/settings/panels/store' => [SettingsController::class, 'storePanel'],
    '/settings/panels/edit' => [SettingsController::class, 'editPanel'],
    '/settings/panels/update' => [SettingsController::class, 'updatePanel'],
    '/settings/panels/delete' => [SettingsController::class, 'deletePanel'],

    // --- NEW TEMPLATE MANAGEMENT ROUTES ---
    '/templates' => [TemplateController::class, 'index'],
    '/templates/store' => [TemplateController::class, 'store'],
    '/templates/delete' => [TemplateController::class, 'delete'],
    '/templates/status-update' => [TemplateController::class, 'updateStatuses'],
    // We will add edit/update routes later if needed.
    // --- END NEW ROUTES ---

    // Subscription Endpoint (Placeholder for now)
    '/subscription' => [SubscriptionController::class, 'generate'],
];