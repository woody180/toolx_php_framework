<?php namespace App\Controllers;

use \R as R;

class HomeController {
    
    public function index($req, $res) {
        
        // Render view
        return $res->render('welcome', [
            'title' => 'APP Title'
        ]);
    }


    public function about($req, $res) {
        return $res->send('about');
    }

    
    public function gallery($req, $res) {
        return $res->send('gallery page');
    }
}