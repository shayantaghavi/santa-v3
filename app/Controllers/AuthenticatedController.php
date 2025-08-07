<?php
// app/Controllers/AuthenticatedController.php

namespace App\Controllers;

/**
 * AuthenticatedController
 *
 * This class acts as a base controller for any pages that require a user to be logged in.
 * It extends the global BaseController to inherit its methods (like view()).
 * Its constructor automatically checks for an active session.
 */
abstract class AuthenticatedController extends BaseController
{
    /**
     * The constructor is called automatically when any child controller is instantiated.
     * It checks if the user is logged in. If not, it redirects to the login page.
     */
    public function __construct()
    {
        // Check if the session variable for login exists and is true.
        if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
            // If the user is not logged in, set a helpful flash message.
            $_SESSION['flash_message'] = ['type' => 'warning', 'text' => 'برای دسترسی به این صفحه، ابتدا باید وارد شوید.'];

            // Redirect them to the login page.
            header('Location: /login');

            // Stop script execution immediately.
            exit;
        }
    }
}