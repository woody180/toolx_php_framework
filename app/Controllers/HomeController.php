<?php namespace App\Controllers;


class HomeController {
    
    public function index($req, $res) {

        // Render view
        return $res->render('welcome', [
            'title' => 'APP Title'
        ]);
    }
}