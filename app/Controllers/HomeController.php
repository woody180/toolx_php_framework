<?php namespace App\Controllers;

use \R as R;

class HomeController {
    
    public function index($req, $res) {
        
        // Render view
        return $res->render('welcome', [
            'title' => 'APP Title'
        ]);
    }
}