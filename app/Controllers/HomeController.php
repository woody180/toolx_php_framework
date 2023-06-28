<?php namespace App\Controllers;

use \R as R;

class HomeController {
    
    public function index($req, $res) {
        
        $res->getCached('welcome');
        
        initModel('articles');
        

        // Render view
        return $res->render('welcome', [
            'title' => 'APP Title',
            'articles' => R::find('articles')
        ])->cache('welcome', 20);
    }
}