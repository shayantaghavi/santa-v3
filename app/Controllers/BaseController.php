<?php
// app/Controllers/BaseController.php

// Define the namespace to match our directory structure.
namespace App\Controllers;

/**
 * Base Controller
 * This class will be extended by all other controllers in the application.
 * It now includes a more robust view renderer that supports layouts.
 */
abstract class BaseController
{
    /**
     * Renders a view by embedding it within a master layout file.
     *
     * @param string $viewName The name of the view file to render (e.g., 'pages.dashboard').
     * @param array $data Data to be passed to the view (e.g., ['pageTitle' => '...']).
     * @return void
     */
    protected function view(string $viewName, array $data = []): void
    {
        // Construct the full path to the specific page's content file.
        // Example: 'pages.dashboard' becomes '/path/to/project/views/pages/dashboard.php'
        $contentFile = VIEWS_PATH . '/' . str_replace('.', DIRECTORY_SEPARATOR, $viewName) . '.php';

        if (!file_exists($contentFile)) {
            header("HTTP/1.0 500 Internal Server Error");
            echo "Error: Content view file not found at '{$contentFile}'.";
            error_log("View Error: Content file not found at '{$contentFile}'.");
            exit;
        }

        // The extract() function turns the keys of the $data array into variables.
        // These variables ($pageTitle, etc.) will be available in both the layout and the content file.
        extract($data);

        // The path to the main layout file is fixed.
        $layoutFile = VIEWS_PATH . '/layouts/app.php';

        if (file_exists($layoutFile)) {
            // Now, instead of including the content directly, we include the main layout.
            // The layout file will be responsible for including the $contentFile.
            require $layoutFile;
        } else {
            header("HTTP/1.0 500 Internal Server Error");
            echo "Error: Main layout file not found at '{$layoutFile}'.";
            error_log("View Error: Layout file not found at '{$layoutFile}'.");
            exit;
        }
    }
}