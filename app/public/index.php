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
use App\Controllers\CMS\CmsMediaController;
use App\Services\AuthService;
use App\Middleware\RoleMiddleware;

/**
 * Define the routes for the application.
 * 
 * @phpstan-ignore-next-line
 */
$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    $r->addRoute('GET', '/', ['App\Controllers\HomeController', 'index']);
    $r->addRoute('GET', '/getSchedule', ['App\Controllers\HomeController', 'getSchedulePartial']);
    $r->addRoute('POST', '/setTheme', ['App\Controllers\HomeController', 'setTheme']);
    $r->addRoute('GET', '/login', ['App\Controllers\AccountController', 'login']);
    $r->addRoute('POST', '/login', ['App\Controllers\AccountController', 'loginPost']);
    $r->addRoute('GET', '/signup', ['App\Controllers\AccountController', 'signup']);
    $r->addRoute('POST', '/signup', ['App\Controllers\AccountController', 'signupPost']);
    //$r->addRoute('POST', '/capcha', ['App\Controllers\AccountController', 'validateCaptcha']);
    $r->addRoute('GET', '/forgot-password', ['App\Controllers\AccountController', 'forgotPassword']);
    $r->addRoute('POST', '/forgot-password', ['App\Controllers\AccountController', 'forgotPasswordPost']);
    $r->addRoute('POST', '/logout', ['App\Controllers\AccountController', 'logout']);
    $r->addRoute('GET', '/reset-password', ['App\Controllers\AccountController', 'resetPassword']);
    $r->addRoute('POST', '/reset-password', ['App\Controllers\AccountController', 'resetPasswordPost']);
    $r->addRoute('GET', '/starting-points', ['App\Controllers\HomeController', 'getStartingPoints']);
    $r->addRoute('GET', '/getScheduleDates', ['App\Controllers\HomeController', 'getScheduleDates']);

    /* Magic Page Route */
    $r->addRoute('GET', '/events-magic', ['App\Controllers\MagicController', 'index']);
    $r->addRoute('GET', '/events-magic-accessibility', ['App\Controllers\MagicController', 'accessibility']);
    $r->addRoute('GET', '/events-magic-lorentz-show', ['App\Controllers\MagicController', 'lorentzFormula']);

    /* Jazz Event Route */
    $r->addRoute('GET', '/events-jazz', ['App\Controllers\JazzController', 'index']);
    $r->addRoute('GET', '/events-jazz/schedule', ['App\Controllers\JazzController', 'schedule']);
    $r->addRoute('GET', '/events-jazz/artist/{slug}', ['App\Controllers\JazzArtistController', 'detail']);
   
    /* Dance Event Route */
    $r->addRoute('GET', '/events-dance', ['App\Controllers\DanceController', 'index']);
    $r->addRoute('GET', '/events-dance/lineup', ['App\Controllers\DanceController', 'lineUp']);
    $r->addRoute('GET', '/events-dance/artist/{slug}', ['App\Controllers\DanceArtistController', 'artistDetail']);

    /* Yummy event page */
    //$r->addRoute('GET', '/events-yummy', ['App\Controllers\YummyController', 'index']);
    $r->addRoute('GET', '/events-yummy', ['App\Controllers\YummyController', 'yummy']);
    $r->addRoute('GET', '/events-yummy/restaurants', ['App\Controllers\YummyController', 'displayRestaurants']);
    $r->addRoute('GET', '/events-yummy/restaurants/{slug}', ['App\Controllers\YummyController', 'restaurantDetail']);
    
    $r->addRoute('GET', '/dance', ['App\Controllers\DanceController', 'index']);
    
    /* History Event Route */
    $r->addRoute('GET', '/events-history', ['App\Controllers\HistoryController', 'index']);
    $r->addRoute('GET', '/history-tour', ['App\Controllers\HistoryController', 'tour']);
    $r->addRoute('GET', '/history/detail/{slug}', ['App\Controllers\HistoryController', 'detail']);

    /* CMS Routes */
    $r->addRoute('GET', '/cms', ['App\Controllers\CmsController', 'dashboard']);
    $r->addRoute('GET', '/cms/page/edit/{slug}', ['App\Controllers\CmsPageController', 'editBySlug']);
    $r->addRoute('POST', '/cms/page/update', ['App\Controllers\CmsPageController', 'update']);

    /* CMS Media Routes (AJAX) */
    $r->addRoute('POST', '/cms/media/upload-tinymce', ['App\Controllers\CMS\CmsMediaController', 'uploadTinyMCE']);


    /* CMS Artist Management*/
    $r->addRoute('GET', '/cms/artists', ['App\Controllers\ArtistController', 'index']);
    $r->addRoute('GET', '/cms/artists/create', ['App\Controllers\ArtistController', 'create']);
    $r->addRoute('POST', '/cms/artists/store', ['App\Controllers\ArtistController', 'store']);
    $r->addRoute('GET', '/cms/artists/edit/{id:\d+}', ['App\Controllers\ArtistController', 'edit']);
    $r->addRoute('POST', '/cms/artists/update/{id:\d+}', ['App\Controllers\ArtistController', 'update']);
    $r->addRoute('POST', '/cms/artists/delete/{id:\d+}', ['App\Controllers\ArtistController', 'delete']);

    /*CMS Restaurant Management */
    $r->addRoute('GET', '/cms/restaurants', ['App\Controllers\RestaurantController', 'index']);
    $r->addRoute('GET', '/cms/restaurants/create', ['App\Controllers\RestaurantController', 'create']);
    $r->addRoute('GET', '/cms/restaurants/edit/{id:\d+}', ['App\Controllers\RestaurantController', 'edit']);
    $r->addRoute('POST', '/cms/restaurants/update/{id:\d+}', ['App\Controllers\RestaurantController', 'update']);
    $r->addRoute('POST', '/cms/restaurants/store', ['App\Controllers\RestaurantController', 'store']);
    $r->addRoute('POST', '/cms/restaurants/delete/{id:\d+}', ['App\Controllers\RestaurantController', 'delete']);
    $r->addRoute('POST', '/cms/artists/gallery-remove/{artistId:\d+}/{mediaId:\d+}', ['App\Controllers\ArtistController', 'removeGalleryImage']);

    /* CMS Venue Management*/
    $r->addRoute('GET', '/cms/venues', ['App\Controllers\VenueController', 'index']);
    $r->addRoute('GET', '/cms/venues/create', ['App\Controllers\VenueController', 'create']);
    $r->addRoute('POST', '/cms/venues/store', ['App\Controllers\VenueController', 'store']);
    $r->addRoute('GET', '/cms/venues/edit/{id:\d+}', ['App\Controllers\VenueController', 'edit']);
    $r->addRoute('POST', '/cms/venues/update/{id:\d+}', ['App\Controllers\VenueController', 'update']);
    $r->addRoute('POST', '/cms/venues/delete/{id:\d+}', ['App\Controllers\VenueController', 'delete']);

    /* CMS User Management*/
    $r->addRoute('GET', '/cms/users', ['App\Controllers\UserController', 'index']);
    $r->addRoute('GET', '/cms/users/create', ['App\Controllers\UserController', 'create']);
    $r->addRoute('POST', '/cms/users/store', ['App\Controllers\UserController', 'store']);
    $r->addRoute('GET', '/cms/users/edit/{id:\d+}', ['App\Controllers\UserController', 'edit']);
    $r->addRoute('POST', '/cms/users/update/{id:\d+}', ['App\Controllers\UserController', 'update']);
    $r->addRoute('POST', '/cms/users/delete/{id:\d+}', ['App\Controllers\UserController', 'delete']);

    /*Landmark cms*/
    $r->addRoute('GET', '/cms/landmarks', ['App\Controllers\LandmarkController', 'index']);

    $r->addRoute('GET', '/cms/landmarks/create', ['App\Controllers\LandmarkController', 'create']);
    $r->addRoute('POST', '/cms/landmarks/store', ['App\Controllers\LandmarkController', 'store']);

    $r->addRoute('POST', '/cms/landmarks/delete/{id:\d+}', ['App\Controllers\LandmarkController', 'delete']);
    $r->addRoute('GET', '/cms/landmarks/edit/{id:\d+}', ['App\Controllers\LandmarkController', 'edit']);
    $r->addRoute('POST', '/cms/landmarks/update/{id:\d+}', ['App\Controllers\LandmarkController', 'update']);

    $r->addRoute('GET', '/settings', ['App\Controllers\AccountController', 'settings']);
    $r->addRoute('POST', '/settings/update', ['App\Controllers\AccountController', 'update']);

    /* CMS Profile Management */
    $r->addRoute('GET', '/cms/profile', ['App\Controllers\AccountController', 'settings']);
    $r->addRoute('POST', '/cms/profile/update', ['App\Controllers\AccountController', 'update']);

    /* CMS Schedule Management */
    $r->addRoute('GET', '/cms/schedules', ['App\Controllers\ScheduleController', 'index']);
    $r->addRoute('GET', '/cms/schedules/create', ['App\Controllers\ScheduleController', 'create']);
    $r->addRoute('POST', '/cms/schedules/store', ['App\Controllers\ScheduleController', 'store']);
    $r->addRoute('GET', '/cms/schedules/edit/{id:\d+}', ['App\Controllers\ScheduleController', 'edit']);
    $r->addRoute('POST', '/cms/schedules/update/{id:\d+}', ['App\Controllers\ScheduleController', 'update']);
    $r->addRoute('POST', '/cms/schedules/delete/{id:\d+}', ['App\Controllers\ScheduleController', 'delete']);

    /* Payment */
    $r->addRoute('GET', '/payment', ['App\Controllers\PaymentController', 'index']);
    $r->addRoute('GET', '/checkout', ['App\Controllers\PaymentController', 'checkout']);
    $r->addRoute('GET', '/create-checkout-session', ['App\Controllers\PaymentController', 'createCheckoutSession']);
    $r->addRoute('GET', '/return', ['App\Controllers\PaymentController', 'return']);
    $r->addRoute('POST', '/payment-status', ['App\Controllers\PaymentController', 'status']);
    $r->addRoute('GET', '/tests', ['App\Controllers\PaymentController', 'test']);
    $r->addRoute('POST', '/tests/create-order', ['App\Controllers\PaymentController', 'createTestOrder']);
    $r->addRoute('GET', '/payment-details', ['App\Controllers\PaymentController', 'details']);

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
        (new HomeController())->notFound();
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