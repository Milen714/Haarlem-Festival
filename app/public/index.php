<?php

/**
 * This is the central route handler of the application.
 * It uses FastRoute to map URLs to controller methods.
 * 
 * See the documentation for FastRoute for more information: https://github.com/nikic/FastRoute
 */

require __DIR__ . '/../vendor/autoload.php';

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;
use App\Controllers\HomeController;
use App\Controllers\AccountController;
use App\Services\AuthService;
use App\Middleware\RoleMiddleware;

/**
 * Define the routes for the application.
 */
$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    $r->addRoute('GET', '/', ['App\Controllers\HomeController', 'index']);
    $r->addRoute('GET', '/cms', ['App\Controllers\HomeController', 'adminIndex']);
    $r->addRoute('POST', '/setTheme', ['App\Controllers\HomeController', 'setTheme']);
    $r->addRoute('GET', '/login', ['App\Controllers\AccountController', 'login']);
    $r->addRoute('POST', '/login', ['App\Controllers\AccountController', 'loginPost']);
    $r->addRoute('GET', '/signup', ['App\Controllers\AccountController', 'signup']);
    $r->addRoute('POST', '/signup', ['App\Controllers\AccountController', 'signupPost']);
    $r->addRoute('GET', '/forgot-password', ['App\Controllers\AccountController', 'forgotPassword']);
    $r->addRoute('POST', '/forgot-password', ['App\Controllers\AccountController', 'forgotPasswordPost']);
    $r->addRoute('POST', '/logout', ['App\Controllers\AccountController', 'logout']);
    $r->addRoute('GET', '/reset-password', ['App\Controllers\AccountController', 'resetPassword']);
    $r->addRoute('POST', '/reset-password', ['App\Controllers\AccountController', 'resetPasswordPost']);
    $r->addRoute('GET', '/wysiwyg-demo', ['App\Controllers\HomeController', 'wysiwygDemo']);
    $r->addRoute('POST', '/wysiwyg-demo-post', ['App\Controllers\HomeController', 'wysiwygDemoPost']);
});


/**
 * Get the request method and URI from the server variables and invoke the dispatcher.
 */
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = strtok($_SERVER['REQUEST_URI'], '?');
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

/**
 * Switch on the dispatcher result and call the appropriate controller method if found.
 */
switch ($routeInfo[0]) {
    // Handle not found routes
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
       // (new HomeController())->notFound();
        break;
    // Handle routes that were invoked with the wrong HTTP method
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo 'Method Not Allowed';
        break;
    // Handle found routes
    case FastRoute\Dispatcher::FOUND:
        /**
         * $routeInfo contains the data about the matched route.
         * 
         * $routeInfo[1] is the whatever we define as the third argument the `$r->addRoute` method.
         *  For instance for: `$r->addRoute('GET', '/hello/{name}', ['App\Controllers\HelloController', 'greet']);`
         *  $routeInfo[1] will be `['App\Controllers\HelloController', 'greet']`
         * 
         * Hint: we can use class strings like `App\Controllers\HelloController` to create new instances of that class.
         * Hint: in PHP we can use a string to call a class method dynamically, like this: `$instance->$methodName($args);`
         */
            
        // TODO: invoke the controller and method using the data in $routeInfo[1]

        /**
         * $route[2] contains any dynamic parameters parsed from the URL.
         * For instance, if we add a route like:
         *  $r->addRoute('GET', '/hello/{name}', ['App\Controllers\HelloController', 'greet']);
         * and the URL is `/hello/dan-the-man`, then `$routeInfo[2][name]` will be `dan-the-man`.
         */

        // TODO: pass the dynamic route data to the controller method
        // When done, visiting `http://localhost/hello/dan-the-man` should output "Hi, dan-the-man!"

        $controller = new $routeInfo[1][0]();
        $method = $routeInfo[1][1];
        $params = $routeInfo[2];
        
    //     if (session_status() === PHP_SESSION_NONE) {
    //     session_start();
    // }
        

        $authService = new AuthService();
        $roleMiddleware = new RoleMiddleware($authService);

        // Run the middleware check
        $roleMiddleware->check($controller, $method);
        $controller->$method($params);

        break;
}