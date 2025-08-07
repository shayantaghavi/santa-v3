<?php
// app/Controllers/AuthController.php

namespace App\Controllers;

/**
 * AuthController
 * Handles user authentication (login and logout).
 */
class AuthController extends BaseController
{
    /**
     * Displays the login form.
     * If the user is already logged in, it redirects them to the dashboard.
     *
     * @return void
     */
    public function showLoginForm(): void
    {
        // Redirect logged-in users away from the login page.
        if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
            header('Location: /dashboard');
            exit;
        }

        // We will create a separate, simpler layout for the login page.
        // For now, we reuse the main one, but this will be improved.
        $this->view('auth.login', ['pageTitle' => 'ورود به پنل مدیریت']);
    }

    /**
     * Handles the login form submission.
     *
     * @return void
     */
    public function handleLogin(): void
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        // Get admin credentials from our settings file.
        $adminUsername = setting('admin_user.username', 'admin');
        $adminPassword = setting('admin_user.password', 'password');

        // Check if the provided credentials are correct.
        if ($username === $adminUsername && $password === $adminPassword) {
            // Credentials are correct. Set session variables.
            $_SESSION['user_logged_in'] = true;
            $_SESSION['username'] = $username;
            
            // Regenerate session ID for security.
            session_regenerate_id(true);

            // Redirect to the main dashboard.
            header('Location: /dashboard');
            exit;
        } else {
            // Credentials are incorrect. Set a flash message and redirect back.
            $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'نام کاربری یا رمز عبور اشتباه است.'];
            header('Location: /login');
            exit;
        }
    }

    /**
     * Logs the user out by destroying the session.
     *
     * @return void
     */
    public function logout(): void
    {
        // Unset all session variables.
        $_SESSION = [];

        // Destroy the session.
        session_destroy();

        // Redirect to the login page.
        header('Location: /login');
        exit;
    }
}