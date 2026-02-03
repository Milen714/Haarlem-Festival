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
}