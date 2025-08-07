<?php
// public_html/index.php

/**
 * The Front Controller.
 * This is the single entry point for all requests into the application.
 * Its job is to bootstrap the application, parse the request, and dispatch
 * it to the appropriate controller based on the defined routes.
 */

// 1. Bootstrap the Application
// This includes all our settings, paths, and the class autoloader.
// From this point on, our entire application environment is ready.
require_once __DIR__ . '/config/bootstrap.php';


// 2. Load the Application Routes
// This file returns the array that maps URIs to controllers.
$routes = require_once ROUTES_PATH . '/web.php';


// 3. Parse the Incoming Request URI
// Get the requested URI (e.g., '/users', '/dashboard') from the server variables.
$requestUri = $_SERVER['REQUEST_URI'];

// Remove query strings (?foo=bar) from the URI to get a clean path.
$requestPath = parse_url($requestUri, PHP_URL_PATH);
// Ensure it's a valid path, defaulting to '/' if empty.
$requestPath = $requestPath ?: '/';


// 4. Route the Request
// Look for the clean request path in our routes array.
if (isset($routes[$requestPath])) {
    // A matching route was found.
    // Unpack the handler array [ControllerName::class, 'methodName']
    [$controllerName, $methodName] = $routes[$requestPath];

    // Check if the controller class and method actually exist.
    if (class_exists($controllerName) && method_exists($controllerName, $methodName)) {
        // Create a new instance of the controller.
        // The autoloader in bootstrap.php will find and load the class file for us.
        $controller = new $controllerName();

        // Call the specified method on the controller to handle the request.
        $controller->$methodName();
    } else {
        // This is a server error: the route is defined, but the class/method is missing.
        header("HTTP/1.0 500 Internal Server Error");
        echo "Error: Controller or method not found for route '{$requestPath}'.";
        // You would log this error in a real application.
        error_log("Routing Error: Class '{$controllerName}' or method '{$methodName}' not found.");
    }
} else {
    // No matching route was found for the requested URI.
    // Send a 404 Not Found response.
    header("HTTP/1.0 404 Not Found");
    // You can later create a nice 404 page in your views.
    echo "<h1>404 Not Found</h1><p>The page you requested could not be found.</p>";
}