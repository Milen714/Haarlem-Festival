<?php 

ini_set('session.use_strict_mode', 1);
ini_set('session.use_only_cookies', 1);
//ini_set('session.gc_maxlifetime', 60);

//secure cookie params for production
// session_set_cookie_params([
//     'lifetime' => 1800,
//     'path' => '/',
//     'domain' => 'localhost', // Set to your domain
//     'secure' => true,
//     'httponly' => true,
//     'samesite' => 'Strict'
// ]);

//dev cookie params for testing over HTTP
session_set_cookie_params([
    'lifetime' => 1800,
    'path' => '/',
    'domain' => null, // CHANGED: Let PHP handle the domain/IP automatically
    'secure' => false, // CHANGED: Must be false for HTTP testing
    'httponly' => true,
    'samesite' => 'Lax' // CHANGED: Better compatibility for dev
]);

session_start();

if(!isset($_SESSION['last_regeneration'])) {

    session_regenerate_id(true); 
    $_SESSION['last_regeneration'] = time();
    // var_dump("Session ID regenerated on first load. ID: " . session_id());
    // var_dump($_SESSION['last_regeneration']);

} else{
    $interval = 60 * 30; // 30 minutes

    if(time() - $_SESSION['last_regeneration'] >= $interval){

        session_regenerate_id(true); 
        $_SESSION['last_regeneration'] = time();
        // var_dump("Session ID regenerated due to timeout. ID: " . session_id());
        // var_dump($_SESSION['last_regeneration']);
    }
}