<?php

namespace App\Controllers;

class BaseController
{
    protected function view($viewName, $vars = [], $layout = 'layouts/mainLayout')
    {
        // Load the view and capture output
        ob_start();
        extract($vars);
        
        require __DIR__ . '/../../Views/' . $viewName . '.php';
        // ob_get_clean(); prevent double output and turns the inside of the require ^^ into a string which is stored in $content
        $content = ob_get_clean();
        
        // Load the layout with the content
        extract(array_merge($vars, ['content' => $content]));
        require __DIR__ . '/../../Views/' . $layout . '.php';
        // The layout will use the $content variable to display the view content in the main tag 
        //Layout is foooter and header around the content
    }

    protected function jsonResponse($data, $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
    }
    protected function redirect($url)
    {
        header('Location: ' . $url);
        exit();         
    }
    protected function notFound()
    {
        http_response_code(404);
        echo "404 Not Found";
        exit();
    }
    protected function internalServerError()
    {
        http_response_code(500);
        echo "500 Internal Server Error";
        exit();
    }
    protected function forbidden()
    {
        http_response_code(403);
        echo "403 Forbidden";
        exit();
    }
    protected function cmsLayout($viewName, $vars = [],$layout = 'layouts/cmsLayout')
    {
         // Load the view and capture output
        ob_start();
        extract($vars);
        
        require __DIR__ . '/../../Views/' . $viewName . '.php';
        // ob_get_clean(); prevent double output and turns the inside of the require ^^ into a string which is stored in $content
        $content = ob_get_clean();
        
        // Load the layout with the content
        extract(array_merge($vars, ['content' => $content]));
        require __DIR__ . '/../../Views/' . $layout . '.php';
        // The layout will use the $content variable to display the view content in the main tag 
        //Layout is foooter and header around the content
    }
}